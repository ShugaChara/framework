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

namespace ShugaChara\Framework\Swoole\Rpc;

use ShugaChara\Core\Traits\Singleton;
use ShugaChara\Framework\Contracts\RpcHandleInterface;
use ShugaChara\Framework\Contracts\RpcServerHandleAbstract;

/**
 * Class RpcHandle
 * @method static $this getInstance(...$args)
 * @package ShugaChara\Framework\Swoole\Rpc
 */
class RpcHandle extends RpcServerHandleAbstract implements RpcHandleInterface
{
    use Singleton;

    /**
     * @var DataBean
     */
    protected $bean;

    /**
     * @param DataBean $bean
     * @return mixed|void
     */
    final public function new(DataBean $bean)
    {
        // TODO: Implement new() method.

        $this->bean = $bean;

        $this->handle();
    }

    /**
     * @return DataBean
     */
    public function Bean(): DataBean
    {
        return $this->bean;
    }

    /**
     * @return mixed|void
     */
    public function handle()
    {
        // TODO: Implement handle() method.
    }
}

