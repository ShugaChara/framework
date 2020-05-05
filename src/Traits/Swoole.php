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

namespace ShugaChara\Framework\Traits;

use Exception;
use ReflectionClass;
use ShugaChara\Core\Utils\Helper\ArrayHelper;
use ShugaChara\Framework\Contracts\MainSwooleEventsInterface;
use ShugaChara\Framework\Helpers\FHelper;
use ShugaChara\Framework\Swoole\Server;
use ShugaChara\Swoole\SwooleHelper;
use Throwable;

/**
 * Trait Swoole
 * @package ShugaChara\Framework\Traits
 */
trait Swoole
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var
     */
    protected $serverName;

    /**
     * swoole 服务管理器对象
     * @return Server
     */
    protected function getServer(): Server
    {
        if (! $this->server instanceof Server) {
            $this->server = new Server();
        }

        return $this->server;
    }

    /**
     * 获取 Swoole 配置
     * @return array
     * @throws Exception
     */
    protected function getConfig(): array
    {
        return FHelper::c()->get('swoole.' . $this->getServerName(), []);
    }

    /**
     * 设置服务名称
     * @param $serverName
     */
    protected function setServerName($serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * 获取服务名称
     * @return mixed
     */
    protected function getServerName()
    {
        if (! $this->serverName) {
            throw new Exception('请先设置 Swoole 服务名称');
        }

        return $this->serverName;
    }

    /**
     * 设置 swoole 守护进程
     * @param bool $value
     */
    protected function setDaemonize(bool $value)
    {
        FHelper::c()->set('swoole.' . $this->getServerName() . '.setting.daemonize', $value);
    }

    /**
     * 是否守护进程模式
     * @return bool
     */
    protected function isDaemonize(): bool
    {
        return (bool) ArrayHelper::get($this->getConfig(), 'setting.daemonize', false);
    }

    /**
     * 获取主进程名称
     * @param $server_name
     * @return string
     */
    protected function getMasterProcessName()
    {
        return $this->getServerName() . ' master';
    }

    /**
     * 获取服务运行状态
     * @return bool
     */
    protected function getServerStatus()
    {
        $pidFile = $this->getSwooleSettingPidFile();
        if (file_exists($pidFile)) {
            // 向进程发送信号,成功表示在运行状态
            return posix_kill(intval(file_get_contents($pidFile)), 0);
        }

        if ($is_running = SwooleHelper::processIsRunning($this->getMasterProcessName())) {
            $is_running = SwooleHelper::portIsRunning(ArrayHelper::get($this->getConfig(), 'port'));
        }

        return $is_running;
    }

    /**
     * 获取Swoole服务状态信息
     */
    protected function getSwooleServerStatusInfo()
    {
        exec("ps axu | grep '{$this->getServerName()}' | grep -v grep", $output);

        // list all process
        $rows = SwooleHelper::getAllProcess($this->getServerName());

        // combine
        $headers = ['USER', 'PID', 'RSS', 'STAT', 'START', 'COMMAND'];
        foreach ($rows as $key => $value) {
            $rows[$key] = array_combine($headers, $value);
        }

        $this->table($headers, $rows);

        unset($table, $headers, $output);
    }

    /**
     * 获取 Swoole 配置 pid_file 文件
     * @return string
     */
    protected function getSwooleSettingPidFile()
     {
         return ArrayHelper::get($this->getConfig(), 'setting.pid_file', FHelper::app()->getRootDirectory() . '/tmp/' . str_replace(' ', '-', $this->getServerName()) . '.pid');
     }

    /**
     * 处理全局 mainSwooleServerEventsCreate 事件
     */
     protected function handleMainSwooleServerEventsCreate()
     {
         $swooleMainEventsClass = FHelper::c()->get('swoole.main_events');
         if (class_exists($swooleMainEventsClass)) {
             try {
                 $refSwooleMainEvents = new ReflectionClass($swooleMainEventsClass);
                 if(! $refSwooleMainEvents->implementsInterface(MainSwooleEventsInterface::class)){
                     throw new Exception('global file for MainSwooleEventsInterface is not compatible for ' . $swooleMainEventsClass);
                 }
                 unset($refSwooleMainEvents);
             } catch (Throwable $throwable){
                 throw new Exception($throwable->getMessage());
             }
         } else {
             throw new Exception("global events file missing!\n");
         }

         $class = new $swooleMainEventsClass();

         // init swoole handle
         $class->initialize();

         // init swoole event handle
         $class->mainSwooleServerEventsCreate(
             $this->getServer()->getEventsRegister(),
             $this->getServer()->getServer()
         );
     }
}