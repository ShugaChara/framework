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
     * 注册默认回调
     * @param swoole_server $server
     * @param               $server_name
     */
    public function registerDefaultCallback(swoole_server $server, $server_name)
    {
        if (in_array($server_name, [static::SWOOLE_HTTP_SERVER, static::SWOOLE_WEBSOCKET_SERVER])) {
            // 注册连接事件
            $this->getEventsRegister()->addEvent(
                EventsRegister::onConnect,
                function (swoole_server $server, int $fd, int $reactorId) {}
            );

            // 注册请求事件
            $this->getEventsRegister()->addEvent(
                EventsRegister::onRequest,
                function (swoole_http_request $swooleRequest, swoole_http_response $swooleResponse) use ($server) {
                    // 转移 Swoole 请求对象
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

            // 注册默认的 Websocket onMessage 事件
            if ($server_name == static::SWOOLE_WEBSOCKET_SERVER) {
                $this->getEventsRegister()->addEvent(
                    EventsRegister::onOpen,
                    function (swoole_websocket_server $server, swoole_http_request $request) {}
                );

                $this->getEventsRegister()->addEvent(
                    EventsRegister::onMessage,
                    function(swoole_websocket_server $server, $frame) {}
                );
            }

            // 注册默认的工作程序启动事件
            $this->getEventsRegister()->addEvent(
                EventsRegister::onWorkerStart,
                function (swoole_server $server, $workerId) {
                    // 建立连接池
                    foreach (container()->getContainerServices() as $service) {
                        if ($service instanceof PoolInterface) {
                            $service->initPool();
                        }
                    }
                }
            );

            // 注册默认的工作进程退出事件
            $this->getEventsRegister()->addEvent(
                EventsRegister::onWorkerExit,
                function () {
                    Timer::clearAll();
                }
            );

            // 注册默认的 onTask 事件
            $this->getEventsRegister()->addEvent(
                EventsRegister::onTask,
                function (swoole_server $serv, int $task_id, int $src_worker_id, $data) {
                    $this->taskDispatcher(new Task($serv, $task_id, $src_worker_id, $data));
                }
            );
        }
    }

    /**
     * 加载进程
     */
    public function loadProcessor()
    {
        $processes = fnc()->c()->get('swoole.processor.swoole_list', []);
        foreach ($processes as $name => $process) {
            $this->getSwooleServer()->addProcess(
                (new $process($this->getName() . '.process.' . $name))->getProcess()
            );
        }

        $this->swooleHotReload();
    }

    /**
     * 加载监听
     */
    public function loadListener()
    {
        $listeners = fnc()->c()->get('swoole.listeners', []);
        foreach ($listeners as $listener) {
            $port = $this->getSwooleServer()->addListener(
                $listener['host'],
                $listener['port'],
                $listener['sock_type']
            );

            // 覆盖主服务器的设置
            if (isset($listener['setting'])) {
                $port->set($listener['setting']);
            }

            // 监听事件注册
            if (isset($listener['events']) && class_exists($listener['events'])) {
                $eventsClass = new $listener['events']($port);
                if ($eventsClass instanceof ListenersAbstract) {
                    // 注册监听事件
                    $this->registerClassEvents($eventsClass, $eventsClass);
                    // 注册到 Swoole on events
                    $this->registerSwooleEvents($eventsClass, $eventsClass->getServerPort());
                }
            }
        }
    }

    /**
     * Task 任务分发器
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
     * 获取进程 PID 目录
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
     * 获取进程 PID 文件
     * @param $process_name
     * @return string
     */
    public function getProcessPidFile($process_name)
    {
        return $this->getProcessPidPath() . '/' . $process_name . '.pid';
    }

    /**
     * 服务 热更新/热重启
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
                fnc()->serverChannel()->reload();   // 重启操作
            });

            $this->getSwooleServer()->addProcess($swooleListenRestart->getProcess());
        }
    }
}

