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
use ShugaChara\Router\RouteCollection;
use ShugaChara\Router\RouteDispatcher;

/**
 * 路由服务
 *
 * Class RouterServiceProvider
 * @package ShugaChara\Framework\ServiceProvider
 */
class RouterServiceProvider implements ServiceProviderInterface
{
    protected $controller_namespace = '\\App\\Http\\Controller\\';

    public function register(Container $container)
    {
        // TODO: Implement register() method.

        $router = new RouteCollection(config()->get('CONTROLLER_NAMESPACE', $this->controller_namespace));

        $router_dispatcher = new RouteDispatcher($router, config()->get('middlewares', []));

        // 注册路由
        $container->add('router', $router);

        // 注册路由分发器
        $container->add('router_dispatcher', $router_dispatcher);

        foreach (glob(app()->getRouterPath() . '/*.php') as $filename) {
            include $filename;
        }
    }
}

