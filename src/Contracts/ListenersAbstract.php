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
     * Swoole event registration
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
     * Get Swoole registration event
     *
     * @return EventsRegister
     */
    public function getEventsRegister(): EventsRegister
    {
        return $this->eventsRegister;
    }

    /**
     * Get Swoole\Server\Port
     * @return Port
     */
    public function getServerPort()
    {
        return $this->serverPort;
    }
}
