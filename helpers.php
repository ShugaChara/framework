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

if (! function_exists('alias')) {
    /**
     * 获取 Alias 类
     * @return Alias
     */
    function alias(): Alias
    {
        return Alias::getInstance();
    }
}

if (! function_exists('container')) {
    /**
     * 获取容器服务
     * @return \ShugaChara\Container\Container
     */
    function container()
    {
        return alias()->get('container');
    }
}

if (! function_exists('sgo')) {
    /**
     * Swoole 协程短名称
     * @param callable $func
     * @param mixed    ...$params
     */
    function sgo(callable $func, ... $params)
    {
        return Coroutine::create($func, ... $params);
    }
}

if (! function_exists('app')) {
    /**
     * 获取应用 Application 对象
     * @return \ShugaChara\Framework\Application
     */
    function app()
    {
        return container()->get('app');
    }
}

if (! function_exists('conf')) {
    /**
     * 获取配置服务
     * @return \ShugaChara\Config\FileConfig
     */
    function conf()
    {
        return container()->get('conf');
    }
}

if (! function_exists('logs')) {
    /**
     * 获取日志服务
     * @param null $name    filename
     * @return \ShugaChara\Logs\Logger
     */
    function logs($name = null)
    {
        return container()->get('logs')(($name ?? conf()->get('app_name')));
    }
}

if (! function_exists('console')) {
    /**
     * 获取控制台服务
     * @return \ShugaChara\Console\Console
     */
    function console()
    {
        return container()->get('console');
    }
}

if (! function_exists('db')) {
    /**
     * 获取数据库服务
     * @param string $drive     驱动名称
     * @return \ShugaChara\Databases\DB|\ShugaChara\Databases\Capsule|\Illuminate\Database\MySqlConnection
     */
    function db($drive = 'default')
    {
        return container()->get('databases')->getConnection($drive);
    }
}

if (! function_exists('redis')) {
    /**
     * 获取 Redis 服务
     * @param string $drive     驱动名称
     * @return \Predis\Client
     */
    function redis($drive = 'default')
    {
        return container()->get('redis')->getConnection($drive);
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

if (! function_exists('request')) {
    /**
     * 获取 Http 请求服务
     * @return \ShugaChara\Framework\Http\Request
     */
    function request()
    {
        return container()->get('request');
    }
}

if (! function_exists('response')) {
    /**
     * 获取 Http 响应服务
     * @return \ShugaChara\Framework\Http\Response
     */
    function response()
    {
        return container()->get('response');
    }
}

if (! function_exists('validator')) {
    /**
     * 获取数据验证服务
     * @return \ShugaChara\Validation\Validator
     */
    function validator()
    {
        return container()->get('validator');
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

if (! function_exists('server_channel')) {
    /**
     * 获取 Swoole 服务通道
     * @return \ShugaChara\Framework\Contracts\BaseServerCommandAbstract
     */
    function server_channel()
    {
        return container()->get('server_channel');
    }
}

if (! function_exists('server')) {
    /**
     * 获取 Swoole 服务对象
     * @return \ShugaChara\Framework\Swoole\Server
     */
    function server()
    {
        return server_channel()->getServer();
    }
}

if (! function_exists('swoole')) {
    /**
     * 获取 swoole_server 对象
     * @return \swoole_server
     */
    function swoole()
    {
        return server()->getSwooleServer();
    }
}