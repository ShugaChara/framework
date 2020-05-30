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

/**
 * State manager
 *
 * Interface StatusManagerInterface
 * @package ShugaChara\Framework\Contracts
 */
interface StatusManagerInterface
{
    /**
     * Status details
     */
    const STATUS_NAME = 'status';

    /**
     * Status start
     */
    const START_NAME = 'start';

    /**
     * Status stop
     */
    const STOP_NAME = 'stop';

    /**
     * Smooth loading
     */
    const RELOAD_NAME = 'reload';

    /**
     * Reboot
     */
    const RESTART_NAME = 'restart';

    /**
     * Status type
     * @var array
     */
    const STATUS_TYPES = [
        self::STATUS_NAME,
        self::START_NAME,
        self::STOP_NAME,
        self::RELOAD_NAME,
        self::RESTART_NAME,
    ];

    /**
     * Status details
     * @return mixed
     */
    public function status();

    /**
     * Status start
     * @return mixed
     */
    public function start();

    /**
     * Status stop
     * @return mixed
     */
    public function stop();

    /**
     * Smooth loading
     * @return mixed
     */
    public function reload();

    /**
     * Reboot
     * @return mixed
     */
    public function restart();
}