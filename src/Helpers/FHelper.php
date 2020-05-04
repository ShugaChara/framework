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

namespace ShugaChara\Framework\Helpers;

use RuntimeException;
use ShugaChara\Framework\Swoole\Server;
use ShugaChara\Swoole\EventsRegister;

/**
 * Class FHelper
 * @package ShugaChara\Framework\Helpers
 */
class FHelper
{
    /**
     * Check runtime extension conflict
     *
     * @param string $minPhp
     * @param string $minSwoole
     */
    public static function checkRuntime(string $minPhp = '7.1', string $minSwoole = '4.4.1'): void
    {
        if (version_compare(PHP_VERSION, $minPhp, '<')) {
            throw new RuntimeException('Run the server requires PHP version > ' . $minPhp . '! current is ' . PHP_VERSION);
        }

        if (! extension_loaded('swoole')) {
            throw new RuntimeException("Run the server, extension 'swoole' is required!");
        }

        if (version_compare(SWOOLE_VERSION, $minSwoole, '<')) {
            throw new RuntimeException('Run the server requires swoole version > ' . $minSwoole . '! current is ' . SWOOLE_VERSION);
        }

        $conflicts = [
            'blackfire',
            'xdebug',
            'uopz',
            'xhprof',
            'zend',
            'trace',
        ];

        foreach ($conflicts as $ext) {
            if (extension_loaded($ext)) {
                throw new RuntimeException("The extension of '{$ext}' must be closed, otherwise swoft will be affected!");
            }
        }
    }

    /**
     * 获取文件基础信息
     * @param $file_name
     * @return array|null
     */
    public static function getFileBaseInfo($file_name)
    {
        if (! file_exists($file_name)) {
            return null;
        }

        return [
            'path'      =>      dirname($file_name),
            'name'      =>      basename($file_name),
        ];
    }

    /**
     * 获取应用 Application
     * @return \ShugaChara\Framework\Application
     */
    public static function app()
    {
        return container()->get('application');
    }

    /**
     * 获取配置服务
     * @return \ShugaChara\Config\FileConfig
     */
    public static function c()
    {
        return container()->get('c');
    }

    /**
     * 日志服务
     * @param null $name    文件名
     * @return \ShugaChara\Logs\Logger
     */
    public static function logs($name = null)
    {
        return container()->get('logs')(($name ?? FHelper::c()->get('app_name')));
    }

    /**
     * 控制台命令服务
     * @return \ShugaChara\Console\Console
     */
    public static function console()
    {
        return container()->get('console');
    }

    /**
     * 获取数据库连接服务对象
     * @param string $drive     库驱动名称
     * @return \ShugaChara\Databases\DB|\ShugaChara\Databases\Capsule|\Illuminate\Database\MySqlConnection
     */
    public static function db($drive = 'default')
    {
        return container()->get('databases')->getConnection($drive);
    }

    /**
     * 获取 Redis 服务
     * @param string $drive     库驱动名称
     * @return \Predis\Client
     */
    public static function redis($drive = 'default')
    {
        return container()->get('redis')->getConnection($drive);
    }

    /**
     * 路由服务
     * @return \ShugaChara\Router\RouteCollection
     */
    public static function router()
    {
        return container()->get('router');
    }

    /**
     * 路由分发服务
     * @return \ShugaChara\Router\RouteDispatcher
     */
    public static function routerDispatcher()
    {
        return container()->get('router_dispatcher');
    }

    /**
     * Http 请求服务
     * @return \ShugaChara\Framework\Http\Request
     */
    public static function request()
    {
        return container()->get('request');
    }

    /**
     * Http 响应服务
     * @return \ShugaChara\Framework\Http\Response
     */
    public static function response()
    {
        return container()->get('response');
    }

    /**
     * 获取数据验证类
     * @return \ShugaChara\Validation\Validator
     */
    public static function validator()
    {
        return container()->get('validator');
    }

    /**
     * 获取 Swoole 服务
     * @return Server
     */
    public static function swoole(): Server
    {
        return container()->get('swoole')->getServer();
    }

    /**
     * 获取 Swoole 事件分发器
     * @return EventsRegister
     */
    public static function swooleEventDispatcher(): EventsRegister
    {
        return container()->get('swoole')->getEventsRegister();
    }
}