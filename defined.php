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

// Check phar env
defined('IN_PHAR') or define('IN_PHAR', false);

// execute mode
defined('EXECUTE_MODE_FPM') or define('EXECUTE_MODE_FPM', 'fpm');
defined('EXECUTE_MODE_SWOOLE') or define('EXECUTE_MODE_SWOOLE', 'swoole');

// environment
defined('ENVIRONMENT_LOCAL') or define('ENVIRONMENT_LOCAL', 'local');
defined('ENVIRONMENT_DEV') or define('ENVIRONMENT_DEV', 'dev');
defined('ENVIRONMENT_PRERELEASE') or define('ENVIRONMENT_PRERELEASE', 'prerelease');
defined('ENVIRONMENT_PROD') or define('ENVIRONMENT_PROD', 'prod');