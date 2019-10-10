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

if (! function_exists('environment')) {
    /**
     * 获取应用环境
     * @return array|false|string
     */
    function environment()
    {
        return trim(getenv('APP_ENV'));
    }
}

if (! function_exists('is_local')) {
    /**
     * 判断是否本地环境
     * @return array|false|string
     */
    function is_local()
    {
        return environment() == 'local';
    }
}

if (! function_exists('is_dev')) {
    /**
     * 判断是否测试环境
     * @return array|false|string
     */
    function is_dev()
    {
        return environment() == 'dev';
    }
}

if (! function_exists('is_prerelease')) {
    /**
     * 判断是否预发布环境
     * @return array|false|string
     */
    function is_prerelease()
    {
        return environment() == 'prerelease';
    }
}

if (! function_exists('is_prod')) {
    /**
     * 判断是否生产环境
     * @return array|false|string
     */
    function is_prod()
    {
        return environment() == 'prod';
    }
}