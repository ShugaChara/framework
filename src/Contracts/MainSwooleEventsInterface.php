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
     * 每个 Worker进程/Task进程启动 回调事件处理
     * @param swoole_server $server
     * @param int           $worker_id
     * @return mixed
     */
    public function doWorkerStart(swoole_server $server, int $worker_id);

    /**
     * 服务启动事件回调处理
     * @param swoole_server $server
     * @return mixed
     */
    public function doStart(swoole_server $server);
}
