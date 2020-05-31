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
use ShugaChara\Framework\Contracts\PoolInterface;
use ShugaChara\Http\SwooleServerRequest;
use ShugaChara\Swoole\EventsRegister;
use ShugaChara\Swoole\Manager\Table;
use ShugaChara\Swoole\Manager\Timer;
use ShugaChara\Swoole\Server as SwooleServer;
use ShugaChara\Swoole\SwooleHelper;
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
     * Register the default callback
     * @param swoole_server $server
     * @param               $server_name
     */
    public function registerDefaultCallback(swoole_server $server, $server_name)
    {
        if (in_array($server_name, [static::SWOOLE_HTTP_SERVER, static::SWOOLE_WEBSOCKET_SERVER])) {
            Table::addTable('fd', [
                'fd'    =>  ['type' => Table::TYPE_INT, 'size' => 4],
                'reactor_id'    =>  ['type' => Table::TYPE_INT, 'size' => 4],
            ], 5);
            $this->getServer()->table = Table::getTable('fd');

            // Register connect event
            $this->getEventsRegister()->addEvent(
                EventsRegister::onConnect,
                function (swoole_server $server, int $fd, int $reactorId) {
                    $server->table->set('fd', ['fd' => $fd, 'reactor_id' => $reactorId]);
                }
            );

            // Register request event
            $this->getEventsRegister()->addEvent(
                EventsRegister::onRequest,
                function (swoole_http_request $swooleRequest, swoole_http_response $swooleResponse) use ($server) {
                    // Transfer Swoole request object
                    $request = SwooleServerRequest::createServerRequestFromSwoole($swooleRequest);
                    $response = fn()->app()->handleRequest($request);
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
                        if( ($workerId < fn()->c()->get('swoole.' . $server_name . '.setting.worker_num')) && $workerId >= 0){
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
            $this->getEventsRegister()->addEvent(EventsRegister::onTask, function (swoole_server $serv, int $task_id, int $src_worker_id, mixed $data) {});
        }
    }

    /**
     * Get connected file descriptor
     * @return int
     */
    public function getFd(): int
    {
        return (int) ArrayHelper::get($this->getServer()->table->get('fd'), 'fd', 0);
    }

    /**
     * Get reactor thread ID of the connection
     * @return int
     */
    public function getReactorId(): int
    {
        return (int) ArrayHelper::get($this->getServer()->table->get('fd'), 'reactor_id', 0);
    }
}

