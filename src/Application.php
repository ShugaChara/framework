<?php
// +----------------------------------------------------------------------
// | Created by linshan. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2019 All rights reserved.
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
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Helpers\czHelper;
use ShugaChara\Framework\Processor\ApplicationProcessor;
use ShugaChara\Framework\Processor\EnvProcessor;
use ShugaChara\Framework\ServiceProvider\ConfigProvider;
use ShugaChara\Framework\ServiceProvider\LogsProvider;
use ShugaChara\Framework\Traits\ApplicationTrait;

class Application implements ApplicationInterface
{
    use ApplicationTrait;

    /**
     * @var
     */
    public static $app;

    /**
     * 容器
     * @var Container
     */
    protected $container;

    /**
     * 容器默认服务
     * @var
     */
    protected $defaultServices = [
        LogsProvider::class,
        ConfigProvider::class
    ];

    /**
     * @var ApplicationProcessor
     */
    private $processor;

    /**
     * 初始 Processor 类
     * @var array
     */
    protected $defaultProcessors = [
        EnvProcessor::class
    ];

    /**
     * Processor 类
     * @var array
     */
    protected $processors = [

    ];

    /**
     * 应用名称
     * @var string
     */
    protected $appName = 'czphp';

    /**
     * 应用版本号
     * @var string
     */
    protected $appVersion = '1.0.0';

    /**
     * 项目根目录
     * @var string
     */
    protected $basePath = '';

    /**
     * 系统核心配置文件
     * @var string
     */
    protected $envFile = '.env';

    /**
     * 系统核心配置目录
     * @var string
     */
    protected $envPath = 'env';

    /**
     * app目录
     * @var string
     */
    protected $appPath = 'app';

    /**
     * 配置目录
     * @var string
     */
    protected $configPath = 'config';

    /**
     * 缓存目录
     * @var string
     */
    protected $runtimePath = 'runtime';

    /**
     * 项目根目录层级
     * @var int
     */
    protected $basePathLevel = 2;

    /**
     * 默认时区
     * @var string
     */
    protected $default_timezone = 'UTC';

    public function __construct()
    {
        // check runtime env
        czHelper::checkRuntime();

        $this->container = new Container();

        $this->processor = new ApplicationProcessor($this);

        $this->beforeInit();

        $this->init();

        $this->afterInit();
    }

    /**
     * 运行框架
     */
    public function run(): void
    {
        // TODO: Implement run() method.

        if (! $this->beforeRun()) {
            return;
        }

        $this->processor->handle();

        if ($appTimeZone = getenv('APP_TIME_ZONE')) {
            $this->setDateTimezone($appTimeZone);
        }
    }

    /**
     * 获取框架版本号
     * @return string
     */
    public static function getFrameworkVersion(): string
    {
        return self::VERSION;
    }

    /**
     * 初始化前置操作
     */
    protected function beforeInit(): void
    {
        if (! defined('IN_PHAR')) {
            define('IN_PHAR', false);
        }

        $this->setDateTimezone($this->default_timezone);
    }

    /**
     * 初始化操作
     */
    protected function init()
    {
        $this->basePath = $this->getBasePath();
        $this->setPathCompletion();

        static::$app = $this;

        // 初始 Processor 配置类
        $defaultProcessors = $this->processors($this->defaultProcessors);
        $this->processor->addFirstProcessor(...$defaultProcessors);
        $this->processor->handle();
        $this->processor->addDisabledProcessors($defaultProcessors);

        // Ioc容器服务注册
        $this->servicesRegister();

        // Processor 服务进程
        $processors = $this->processors($this->processors);
        $this->processor->addFirstProcessor(...$processors);
    }

    /**
     * 初始化后置操作
     */
    protected function afterInit()
    {

    }

    /**
     * 获取根目录
     *
     * @return string|void
     * @throws \ReflectionException
     */
    public function getBasePath()
    {
        if ($this->basePath) {
            return $this->basePath;
        }

        // 获取当前类所在的位置

        $ReflectionClass = new \ReflectionClass(static::class);

        return dirname($ReflectionClass->getFileName(), $this->basePathLevel);
    }

    protected function processors(array $processors): array
    {
        $processorsServer = [];

        if ($processors) {
            foreach ($processors as $processor) {
                $processorsServer[] = new $processor($this);
            }
        }

        return $processorsServer;
    }

    /**
     * 服务容器注册
     */
    protected function servicesRegister()
    {
        foreach ($this->defaultServices as $service) {
            (new $service)->register($this->container);
        }
    }

    /**
     * 目录路径补全
     * @throws \ReflectionException
     */
    private function setPathCompletion()
    {
        $this->envFile = sprintf('%s/%s', $this->getBasePath(), $this->envFile);
        $this->envPath = sprintf('%s/%s', $this->getBasePath(), $this->envPath);
        $this->appPath = sprintf('%s/%s', $this->getBasePath(), $this->appPath);
        $this->configPath = sprintf('%s/%s', $this->getBasePath(), $this->configPath);
        $this->runtimePath = sprintf('%s/%s', $this->getBasePath(), $this->runtimePath);
    }
}

