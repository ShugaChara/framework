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

namespace ShugaChara\Framework\Exceptions;

use Exception;
use ShugaChara\Framework\Contracts\ThrowExceptionReturnInterface;
use ShugaChara\Framework\Helpers\FHelper;

/**
 * 用于接口response响应错误
 * Class ResponseException
 * @package ShugaChara\Framework\Exceptions
 */
class ResponseException implements ThrowExceptionReturnInterface
{
    /**
     * @param Exception $exception
     * @return mixed|void
     */
    public static function getReturn(Exception $exception)
    {
        // TODO: Implement getReturn() method.

        return (FHelper::c()->get('is_debug') == 'true') ?
            DebugLogsException::getReturn($exception)
            : null;
    }
}