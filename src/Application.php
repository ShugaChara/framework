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
use ShugaChara\Framework\Helpers\FHelper;
use ShugaChara\Framework\ServiceProvider\ConfigServiceProvider;
use ShugaChara\Framework\Traits\Application as ApplicationTraits;
use function container;

/**
 * Class Application
 * @package ShugaChara\Framework
 */
abstract class Application implements ApplicationInterface
{
    use ApplicationTraits;

    /**
     * 应用框架本身 static
     * @var Application
     */
    public static $application;

    /**
     * 应用根目录
     * @var string
     */
    protected $rootDirectory;

    /**
     * .env 配置文件路径
     * @var string
     */
    protected $envFilePath;

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
     * @throws Exception
     */
    final public function __construct()
    {
        // check runtime env
        FHelper::checkRuntime();

        // set root directory
        $this->setRootDirectory();
        if ($this->rootDirectory[strlen($this->rootDirectory) - 1] != '/') {
            $this->rootDirectory .= '/';
        }

        // set c .env file path
        $this->setEnvFilePath();

        // load static application
        static::$application = $this;

        // load container
        Alias::set('container', new Container());

        // container add aplication
        container()->add('application', static::$application);

        // load initialize
        $this->handleInitialize();
    }

    /**
     * 初始化处理器 Application
     */
    final protected function handleInitialize()
    {
        // init application
        $this->initialize();

        if (! $this->getRootDirectory()) {
            throw new Exception('Please configure the application root directory first.');
        }

        // register c (Configuration Center)
        container()->register(new ConfigServiceProvider());
    }

    /**
     * 设置应用根目录 (设置 $this->rootDirectory)
     * @return mixed
     */
    abstract protected function setRootDirectory();

    /**
     * 设置应用.env配置文件 (设置 $this->envFilePath)
     * @return mixed
     */
    abstract protected function setEnvFilePath();

    /**
     * 框架前置操作
     * @return mixed
     */
    abstract public function initialize();

    /**
     * @return mixed|void
     */
    final public function execute()
    {
        // TODO: Implement run() method.
    }
}

