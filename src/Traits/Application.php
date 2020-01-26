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
 * Trait Application
 * @package ShugaChara\Framework\Traits
 */
trait Application
{
    /**
     * 系统核心配置文件
     * @var string
     */
    protected $envFile = '@path/.env';

    /**
     * 系统核心配置目录
     * @var string
     */
    protected $envPath = '@path/env';

    /**
     * App项目目录
     * @var string
     */
    protected $appPath = '@path/app';

    /**
     * App配置目录
     * @var string
     */
    protected $configPath = '@path/config';

    /**
     * App路由目录
     * @var string
     */
    protected $routerPath = '@path/router';

    /**
     * App缓存目录
     * @var string
     */
    protected $runtimePath = '@path/runtime';

    /**
     * 初始化项目路径
     */
    protected function initApplicationPath()
    {
        $this->setEnvFile(str_replace('@path', $this->getAppBasePath(), $this->envFile));
        $this->setEnvPath(str_replace('@path', $this->getAppBasePath(), $this->envPath));
        $this->setAppPath(str_replace('@path', $this->getAppBasePath(), $this->appPath));
        $this->setConfigPath(str_replace('@path', $this->getAppBasePath(), $this->configPath));
        $this->setRouterPath(str_replace('@path', $this->getAppBasePath(), $this->routerPath));
        $this->setRuntimePath(str_replace('@path', $this->getAppBasePath(), $this->runtimePath));
    }

    /**
     * 设置系统核心配置文件
     * @param string $envFile
     * @return $this
     */
    public function setEnvFile(string $envFile)
    {
        $this->envFile = $envFile;
        return $this;
    }

    /**
     * 获取系统核心配置文件
     * @return string
     */
    public function getEnvFile()
    {
        return $this->envFile;
    }

    /**
     * 设置系统核心配置目录
     * @param string $envPath
     * @return $this
     */
    public function setEnvPath(string $envPath)
    {
        $this->envPath = $envPath;
        return $this;
    }

    /**
     * 获取系统核心配置目录
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * 设置App项目目录
     * @param string $appPath
     * @return $this
     */
    public function setAppPath(string $appPath)
    {
        $this->appPath = $appPath;
        return $this;
    }

    /**
     * 获取App项目目录
     * @return string
     */
    public function getAppPath()
    {
        return $this->appPath;
    }

    /**
     * 设置App配置目录
     * @param string $configPath
     * @return $this
     */
    public function setConfigPath(string $configPath)
    {
        $this->configPath = $configPath;
        return $this;
    }

    /**
     * 获取App配置目录
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

    /**
     * 设置App路由目录
     * @param string $routerPath
     * @return $this
     */
    public function setRouterPath(string $routerPath)
    {
        $this->routerPath = $routerPath;
        return $this;
    }

    /**
     * 获取App路由目录
     * @return string
     */
    public function getRouterPath()
    {
        return $this->routerPath;
    }

    /**
     * 设置App缓存目录
     * @param string $runtimePath
     * @return $this
     */
    public function setRuntimePath(string $runtimePath)
    {
        $this->runtimePath = $runtimePath;
        return $this;
    }

    /**
     * 获取App缓存目录
     * @return string
     */
    public function getRuntimePath()
    {
        return $this->runtimePath;
    }
}