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

if (! function_exists('router_dispatcher')) {
    /**
     * 路由分发服务
     * @return RouteDispatcher
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
        $status = $apiStatus = (int) $status;

        if ( ($status < 100) || ($status > 599) ) {
            $status = Response::HTTP_OK;
        }

        $endResponseTime = microtime(true) - Helpers::array_get(request()->getServerParams(), 'REQUEST_TIME_FLOAT', 0);

        $CodeAPI = config()->get('APP_CODE_API') ? : CodeAPI::class;

        return response()->json([
            'code'              =>      $apiStatus,
            'message'           =>      $CodeAPI::getInstance()->getCodeMessage($apiStatus),
            'data'              =>      $data,
            'response_time'     =>      $endResponseTime
        ], $status, $headers);
    }
}

if (! function_exists('db')) {
    /**
     * Databases
     *
     * @return \ShugaChara\Databases\DB|\ShugaChara\Databases\Capsule
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
     * @return \ShugaChara\Redis\Redis
     */
    function redis($drive = 'default')
    {
        return container()->get('redis')->getConnection($drive);
    }
}

if (! function_exists('validator')) {
    /**
     * 数据验证类
     * @return \ShugaChara\Validation\Validator
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

if (! function_exists('swooleHttpServerCommandIOC')) {
    /**
     * Http Swoole 命令管理通道
     * @return mixed
     */
    function swooleHttpServerCommandIOC()
    {
        return container()->get('swooleHttpServerCommandIOC');
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