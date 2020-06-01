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
     * Check runtime extension conflict
     *
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
     * Get basic file information
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
     * Get the Application
     * @return \ShugaChara\Framework\Application
     */
    public function app()
    {
        return container()->get('application');
    }

    /**
     * Configuration service
     * @return \ShugaChara\Config\FileConfig
     */
    public function c()
    {
        return container()->get('c');
    }

    /**
     * Logs service
     * @param null $name    filename
     * @return \ShugaChara\Logs\Logger
     */
    public function logs($name = null)
    {
        return container()->get('logs')(($name ?? static::c()->get('app_name')));
    }

    /**
     * Console service
     * @return \ShugaChara\Console\Console
     */
    public function console()
    {
        return container()->get('console');
    }

    /**
     * Get database connection service object
     * @param string $drive     Library driver name
     * @return \ShugaChara\Databases\DB|\ShugaChara\Databases\Capsule|\Illuminate\Database\MySqlConnection
     */
    public function db($drive = 'default')
    {
        return container()->get('databases')->getConnection($drive);
    }

    /**
     * Redis service
     * @param string $drive     Library driver name
     * @return \Predis\Client
     */
    public function redis($drive = 'default')
    {
        return container()->get('redis')->getConnection($drive);
    }

    /**
     * Routing service
     * @return \ShugaChara\Router\RouteCollection
     */
    public function router()
    {
        return container()->get('router');
    }

    /**
     * Routing distribution service
     * @return \ShugaChara\Router\RouteDispatcher
     */
    public function routerDispatcher()
    {
        return container()->get('router_dispatcher');
    }

    /**
     * Http request service
     * @return \ShugaChara\Framework\Http\Request
     */
    public function request()
    {
        return container()->get('request');
    }

    /**
     * Http response service
     * @return \ShugaChara\Framework\Http\Response
     */
    public function response()
    {
        return container()->get('response');
    }

    /**
     * Data verification service
     * @return \ShugaChara\Validation\Validator
     */
    public function validator()
    {
        return container()->get('validator');
    }

    /**
     * Get server channel
     * @return BaseServerCommandAbstract
     */
    public function serverChannel()
    {
        return container()->get('server_channel');
    }

    /**
     * Get service object
     * @return Server
     */
    public function server()
    {
        return $this->serverChannel()->getServer();
    }

    /**
     * Get swoole server
     * @return \swoole_server
     */
    public function swoole()
    {
        return $this->server()->getServer();
    }
}