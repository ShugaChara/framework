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
| shugachara Response
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework\Http;

use ShugaChara\Core\Utils\Helper\ArrayHelper;
use ShugaChara\Framework\Tools\StatusCode;
use ShugaChara\Http\Response as HttpResponse;

/*
|--------------------------------------------------------------------------
| shugachara 响应类
|--------------------------------------------------------------------------
 */

/**
 * Class Response
 * @package ShugaChara\Framework\Http
 */
class Response extends HttpResponse
{
    /**
     * Api Json 格式响应
     *
     * @param null  $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    public function api($data = null, $status = Response::HTTP_OK, array $headers = [])
    {
        $status = (int) $status;

        $startResponseTime = ArrayHelper::get(request()->getServerParams(), 'REQUEST_TIME_FLOAT', 0) ? : ArrayHelper::get($_SERVER, 'REQUEST_TIME_FLOAT', 0);
        $responseTime = microtime(true) - $startResponseTime;

        $StatusCode = config()->get('apicode') ? : StatusCode::class;
        list($httpCode, $message) = $StatusCode::getInstance()->getCodeMessage($status);

        return $this->json(
            [
                'code'              =>      $status,
                'message'           =>      $message,
                'data'              =>      $data,
                'response_time'     =>      $responseTime
            ],
            $httpCode,
            $headers
        );
    }
}

