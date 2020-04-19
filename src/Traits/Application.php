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

use ShugaChara\Framework\Components\Alias;

/**
 * Trait Application
 * @package ShugaChara\Framework\Traits
 */
trait Application
{
    /**
     * 获取应用名称
     * @return string
     */
    public function getAppName(): string
    {
        // TODO: Implement getAppName() method.

        return $this->appName;
    }

    /**
     * 获取应用版本
     * @return string
     */
    public function getAppVersion(): string
    {
        // TODO: Implement getAppVersion() method.

        return $this->appVersion;
    }

    /**
     * 设置各目录配置
     */
    protected function setPaths()
    {
        $this->_setByermPath();
        $this->_setEnvFile();
        $this->_setEnvPath();
        $this->_setConfigPath();
        $this->_setAppPath();
        $this->_setRouterPath();
        $this->_setRuntimePath();
    }

    /**
     * 设置根目录
     */
    private function _setByermPath()
    {
        Alias::set('path.base', $this->getByermPath());
    }

    /**
     * 获取根目录
     * @return string
     */
    public function getByermPath(): string
    {
        return BYERM_PATH;
    }

    /**
     * 设置配置文件
     */
    private function _setEnvFile()
    {
        Alias::set('path.file_env', $this->getByermPath() . '/.env');
    }

    /**
     * 获取配置文件
     * @return mixed|void
     */
    public function getEnvFile(): string
    {
        return Alias::get('path.file_env');
    }

    /**
     * 设置配置文件目录
     */
    private function _setEnvPath()
    {
        Alias::set('path.env', $this->getByermPath() . '/env');
    }

    /**
     * 获取配置文件目录
     * @return mixed|void
     */
    public function getEnvPath(): string
    {
        return Alias::get('path.env');
    }

    /**
     * 设置应用目录
     */
    private function _setAppPath()
    {
        Alias::set('path.app', $this->getByermPath() . '/app');
    }

    /**
     * 获取应用目录
     * @return mixed|void
     */
    public function getAppPath(): string
    {
        return Alias::get('path.app');
    }

    /**
     * 设置配置目录
     */
    private function _setConfigPath()
    {
        Alias::set('path.config', $this->getByermPath() . '/config');
    }

    /**
     * 获取配置目录
     * @return mixed|void
     */
    public function getConfigPath(): string
    {
        return Alias::get('path.config');
    }

    /**
     * 设置路由目录
     */
    private function _setRouterPath()
    {
        Alias::set('path.router', $this->getByermPath() . '/router');
    }

    /**
     * 获取路由目录
     * @return mixed|void
     */
    public function getRouterPath(): string
    {
        return Alias::get('path.router');
    }

    /**
     * 设置缓存目录
     */
    private function _setRuntimePath()
    {
        Alias::set('path.runtime', $this->getByermPath() . '/runtime');
    }

    /**
     * 获取缓存目录
     * @return mixed|void
     */
    public function getRuntimePath(): string
    {
        return Alias::get('path.runtime');
    }

    /**
     * 应用是否已启动
     * @return bool
     */
    protected function isRun(): bool
    {
        return (bool) $this->isRun;
    }
}