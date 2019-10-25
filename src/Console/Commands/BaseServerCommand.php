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
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class BaseServerCommand
 * @package ShugaChara\Framework\Console\Commands
 */
abstract class BaseServerCommand extends Command
{
    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * 服务状态类型
     * @var array
     */
    protected $serverStatusType = [
        Consts::SWOOLE_SERVER_START_NAME,
        Consts::SWOOLE_SERVER_STATUS_NAME,
        Consts::SWOOLE_SERVER_STOP_NAME,
        Consts::SWOOLE_SERVER_RELOAD_NAME,
        Consts::SWOOLE_SERVER_RESTART_NAME,
    ];

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->output = new ConsoleOutput();
    }

    abstract function status();

    abstract function start();

    abstract function stop();

    abstract function reload();

    abstract function restart();
}

