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

namespace ShugaChara\Framework\Tools;

use ShugaChara\Core\Traits\Singleton;
use ShugaChara\Core\Utils\Helper\ArrayHelper;
use ShugaChara\Http\Message\Response;

/**
 * Class StatusCode
 * @method static $this getInstance(...$args)
 * @package ShugaChara\Framework\Tools
 */
class StatusCode
{
    use Singleton;

    const UNKNOWN_STATUS_CODE_MSG =  'Unknown status code';

    /**
     * 业务自定义API状态码
     * @var array
     */
    protected static $statusTexts = [];

    /**
     * CodeAPI constructor.
     */
    final public function __construct()
    {
        static::$statusTexts = Response::$statusTexts + static::$statusTexts;
    }

    /**
     * 获取状态码内容
     * @param $status
     */
    public function getCodeMessage($status)
    {
        if (! isset(static::$statusTexts[$status])) {
            return [
                Response::HTTP_OK,
                static::UNKNOWN_STATUS_CODE_MSG
            ];
        }

        if (is_array(static::$statusTexts[$status])) {
            return [
                ArrayHelper::get(static::$statusTexts[$status], 1, Response::HTTP_OK),
                static::$statusTexts[$status][0]
            ];
        }

        return [
            Response::HTTP_OK,
            static::$statusTexts[$status]
        ];
    }
}