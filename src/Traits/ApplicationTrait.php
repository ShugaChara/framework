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

/**
 * Trait ApplicationTrait
 *
 * @package ShugaChara\Framework\Traits
 */
trait ApplicationTrait
{
    /**
     * 启动框架前置操作
     *
     * @return bool
     */
    public function beforeRun(): bool
    {
        return true;
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
     * @return mixed
     */
    public function getEnvFile()
    {
        return $this->envFile;
    }

    /**
     * @return mixed
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * @return mixed
     */
    public function getAppPath()
    {
        return $this->appPath;
    }

    /**
     * @return mixed
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

    /**
     * @return mixed
     */
    public function getRuntimePath()
    {
        return $this->runtimePath;
    }

    /**
     * @return mixed
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @return mixed
     */
    public function getAppVersion()
    {
        return $this->appVersion;
    }

    /**
     * 获取IOC容器
     * @return Container
     */
    public function container()
    {
        return $this->container;
    }
}