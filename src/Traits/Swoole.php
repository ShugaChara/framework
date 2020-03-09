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

use ShugaChara\Core\Helpers;
use ShugaChara\Framework\Swoole\ServerManager;

/**
 * Trait Swoole
 * @package ShugaChara\Framework\Traits
 */
trait Swoole
{
    /**
     * swoole 服务配置
     * @var
     */
    protected $swooleConfig;

    /**
     * swoole 服务管理器对象
     * @return ServerManager
     */
    protected function serverManager(): ServerManager
    {
        return ServerManager::getInstance();
    }

    /**
     * 获取主进程名称
     * @param $server_name
     * @return string
     */
    protected function getMasterProcessName($server_name)
    {
        return $server_name . ' master';
    }

    /**
     * 获取 swoole 服务配置信息
     * @param $server_name
     * @return mixed|null
     */
    protected function getConfig($server_name)
    {
        if (isset($this->swooleConfig[$server_name])) {
            return $this->swooleConfig[$server_name];
        }

        if ($config = config()->get('swoole.' . $server_name, [])) {
            return $this->swooleConfig[$server_name] = $config;
        }
    }

    /**
     * 设置 swoole 守护进程
     * @param      $server_name
     * @param bool $value
     */
    protected function setDaemonize($server_name, bool $value)
    {
        if ($this->getConfig($server_name)) {
            $this->swooleConfig[$server_name]['setting']['daemonize'] = $value;
        }
    }

    /**
     * 是否守护进程模式
     * @param $server_name
     * @return bool
     */
    protected function isDaemonize($server_name): bool
    {
        return (bool) Helpers::array_get($this->getConfig($server_name), 'setting.daemonize', false);
    }

    /**
     * 获取服务运行状态
     * @param $server_name
     * @return bool
     */
    protected function getServerStatus($server_name)
    {
        $config = $this->getConfig($server_name);
        $pidFile = Helpers::array_get($config, 'setting.pid_file', '');
        if (file_exists($pidFile)) {
            // 向进程发送信号,成功表示在运行状态
            return posix_kill(intval(file_get_contents($pidFile)), 0);
        }

        if ($is_running = process_is_running($this->getMasterProcessName($server_name))) {
            $is_running = port_is_running($config['port']);
        }

        return $is_running;
    }

    /**
     * 获取Swoole服务状态信息
     * @param $server_name
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