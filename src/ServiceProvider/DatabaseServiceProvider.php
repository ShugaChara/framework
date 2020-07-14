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
use ShugaChara\Container\Contracts\ServiceProviderInterface;
use ShugaChara\Framework\Components\Alias;
use ShugaChara\Framework\Pools\DatabasesPool;

/**
 * 数据库服务
 * Class DatabaseServiceProvider
 * @package ShugaChara\Framework\ServiceProvider
 */
class DatabaseServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return mixed|void
     */
    public function register(Container $container)
    {
        // TODO: Implement register() method.

        if ($databases = fnc()->c()->get('databases')) {
            $container->add('databases', new DatabasesPool($databases));

            // php-fpm 模式
            if (! Alias::get('argv')) {
                $container->get('databases')->initPool();
            }
        }
    }
}

