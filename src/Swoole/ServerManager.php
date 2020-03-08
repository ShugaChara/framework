<?php
// +----------------------------------------------------------------------
// | Created by linshan. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2020 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

namespace ShugaChara\Framework\Swoole;

use ShugaChara\Framework\Contracts\PoolInterface;
use ShugaChara\Http\SwooleServerRequest;
use ShugaChara\Swoole\Events\EventsRegister;
use ShugaChara\Swoole\SwooleServerManager;
use ShugaChara\Swoole\TimerManager;
use swoole_server;
use swoole_http_request;
use swoole_http_response;
use swoole_websocket_server;

/**
 * Class ServerManager
 * @package ShugaChara\Framework\Swoole
 */
class ServerManager extends SwooleServerManager
{
    /**
     * 注册默认回调
     * @param swoole_server $server
     * @param               $server_name
     */
    public function registerDefaultCallback(swoole_server $server, $server_name)
    {
        if (in_array($server_name, [SWOOLE_HTTP_SERVER, SWOOLE_WEBSOCKET_SERVER])) {
            // 注册请求事件 request event
            $this->getEventsRegister()->on(
                $server,
                EventsRegister::onRequest,
                function (swoole_http_request $swooleRequest, swoole_http_response $swooleResponse) {
                    // 转接 Swoole request 对象
                    $request = SwooleServerRequest::createServerRequestFromSwoole($swooleRequest);
                    $response = app()->handleRequest($request);
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

            // 注册默认的 websocket onMessage event
            if ($server_name == SWOOLE_WEBSOCKET_SERVER) {
                $this->getEventsRegister()->addEvent('message', function(swoole_websocket_server $server, $frame) {});
            }

            // 注册默认的 worker start event
            $this->getEventsRegister()->addEvent(
                EventsRegister::onWorkerStart,
                function (swoole_server $server, $workerId) use ($server_name) {
                    if(PHP_OS != 'Darwin'){
                        if( ($workerId < config()->get('swoole.' . $server_name . '.setting.worker_num')) && $workerId >= 0){
                            process_rename("{$server_name}.Worker.{$workerId}");
                        }
                    }

                    // 建立Pool连接池
                    foreach (container()->getContainerServices() as $service) {
                        if ($service instanceof PoolInterface) {
                            $service->initPool();
                        }
                    }
                }
            );

            // 注册默认的 worker exit event
            $this->getEventsRegister()->addEvent(
                EventsRegister::onWorkerExit,
                function () {
                    TimerManager::getInstance()->clearAll();
                }
            );

            // 注册onTask事件
            $this->getEventsRegister()->addEvent(EventsRegister::onTask, function (swoole_server $serv, int $task_id, int $src_worker_id, mixed $data) {
                // ...待填充
            });
        }
    }
}