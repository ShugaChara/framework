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

use Psr\Http\Message\ServerRequestInterface;
use ShugaChara\Framework\Swoole\Server;
use Throwable;
use Exception;
use ErrorException;
use ShugaChara\Http\Exceptions\HttpException;
use ShugaChara\Container\Container;
use ShugaChara\Framework\Components\Alias;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Exceptions\DebugLogsException;
use ShugaChara\Framework\Exceptions\ResponseException;
use ShugaChara\Framework\Helpers\FHelper;
use ShugaChara\Framework\Http\Response;
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
     * 应用名称
     * @var string
     */
    protected $appName = 'framework';

    /**
     * 应用版本
     * @var string
     */
    protected $appVersion = 'v1.0.0';

    /**
     * 命令行参数
     * @var
     */
    protected $argv = [];

    /**
     * 服务对象
     * @var Server
     */
    protected $server;

    /**
     * 应用框架是否运行
     * @var bool
     */
    protected $isExecute = false;

    /**
     * @return mixed|void
     */
    public function getAppName()
    {
        // TODO: Implement getAppName() method.

        return $this->appName;
    }

    /**
     * @return mixed|void
     */
    public function getAppVersion()
    {
        // TODO: Implement getAppVersion() method.

        return $this->appVersion;
    }

    /**
     * Application constructor.
     * @param $argv
     * @throws Exception
     */
    final public function __construct($argv)
    {
        // check runtime env
        FHelper::checkRuntime();

        // set root directory
        $this->setRootDirectory();
        if ($this->rootDirectory[strlen($this->rootDirectory) - 1] == '/') {
            $this->rootDirectory = rtrim($this->rootDirectory, '/');
        }

        // load static application
        static::$application = $this;

        // set argv
        $this->setArgv($argv);

        // load container
        Alias::set('container', new Container());

        // container add aplication
        container()->add('application', static::$application);

        // register c (Configuration Center)
        container()->register(new ConfigServiceProvider());

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

        // load service providers
        $this->serviceProviderRegister(FHelper::c()->get('service_providers'));

        // set timezone
        date_default_timezone_set(FHelper::c()->get('timezone', 'UTC'));

        // register response
        container()->add('response', new Response());

        // register exception
        $this->registerExceptionHandler();
    }

    /**
     * 设置应用根目录 (设置 $this->rootDirectory)
     * @return mixed
     */
    abstract protected function setRootDirectory();

    /**
     * 框架前置操作
     * @return mixed
     */
    abstract public function initialize();

    /**
     * 注册错误处理
     */
    protected function registerExceptionHandler()
    {
        $level = FHelper::c()->get('error_reporting');
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
        // 异常捕获转换
        if (!$e instanceof Exception) {
            $e = new ErrorException($e);
        }

        // 抛出没有创建request的异常
        if (! container()->has('request')) {
            throw new Exception($e);
        }

        try {
            $trace = DebugLogsException::getReturn($e);
        } catch (Exception $exception) {
            $trace = [
                'original' => explode("\n", $e->getTraceAsString()),
                'handler'  => explode("\n", $exception->getTraceAsString()),
            ];
        }

        FHelper::logs()->error($e->getMessage(), $trace);

        $status = ($e instanceof HttpException) ? $e->getStatusCode() : $e->getCode();

        if (! array_key_exists($status, Response::$statusTexts)) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $resposne = FHelper::response()->api(ResponseException::getReturn($e), $status);
        if (! $this->isExecute()) {
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
            container()->add('request', $request);

            if ((! (($response = FHelper::routerDispatcher()->dispatch($request)) instanceof Response))) {
                return FHelper::response()->json($response);
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
    final public function execute()
    {
        // TODO: Implement run() method.

        $this->isExecute = true;

        FHelper::console()->run();

        exit(1);
    }
}

