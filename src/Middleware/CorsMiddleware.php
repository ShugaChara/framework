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

/*
|--------------------------------------------------------------------------
| shugachara 默认中间件分发器
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use ShugaChara\Middleware\Contracts\DelegateInterface;
use ShugaChara\Middleware\Middleware;

/**
 * Class CorsMiddleware
 * @package ShugaChara\Framework\Middleware
 */
class CorsMiddleware extends Middleware
{
    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request, DelegateInterface $next)
    {
        // TODO: Implement handle() method.

        return $next->process($request);
    }
}
