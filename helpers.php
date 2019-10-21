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
        return app()->getContainer();
    }
}

if (! function_exists('logs')) {
    /**
     * 日志服务
     * @param string $name
     * @param string $server_name
     * @return mixed
     */
    function logs($name = null)
    {
        $name = $name ?? app()->getAppName();
        return container()->get('logs')($name);
    }
}

if (! function_exists('config')) {
    /**
     * 配置服务
     * @return mixed
     */
    function config()
    {
        return container()->get('config');
    }
}

if (! function_exists('console')) {
    /**
     * 控制台命令服务
     * @return mixed
     */
    function console()
    {
        return container()->get('console');
    }
}

if (! function_exists('router')) {
    /**
     * 路由服务
     * @return mixed
     */
    function router()
    {
        return container()->get('router');
    }
}

if (! function_exists('router_dispatcher')) {
    /**
     * 路由分发服务
     * @return mixed
     */
    function router_dispatcher()
    {
        return container()->get('router_dispatcher');
    }
}

if (! function_exists('request')) {
    /**
     * Http 请求服务
     * @return mixed
     */
    function request()
    {
        return container()->get('request');
    }
}

if (! function_exists('sw')) {
    /**
     * swoole 服务
     * @return mixed
     */
    function sw()
    {
        return container()->get('sw');
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