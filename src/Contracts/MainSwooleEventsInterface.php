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

namespace ShugaChara\Framework\Contracts;

use swoole_server;

interface MainSwooleEventsInterface
{
    /**
     * 服务启动事件回调处理
     * @param swoole_server $server
     * @return mixed
     */
    public function doStart(swoole_server $server);
}
