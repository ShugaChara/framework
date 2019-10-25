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

use function date_default_timezone_set;
use ShugaChara\Framework\Application;
use ShugaChara\Framework\Constant\Consts;

/**
 * Trait ApplicationTrait
 *
 * @package ShugaChara\Framework\Traits
 */
trait ApplicationTrait
{
    /**
     * 应用运行状态
     * @return bool
     */
    public function isRun(): bool
    {
        return $this->isRun;
    }

    /**
     * App是否普通模式 web为普通模式
     * @return bool
     */
    public function isGeneralMode()
    {
        return ($this->getAppMode() == Consts::APP_WEB_MODE) ? true : false;
    }

    /**
     * 设置APP模式
     * @param string $mode
     * @return $this
     */
    public function setAppMode(string $mode): Application
    {
        $mode = strtolower($mode);
        if (in_array($mode, static::APP_MODE)) {
            $this->appMode = $mode;
        }

        return $this;
    }

    /**
     * 设置时区
     *
     * @param string $timezone
     */
    public function setDateTimezone($timezone)
    {
        date_default_timezone_set($timezone);
    }

    /**
     * 获取时区
     * @return string
     */
    public function getDateTimezone()
    {
        return date_default_timezone_get();
    }

    /**
     * @return mixed
     */
    public function getEnvFile(): string
    {
        return $this->envFile;
    }

    /**
     * @return mixed
     */
    public function getEnvPath(): string
    {
        return $this->envPath;
    }

    /**
     * @return mixed
     */
    public function getMainSwooleEventsFilePath(): string
    {
        return sprintf('%s/%s.php', $this->getBasePath(), $this->mainSwooleEventsClassName);
    }

    /**
     * @return mixed
     */
    public function getAppPath(): string
    {
        return $this->appPath;
    }

    /**
     * @return mixed
     */
    public function getRouterPath(): string
    {
        return $this->routerPath;
    }

    /**
     * @return mixed
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * @return mixed
     */
    public function getRuntimePath(): string
    {
        return $this->runtimePath;
    }

    /**
     * @return mixed
     */
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     * 获取APP VERSION
     * @return mixed
     */
    public function getAppVersion(): string
    {
        return $this->appVersion;
    }

    /**
     * 获取APP 模式
     * @return mixed
     */
    public function getAppMode(): string
    {
        return $this->appMode;
    }

    /**
     * 获取IOC容器
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * 获取Swoole监听事件对象
     * @return mixed
     */
    public function getSwooleEventsObjectName()
    {
        return $this->mainSwooleEventsObjectName;
    }
}