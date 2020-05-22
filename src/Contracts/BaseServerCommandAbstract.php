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

use Exception;
use ShugaChara\Console\Command;
use ShugaChara\Core\Utils\Helper\PhpHelper;
use ShugaChara\Framework\Helpers\FHelper;
use ShugaChara\Framework\Traits\Swoole;
use ShugaChara\Swoole\SwooleHelper;
use Symfony\Component\Console\Input\InputInterface;
use swoole_process;

/**
 * Class BaseServerCommandAbstract
 * @package ShugaChara\Framework\Contracts
 */
abstract class BaseServerCommandAbstract extends Command implements StatusManagerInterface
{
    use Swoole;

    /**
     * 初始服务
     * @param                $server_name
     * @param InputInterface $input
     * @return int
     * @throws Exception
     */
    protected function initServer($server_name, InputInterface $input)
    {
        $this->setServerName($server_name);

        $status = strtolower($input->getArgument('status')) ? : static::STATUS_NAME;
        $daemon = $input->hasParameterOption(['--daemon', '-d'], true) ? true : false;
        if (! in_array($status, self::STATUS_TYPES)) {
            throw new Exception($status . ' 服务状态未定义,请通过 --help 查看命令');
        }

        // 服务守护进程
        if ($daemon) {
            $this->setDaemonize($daemon);
        }

        $this->$status();
    }

    /**
     * 获取服务状态
     * @return mixed|void
     * @throws Exception
     */
    public function status()
    {
        // TODO: Implement status() method.

        if (! $this->getServerStatus()) {
            throw new Exception($this->getServerName() . ' 服务未启动');
        }

        $this->getSwooleServerStatusInfo();

        return $this->info($this->getServerName() . ' 服务已启动');
    }

    /**
     * 服务启动
     * @return mixed|void
     * @throws Exception
     */
    public function start()
    {
        // TODO: Implement start() method.

        // 创建服务器
        $this->getServer()->createServer(
            $this->getServerName(),
            $this->getServerConfig('port'),
            $this->getServerConfig('host', '0.0.0.0'),
            $this->getServerConfig('setting', [])
        );

        // 注册默认回调事件
        $this->getServer()->registerDefaultCallback(
            $this->getServer()->getServer(),
            $this->getServerName()
        );

        // Swoole 容器注入
        container()->add('swoole', $this->getServer());

        // hook 全局 mainSwooleServerEventsCreate 事件
        $this->handleMainSwooleServerEventsCreate();

        // pid 进程文件
        $pidFile = $this->getSwooleSettingPidFile();
        if (! file_exists($dir = dirname($pidFile))) {
            mkdir($dir, 0755, true);
        }

        // 主进程命名
        SwooleHelper::setProcessRename($this->getMasterProcessName($this->getServerName()));

        // 获取当前机器的所有网络接口的IP地址
        $swooleGetLocalIp = '';
        $ips = swoole_get_local_ip();
        foreach ($ips as $eth => $val){
            $swooleGetLocalIp .= 'ip@' . $eth . $val . ', ';
        }

        $tableData = [
            [
                '主服务名称', $this->getServerName()
            ],
            [
                '服务监听地址', $this->getServerConfig('host', '0.0.0.0')
            ],
            [
                '服务监听端口', $this->getServerConfig('port')
            ],
            [
                '当前机器的所有网络接口的IP地址', $swooleGetLocalIp
            ],
        ];

        foreach ($this->getServerConfig('setting', []) as $key => $datum) {
            $countSetting = count($tableData);
            $tableData[$countSetting][0] = $key;
            $tableData[$countSetting][1] = (string) $datum;
        }

        $tableData[] = ['运行服务用户', $this->getServerConfig('setting.user', get_current_user())];
        $tableData[] = ['服务守护进程状态', $this->isDaemonize($this->getServerName()) ? '是' : '否'];
        $tableData[] = ['框架运行版本', FHelper::app()->getAppVersion()];
        $tableData[] = ['php 运行版本', phpversion()];
        $tableData[] = ['swoole 服务运行版本', SWOOLE_VERSION];

        $this->table(
            [
                'NAME', 'VALUE'
            ],
            $tableData
        );

        // 注册回调事件
        $this->getServer()->start();
    }

    /**
     * 服务停止
     * @return mixed|void
     * @throws Exception
     */
    public function stop()
    {
        // TODO: Implement stop() method.

        $pidFile = $this->getSwooleSettingPidFile();
        if (file_exists($pidFile)) {
            $pid = intval(file_get_contents($pidFile));
            if (! swoole_process::kill($pid, 0)) {
                throw new Exception("服务PID : {$pid} 不存在 ");
            }

            swoole_process::kill($pid);

            // 等待5秒
            $time = time();
            while (true) {
                usleep(1000);
                if (! swoole_process::kill($pid, 0)) {
                    if (is_file($pidFile)) {
                        unlink($pidFile);
                    }
                    return $this->info('服务停止时间: ' . date('Y-m-d H:i:s'));
                    break;
                } else {
                    if (time() - $time > 15) {
                        throw new Exception('服务停止失败 , try : 请尝试强制停止服务');
                        break;
                    }
                }
            }

            throw new Exception('服务停止失败');
        }

        throw new Exception('服务PID文件不存在, 请检查是否以守护程序模式运行!');
    }

    /**
     * 服务重载
     * @return mixed|void
     * @throws Exception
     */
    public function reload()
    {
        // TODO: Implement reload() method.

        $pidFile = $this->getSwooleSettingPidFile();
        if (file_exists($pidFile)) {
            PhpHelper::opCacheClear();
            $pid = file_get_contents($pidFile);
            if (! swoole_process::kill($pid, 0)) {
                throw new Exception("服务PID : {$pid} 不存在 ");
            }
            swoole_process::kill($pid, SIGUSR1);
            return $this->info('服务PID: ' . $pid . ' 向所有worker进程发送通知重载服务,命令执行于 ' . date('Y-m-d H:i:s'));
        }

        throw new Exception('服务PID文件不存在, 请检查是否以守护程序模式运行!');
    }

    /**
     * 服务重启
     * @return mixed|void
     * @throws Exception
     */
    public function restart()
    {
        // TODO: Implement restart() method.

        $this->stop();

        $this->start();
    }
}

