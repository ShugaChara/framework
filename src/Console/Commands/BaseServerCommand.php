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

namespace ShugaChara\Framework\Console\Commands;

use ShugaChara\Console\Command;
use ShugaChara\Core\Helpers;
use ShugaChara\Framework\Constant\Consts;
use ShugaChara\Framework\Swoole\SwooleServerManager;
use swoole_process;

/**
 * Class BaseServerCommand
 * @package ShugaChara\Framework\Console\Commands
 */
abstract class BaseServerCommand extends Command
{
    /**
     * 服务管理
     * @var SwooleServerManager
     */
    protected $swooleServerManager;

    /**
     * 服务名称
     * @var
     */
    protected $server_name;

    /**
     * 服务配置
     * @var array
     */
    protected $config = [];

    /**
     * 是否守护进程模式
     * @var bool
     */
    protected $daemon = false;

    /**
     * 是否强制终止进程
     * @var bool
     */
    protected $force = false;

    /**
     * 服务状态类型
     * @var array
     */
    protected $serverStatusType = [
        Consts::SWOOLE_SERVER_START_NAME,
        Consts::SWOOLE_SERVER_STATUS_NAME,
        Consts::SWOOLE_SERVER_STOP_NAME,
        Consts::SWOOLE_SERVER_RELOAD_NAME,
        Consts::SWOOLE_SERVER_RESTART_NAME,
    ];

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    /**
     * 服务状态
     * @return mixed
     */
    abstract function status();

    /**
     * 服务启动
     * @return mixed
     */
    abstract function start();

    /**
     * 服务停止
     * @return mixed
     */
    abstract function stop();

    /**
     * 服务平滑加载
     * @return mixed
     */
    abstract function reload();

    /**
     * 服务重启
     * @return mixed
     */
    abstract function restart();

    /**
     * 获取Swoole服务管理对象
     * @return SwooleServerManager
     */
    public function getSwooleServerManager(): SwooleServerManager
    {
        return $this->swooleServerManager;
    }

    /**
     * 获取主进程名称
     * @return string
     */
    protected function getMasterProcessName()
    {
        return $this->server_name . ' master';
    }

    /**
     * 获取服务运行状态
     * @return bool
     */
    protected function getServerStatus()
    {
        $pidFile = Helpers::array_get($this->config, 'setting.pid_file', '');
        if (file_exists($pidFile)) {
            // 向进程发送信号,成功表示在运行状态
            return posix_kill(intval(file_get_contents($pidFile)), 0);
        }

        if ($is_running = process_is_running($this->getMasterProcessName())) {
            $is_running = port_is_running($this->config['port']);
        }

        return $is_running;
    }

    /**
     * 处理服务状态
     */
    protected function handleServerStatus()
    {
        if ($this->getServerStatus()) {
            $this->getSwooleServerStatusInfo($this->server_name);
            return $this->alert('服务已启动');
        }

        return $this->alert('服务未启动');
    }

