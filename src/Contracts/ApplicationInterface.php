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
     * 获取App应用框架名称
     * @return mixed
     */
    public function getName();

    /**
     * 获取App应用框架版本
     * @return mixed
     */
    public function getVersion();

    /**
     * 运行框架
     */
    public function run(): void;
}