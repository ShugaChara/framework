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
use ShugaChara\Framework\Middleware\HttpMiddleware;
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
     * 默认命名空间
     * @var string
     */
    protected $controller_namespace = '\\App\\Http\\Controllers\\';

    public function register(Container $container)
    {
        // TODO: Implement register() method.

        $router = new RouteCollection(config()->get('CONTROLLER_NAMESPACE', $this->controller_namespace));

        $routerDispatcher = new RouteDispatcher($router, array_merge($this->appMiddleware(), config()->get('middlewares', [])));

        // 注册路由
        $container->add('router', $router);

        // 注册路由分发器
        $container->add('routerDispatcher', $routerDispatcher);

        /**
         * 加载默认中间件
         */
        $router->group(['prefix' => '', 'middleware' => 'http_middleware'], function () {
            foreach (glob(app()->getRouterPath() . '/*.php') as $filename) {
                include $filename;
            }
        });
    }

    /**
     * app default server middleware
     * @return array
     */
    private function appMiddleware(): array
    {
        return [
            'http_middleware'   =>  new HttpMiddleware()
        ];
    }
}

