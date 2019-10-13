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

use ShugaChara\Framework\Application;

if (! function_exists('app')) {
    /**
     * 获取应用App
     * @return mixed
     */
    function app()
    {
        return Application::$app;
    }
}

/**********************************   应用服务    *******************************/

if (! function_exists('container')) {
    /**
     * 获取应用IOC服务容器
     * @return mixed
     */
    function container()
    {
        return app()->container();
    }
}

if (! function_exists('logs')) {
    /**
     * 日志服务
     * @param string $name
     * @param string $server_name
     * @return mixed
     */
    function logs($name = null, $server_name = 'logs')
    {
        $name = $name ?? app()->getAppName();
        return container()->get($server_name)($name);
    }
}

/**********************************   应用环境    *******************************/

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