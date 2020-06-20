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

use ShugaChara\Framework\Swoole\Rpc\DataBean;

/**
 * Interface RpcHandleInterface
 * @package ShugaChara\Framework\Contracts
 */
interface RpcHandleInterface
{
    /**
     * New DataBean
     * @param DataBean $bean
     * @return mixed
     */
    public function new(DataBean $bean);

    /**
     * Handle DataBean
     * @return mixed
     */
    public function __hook();
}