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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use swoole_process;

/**
 * Class BaseServerCommand
 * @package ShugaChara\Framework\Console\Commands
 */
abstract class BaseServerCommand extends Command
{
    /**
     * @var ConsoleOutput
     */
    protected $output;

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

        $this->output = new ConsoleOutput();
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
            return $this->output->writeln('server is running ...');
        }

        return $this->output->writeln('server is not running ...');
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
        app()->getSwooleEventsObjectName()::mainSwooleServerEventsCreate($this->getSwooleServerManager()->getSwooleServerEventRegister());

        $pidFile = isset($this->config['pid_file']) ? $this->config['pid_file']  : '/tmp/' . str_replace(' ', '-', $this->server_name) . '.pid';
        if (! file_exists($dir = dirname($pidFile))) {
            mkdir($dir, 0755, true);
        }

        // 主进程命名
        process_rename($this->getMasterProcessName());

        $this->output->writeln('main server : ' . $this->server_name . PHP_EOL);
        $this->output->writeln('listen address : ' . $this->config['host'] . PHP_EOL);
        $this->output->writeln('listen port : ' . $this->config['port'] . PHP_EOL);

        $ips = swoole_get_local_ip();
        foreach ($ips as $eth => $val){
            $this->output->writeln('ip@' . $eth . $val) . PHP_EOL;
        }

        foreach (Helpers::array_get($this->config, 'setting', []) as $key => $datum){
            $this->output->writeln($key . " : " . (string)$datum) . PHP_EOL;
        }

        $user = Helpers::array_get($this->config, 'setting.user', get_current_user());
        $this->output->writeln('run at user : ' . $user) . PHP_EOL;
        $this->output->writeln('daemonize : ' . $this->daemon) . PHP_EOL;
        $this->output->writeln('swoole version : ' . phpversion('swoole')) . PHP_EOL;
        $this->output->writeln('php version : ' . phpversion()) . PHP_EOL;
        $this->output->writeln('czphp app swoole : ' . app()->getAppVersion()) . PHP_EOL;
        $this->output->writeln('environment : ' . environment()) . PHP_EOL;

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
                return $this->output->writeln("PID :{$pid} not exist ");
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
                    return $this->output->writeln('server stop at ' . date('Y-m-d H:i:s'));
                    break;
                } else {
                    if (time() - $time > 15) {
                        return $this->output->writeln('stop server fail , try : php czphp stop force');
                        break;
                    }
                }
            }
            return $this->output->writeln('stop server fail');
        } else {
            return $this->output->writeln('PID file does not exist, please check whether to run in the daemon mode!');
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
                return $this->output->writeln("pid :{$pid} not exist ");
            }
            swoole_process::kill($pid, SIGUSR1);
            return $this->output->writeln('PID: ' . $pid . ' reloadType all-worker send server reload command at ' . date('Y-m-d H:i:s'));
        } else {
            return $this->output->writeln('PID file does not exist, please check whether to run in the daemon mode!');
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
        $output = get_all_process($server_name);

        // combine
        $headers = ['USER', 'PID', 'RSS', 'STAT', 'START', 'COMMAND'];
        foreach ($output as $key => $value) {
            $output[$key] = array_combine($headers, $value);
        }

        $table = new Table($this->output);
        $table
            ->setHeaders($headers)
            ->setRows($output)
        ;
        $table->render();

        unset($table, $headers, $output);
    }
}

