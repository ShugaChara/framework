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

interface SwooleManagerInterface
{
    /**
     * 服务启动
     * @return bool
     */
    public function start(): bool;

    /**
     * 服务停止
     * @return bool
     */
    public function stop(): bool;

    /**
     * 服务状态
     * @return bool
     */
    public function status(): bool;

    /**
     * 服务重启
     * @return bool
     */
    public function reload(): bool;
}
