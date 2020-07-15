<?php
// +----------------------------------------------------------------------
// | Created by ShugaChara. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2020 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| shugachara Rpc Server
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework\Swoole\Rpc;

use ShugaChara\Framework\Contracts\PoolInterface;
use ShugaChara\Framework\Swoole\Task;
use ShugaChara\Swoole\EventsRegister;
use ShugaChara\Swoole\Manager\Timer;
use ShugaChara\Swoole\Rpc\Server as RpcServer;
use swoole_server;

/**
 * Class Server
 * @package ShugaChara\Framework\Swoole\Rpc
 */
class Server extends RpcServer
{
    /**
     * 注册默认回调
     * @param swoole_server $server
     */
    public function registerDefaultCallback(swoole_server $server)
    {
        // 注册连接事件
        $this->getEventsRegister()->addEvent(
            EventsRegister::onConnect,
            function (swoole_server $server, int $fd, int $reactorId) {}
        );

        // 注册默认的工作程序启动事件
        $this->getEventsRegister()->addEvent(
            EventsRegister::onWorkerStart,
            function (swoole_server $server, $workerId) {
                // 建立连接池
                foreach (container()->getContainerServices() as $service) {
                    if ($service instanceof PoolInterface) {
                        $service->initPool();
                    }
                }
            }
        );

        // 注册默认的工作进程退出事件
        $this->getEventsRegister()->addEvent(
            EventsRegister::onWorkerExit,
            function () {
                Timer::clearAll();
            }
        );

        // 注册默认的数据接收事件
        $this->getEventsRegister()->addEvent(
            EventsRegister::onReceive,
            function (swoole_server $server, int $fd, int $reactor_id, $data) {
                $this->rpcHandle(new DataBean($server, $fd, $reactor_id, $data, $this));
            }
        );

        // 注册默认的 onTask 事件
        $this->getEventsRegister()->addEvent(
            EventsRegister::onTask,
            function (swoole_server $serv, int $task_id, int $src_worker_id, $data) {
                $this->taskDispatcher(new Task($serv, $task_id, $src_worker_id, $data));
            }
        );
    }

    /**
     * Task 任务分发器
     * @param Task $task
     */
    public function taskDispatcher(Task $task)
    {
        $taskDispatcherClass = config()->get('swoole.task.dispatcher_class');
        $taskDispatcherClassInstance = class_exists($taskDispatcherClass) ? $taskDispatcherClass::getInstance() : null;
        if ($taskDispatcherClassInstance) {
            $taskDispatcherClassInstance->new($task);
        }
    }

    /**
     * 接收数据处理器
     * @param DataBean $bean
     */
    public function rpcHandle(DataBean $bean)
    {
        $taskDispatcherClass = config()->get('swoole.rpc.handle_class');
        $taskDispatcherClassInstance = class_exists($taskDispatcherClass) ? $taskDispatcherClass::getInstance() : null;
        if ($taskDispatcherClassInstance) {
            $taskDispatcherClassInstance->new($bean);
        }
    }
}