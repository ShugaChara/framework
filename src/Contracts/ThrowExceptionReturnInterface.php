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

namespace ShugaChara\Framework\Contracts;

use Exception;

/**
 * Throw exception result set
 * Interface ThrowExceptionReturnInterface
 * @package ShugaChara\Framework\Contracts
 */
interface ThrowExceptionReturnInterface
{
    /**
     * Return the result set that throws an exception
     * @param Exception $exception
     * @return mixed
     */
    public static function getReturn(Exception $exception);
}