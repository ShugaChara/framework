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

namespace ShugaChara\Framework\Contracts;

use ShugaChara\Swoole\EventsRegister;
use swoole_server;

/**
 * Interface MainSwooleEventsInterface
 * @package ShugaChara\Framework\Contracts
 */
interface MainSwooleEventsInterface
{
    /**
     * Initialize operation
     * @return mixed
     */
    public function initialize();

    /**
     * Hook global mainSwooleServerEventsCreate event
     * @param EventsRegister $register
     * @param swoole_server  $swoole_server
     * @return mixed
     */
    public function mainSwooleServerEventsCreate(EventsRegister $register, swoole_server $swoole_server);
}
