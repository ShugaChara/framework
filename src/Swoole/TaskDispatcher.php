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

namespace ShugaChara\Framework\Swoole;

use ShugaChara\Core\Traits\Singleton;
use ShugaChara\Framework\Contracts\TaskDispatcherInterface;

/**
 * Class TaskDispatcher
 * @method static $this getInstance(...$args)
 * @package ShugaChara\Framework\Swoole
 */
class TaskDispatcher implements TaskDispatcherInterface
{
    use Singleton;

    /**
     * @var Task
     */
    protected $task;

    /**
     * @param Task $task
     * @return mixed|void
     */
    final public function new(Task $task)
    {
        // TODO: Implement new() method.

        $this->task = $task;

        $this->handle();
    }

    /**
     * @return Task
     */
    public function task(): Task
    {
        return $this->task;
    }

    /**
     * @return mixed|void
     */
    public function handle()
    {
        // TODO: Implement handle() method.
    }
}

