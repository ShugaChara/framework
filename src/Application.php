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

use ShugaChara\Container\Container;
use ShugaChara\Framework\Components\Alias;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Helpers\FHelper;
use ShugaChara\Framework\Traits\Application as ApplicationTraits;
use function container;

/**
 * Class Application
 * @package ShugaChara\Framework
 */
class Application implements ApplicationInterface
{
    use ApplicationTraits;

    /**
     * 应用框架本身 static
     * @var Application
     */
    public static $application;

    /**
     * 应用运行模式
     * @var string
     */
    protected $appMode = EXECUTE_MODE_SWOOLE;

    /**
     * 应用框架是否运行
     * @var bool
     */
    protected $isExecute = false;

    /**
     * Application constructor.
     */
    final public function __construct()
    {
        // check runtime env
        FHelper::checkRuntime();

        // load static application
        static::$application = $this;

        // load container
        Alias::set('container', new Container());

        container()->add('application', static::$application);
    }

    /**
     * @return mixed|void
     */
    final public function execute()
    {
        // TODO: Implement run() method.
    }
}

