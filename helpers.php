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

/**********************************   应用环境/服务    *******************************/

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

if (! function_exists('container')) {
    /**
     * 获取应用IOC服务容器
     * @return \ShugaChara\Container\Container
     */
    function container()
    {
        return app()->getContainer();
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

if (! function_exists('logs')) {
    /**
     * 获取日志服务
     * @param string $name
     * @param string $server_name
     * @return \ShugaChara\Logs\Logger
     */
    function logs($name = null)
    {
        $name = $name ?? app()->getAppName();
        return container()->get('logs')($name);
    }
}

if (! function_exists('router')) {
    /**
     * 获取路由服务
     * @return \ShugaChara\Router\RouteCollection
     */
    function router()
    {
        return container()->get('router');
    }
}

if (! function_exists('router_dispatcher')) {
    /**
     * 获取路由分发服务
     * @return \ShugaChara\Router\RouteDispatcher
     */
    function router_dispatcher()
    {
        return container()->get('router_dispatcher');
    }
}

if (! function_exists('request')) {
    /**
     * 获取Http请求服务
     * @return \ShugaChara\Framework\Http\Request
     */
    function request()
    {
        return container()->get('request');
    }
}

if (! function_exists('response')) {
    /**
     * 获取Http响应服务
     * @return \ShugaChara\Framework\Http\Response
     */
    function response()
    {
        return container()->get('response');
    }
}

if (! function_exists('validator')) {
    /**
     * 获取数据验证类
     * @return Validator
     */
    function validator()
    {
        return container()->get('validator');
    }
}

if (! function_exists('db')) {
    /**
     * 获取数据库连接服务对象
     *
     * @return \ShugaChara\Databases\DB|\ShugaChara\Databases\Capsule|\Illuminate\Database\MySqlConnection
     */
    function db($drive = 'default')
    {
        return container()->get('databases')->getConnection($drive);
    }
}

if (! function_exists('redis')) {
    /**
     * 获取Redis服务
     * @param string $drive
     * @return \Predis\Client
     */
    function redis($drive = 'default')
    {
        return container()->get('redis')->getConnection($drive);
    }
}

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
        return getenv('APP_DEBUG') === 'true' ? true : false;
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

if (! function_exists('get_framework_name')) {
    /**
     * 获取系统内核框架名称
     * @return string
     */
    function get_framework_name()
    {
        return FRAMEWORK_NAME;
    }
}

if (! function_exists('get_framework_version')) {
    /**
     * 获取系统内核框架版本
     * @return string
     */
    function get_framework_version()
    {
        return FRAMEWORK_VERSION;
    }
}
