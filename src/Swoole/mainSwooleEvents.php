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

use ShugaChara\Framework\Contracts\MainSwooleEventsInterface;
use ShugaChara\Swoole\EventsRegister;
use swoole_server;

/**
 * Swoole main service event monitoring object
 *
 * Class MainSwooleEvents
 * @package ShugaChara\Framework\Swoole
 */
class MainSwooleEvents implements MainSwooleEventsInterface
{
    /**
     * Initialize action
     * @return mixed|void
     */
    public function initialize()
    {
        // TODO: Implement initialize() method.
    }

    /**
     * Hook global mainSwooleServerEventsCreate event
     * @param EventsRegister $register
     * @param swoole_server  $swoole_server
     * @return mixed|void
     */
    public function mainSwooleServerEventsCreate(EventsRegister $register, swoole_server $swoole_server)
    {
        // TODO: Implement mainSwooleServerEventsCreate() method.
    }
}

