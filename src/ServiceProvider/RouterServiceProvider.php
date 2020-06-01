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
 * Routing service
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

        $router = new RouteCollection(fnc()->c()->get('controller_namespace'));

        $routerDispatcher = new RouteDispatcher($router, fnc()->c()->get('middlewares'));

        // Register route
        $container->add('router', $router);

        // Register route distributor
        $container->add('router_dispatcher', $routerDispatcher);

        // Load route
        $router->group(['prefix' => '', 'middleware' => 'dispatch'], function () {
            foreach (glob(fnc()->c()->get('router.path') . '*' . fnc()->c()->get('router.ext')) as $filename) {
                include $filename;
            }
        });
    }
}

