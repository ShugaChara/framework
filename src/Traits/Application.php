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
     * 获取应用根目录
     * @return mixed
     */
    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }

    /**
     * 获取应用.env配置文件路径
     * @return mixed
     */
    public function getEnvFilePath()
    {
        return $this->envFilePath;
    }

    /**
     * 设置应用运行模式
     * @param $modeType
     * @return $this
     */
    public function setAppMode($modeType)
    {
        $modeType = strtolower($modeType);

        if (in_array($modeType, [EXECUTE_MODE_FPM, EXECUTE_MODE_SWOOLE])) {
            $this->appMode = $modeType;
        }

        return $this;
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
     * 应用是否已运行
     * @return bool
     */
    protected function isExecute(): bool
    {
        return (bool) $this->isExecute;
    }
}