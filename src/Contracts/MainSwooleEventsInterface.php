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

use ShugaChara\Swoole\Events\EventsRegister;

/**
 * Interface MainSwooleEventsInterface
 * @package ShugaChara\Framework\Contracts
 */
interface MainSwooleEventsInterface
{
    /**
     * 初始化操作
     * @return mixed
     */
    public static function initialize();

    /**
     * hook 全局的 mainSwooleServerEventsCreate 事件
     * @param EventsRegister $register
     * @return mixed
     */
    public static function mainSwooleServerEventsCreate(EventsRegister $register);
}
