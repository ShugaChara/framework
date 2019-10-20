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

namespace ShugaChara\Framework\Console\Commands;

use ShugaChara\Console\Command;
use ShugaChara\Framework\Constant\Consts;
use ShugaChara\Framework\Server\SwooleServer;

/**
 * Class BaseServerCommand
 * @package ShugaChara\Framework\Console\Commands
 */
abstract class BaseServerCommand extends Command
{
    /**
     * 服务对象
     * @var SwooleServer
     */
    protected $server;

    /**
     * 操作状态类型
     * @var array
     */
    protected $handleType = [
        Consts::SWOOLE_SERVER_START_NAME,
        Consts::SWOOLE_SERVER_STATUS_NAME,
        Consts::SWOOLE_SERVER_STOP_NAME,
        Consts::SWOOLE_SERVER_RELOAD_NAME,
        Consts::SWOOLE_SERVER_RESTART_NAME,
    ];

    /**
     * @return SwooleServer
     */
    protected function getSwooleServer()
    {
        return $this->server;
    }

    /**
     * 服务状态
     * @return mixed
     */
    abstract function status();

    /**
     * 启动服务
     * @return mixed
     */
    abstract function start();

    /**
     * 停止服务
     * @return mixed
     */
    abstract function stop();

    /**
     * 服务平滑加载
     * @return mixed
     */
    abstract function reload();

    /**
     * 重启服务
     * @return mixed
     */
    abstract function restart();
}

