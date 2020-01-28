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

namespace ShugaChara\Framework\ServiceProvider;

use ShugaChara\Container\Container;
use ShugaChara\Container\Contracts\ServiceProviderInterface;
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
    /**
     * @param Container $container
     * @return mixed|void
     */
    public function register(Container $container)
    {
        // TODO: Implement register() method.

        $router = new RouteCollection(config()->get('APP_CONTROLLER_NAMESPACE', APP_DEFAULT_CONTROLLER_NAMESPACE));

        $routerDispatcher = new RouteDispatcher($router, config()->get('SERVICE_MIDDLEWARES', []));

        // 注册路由
        $container->add('router', $router);

        // 注册路由分发器
        $container->add('router_dispatcher', $routerDispatcher);

        /**
         * 加载默认中间件
         */
        $router->group(['prefix' => ''], function () {
            foreach (glob(app()->getRouterPath() . '/*.php') as $filename) {
                include $filename;
            }
        });
    }
}

