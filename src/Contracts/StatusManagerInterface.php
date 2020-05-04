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
 * 状态管理器
 *
 * Interface StatusManagerInterface
 * @package ShugaChara\Framework\Contracts
 */
interface StatusManagerInterface
{
    /**
     * 状态详情
     */
    const STATUS_NAME = 'status';

    /**
     * 启动
     */
    const START_NAME = 'start';

    /**
     * 停止
     */
    const STOP_NAME = 'stop';

    /**
     * 平滑加载
     */
    const RELOAD_NAME = 'reload';

    /**
     * 重启
     */
    const RESTART_NAME = 'restart';

    /**
     * 状态类型
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
     * 状态详情
     * @return mixed
     */
    public function status();

    /**
     * 启动
     * @return mixed
     */
    public function start();

    /**
     * 停止
     * @return mixed
     */
    public function stop();

    /**
     * 平滑加载
     * @return mixed
     */
    public function reload();

    /**
     * 重启
     * @return mixed
     */
    public function restart();
}