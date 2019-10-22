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

use Exception;
use ErrorException;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ShugaChara\Container\Container;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Helpers\czHelper;
use ShugaChara\Framework\Processor\ApplicationProcessor;
use ShugaChara\Framework\ServiceProvider\ConfigServiceProvider;
use ShugaChara\Framework\ServiceProvider\ConsoleServiceProvider;
use ShugaChara\Framework\ServiceProvider\LogsServiceProvider;
use ShugaChara\Framework\ServiceProvider\RouterServiceProvider;
use ShugaChara\Framework\Traits\ApplicationTrait;
use ShugaChara\Http\HttpException;
use ShugaChara\Http\JsonResponse;
use ShugaChara\Http\Response;
use Throwable;

class Application implements ApplicationInterface
{
    use ApplicationTrait;

    /**
     * @var
     */
    public static $app;

    /**
     * 应用运行状态
     * @var
     */
    protected $isRun = false;

    /**
     * 容器
     * @var Container
     */
    protected $container;

    /**
     * 进程处理器
     * @var
     */
    protected $processor;

    /**
     * Swoole 主进程类对象
     * @var
     */
    protected $mainSwooleEvents;

    /**
     * 前置加载应用服务
     * @var array
     */
    protected $before_services = [
        ConfigServiceProvider::class
    ];

    /**
     * 容器服务
     * @var array
     */
    protected $services = [
        LogsServiceProvider::class,
        ConsoleServiceProvider::class,
        RouterServiceProvider::class,
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
     * Swoole 主事件监听文件名 (类文件名和类名必须保持一致)
     * @var string
     */
    protected $mainSwooleEventsClassName = 'mainSwooleEvents';

    /**
     * Swoole 主事件监听文件
     * @var
     */
    protected $mianSwooleEventsFilePath;

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
     * 路由目录
     * @var string
     */
    protected $routerPath = 'router';

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
    protected $timezone = 'UTC';

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
     * 初始化前置操作
     */
    final protected function beforeInit(): void
    {
        if (! defined('IN_PHAR')) {
            define('IN_PHAR', false);
        }

        $this->setDateTimezone($this->timezone);
        $this->basePath = $this->getBasePath();
        $this->setPathCompletion();

        // 加载 Swoole 主进程监听文件
        if (file_exists($this->getmainSwooleEventsFilePath()) && (! $this->getMainSwooleEvents())) {
            require_once $this->getmainSwooleEventsFilePath();
            $this->mainSwooleEvents = new $this->mainSwooleEventsClassName();
        }

        static::$app = $this;

        // load before services container
        $this->servicesRegister($this->before_services);
    }

    /**
     * 初始化操作
     */
    protected function init()
    {
        $this->registerExceptionHandler();
    }

    /**
     * 初始化后置操作
     */
    protected function afterInit()
    {
        // app services register
        $this->servicesRegister($this->services);
    }

    /**
     * 运行框架
     */
    public function run(): void
    {
        // TODO: Implement run() method.

        $this->isRun = true;

        // Container 注入App应用
        $this->container->add('app', $this);

        // 控制台 shell 命令启动
        console()->run();

        // Web Http 请求响应
        // $request = ServerRequest::createServerRequestFromGlobals();
        // $response = $this->handleRequest($request);
        // $this->handleResponse($response);
    }

    /**
     * 注册错误处理
     */
    protected function registerExceptionHandler()
    {
        $level = config()->get('exception.error_reporting', E_ALL);
        error_reporting($level);

        set_exception_handler([$this, 'handleException']);

        set_error_handler(function ($level, $message, $file, $line) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }, $level);
    }

    /**
     * 服务容器注册
     *
     * @param array $services
     */
    protected function servicesRegister(array $services)
    {
        foreach ($services as $service) {
            (new $service)->register($this->container);
        }
    }

    /**
     * 请求处理
     *
     * @param ServerRequestInterface $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function handleRequest(ServerRequestInterface $request)
    {
        try {
            $this->container->add('request', $request);
            if (! (($response = router_dispatcher()->dispatch($request)) instanceof Response)) {
                if (! is_array($response)) {
                    $response = (array) $response;
                }
                return new JsonResponse($response);
            }

            return $response;

        } catch (Exception $exception) {
            return $this->handleException($exception);
        } catch (Throwable $exception) {
            $exception = new ErrorException($exception);
            return $this->handleException($exception);
        }
    }

    /**
     * 响应处理
     *
     * @param $response
     */
    public function handleResponse($response)
    {
        $response->send();
    }

    /**
     * 错误处理
     *
     * @param $e
     * @return Response
     * @throws FatalThrowableError
     */
    public function handleException($e)
    {
        if (!$e instanceof Exception) {
            $e = new ErrorException($e);
        }

        try {
            $trace = call_user_func(config()->get('exception.log'), $e);
        } catch (Exception $exception) {
            $trace = [
                'original' => explode("\n", $e->getTraceAsString()),
                'handler'  => explode("\n", $exception->getTraceAsString()),
            ];
        }

        logs()->error($e->getMessage(), $trace);

        $status = ($e instanceof HttpException) ? $e->getStatusCode() : $e->getCode();

        if (! array_key_exists($status, Response::$statusTexts)) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $resposne = new JsonResponse(call_user_func(config()->get('exception.response'), $e), $status);
        if (! $this->isRun()) {
            $this->handleResponse($resposne);
        }

        return $resposne;
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
        $ReflectionClass = new ReflectionClass(static::class);
        return dirname($ReflectionClass->getFileName(), $this->basePathLevel);
    }

    /**
     * 目录路径补全
     * @throws \ReflectionException
     */
    private function setPathCompletion()
    {
        $this->envFile = sprintf('%s/%s', $this->getBasePath(), $this->envFile);
        $this->envPath = sprintf('%s/%s', $this->getBasePath(), $this->envPath);
        $this->mainSwooleEventsFilePath = sprintf('%s/%s.php', $this->getBasePath(), $this->mainSwooleEventsClassName);
        $this->appPath = sprintf('%s/%s', $this->getBasePath(), $this->appPath);
        $this->configPath = sprintf('%s/%s', $this->getBasePath(), $this->configPath);
        $this->routerPath = sprintf('%s/%s', $this->getBasePath(), $this->routerPath);
        $this->runtimePath = sprintf('%s/%s', $this->getBasePath(), $this->runtimePath);
    }
}

