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
 * Class byermHelper
 * @package ShugaChara\Framework\Helpers
 */
class byermHelper
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
}