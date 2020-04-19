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

namespace ShugaChara\Framework\Contracts;

/**
 * Interface ApplicationInterface
 * @package ShugaChara\Framework\Contracts
 */
interface ApplicationInterface
{
    /**
     * 获取应用名称
     * @return string
     */
    public function getAppName(): string;

    /**
     * 获取应用版本
     * @return string
     */
    public function getAppVersion(): string;

    /**
     * 运行框架
     * @return mixed
     */
    public function run();
}