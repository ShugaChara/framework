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
     * 获取当前应用环境
     * @return string
     */
    public static function environment()
    {
        return trim(getenv('APP_ENV'));
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
        return container()->get('logs')(($name ?? static::app()->getAppName()));
    }
}