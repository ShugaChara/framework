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
| shugachara Response 响应类
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework\Http;

use ShugaChara\Core\Utils\Helper\ArrayHelper;
use ShugaChara\Framework\Helpers\ByermHelper;
use ShugaChara\Framework\Tools\CodeApi;
use ShugaChara\Http\Response as HttpResponse;

/**
 * Class Response
 * @package ShugaChara\Framework\Http
 */
class Response extends HttpResponse
{
    /**
     * Api Json格式响应
     *
     * @param null  $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    public function api($data = null, $status = Response::HTTP_OK, array $headers = [])
    {
        $status = (int) $status;

        $startResponseTime = ArrayHelper::get(ByermHelper::request()->getServerParams(), 'REQUEST_TIME_FLOAT', 0) ? : ArrayHelper::get($_SERVER, 'REQUEST_TIME_FLOAT', 0);
        $responseTime = microtime(true) - $startResponseTime;

        $CodeApi = ByermHelper::config()->get('APP_CODE_API') ? : CodeApi::class;
        list($httpCode, $message) = $CodeApi::getInstance()->getCodeMessage($status);

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

