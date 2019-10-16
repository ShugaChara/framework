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

namespace ShugaChara\Framework\Server;

use ShugaChara\Core\Helpers;
use ShugaChara\Swoole\Server\Http;
use ShugaChara\Swoole\Server\WebSocket;

/**
 * Class SwooleServer
 * @package ShugaChara\Framework\Server
 */
class SwooleServer
{
    const DEFAULT_SERVER = 'HTTP';

    const DEFAULT_SERVER_HANDLE = 'status';

    protected $swoole;

    /**
     * 服务 http | websocket
     * @var
     */
    protected $server;

    /**
     * 参数配置
     * @var array
     */
    protected $options = [];

    /**
     * 操作状态 status | start | stop | restart
     * @var
     */
    protected $handle;

    public function __construct(
        $serverName = self::DEFAULT_SERVER,
        array $options = [],
        string $handle = self::DEFAULT_SERVER_HANDLE
    )
    {
        $this->server = $serverName;
        $this->options = $options;
        $this->handle = $handle;

        $this->server();
    }

    protected function server()
    {
        switch (strtoupper($this->server)) {
            case 'HTTP':
                {
                    $this->swoole = new Http(
                        Helpers::array_get($this->options, 'host', '127.0.0.1'),
                        Helpers::array_get($this->options, 'port')
                    );
                    break;
                }
            case 'WEBSOCKET':
                {
                    $this->swoole = new WebSocket(
                        Helpers::array_get($this->options, 'host', '127.0.0.1'),
                        Helpers::array_get($this->options, 'port')
                    );
                    break;
                }
            default:
        }
    }

    /**
     * 获取swoole服务
     * @return mixed
     */
    protected function swoole()
    {
        return $this->swoole;
    }
}

