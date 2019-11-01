<?php
// +----------------------------------------------------------------------
// | Created by linshan. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2019 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

namespace ShugaChara\Framework\Swoole;

use ShugaChara\Core\Traits\Singleton;
use ShugaChara\Framework\Constant\Consts;
use ShugaChara\Framework\Contracts\PoolInterface;
use ShugaChara\Http\SwooleServerRequest;
use ShugaChara\Swoole\Events\EventsRegister;
use ShugaChara\Swoole\Manager\SwooleServerManager as MasterManager;
use ShugaChara\Swoole\Manager\TimerManager;
use swoole_server;
use swoole_http_request;
use swoole_http_response;

/**
 * Class SwooleServerManager
 * @method static $this getInstance(...$args)
 * @package ShugaChara\Framework\Swoole
 */
class SwooleServerManager extends MasterManager
{
    use Singleton;

    /**
     * 注册默认回调
     */
    public function registerDefaultCallback(swoole_server $server, $serverName)
    {
        if (in_array(strtolower($serverName), [Consts::SWOOLE_SERVER_HTTP, Consts::SWOOLE_SERVER_WEBSOCKET])) {
            // 注册请求事件 request event
            $this->getSwooleServerEventRegister()->on(
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

            // 注册默认的 worker start
            $this->getSwooleServerEventRegister()->addEvent(
                EventsRegister::onWorkerStart,
                function (swoole_server $server, $workerId) use ($serverName) {
                    if(PHP_OS != 'Darwin'){
                        if( ($workerId < config()->get('swoole.' . $serverName . '.setting.worker_num')) && $workerId >= 0){
                            process_rename("{$serverName}.Worker.{$workerId}");
                        }
                    }

                    // 建立Pool连接池
                    foreach (container()->all() as $service) {
                        if ($service instanceof PoolInterface) {
                            $service->initPool();
                        }
                    }
                }
            );
            $this->getSwooleServerEventRegister()->addEvent(
                EventsRegister::onWorkerExit,
                function () {
                    TimerManager::getInstance()->clearAllTimer();
                }
            );
        }
    }
}