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

use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Helpers\czHelper;
use ShugaChara\Framework\Processor\ApplicationProcessor;
use ShugaChara\Framework\Processor\EnvProcessor;
use ShugaChara\Framework\Traits\ApplicationTrait;

class Application implements ApplicationInterface
{
    use ApplicationTrait;

    /**
     * @var ApplicationProcessor
     */
    private $processor;

    /**
     * 初始 Processor 类
     * @var array
     */
    private $processorsClassName = [
        EnvProcessor::class
    ];

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
     * app目录
     * @var string
     */
    protected $appPath = 'app';

    /**
     * 配置文件
     * @var string
     */
    protected $configPath = 'config';

    /**
     * 缓存文件
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
    }

    /**
     * 获取框架版本号
     * @return string
     */
    public static function getVersion(): string
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

        $processors = $this->processors();
        $this->processor = new ApplicationProcessor($this);
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

    protected function processors(): array
    {
        $processors = [];

        if ($this->processorsClassName) {
            foreach ($this->processorsClassName as $processor) {
                $processors[] = new $processor($this);
            }
        }

        return $processors;
    }

    /**
     * 目录路径补全
     * @throws \ReflectionException
     */
    private function setPathCompletion()
    {
        $this->envFile = sprintf('%s/%s', $this->getBasePath(), $this->envFile);
        $this->appPath = sprintf('%s/%s', $this->getBasePath(), $this->appPath);
        $this->configPath = sprintf('%s/%s', $this->getBasePath(), $this->configPath);
        $this->runtimePath = sprintf('%s/%s', $this->getBasePath(), $this->runtimePath);
    }
}

