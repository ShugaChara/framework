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

use Exception;
use ShugaChara\Container\Container;
use ShugaChara\Framework\Components\Alias;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Helpers\ByermHelper;
use ShugaChara\Framework\ServiceProvider\ConfigServiceProvider;
use ShugaChara\Framework\Traits\Application as ByermApplication;

/**
 * Class Application
 * @package ShugaChara\Framework
 */
class Application implements ApplicationInterface
{
    use ByermApplication;

    /**
     * 应用框架本身 static
     * @var Application
     */
    public static $application;

    /**
     * 应用框架名称
     * @var string
     */
    protected $appName = 'byerm';

    /**
     * 应用框架版本
     * @var string
     */
    protected $appVersion = '1.0';

    /**
     * 应用框架是否启动
     * @var bool
     */
    protected $isRun = false;

    /**
     * Application constructor.
     */
    final public function __construct()
    {
        // check runtime env
        ByermHelper::checkRuntime();

        // load static application
        static::$application = $this;
        Alias::set('application', static::$application);

        // set application paths
        $this->setPaths();

        // load container
        Alias::set('container', new Container());

        // load initialize
        $this->handleInitialize();
    }

    /**
     * 初始化处理器
     * @throws \ReflectionException
     */
    final protected function handleInitialize()
    {
        // init application
        $this->initialize();

        if (! file_exists($this->getEnvFile())) {
            throw new Exception($this->getEnvFile() . ' 不存在！请先将 .env.example 文件复制为 .env');
        }

        // 加载配置服务
        container()->register(new ConfigServiceProvider());
    }

    /**
     * 初始化 Application
     */
    protected function initialize() {}

    /**
     * @return mixed|void
     */
    public function run()
    {
        // TODO: Implement run() method.
    }
}

