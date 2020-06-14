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
use ShugaChara\Core\Utils\Helper\PhpHelper;
use ShugaChara\Framework\Contracts\MainSwooleEventsInterface;
use swoole_process;
use Throwable;

/**
 * 主要：该 Trait 必须在 Command 里使用，并且确保已 use \ShugaChara\Framework\Traits\Swoole
 */

/**
 * Trait SwooleCommand
 * @package ShugaChara\Framework\Traits
 */
trait SwooleCommand
{
    /**
     * 服务信息
     */
    public function serverInfo()
    {
        // 获取当前计算机所有网络接口的IP地址
        $swooleGetLocalIp = '';
        $ips = swoole_get_local_ip();
        foreach ($ips as $eth => $val){
            $swooleGetLocalIp .= 'ip@' . $eth . $val . ', ';
        }

        if ($appLogo = fnc()->app()->getLogo()) {
            $this->getIO()->text('<ft-yellow-bold>' . fnc()->app()->getLogo() . '</ft-yellow-bold>');
        }

        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', '主服务名称', $this->getServerName()) . PHP_EOL . PHP_EOL);
        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', '服务监控地址', $this->getServerConfig('host', '0.0.0.0')) . PHP_EOL . PHP_EOL);
        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', '服务监听端口', $this->getServerConfig('port')) . PHP_EOL . PHP_EOL);
        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', '当前机器所有网络接口的IP地址', $swooleGetLocalIp) . PHP_EOL . PHP_EOL);

        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', '正在运行的服务用户', $this->getServerConfig('setting.user', get_current_user())) . PHP_EOL . PHP_EOL);
        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', '服务守护程序状态', $this->isDaemonize($this->getServerName()) ? 'YES' : 'NO') . PHP_EOL . PHP_EOL);
        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', '框架运行名称', fnc()->app()->getName()) . PHP_EOL . PHP_EOL);
        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', '框架运行版本', fnc()->app()->getVersion()) . PHP_EOL . PHP_EOL);
        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', 'PHP 运行版本', phpversion()) . PHP_EOL . PHP_EOL);
        $this->getIO()->write(sprintf('<ft-blue-bold>%s</ft-blue-bold> <ft-cyan-bold>%s</ft-cyan-bold>', 'Swoole 服务运行版本', SWOOLE_VERSION) . PHP_EOL . PHP_EOL);

        $rows = [];
        foreach ($this->getServerConfig('setting', []) as $key => $value) {
            $rows[] = ['<ft-blue-bold>' . $key . '</ft-blue-bold>', '<ft-cyan-bold>' . ((string) $value) . '</ft-cyan-bold>'];
            $rows[] = $this->getTableSeparator();
        }

        $this->getTable()->setHeaders(['NAME', 'VALUE'])->addRows($rows)->render();

        $this->getIO()->write(PHP_EOL);
    }

    /**
     * 服务停止
     * @return mixed
     * @throws Exception
     */
    public function serverStop()
    {
        $pidFile = $this->getSwooleSettingPidFile();
        if (file_exists($pidFile)) {
            $pid = intval(file_get_contents($pidFile));
            if (! swoole_process::kill($pid, 0)) {
                throw new Exception("服务 PID : {$pid} 不存在 ");
            }

            swoole_process::kill($pid);

            // Wait 5 seconds
            $time = time();
            while (true) {
                usleep(1000);
                if (! swoole_process::kill($pid, 0)) {
                    if (is_file($pidFile)) {
                        unlink($pidFile);
                    }
                    return $this->writelnBlock('服务停止时间 : ' . date('Y-m-d H:i:s'));
                    break;
                } else {
                    if (time() - $time > 15) {
                        throw new Exception('服务停止失败 , 异常：请尝试强制停止服务或kill进程');
                        break;
                    }
                }
            }

            throw new Exception('服务停止失败');
        }

        throw new Exception('服务PID文件不存在，请检查它是否在守护程序模式下运行！');
    }

    /**
     * 服务平滑加载
     * @throws Exception
     */
    public function serverReload()
    {
        $pidFile = $this->getSwooleSettingPidFile();
        if (file_exists($pidFile)) {
            PhpHelper::opCacheClear();
            $pid = file_get_contents($pidFile);
            if (! swoole_process::kill($pid, 0)) {
                throw new Exception("服务 PID : {$pid} 不存在");
            }
            swoole_process::kill($pid, SIGUSR1);
            $this->writelnBlock('服务 PID: ' . $pid . ' 正在向所有工作进程发送平滑加载通知服务, 于 ' . date('Y-m-d H:i:s') . ' 完成加载');
            return ;
        }

        throw new Exception('服务PID文件不存在，请检查它是否在守护程序模式下运行！');
    }

    /**
     * 处理全局 mainSwooleServerEventsCreate 事件
     */
    protected function handleMainSwooleServerEventsCreate()
    {
        $swooleMainEventsClass = fnc()->c()->get('swoole.main_events');
        if (class_exists($swooleMainEventsClass)) {
            try {
                $refSwooleMainEvents = new ReflectionClass($swooleMainEventsClass);
                if(! $refSwooleMainEvents->implementsInterface(MainSwooleEventsInterface::class)){
                    throw new Exception('MainSwooleEventsInterface 的全局文件不兼容 ' . $swooleMainEventsClass);
                }
                unset($refSwooleMainEvents);
            } catch (Throwable $throwable){
                throw new Exception($throwable->getMessage());
            }
        } else {
            throw new Exception('缺少全局事件文件');
        }

        $class = new $swooleMainEventsClass();

        // 初始化处理
        $class->initialize();

        // 注册 Swoole 事件
        $classFunctions = get_class_methods($class);
        foreach ($classFunctions as $event) {
            if ('on' != substr($event, 0, 2)) {
                continue;
            }

            $this->getServer()->getEventsRegister()->addEvent(lcfirst(substr($event, 2)), [$class, $event]);
        }

        // Swoole 处理
        $class->mainSwooleServerEventsCreate(
            $this->getServer()->getEventsRegister(),
            $this->getServer()->getSwooleServer()
        );
    }
}