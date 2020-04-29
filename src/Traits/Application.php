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
use ShugaChara\Framework\Helpers\ByermHelper;

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
     * 设置应用运行模式
     * @param $mode_type
     */
    public function setAppMode($mode_type)
    {
        $mode_type = strtolower($mode_type);

        if (in_array($mode_type, [static::MODE_SWOOLE, static::MODE_FPM])) {
            $this->appMode = $mode_type;
        }
    }

    /**
     * 获取应用运行模式
     * @return string
     */
    public function getAppMode(): string
    {
        return $this->appMode;
    }

    /**
     * 设置各目录配置
     */
    protected function setPaths()
    {
        $this->_setBasePath();
        $this->_setEnvFile();
        $this->_setEnvPath();
        $this->_setConfigPath();
        $this->_setAppPath();
        $this->_setRouterPath();
        $this->_setRuntimePath();

        Alias::set('paths', static::$paths);
    }

    /**
     * 设置根目录
     */
    private function _setBasePath()
    {
        static::$paths['base'] = $this->getBasePath();
    }

    /**
     * 获取根目录
     * @return string
     */
    public function getBasePath(): string
    {
        return BYERM_PATH;
    }

    /**
     * 设置配置文件
     */
    private function _setEnvFile()
    {
        static::$paths['file_env'] = $this->getBasePath() . '/.env';
    }

    /**
     * 获取配置文件
     * @return mixed|void
     */
    public function getEnvFile(): string
    {
        return static::$paths['file_env'];
    }

    /**
     * 设置配置文件目录
     */
    private function _setEnvPath()
    {
        static::$paths['env'] = $this->getBasePath() . '/env';
    }

    /**
     * 获取配置文件目录
     * @return mixed|void
     */
    public function getEnvPath(): string
    {
        return static::$paths['env'];
    }

    /**
     * 设置应用目录
     */
    private function _setAppPath()
    {
        static::$paths['app'] = $this->getBasePath() . '/app';
    }

    /**
     * 获取应用目录
     * @return mixed|void
     */
    public function getAppPath(): string
    {
        return static::$paths['app'];
    }

    /**
     * 设置配置目录
     */
    private function _setConfigPath()
    {
        static::$paths['config'] = $this->getBasePath() . '/config';
    }

    /**
     * 获取配置目录
     * @return mixed|void
     */
    public function getConfigPath(): string
    {
        return static::$paths['config'];
    }

    /**
     * 设置路由目录
     */
    private function _setRouterPath()
    {
        static::$paths['router'] = $this->getBasePath() . '/router';
    }

    /**
     * 获取路由目录
     * @return mixed|void
     */
    public function getRouterPath(): string
    {
        return static::$paths['router'];
    }

    /**
     * 设置缓存目录
     */
    private function _setRuntimePath()
    {
        static::$paths['runtime'] = $this->getBasePath() . '/runtime';
    }

    /**
     * 获取缓存目录
     * @return mixed|void
     */
    public function getRuntimePath(): string
    {
        return static::$paths['runtime'];
    }

    /**
     * 服务容器注册
     * @param array $services
     */
    public function serviceProviderRegister(array $services)
    {
        foreach ($services as $service) {
            (new $service)->register(ByermHelper::container());
        }
    }

    /**
     * 注册默认系统别名
     */
    public function defaultSystemAlias()
    {
        Alias::set('env', [
            //  当前应用环境
            'current'       =>  ByermHelper::environment(),
            //  是否调试环境模式
            'is_debug'      =>  ByermHelper::config()->get('APP_DEBUG') === 'true' ? true : false,
            //  是否本地环境
            'is_local'      =>  ByermHelper::environment() == C_ENVIRONMENT_LOCAL,
            //  是否测试环境
            'is_dev'        =>  ByermHelper::environment() == C_ENVIRONMENT_DEV,
            // 是否预发布环境
            'is_prerelease' =>  ByermHelper::environment() == C_ENVIRONMENT_PRERELEASE,
            // 是否生产环境
            'is_prod'       =>  ByermHelper::environment() == C_ENVIRONMENT_PROD,
        ]);
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