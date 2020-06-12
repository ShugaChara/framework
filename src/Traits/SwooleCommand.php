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
}