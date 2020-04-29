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

use ErrorException;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use ShugaChara\Container\Container;
use ShugaChara\Framework\Components\Alias;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Exceptions\DebugLogsException;
use ShugaChara\Framework\Exceptions\ResponseException;
use ShugaChara\Framework\Helpers\ByermHelper;
use ShugaChara\Framework\Http\Request;
use ShugaChara\Framework\Http\Response;
use ShugaChara\Framework\Traits\Application as ByermApplication;
use ShugaChara\Framework\ServiceProvider\ConsoleServiceProvider;
use ShugaChara\Framework\ServiceProvider\LogsServiceProvider;
use ShugaChara\Framework\ServiceProvider\RouterServiceProvider;
use ShugaChara\Framework\ServiceProvider\DatabaseServiceProvider;
use ShugaChara\Framework\ServiceProvider\ValidatorServiceProvider;
use ShugaChara\Framework\ServiceProvider\ConfigServiceProvider;
use ShugaChara\Http\Exceptions\HttpException;
use Throwable;

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
     * 目录路径
     * @var
     */
    protected static $paths = [];

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

        // register default system alias
        $this->defaultSystemAlias();

        // load app service provider
        $serviceProviders = array_diff(ByermHelper::config()->get('service_providers'), $this->defaultServiceProviders);
        if ($serviceProviders) {
            $this->serviceProviderRegister($serviceProviders);
        }

        date_default_timezone_set(ByermHelper::config()->get('APP_TIME_ZONE', 'UTC'));

        // register response
        ByermHelper::container()->add('response', new Response());

        // register exception
        $this->registerExceptionHandler();
    }

    /**
     * 初始化 Application
     */
    protected function initialize() {}

    /**
     * 注册错误处理
     */
    protected function registerExceptionHandler()
    {
        $level = ByermHelper::config()->get('ERROR_REPORTING', E_ALL);
        error_reporting($level);

        set_exception_handler([$this, 'handleException']);

        set_error_handler(function ($level, $message, $file, $line) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }, $level);
    }

    /**
     * 错误处理
     *
     * @param $e
     * @return Response
     * @throws FatalThrowableErro
     */
    public function handleException($e)
    {
        if (!$e instanceof Exception) {
            $e = new ErrorException($e);
        }

        try {
            $trace = DebugLogsException::getReturn($e);
        } catch (Exception $exception) {
            $trace = [
                'original' => explode("\n", $e->getTraceAsString()),
                'handler'  => explode("\n", $exception->getTraceAsString()),
            ];
        }

        ByermHelper::logs()->error($e->getMessage(), $trace);

        $status = ($e instanceof HttpException) ? $e->getStatusCode() : $e->getCode();

        if (! array_key_exists($status, Response::$statusTexts)) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $resposne = ByermHelper::response()->api(ResponseException::getReturn($e), $status);
        if (! $this->isRun()) {
            return $this->handleResponse($resposne);
        }

        return $resposne;
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
            ByermHelper::container()->add('request', $request);

            if ((! (($response = ByermHelper::routerDispatcher()->dispatch($request)) instanceof Response))) {
                return ByermHelper::response()->json($response);
            }

            return $response;

        } catch (Exception $exception) {
            return $this->handleException($exception);
        } catch (Throwable $exception) {
            return $this->handleException(new ErrorException($exception));
        }
    }

    /**
     * 响应处理
     *
     * @param $response
     * @return mixed
     */
    public function handleResponse($response)
    {
        return $response->send();
    }

    /**
     * @return mixed|void
     */
    public function run()
    {
        // TODO: Implement run() method.

        $this->isRun = true;

        $request = Request::createServerRequestFromGlobals();
        $response = $this->handleRequest($request);
        $this->handleResponse($response);

        return ;
    }
}

