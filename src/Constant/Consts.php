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

namespace ShugaChara\Framework\Constant;

class Consts
{
    // app
    const APP_WEB_MODE = 'web';
    const APP_SWOOLE_MODE = 'swoole';

    // swoole server
    const SWOOLE_SERVER_HTTP = 'http';
    const SWOOLE_SERVER_WEBSOCKET = 'websocket';

    const SWOOLE_SERVER_STATUS_NAME = 'status';
    const SWOOLE_SERVER_START_NAME = 'start';
    const SWOOLE_SERVER_STOP_NAME = 'stop';
    const SWOOLE_SERVER_RELOAD_NAME = 'reload';
    const SWOOLE_SERVER_RESTART_NAME = 'restart';
}