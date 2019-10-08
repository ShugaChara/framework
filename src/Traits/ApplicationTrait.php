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

namespace ShugaChara\Framework\Traits;

use function date_default_timezone_set;

/**
 * Trait ApplicationTrait
 *
 * @package ShugaChara\Framework\Traits
 */
trait ApplicationTrait
{
    /**
     * 启动框架前置操作
     *
     * @return bool
     */
    public function beforeRun(): bool
    {
        return true;
    }

    /**
     * 设置时区
     *
     * @param string $timezone
     */
    public function setDateTimezone($timezone)
    {
        date_default_timezone_set($timezone);
    }
}