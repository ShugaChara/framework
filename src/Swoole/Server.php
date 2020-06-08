<?php
// +----------------------------------------------------------------------
// | Created by ShugaChara. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2020 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| shugachara Swoole Server
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework\Swoole;

use ShugaChara\Core\Utils\Helper\ArrayHelper;
use ShugaChara\Framework\Contracts\ListenersAbstract;
use ShugaChara\Framework\Contracts\PoolInterface;
use ShugaChara\Http\SwooleServerRequest;
use ShugaChara\Swoole\EventsRegister;
use ShugaChara\Swoole\Manager\Timer;
use ShugaChara\Swoole\Server as SwooleServer;
use ShugaChara\Swoole\SwooleHelper;
use ShugaChara\Swoole\Tools\SwooleListenRestart;
use swoole_http_request;
use swoole_http_response;
use swoole_server;
use swoole_websocket_server;

/**
 * Class Server
 * @package ShugaChara\Framework\Swoole
 */
class Server extends SwooleServer
{
    /**
     * connected file descriptor
     * @var int
     */
    protected $fd = 0;

    /**
     * reactor thread ID of the connection
     * @var int
     */
    protected $reactorId = 0;

    /**
     * Register the default callback
     * @param swoole_server $server
     * @param               $server_name
     */
    public function registerDefaultCallback(swoole_server $server, $server_name)
    {
        if (in_array($server_name, [static::SWOOLE_HTTP_SERVER, static::SWOOLE_WEBSOCKET_SERVER])) {
            // Register connect event
            $this->getEventsRegister()->addEvent(
                EventsRegister::onConnect,
                function (swoole_server $server, int $fd, int $reactorId) {
                    $this->setFd($fd);
                    $this->setReactorId($reactorId);
                }
            );

            // Register request event
            $this->getEventsRegister()->addEvent(
                EventsRegister::onRequest,
                function (swoole_http_request $swooleRequest, swoole_http_response $swooleResponse) use ($server) {
                    // Transfer Swoole request object
                    $request = SwooleServerRequest::createServerRequestFromSwoole($swooleRequest);
                    $response = fnc()->app()->handleRequest($request);
                    foreach ($response->getHeaders() as $key => $header) {
                        $swooleResponse->header($key, $response->getHeaderLine($key));
                    }
                    foreach ($response->getCookieParams() as $key => $cookieParam) {
                        $swooleResponse->cookie(
                            $key,
                            $cookieParam->getValue(),
                            $cookieParam->getExpire(),
                            $cookieParam->getPath(),
                            $cookieParam->getDomain(),
                            $cookieParam->isSecure(),
                            $cookieParam->isHttpOnly()
                        );
                    }

                    $swooleResponse->status($response->getStatusCode());
                    $swooleResponse->end((string) $response->getBody());
                    return true;
                }
            );

            // Register the default websocket onMessage event
            if ($server_name == static::SWOOLE_WEBSOCKET_SERVER) {
                $this->getEventsRegister()->addEvent('message', function(swoole_websocket_server $server, $frame) {});
            }

            // Register the default worker start event
            $this->getEventsRegister()->addEvent(
                EventsRegister::onWorkerStart,
                function (swoole_server $server, $workerId) use ($server_name) {
                    if(PHP_OS != 'Darwin'){
                        if( ($workerId < fnc()->c()->get('swoole.' . $server_name . '.setting.worker_num')) && $workerId >= 0){
                            SwooleHelper::setProcessRename(("{$server_name}.Worker.{$workerId}"));
                        }
                    }

                    // Establish Pool connection pool
                    foreach (container()->getContainerServices() as $service) {
                        if ($service instanceof PoolInterface) {
                            $service->initPool();
                        }
                    }
                }
            );

            // Register the default worker exit event
            $this->getEventsRegister()->addEvent(
                EventsRegister::onWorkerExit,
                function () {
                    Timer::clearAll();
                }
            );

            // Register the default onTask event
            $this->getEventsRegister()->addEvent(EventsRegister::onTask, function (swoole_server $serv, int $task_id, int $src_worker_id, $data) {
                $this->taskDispatcher(new Task($serv, $task_id, $src_worker_id, $data));
            });
        }
    }

