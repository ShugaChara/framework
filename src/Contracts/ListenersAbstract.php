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

namespace ShugaChara\Framework\Contracts;

use ShugaChara\Swoole\EventsRegister;
use Swoole\Server\Port;

/**
 * Class ListenersAbstract
 * @package ShugaChara\Framework\Contracts
 */
abstract class ListenersAbstract
{
    /**
     * @var Port
     */
    private $serverPort;

    /**
     * Swoole 事件注册
     * @var EventsRegister
     */
    private $eventsRegister;

    /**
     * ListenersAbstract constructor.
     * @param Port $serverPort
     */
    final public function __construct(Port $serverPort)
    {
        $this->serverPort = $serverPort;

        $this->eventsRegister = new EventsRegister();
    }

    /**
     * 获取 Swoole 注册事件
     *
     * @return EventsRegister
     */
    public function getEventsRegister(): EventsRegister
    {
        return $this->eventsRegister;
    }

    /**
     * 获取 Swoole\Server\Port
     * @return Port
     */
    public function getServerPort()
    {
        return $this->serverPort;
    }
}
