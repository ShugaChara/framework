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

namespace ShugaChara\Framework\Contracts;

use ShugaChara\Framework\Swoole\Task;

/**
 * Interface TaskDispatcherInterface
 * @package ShugaChara\Framework\Contracts
 */
interface TaskDispatcherInterface
{
    /**
     * New Task
     * @param Task $task
     * @return mixed
     */
    public function new(Task $task);

    /**
     * Handle Task
     * @return mixed
     */
    public function handle();
}