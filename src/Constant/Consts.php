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
    // swoole server
    const SWOOLE_SERVER_HTTP = 'http';
    const SWOOLE_SERVER_WEBSOCKET = 'websocket';

    const SWOOLE_SERVER_STATUS_NAME = 'status';
    const SWOOLE_SERVER_START_NAME = 'start';
    const SWOOLE_SERVER_STOP_NAME = 'stop';
    const SWOOLE_SERVER_RELOAD_NAME = 'reload';
    const SWOOLE_SERVER_RESTART_NAME = 'restart';

    const SWOOLE_SERVER_SCHEME_TCP = 'tcp';
    const SWOOLE_SERVER_SCHEME_TCP6 = 'tcp6';
    const SWOOLE_SERVER_SCHEME_UNIX_DGRAM = 'unix_dgram';
    const SWOOLE_SERVER_SCHEME_UNIX_STREAM = 'unix_stream';
    const SWOOLE_SERVER_SCHEME_UDP = 'udp';
    const SWOOLE_SERVER_SCHEME_UDP6 = 'udp6';
}