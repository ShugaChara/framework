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

defined('IN_PHAR') or define('IN_PHAR', false);

// 系统内核框架名称
defined('FRAMEWORK_NAME') or define('FRAMEWORK_NAME', 'shugachara');
// 系统内核框架版本
defined('FRAMEWORK_VERSION') or define('FRAMEWORK_VERSION', 'v1.0');

// php-fpm 运行模式
defined('PHP_FPM_MODE') or define('PHP_FPM_MODE', 'php-fpm');
// swoole 运行模式
defined('PHP_SWOOLE_MODE') or define('PHP_SWOOLE_MODE', 'swoole');

// swoole
defined('SWOOLE_SERVER_HTTP') or define('SWOOLE_SERVER_HTTP', 'http');
defined('SWOOLE_SERVER_WEBSOCKET') or define('SWOOLE_SERVER_WEBSOCKET', 'websocket');

// 系统应用控制器默认命名空间
defined('APP_DEFAULT_CONTROLLER_NAMESPACE') or define('APP_DEFAULT_CONTROLLER_NAMESPACE', '\\App\\Http\\Controllers\\');
