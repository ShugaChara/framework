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

namespace ShugaChara\Framework\ServiceProvider;

use ShugaChara\Container\Container;
use ShugaChara\Container\ServiceProviderInterface;
use ShugaChara\Framework\Pools\RedisPool;

/**
 * Redis服务
 *
 * Class RedisServiceProvider
 * @package ShugaChara\Framework\ServiceProvider
 */
class RedisServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // TODO: Implement register() method.

        $redis = config()->get('redis');
        if ($redis) {
            $container->add('redis', new RedisPool($redis));
        }

    }
}

