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

// environment
defined('C_ENVIRONMENT_LOCAL') or define('C_ENVIRONMENT_LOCAL', 'local');
defined('C_ENVIRONMENT_DEV') or define('C_ENVIRONMENT_DEV', 'dev');
defined('C_ENVIRONMENT_PRERELEASE') or define('C_ENVIRONMENT_PRERELEASE', 'prerelease');
defined('C_ENVIRONMENT_PROD') or define('C_ENVIRONMENT_PROD', 'prod');