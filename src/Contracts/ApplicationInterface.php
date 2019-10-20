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

namespace ShugaChara\Framework\Contracts;

/**
 * Interface ApplicationInterface
 * @package ShugaChara\Framework\Contracts
 */
interface ApplicationInterface
{
    /**
     * 运行框架
     */
    public function run(): void;
}