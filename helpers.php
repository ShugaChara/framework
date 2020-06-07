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

use Swoole\Coroutine;
use ShugaChara\Framework\Components\Alias;
use ShugaChara\Framework\Helpers\FHelper;

if (! function_exists('fnc')) {
    /**
     * Get the FHelper method package
     * @return FHelper
     */
    function fnc()
    {
        return FHelper::getInstance();
    }
}

if (! function_exists('container')) {
    /**
     * Get container service
     * @return \ShugaChara\Container\Container
     */
    function container()
    {
        return Alias::get('container');
    }
}

if (! function_exists('sgo')) {
    /**
     * Swoole coroutine shortname
     * @param callable $func
     * @param mixed    ...$params
     */
    function sgo(callable $func, ... $params)
    {
        return Coroutine::create($func, ... $params);
    }
}

