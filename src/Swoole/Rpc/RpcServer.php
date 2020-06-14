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
| shugachara Swoole Rpc Server
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework\Swoole\Rpc;

use ShugaChara\Framework\Contracts\PoolInterface;
use ShugaChara\Framework\Swoole\Task;
use ShugaChara\Swoole\EventsRegister;
use ShugaChara\Swoole\Manager\Timer;
use ShugaChara\Swoole\Rpc\Server;
use ShugaChara\Swoole\SwooleHelper;
use swoole_server;

/**
 * Class RpcServer
 * @package ShugaChara\Framework\Swoole\Rpc
 */
class RpcServer extends Server
{
    /**
     * 注册默认回调
     * @param swoole_server $server
     * @param               $server_name
     */
    public function registerDefaultCallback(swoole_server $server)
    {
        // 注册默认的工作程序启动事件
        $this->getEventsRegister()->addEvent(
            EventsRegister::onWorkerStart,
            function (swoole_server $server, $workerId) {
                if(PHP_OS != 'Darwin'){
                    if( ($workerId < fnc()->c()->get('swoole.rpc.setting.worker_num')) && $workerId >= 0){
                        SwooleHelper::setProcessRename(("rpc.Worker.{$workerId}"));
                    }
                }

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

        // 接收到数据时回调此函数，发生在 worker 进程中
        $this->getEventsRegister()->addEvent(
            EventsRegister::onReceive,
            function (swoole_server $server, int $fd, int $reactorId, string $data) {
                $this->rpcHandle(new DataBean($server, $fd, $reactorId, $data));
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
     * RPC 接口数据处理器
     * @param DataBean $bean
     */
    public function rpcHandle(DataBean $bean)
    {
        $rpcHandleClass = fnc()->c()->get('swoole.rpc.handle_class');
        $rpcHandleClassInstance = class_exists($rpcHandleClass) ? $rpcHandleClass::getInstance() : null;
        if ($rpcHandleClassInstance) {
            $rpcHandleClassInstance->new($bean);
        }
    }

    /**
     * Task 任务分发器
     * @param Task $task
     */
    public function taskDispatcher(Task $task)
    {
        $taskDispatcherClass = fnc()->c()->get('swoole.task.dispatcher_class');
        $taskDispatcherClassInstance = class_exists($taskDispatcherClass) ? $taskDispatcherClass::getInstance() : null;
        if ($taskDispatcherClassInstance) {
            $taskDispatcherClassInstance->new($task);
        }
    }
}