    /**
     * Set connected file descriptor
     * @param $fd
     */
    public function setFd($fd)
    {
        $this->fd = $fd;
    }

    /**
     * Get connected file descriptor
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * Set reactor thread ID of the connection
     * @param $fd
     */
    public function setReactorId($reactorId)
    {
        $this->reactorId = $reactorId;
    }

    /**
     * Get reactor thread ID of the connection
     * @return int
     */
    public function getReactorId(): int
    {
        return $this->reactorId;
    }

    /**
     * Loading process
     */
    public function loadProcessor()
    {
        $processes = fnc()->c()->get('swoole.processor.swoole_list', []);
        foreach ($processes as $process) {
            $this->getServer()->addProcess(
                (new $process($this->getName() . ' process'))->getProcess()
            );
        }

        $this->swooleHotReload();
    }

    /**
     * Loading listener
     */
    public function loadListener()
    {
        $listeners = fnc()->c()->get('swoole.listeners', []);
        foreach ($listeners as $listener) {
            $port = $this->getServer()->addListener(
                $listener['host'],
                $listener['port'],
                $listener['sock_type']
            );

            // Override the main server's settings
            if (isset($listener['setting'])) {
                $port->set($listener['setting']);
            }

            // Listen event registration
            if (isset($listener['events']) && class_exists($listener['events'])) {
                $eventsClass = new $listener['events']($port);
                if ($eventsClass instanceof ListenersAbstract) {
                    // Register listener events
                    $classFunctions = get_class_methods($eventsClass);
                    foreach ($classFunctions as $event) {
                        if ('on' != substr($event, 0, 2)) {
                            continue;
                        }

                        $eventsClass->getEventsRegister()->addEvent(lcfirst(substr($event, 2)), [$eventsClass, $event]);
                    }

                    $events = $eventsClass->getEventsRegister()->allEvent();
                    foreach ($events as $event => $callback){
                        $eventsClass->getEventsRegister()->on(
                            $eventsClass->getServerPort(),
                            $event,
                            function (...$args) use ($callback) {
                                foreach ($callback as $item) {
                                    call_user_func($item, ...$args);
                                }
                            }
                        );
                    }
                }
            }
        }
    }

    /**
     * TaskDispatcher
     * @param Task $task
     */
    public function taskDispatcher(Task $task)
    {
        $taskDispatcherClass = fnc()->c()->get('swoole.task.dispatcher_class');
        $taskDispatcherClassInstance = class_exists($taskDispatcherClass) ? $taskDispatcherClass::getInstance() : null;
        if ($taskDispatcherClassInstance) {
            $taskDispatcherClassInstance->new($task);
        }
    }

    /**
     * Get process pid path
     * @return mixed|null
     */
    public function getProcessPidPath()
    {
        $pid_path = fnc()->c()->get('swoole.processor.pid_path');
        if (! file_exists($pid_path)) {
            mkdir($pid_path, 0755, true);
        }

        return $pid_path;
    }

    /**
     * Get process pid file
     * @param $process_name
     * @return string
     */
    public function getProcessPidFile($process_name)
    {
        return $this->getProcessPidPath() . '/' . $process_name . '.pid';
    }

    /**
     * Service hot update/hot restart
     */
    protected function swooleHotReload()
    {
        $hotreload = fnc()->c()->get('swoole.hotreload');
        if (ArrayHelper::get($hotreload, 'status', false)) {
            $swooleListenRestart = new SwooleListenRestart(ArrayHelper::get($hotreload, 'name', 'HotReload'));
            $swooleListenRestart->setConfig([
                'monitorDir'     =>    ArrayHelper::get($hotreload, 'monitorDir', fnc()->app()->getRootDirectory()),
                'monitorExt'     =>    ArrayHelper::get($hotreload, 'monitorExt', ['php']),
                'disableInotify' =>    ArrayHelper::get($hotreload, 'disableInotify', false),
            ]);

            $swooleListenRestart->restartSwooleServer(function () {
                // 重启操作
                fnc()->serverChannel()->reload();
            });

            $this->getServer()->addProcess($swooleListenRestart->getProcess());
        }
    }
}

