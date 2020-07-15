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
use ShugaChara\Framework\Middleware\HttpMiddleware;
use ShugaChara\Router\RouteCollection;
use ShugaChara\Router\RouteDispatcher;

/**
 * 路由服务
 * Class RouterServiceProvider
 * @package ShugaChara\Framework\ServiceProvider
 */
class RouterServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return mixed|void
     */
    public function register(Container $container)
    {
        // TODO: Implement register() method.

        $router = new RouteCollection(conf()->get('controller_namespace'));

        $routerDispatcher = new RouteDispatcher($router, conf()->get('middlewares'));

        // 注册路由
        $container->add('router', $router);

        // 注册路由分发
        $container->add('router_dispatcher', $routerDispatcher);

        // 加载路由
        $router->group(['prefix' => '', 'middleware' => 'dispatch'], function () {
            foreach (glob(conf()->get('router.path') . '*' . conf()->get('router.ext')) as $filename) {
                include $filename;
            }
        });
    }
}

