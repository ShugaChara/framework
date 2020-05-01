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

/**
 * Interface ApplicationInterface
 * @package ShugaChara\Framework\Contracts
 */
interface ApplicationInterface
{
    /**
     * get app name
     * @return mixed
     */
    public function getAppName();

    /**
     * get app version
     * @return mixed
     */
    public function getAppVersion();

    /**
     * run app
     * @return mixed
     */
    public function execute();
}