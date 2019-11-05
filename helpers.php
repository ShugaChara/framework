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

use ShugaChara\Config\FileConfig;
use ShugaChara\Framework\Application;
use ShugaChara\Http\Response;
use ShugaChara\Console\Console;
use ShugaChara\Router\RouteCollection;
use ShugaChara\Router\RouteDispatcher;
use ShugaChara\Core\Helpers;
use ShugaChara\Framework\Tools\CodeAPI;
use ShugaChara\Http\Message\Request;
use ShugaChara\Framework\Console\Commands\HttpServerCommand;
use ShugaChara\Framework\Console\Commands\WebsocketServerCommand;
use ShugaChara\Databases\Capsule;
use ShugaChara\Databases\DB;
use ShugaChara\Redis\Redis;
use ShugaChara\Swoole\Events\EventsRegister;
use ShugaChara\Validation\Validator;

if (! function_exists('app')) {
    /**
     * 获取应用App
     * @return Application
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
     * @return FileConfig
     */
    function config()
    {
        return container()->get('config');
    }
}

if (! function_exists('console')) {
    /**
     * 控制台命令服务
     * @return Console
     */
    function console()
    {
        return container()->get('console');
    }
}

if (! function_exists('router')) {
    /**
     * 路由服务
     * @return RouteCollection
     */
    function router()
    {
        return container()->get('router');
    }
}

if (! function_exists('routerDispatcher')) {
    /**
     * 路由分发服务
     * @return RouteDispatcher
     */
    function routerDispatcher()
    {
        return container()->get('routerDispatcher');
    }
}

if (! function_exists('request')) {
    /**
     * Http 请求服务
     * @return Request
     */
    function request()
    {
        return container()->get('request');
    }
}

if (! function_exists('response')) {
    /**
     * Http 响应服务
     * @return Response
     */
    function response()
    {
        return container()->get('response');
    }
}

if (! function_exists('responseAPI')) {
    /**
     * API Json响应
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    function responseAPI($data = [], $status = Response::HTTP_OK, array $headers = [])
    {
        $status = (int) $status;

        $startResponseTime = Helpers::array_get(request()->getServerParams(), 'REQUEST_TIME_FLOAT', 0) ? : Helpers::array_get($_SERVER, 'REQUEST_TIME_FLOAT', 0);
        $endResponseTime = microtime(true) - $startResponseTime;

        $CodeAPI = config()->get('APP_CODE_API') ? : CodeAPI::class;
        list($httpCode, $message) = $CodeAPI::getInstance()->getCodeMessage($status);

        return response()->json([
            'code'              =>      $status,
            'message'           =>      $message,
            'data'              =>      $data,
            'response_time'     =>      $endResponseTime
        ], $httpCode, $headers);
    }
}

if (! function_exists('db')) {
    /**
     * Databases
     *
     * @return DB|Capsule
     */
    function db($drive = 'default')
    {
        return container()->get('databases')->getConnection($drive);
    }
}

if (! function_exists('redis')) {
    /**
     * Redis
     * @param string $drive
     * @return Redis
     */
    function redis($drive = 'default')
    {
        return container()->get('redis')->getConnection($drive);
    }
}

if (! function_exists('validator')) {
    /**
     * 数据验证类
     * @return Validator
     */
    function validator()
    {
        return container()->get('validator');
    }
}

if (! function_exists('swoole')) {
    /**
     * swoole 服务
     * @return mixed
     */
    function swoole()
    {
        return container()->get('swoole');
    }
}

if (! function_exists('swooleEventDispatcher')) {
    /**
     * Swoole 事件分发器
     * @return EventsRegister
     */
    function swooleEventDispatcher()
    {
        return container()->get('swooleEventDispatcher');
    }
}

if (! function_exists('swooleServerCommandIOC')) {
    /**
     * Swoole 命令管理通道 | 管理swoole的 status start stop reload restart ...等
     * @return HttpServerCommand | WebsocketServerCommand
     */
    function swooleServerCommandIOC()
    {
        return container()->get('swooleServerCommandIOC');
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