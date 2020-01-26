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

use ShugaChara\Framework\Application;

if (! function_exists('get_framework_name')) {
    /**
     * 获取系统内核框架名称
     * @return string
     */
    function get_framework_name() { return SGC_FRAMEWORK_NAME; }
}

if (! function_exists('get_framework_version')) {
    /**
     * 获取系统内核框架版本
     * @return string
     */
    function get_framework_version() { return SGC_FRAMEWORK_VERSION; }
}

if (! function_exists('app')) {
    /**
     * 获取应用App
     * @return Application
     */
    function app()
    {
        return Application::getApplication();
    }
}