    /**
     * 处理服务启动
     * @throws \Exception
     */
    protected function handleServerStart()
    {
        // 创建服务器
        $this->getSwooleServerManager()->createServer(
            $this->server_name,
            Helpers::array_get($this->config, 'port'),
            Helpers::array_get($this->config, 'host', '0.0.0.0'),
            Helpers::array_get($this->config, 'setting', [])
        );

        // 注册默认回调事件
        $this->getSwooleServerManager()->registerDefaultCallback(
            $this->getSwooleServerManager()->getServer(),
            $this->server_name
        );

        // hook 全局 mainSwooleServerEventsCreate 事件
        app()->getSwooleEventsObjectName()::mainSwooleServerEventsCreate($this->getSwooleServerManager()->getSwooleServerEventRegister(), $this->getSwooleServerManager()->getServer());

        $pidFile = isset($this->config['pid_file']) ? $this->config['pid_file']  : app()->getRuntimePath() . '/tmp/' . str_replace(' ', '-', $this->server_name) . '.pid';
        if (! file_exists($dir = dirname($pidFile))) {
            mkdir($dir, 0755, true);
        }

        // 主进程命名
        process_rename($this->getMasterProcessName());

        $this->info('主服务 Master : ' . $this->server_name);
        $this->info('服务监听地址 : ' . $this->config['host']);
        $this->info('服务监听端口 : ' . $this->config['port']);

        $ips = swoole_get_local_ip();
        foreach ($ips as $eth => $val){
            $this->info('ip@' . $eth . $val);
        }

        foreach (Helpers::array_get($this->config, 'setting', []) as $key => $datum){
            $this->info($key . " : " . (string)$datum);
        }

        $user = Helpers::array_get($this->config, 'setting.user', get_current_user());
        $this->info('运行服务用户 : ' . $user);
        $this->info('服务守护进程状态 : ' . $this->daemon);
        $this->info('swoole 服务运行版本 : ' . phpversion('swoole'));
        $this->info('php 运行版本 : ' . phpversion());
        $this->info('czphp 框架运行版本 : ' . app()->getAppVersion());
        $this->info('服务环境 : ' . environment());

        // 注入swoole
        container()->add('swoole', $this->getSwooleServerManager()->getServer());

        // 注入 Swoole 事件分发器
        container()->add('swooleEventDispatcher', $this->getSwooleServerManager()->getSwooleServerEventRegister());

        // 注册回调事件
        $this->getSwooleServerManager()->start();
    }

    /**
     * 处理服务停止
     */
    protected function handleServerStop()
    {
        $pidFile = Helpers::array_get($this->config, 'setting.pid_file', '');
        if (file_exists($pidFile)) {
            $pid = intval(file_get_contents($pidFile));
            if (! swoole_process::kill($pid, 0)) {
                return $this->error("服务PID :{$pid} 不存在 ");
            }

            if ($this->force) {
                swoole_process::kill($pid, SIGKILL);
            } else {
                swoole_process::kill($pid);
            }

            // 等待5秒
            $time = time();
            while (true) {
                usleep(1000);
                if (! swoole_process::kill($pid, 0)) {
                    if (is_file($pidFile)) {
                        unlink($pidFile);
                    }
                    return $this->alert('服务停止时间: ' . date('Y-m-d H:i:s'));
                    break;
                } else {
                    if (time() - $time > 15) {
                        return $this->error('服务停止失败 , try : 请尝试强制停止服务');
                        break;
                    }
                }
            }
            return $this->error('服务停止失败');
        } else {
            return $this->warn('服务PID文件不存在, 请检查是否以守护程序模式运行!');
        }
    }

    /**
     * 处理服务平滑加载
     */
    protected function handleServerReload()
    {
        $pidFile = Helpers::array_get($this->config, 'setting.pid_file', '');
        if (file_exists($pidFile)) {
            Helpers::opCacheClear();
            $pid = file_get_contents($pidFile);
            if (! swoole_process::kill($pid, 0)) {
                return $this->error("服务PID :{$pid} 不存在 ");
            }
            swoole_process::kill($pid, SIGUSR1);
            return $this->info('服务PID: ' . $pid . ' 重新加载所有Worker 并发送服务器重载命令: ' . date('Y-m-d H:i:s'));
        } else {
            return $this->warn('服务PID文件不存在, 请检查是否以守护程序模式运行!');
        }
    }

    /**
     * 处理服务重启
     */
    protected function handleServerRestart()
    {
        $this->stop();
        $this->start();
    }

    /**
     * 获取Swoole服务状态信息
     */
    protected function getSwooleServerStatusInfo($server_name)
    {
        exec("ps axu | grep '{$server_name}' | grep -v grep", $output);

        // list all process
        $rows = get_all_process($server_name);

        // combine
        $headers = ['USER', 'PID', 'RSS', 'STAT', 'START', 'COMMAND'];
        foreach ($rows as $key => $value) {
            $rows[$key] = array_combine($headers, $value);
        }

        $this->table($headers, $rows);

        unset($table, $headers, $output);
    }
}

