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
use ShugaChara\Framework\Components\Alias;

/**
 * Class ByermHelper
 * @package ShugaChara\Framework\Helpers
 */
class ByermHelper
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
     * 获取当前应用环境
     * @return string
     */
    public static function environment()
    {
        return trim(getenv('APP_ENV'));
    }

    /**
     * 获取应用App
     * @return \ShugaChara\Framework\Application
     */
    public static function app()
    {
        return Alias::get('application');
    }

    /**
     * 获取服务容器
     * @return \ShugaChara\Container\Container
     */
    public static function container()
    {
        return Alias::get('container');
    }

    /**
     * 日志服务
     * @param null $name    文件名
     * @return \ShugaChara\Logs\Logger
     */
    public static function logs($name = null)
    {
        return static::container()->get('logs')(($name ?? static::app()->getAppName()));
    }

    /**
     * 获取配置服务
     * @return \ShugaChara\Config\FileConfig
     */
    public static function config()
    {
        return static::container()->get('config');
    }

    /**
     * 控制台命令服务
     * @return \ShugaChara\Console\Console
     */
    public static function console()
    {
        return static::container()->get('console');
    }

    /**
     * 路由服务
     * @return \ShugaChara\Router\RouteCollection
     */
    public static function router()
    {
        return static::container()->get('router');
    }

    /**
     * 路由分发服务
     * @return \ShugaChara\Router\RouteDispatcher
     */
    public static function routerDispatcher()
    {
        return static::container()->get('router_dispatcher');
    }

    /**
     * Http 请求服务
     * @return \ShugaChara\Framework\Http\Request
     */
    public static function request()
    {
        return static::container()->get('request');
    }

    /**
     * Http 响应服务
     * @return \ShugaChara\Framework\Http\Response
     */
    public static function response()
    {
        return static::container()->get('response');
    }

    /**
     * 获取数据验证类
     * @return \ShugaChara\Validation\Validator
     */
    public static function validator()
    {
        return static::container()->get('validator');
    }

    /**
     * 获取数据库连接服务对象
     * @param string $drive     库驱动名称
     * @return \ShugaChara\Databases\DB|\ShugaChara\Databases\Capsule|\Illuminate\Database\MySqlConnection
     */
    public static function db($drive = 'default')
    {
        return static::container()->get('databases')->getConnection($drive);
    }

    /**
     * 获取 Redis 服务
     * @param string $drive     库驱动名称
     * @return \Predis\Client
     */
    public static function redis($drive = 'default')
    {
        return static::container()->get('redis')->getConnection($drive);
    }
}