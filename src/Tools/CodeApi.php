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

use ShugaChara\Core\Helpers;
use ShugaChara\Core\Traits\Singleton;
use ShugaChara\Http\Message\Response;

/**
 * Class CodeApi
 * @method static $this getInstance(...$args)
 * @package ShugaChara\Framework\Tools
 */
class CodeApi
{
    use Singleton;

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
            return [ Response::HTTP_OK, 'Unknown status code' ];
        }

        if (is_array(static::$statusTexts[$status])) {
            return [ Helpers::array_get(static::$statusTexts[$status], 1, Response::HTTP_OK), static::$statusTexts[$status][0] ];
        }

        return [ Response::HTTP_OK, static::$statusTexts[$status] ];
    }
}