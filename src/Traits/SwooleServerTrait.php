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

namespace ShugaChara\Framework\Traits;

use ShugaChara\Framework\Constant\Consts;

/**
 * Trait SwooleServerTrait
 *
 * @package ShugaChara\Framework\Traits
 */
trait SwooleServerTrait
{
    /**
     * 获取 App Swoole 服务器
     * @return mixed
     */
    public function getAppSwooleServer()
    {
        return $this->swooleServer;
    }

    /**
     * 获取Swoole服务
     * @return mixed
     */
    public function getSwooleServer()
    {
        return $this->getAppSwooleServer()->getSwooleServer();
    }

    /**
     * 获取服务版本
     * @return mixed
     */
    public function getServerVersion()
    {
        return self::VERSION;
    }

    /**
     * 获取服务名称
     * @return mixed
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * 获取服务地址
     * @return mixed
     */
    public function getServerHost()
    {
        return $this->host;
    }

    /**
     * 获取服务端口
     * @return mixed
     */
    public function getServerPort()
    {
        return $this->port;
    }

    /**
     * 获取服务配置参数
     * @return mixed
     */
    public function getServerOptions()
    {
        return $this->options;
    }

    /**
     * 获取APP Swoole服务项目名称
     * @return string
     */
    public function getAppSwooleServerName()
    {
        return config()->get('APP_NAME') . '.' . $this->getServerName();
    }

    /**
     * 设置PidFile
     * @param string $pidFile
     * @return bool
     */
    public function setPidFile(string $pidFile = '')
    {
        if ($pidFile) {
            $this->pid_file = $pidFile;
        } else {
            if (isset($this->options['pid_file'])) {
                $this->pid_file = $this->options['pid_file'];
            }
            if (empty($this->pid_file)) {
                $this->options['pid_file'] = $this->pid_file = '/tmp/' . str_replace(' ', '-', $this->getAppSwooleServerName()) . '.pid';
            }
        }

        return true;
    }

    /**
     * 获取Pid
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * 获取PidFile
     * @return mixed
     */
    public function getPidFile()
    {
        return $this->pid_file;
    }

    /**
     * Swoole 运行模式设置
     * @param $mode
     * @return $this
     */
    public function setSwooleMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Swoole 运行模式获取
     * @return mixed
     */
    public function getSwooleMode()
    {
        return $this->mode;
    }

    /**
     * Swoole Socket类型设置
     * @param $socket_type
     * @return $this
     */
    public function setSwooleSocketType($socket_type)
    {
        $this->socket_type = $socket_type;
        return $this;
    }

    /**
     * Swoole Socket类型获取
     * @return mixed
     */
    public function getSwooleSocketType()
    {
        return $this->socket_type;
    }

    /**
     * 获取Swoole Socket类型名称
     * @return string
     */
    public function getSwooleSocketTypeName()
    {
        switch ($this->getSwooleSocketType()) {
            case 1: return Consts::SWOOLE_SERVER_SCHEME_TCP;
            case 2: return Consts::SWOOLE_SERVER_SCHEME_UDP;
            case 3: return Consts::SWOOLE_SERVER_SCHEME_TCP6;
            case 4: return Consts::SWOOLE_SERVER_SCHEME_UDP6;
            case 5: return Consts::SWOOLE_SERVER_SCHEME_UNIX_DGRAM;
            case 6: return Consts::SWOOLE_SERVER_SCHEME_UNIX_STREAM;
        }
    }
}