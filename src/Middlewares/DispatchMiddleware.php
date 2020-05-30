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
| shugachara 中间件分发器
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use ShugaChara\Middleware\Contracts\DelegateInterface;

class DispatchMiddleware extends Middleware
{
    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request, DelegateInterface $next)
    {
        // TODO: Implement handle() method.

        $response = $next->process($request);
        return $response;
    }
}
