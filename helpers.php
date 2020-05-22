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

use ShugaChara\Framework\Components\Alias;

if (! function_exists('container')) {
    /**
     * 获取容器服务
     * @return \ShugaChara\Container\Container
     */
    function container()
    {
        return Alias::get('container');
    }
}
