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

/**
 * Used for error logging (not as an interface response)
 * Class DebugLogsException
 * @package ShugaChara\Framework\Exceptions
 */
class DebugLogsException implements ThrowExceptionReturnInterface
{
    /**
     * @param Exception $exception
     * @return mixed|void
     */
    public static function getReturn(Exception $exception)
    {
        // TODO: Implement getReturn() method.

        return [
            'message'   =>  $exception->getMessage(),
            'code'      =>  $exception->getCode(),
            'file'      =>  $exception->getFile(),
            'line'      =>  $exception->getLine(),
            'trace'     =>  explode("\n", $exception->getTraceAsString()),
        ];
    }
}