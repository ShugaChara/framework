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

if (! function_exists('app')) {
    /**
     * 获取应用App
     * @return \ShugaChara\Framework\Application
     */
    function app()
    {
        return Alias::get('application');
    }
}

if (! function_exists('container')) {
    /**
     * 获取服务容器
     * @return \ShugaChara\Container\Container
     */
    function container()
    {
        return Alias::get('container');
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
        return container()->get('logs')(($name ?? app()->getAppName()));
    }
}

if (! function_exists('config')) {
    /**
     * 获取配置服务
     * @return \ShugaChara\Config\FileConfig
     */
    function config()
    {
        return container()->get('config');
    }
}

if (! function_exists('console')) {
    /**
     * 控制台命令服务
     * @return \ShugaChara\Console\Console
     */
    function console()
    {
        return container()->get('console');
    }
}

if (! function_exists('router')) {
    /**
     * 路由服务
     * @return \ShugaChara\Router\RouteCollection
     */
    function router()
    {
        return container()->get('router');
    }
}

if (! function_exists('routerDispatcher')) {
    /**
     * 路由分发服务
     * @return \ShugaChara\Router\RouteDispatcher
     */
    function routerDispatcher()
    {
        return container()->get('routerDispatcher');
    }
}

if (! function_exists('request')) {
    /**
     * Http 请求服务
     * @return \ShugaChara\Framework\Http\Request
     */
    function request()
    {
        return container()->get('request');
    }
}

if (! function_exists('response')) {
    /**
     * Http 响应服务
     * @return \ShugaChara\Framework\Http\Response
     */
    function response()
    {
        return container()->get('response');
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

if (! function_exists('is_debug')) {
    /**
     * 是否调试模式
     * @return mixed
     */
    function is_debug()
    {
        return config()->get('APP_DEBUG') === 'true' ? true : false;
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