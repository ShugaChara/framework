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
use ShugaChara\Core\Traits\Singleton;
use ShugaChara\Framework\Contracts\BaseServerCommandAbstract;
use ShugaChara\Framework\Swoole\Server;

/**
 * Class FHelper
 * @method static $this getInstance(...$args)
 * @package ShugaChara\Framework\Helpers
 */
class FHelper
{
    use Singleton;

    /**
     * 检查运行时扩展冲突
     * @param string $minPhp
     * @param string $minSwoole
     */
    public function checkRuntime(string $minPhp = '7.1', string $minSwoole = '4.4.1'): void
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
            'xdebug',
            'phptrace',
            'aop',
            'molten',
            'xhprof',
            'phalcon',
        ];

        foreach ($conflicts as $ext) {
            if (extension_loaded($ext)) {
                throw new RuntimeException("The extension of '{$ext}' must be closed, otherwise swoft will be affected!");
            }
        }
    }

    /**
     * 获取基本文件信息
     * @param $file_name
     * @return array|null
     */
    public function getFileBaseInfo($file_name)
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
     * 获取 Application
     * @return \ShugaChara\Framework\Application
     */
    public function app()
    {
        return container()->get('application');
    }

    /**
     * 获取配置服务
     * @return \ShugaChara\Config\FileConfig
     */
    public function c()
    {
        return container()->get('c');
    }

    /**
     * 获取日志服务
     * @param null $name    filename
     * @return \ShugaChara\Logs\Logger
     */
    public function logs($name = null)
    {
        return container()->get('logs')(($name ?? static::c()->get('app_name')));
    }

    /**
     * 获取控制台服务
     * @return \ShugaChara\Console\Console
     */
    public function console()
    {
        return container()->get('console');
    }

    /**
     * 获取数据库服务
     * @param string $drive     驱动名称
     * @return \ShugaChara\Databases\DB|\ShugaChara\Databases\Capsule|\Illuminate\Database\MySqlConnection
     */
    public function db($drive = 'default')
    {
        return container()->get('databases')->getConnection($drive);
    }

    /**
     * 获取 Redis 服务
     * @param string $drive     驱动名称
     * @return \Predis\Client
     */
    public function redis($drive = 'default')
    {
        return container()->get('redis')->getConnection($drive);
    }

    /**
     * 获取路由服务
     * @return \ShugaChara\Router\RouteCollection
     */
    public function router()
    {
        return container()->get('router');
    }

    /**
     * 获取路由分发服务
     * @return \ShugaChara\Router\RouteDispatcher
     */
    public function routerDispatcher()
    {
        return container()->get('router_dispatcher');
    }

    /**
     * 获取 Http 请求服务
     * @return \ShugaChara\Framework\Http\Request
     */
    public function request()
    {
        return container()->get('request');
    }

    /**
     * 获取 Http 响应服务
     * @return \ShugaChara\Framework\Http\Response
     */
    public function response()
    {
        return container()->get('response');
    }

    /**
     * 获取数据验证服务
     * @return \ShugaChara\Validation\Validator
     */
    public function validator()
    {
        return container()->get('validator');
    }

    /**
     * 获取 Swoole 服务通道
     * @return BaseServerCommandAbstract
     */
    public function serverChannel()
    {
        return container()->get('server_channel');
    }

    /**
     * 获取 Swoole 服务对象
     * @return Server
     */
    public function server()
    {
        return $this->serverChannel()->getServer();
    }

    /**
     * 获取 swoole_server 对象
     * @return \swoole_server
     */
    public function swoole()
    {
        return $this->server()->getSwooleServer();
    }
}