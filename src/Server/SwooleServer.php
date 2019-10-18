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
use ShugaChara\Framework\Constant\Consts;
use ShugaChara\Framework\Contracts\SwooleManagerInterface;
use ShugaChara\Swoole\Server\Http;
use ShugaChara\Swoole\Server\WebSocket;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SwooleServer
 * @package ShugaChara\Framework\Server
 */
class SwooleServer implements SwooleManagerInterface
{
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
     * 操作状态 status | start | stop | reload
     * @var
     */
    protected $handle;

    /**
     * 控制台输出
     * @var
     */
    protected $consoleOutput;

    /**
     * 操作状态类型
     * @var array
     */
    protected $handleType = [
        Consts::SWOOLE_SERVER_START_NAME,
        Consts::SWOOLE_SERVER_STATUS_NAME,
        Consts::SWOOLE_SERVER_STOP_NAME,
        Consts::SWOOLE_SERVER_RELOAD_NAME
    ];

    public function __construct(
        $serverName = Consts::SWOOLE_SERVER_HTTP,
        array $options = [],
        string $handle = Consts::SWOOLE_SERVER_STATUS_NAME,
        OutputInterface $output = null
    )
    {
        $this->server = $serverName;
        $this->options = $options;
        $this->handle = $handle;

        $this->consoleOutput = null === $output ? new ConsoleOutput() : $output;
dd(da());
        $this->server();
    }

    protected function server()
    {
        $host = Helpers::array_get($this->options, 'host', '127.0.0.1');
        $port = Helpers::array_get($this->options, 'port');
        $options = Helpers::array_get($this->options, 'options', []);

        $handle = strtolower($this->handle);

        if (in_array($handle, $this->handleType)) {

            switch ($handle) {
                case Consts::SWOOLE_SERVER_START_NAME:
                    {
                        $this->createSwooleServer($host, $port, $options);
                        break;
                    }
                case Consts::SWOOLE_SERVER_STOP_NAME:
                    {
                        return $this->$handle();
                        break;
                    }
                case Consts::SWOOLE_SERVER_STATUS_NAME:
                    {
                        break;
                    }
                case Consts::SWOOLE_SERVER_RELOAD_NAME:
                    {
                        break;
                    }
            }
        }
    }

    /**
     * 创建Swoole服务器
     *
     * @param       $host
     * @param       $port
     * @param array $options
     */
    protected function createSwooleServer($host, $port, $options = [])
    {
        switch (strtoupper($this->server)) {
            case Consts::SWOOLE_SERVER_HTTP:
                {
                    $this->swoole = new Http($host, $port, $options);
                    break;
                }
            case Consts::SWOOLE_SERVER_WEBSOCKET:
                {
                    $this->swoole = new WebSocket($host, $port, $options);
                    break;
                }
            default:
        }

        return $this->swoole;
    }

    /**
     * 获取swoole服务
     * @return mixed
     */
    protected function swoole()
    {
        return $this->swoole;
    }

    public function status(): bool
    {
        // TODO: Implement status() method.

        var_dump($this->handle);

        return true;
    }

    public function start(): bool
    {
        // TODO: Implement start() method.

        $this->swoole->start();

        return true;
    }

    public function stop(): bool
    {
        // TODO: Implement stop() method.

        $this->swoole->shutdown();

        return true;
    }

    public function reload(): bool
    {
        // TODO: Implement reload() method.

        return true;
    }
}

