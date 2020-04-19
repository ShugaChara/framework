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
| shugachara 应用服务类
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework;

use ShugaChara\Framework\Components\Alias;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Helpers\ByermHelper;
use ShugaChara\Framework\Traits\Application as ByermApplication;

/**
 * Class Application
 * @package ShugaChara\Framework
 */
class Application implements ApplicationInterface
{
    use ByermApplication;

    /**
     * Application constructor.
     */
    final public function __construct()
    {
        // check runtime env
        ByermHelper::checkRuntime();

        // 初始化项目路径
        $this->setPaths();

    }

    /**
     * @return mixed|void
     */
    public function run()
    {
        // TODO: Implement run() method.
    }
}

