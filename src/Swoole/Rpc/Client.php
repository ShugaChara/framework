<?php
// +----------------------------------------------------------------------
// | Created by ShugaChara. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2020 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| shugachara Rpc Client
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework\Swoole\Rpc;

use ShugaChara\Swoole\Rpc\Client as RpcClient;

/**
 * Class Client
 * @package ShugaChara\Framework\Swoole\Rpc
 */
class Client extends RpcClient
{
    /**
     * 服务调用
     * @param       $service
     * @param       $method
     * @param array $params
     * @return mixed
     */
    public function call($service, $method, $params = [])
    {
        $data = [
            Request::SERVICE_NAME_PARAMETER   => $service,
            Request::SERVICE_METHOD_PARAMETER => $method,
            Request::SERVICE_PARAMS_PARAMETER => $params
        ];

        $this->send(json_encode($data));

        $result = $this->recv();

        return json_decode($result, true);
    }
}