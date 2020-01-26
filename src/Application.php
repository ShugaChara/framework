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

use Carbon\Carbon;
use Exception;
use ReflectionClass;
use ShugaChara\Container\Container;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Helpers\czHelper;
use ShugaChara\Framework\Traits\Application as ApplicationTraits;

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
     * 应用框架是否启动
     * @var bool
     */
    protected $isRun = false;

    /**
     * APP应用框架启动模式, 目前有php-fpm / swoole , 默认 swoole
     * @var string
     */
    protected $appMode = PHP_SWOOLE_MODE;

    /**
     * App 应用框架名称
     * @var string
     */
    protected $appName = 'czphp';

    /**
     * App 应用框架版本
     * @var string
     */
    protected $appVersion = 'v1.0';

    /**
     * App 应用根目录
     * @var
     */
    protected $appBasePath;

    /**
     * 容器服务
     * @var Container
     */
    protected $container;

    /**
     * Application constructor.
     * @param null   $appBasePath
     * @param string $appMode
     * @throws \ReflectionException
     */
    final public function __construct($appBasePath = null, $appMode = PHP_SWOOLE_MODE)
    {
        // check runtime env
        czHelper::checkRuntime();

        // 设置App应用项目路径
        $this->setAppBasePath(
            $appBasePath ?
                : dirname((new ReflectionClass(self::class))->getFileName(), 2)
        );

        // 初始化项目路径
        $this->initApplicationPath();

        // 设置App启动模式
        $this->setAppMode($appMode);

        // static application
        static::$application = $this;

        // load container
        $this->container = new Container();

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

        // 加载配置服务组件

    }

    /**
     * 初始化 Application
     */
    protected function initialize() {}

    /**
     * 设置App启动模式
     * @param string $appMode
     */
    final private function setAppMode(string $appMode): void
    {
        $appMode = strtolower($appMode);
        if (in_array($appMode, [PHP_FPM_MODE, PHP_SWOOLE_MODE])) {
            $this->appMode = $appMode;
        }
    }

    /**
     * 获取App启动模式
     * @return string
     */
    public function getAppMode(): string
    {
        return $this->appMode;
    }

    /**
     * 设置App应用框架名称
     * @param string $appName
     */
    public function setAppName(string $appName)
    {
        $this->appName = $appName;
        return $this;
    }

    /**
     * 获取App应用框架名称
     * @return mixed|string
     */
    public function getName()
    {
        // TODO: Implement getName() method.

        return $this->appName;
    }

    /**
     * 设置App应用框架版本
     * @param string $appVersion
     */
    public function setAppVersion(string $appVersion)
    {
        $this->appVersion = $appVersion;
        return $this;
    }

    /**
     * 获取App应用框架版本
     * @return mixed|string
     */
    public function getVersion()
    {
        // TODO: Implement getVersion() method.

        return $this->appVersion;
    }

    /**
     * 设置App应用根目录
     * @param mixed $appBasePath
     */
    final private function setAppBasePath($appBasePath): void
    {
        $this->appBasePath = $appBasePath;
    }

    /**
     * 获取App应用根目录
     * @return mixed
     */
    public function getAppBasePath()
    {
        return $this->appBasePath;
    }

    /**
     * 应用框架是否启动
     * @return bool
     */
    public function isRun(): bool
    {
        return $this->isRun;
    }

    /**
     * 获取应用框架本身 static
     * @return Application
     */
    public static function getApplication(): Application
    {
        return self::$application;
    }

    /**
     * 获取容器服务
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * 运行框架
     */
    public function run(): void
    {
        // TODO: Implement run() method.

    }
}

