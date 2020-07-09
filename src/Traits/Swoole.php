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
use ShugaChara\Core\Utils\Helper\ArrayHelper;
use ShugaChara\Framework\Swoole\Rpc\Server as RpcServer;
use ShugaChara\Framework\Swoole\Server;
use ShugaChara\Swoole\SwooleHelper;

/**
 * Trait Swoole
 * @package ShugaChara\Framework\Traits
 */
trait Swoole
{
    /**
     * @var 服务
     */
    protected $server;

    /**
     * Server 名称
     * @var
     */
    protected $serverName;

    /**
     * Server 配置名称
     * @var
     */
    protected $serverConfigName;

    /**
     * Server 配置
     * @var
     */
    protected $serverConfig;

    /**
     * 设置服务
     * @param $server
     */
    public function setServer($server)
    {
        $this->server = $server;
    }

    /**
     * 获取服务
     * @return Server | RpcServer
     * @throws Exception
     */
    public function getServer()
    {
        if (empty($this->server)) {
            throw new Exception('请先设置服务');
        }

        return $this->server;
    }

    /**
     * 设置服务名称
     * @param $name
     */
    public function setServerName($name)
    {
        if (in_array($name, [
            Server::SWOOLE_HTTP_SERVER,
            Server::SWOOLE_WEBSOCKET_SERVER,
            Server::SWOOLE_SERVER
        ])) {
            return $this->serverName = $name;
        }

        throw new Exception('找不到 ' . $name . ' 服务');
    }

    /**
     * 获取服务名称
     * @return string
     * @throws Exception
     */
    public function getServerName()
    {
        if (! $this->serverName) {
            throw new Exception('请先设置 Swoole 服务名称');
        }

        return $this->serverName;
    }

    /**
     * 设置服务配置名称
     * @param $name
     */
    public function setServerConfigName($name)
    {
        $this->serverConfigName = $name;
    }

    /**
     * 获取服务配置名称
     * @return mixed
     */
    public function getServerConfigName()
    {
        return $this->serverConfigName ? : $this->serverName;
    }

    /**
     * 获取 Swoole 服务配置
     * @return array
     * @throws Exception
     */
    public function getServerConfig($key = null, $default = null)
    {
        if (! $this->serverConfig) {
            if (! ($serverConfig = fnc()->c()->get('swoole.' . $this->getServerConfigName(), []))) {
                throw new Exception('请完成 Swoole 配置以启动服务');
            }

            $this->serverConfig = $serverConfig;
        }

        return ArrayHelper::get($this->serverConfig, $key, $default);
    }

    /**
     * 设置 Swoole 守护程序
     * @param bool $value
     */
    protected function setDaemonize(bool $value)
    {
        fnc()->c()->set('swoole.' . $this->getServerConfigName() . '.setting.daemonize', $value);
    }

    /**
     * 是否守护程序模式
     * @return bool
     */
    public function isDaemonize(): bool
    {
        return (bool) $this->getServerConfig('setting.daemonize', false);
    }

    /**
     * 获取主进程名称
     * @param $server_name
     * @return string
     */
    public function getMasterProcessName()
    {
        return $this->getServerConfigName() . '.master';
    }

    /**
     * 获取 Swool e 配置 pid_file 文件
     * @return string
     */
    protected function getSwooleSettingPidFile()
    {
        return $this->getServerConfig('setting.pid_file', fnc()->app()->getRootDirectory() . '/tmp/' . str_replace(' ', '-', $this->getServerConfigName()) . '.pid');
    }

    /**
     * 创建 Swoole 配置 Pid 目录
     */
    protected function createSwooleSettingPidDir()
    {
        if (! file_exists($dir = dirname($this->getSwooleSettingPidFile()))) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * 获取服务运行状态
     * @return bool
     */
    protected function getServerStatus()
    {
        $pidFile = $this->getSwooleSettingPidFile();
        if (file_exists($pidFile)) {
            // 向进程发送信号，成功表示它处于运行状态
            return posix_kill(intval(file_get_contents($pidFile)), 0);
        }

        if ($is_running = SwooleHelper::processIsRunning($this->getMasterProcessName())) {
            $is_running = SwooleHelper::portIsRunning($this->getServerConfig('port'));
        }

        return $is_running;
    }
}