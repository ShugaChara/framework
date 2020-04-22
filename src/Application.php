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
use ShugaChara\Framework\Traits\Application as ByermApplication;
use ShugaChara\Framework\ServiceProvider\ConsoleServiceProvider;
use ShugaChara\Framework\ServiceProvider\LogsServiceProvider;
use ShugaChara\Framework\ServiceProvider\RouterServiceProvider;
use ShugaChara\Framework\ServiceProvider\DatabaseServiceProvider;
use ShugaChara\Framework\ServiceProvider\ValidatorServiceProvider;
use ShugaChara\Framework\ServiceProvider\ConfigServiceProvider;

/**
 * Class Application
 * @package ShugaChara\Framework
 */
class Application implements ApplicationInterface
{
    use ByermApplication;

    /**
     * fpm 模式
     */
    const MODE_FPM = 'fpm';

    /**
     * swoole 模式
     */
    const MODE_SWOOLE = 'swoole';

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
     * 应用运行模式 [fpm, swoole]
     * @var string
     */
    protected $appMode = self::MODE_SWOOLE;

    /**
     * 默认服务组件
     * @var array
     */
    protected $defaultServiceProviders = [
        LogsServiceProvider::class,
        ConfigServiceProvider::class,
        ConsoleServiceProvider::class,
        RouterServiceProvider::class,
        DatabaseServiceProvider::class,
        ValidatorServiceProvider::class,
    ];

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

        // default service provider register
        $this->serviceProviderRegister($this->defaultServiceProviders);

        // load app service provider
        $serviceProviders = array_diff(config()->get('service_providers'), $this->defaultServiceProviders);
        if ($serviceProviders) {
            $this->serviceProviderRegister($serviceProviders);
        }

        date_default_timezone_set(config()->get('APP_TIME_ZONE'));
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

