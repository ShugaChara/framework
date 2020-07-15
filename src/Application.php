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
| shugachara Application Service
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework;

use Exception;
use ShugaChara\Framework\Helpers\FHelper;
use Throwable;
use ErrorException;
use Psr\Http\Message\ServerRequestInterface;
use ShugaChara\Http\Exceptions\HttpException;
use ShugaChara\Container\Container;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Exceptions\DebugLogsException;
use ShugaChara\Framework\Exceptions\ResponseException;
use ShugaChara\Framework\Http\Request;
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
     * application static
     * @var Application
     */
    public static $application;

    /**
     * Application 根目录
     * @var string
     */
    protected $rootDirectory;

    /**
     * 应用程序框架是否正在运行
     * @var bool
     */
    protected $isExecute = false;

    /**
     * Application constructor.
     * @param array $argv
     */
    final public function __construct($argv = [])
    {
        // 检查运行环境
        FHelper::checkRuntime();

        // 设置应用根目录
        $this->setRootDirectory();
        if ($this->rootDirectory[strlen($this->rootDirectory) - 1] == '/') {
            $this->rootDirectory = rtrim($this->rootDirectory, '/');
        }

        // 加载容器
        alias()->set('container', new Container());

        // 设置 argv
        alias()->set('argv', $argv);

        // 加载应用
        static::$application = $this;

        // 容器添加应用服务
        container()->add('app', static::$application);

        // 注册配置中心
        container()->register(new ConfigServiceProvider());

        // 加载初始化
        $this->handleInitialize();
    }

    /**
     * 初始化处理器应用程序
     */
    final protected function handleInitialize()
    {
        // 初始化应用
        $this->initialize();

        if (! $this->getRootDirectory()) {
            throw new Exception('请首先配置应用程序的根目录');
        }

        // 注册服务
        $this->registerServiceProviders(config()->get('service_providers'));

        // 设置时区
        date_default_timezone_set(config()->get('timezone', 'PRC'));

        // 注册响应服务
        container()->add('response', new Response());

        // 注册异常处理
        $this->registerExceptionHandler();
    }

    /**
     * 注册错误处理
     */
    protected function registerExceptionHandler()
    {
        $level = config()->get('error_reporting');
        error_reporting($level);

        set_exception_handler([$this, 'handleException']);

        set_error_handler(function ($level, $message, $file, $line) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }, $level);
    }

    /**
     * 错误处理过程
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

        // 抛出不创建请求的异常
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

        logs()->error($e->getMessage(), $trace);

        $status = ($e instanceof HttpException) ? $e->getStatusCode() : $e->getCode();

        if (! array_key_exists($status, Response::$statusTexts)) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $resposne = response()->api(ResponseException::getReturn($e), $status);
        if (! $this->isExecute()) {
            return $this->handleResponse($resposne);
        }

        return $resposne;
    }

    /**
     * 请求处理
     * @param ServerRequestInterface $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function handleRequest(ServerRequestInterface $request)
    {
        try {
            container()->add('request', $request);

            if ((! (($response = router_dispatcher()->dispatch($request)) instanceof Response))) {
                return response()->json($response);
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
     * @param $response
     * @return mixed
     */
    public function handleResponse($response)
    {
        return $response->send();
    }

    /**
     * 设置应用程序根目录
     * @return mixed
     */
    abstract protected function setRootDirectory();

    /**
     * 框架初始化操作
     * @return mixed
     */
    abstract public function initialize();

    /**
     * @return mixed|void
     */
    public function execute()
    {
        // TODO: Implement execute() method.

        $this->isExecute = true;

        // swoole server
        if (alias()->get('argv')) {
            console()->run();
        }

        // php-fpm
        $request = Request::createServerRequestFromGlobals();
        $response = $this->handleRequest($request);
        $this->handleResponse($response);

        exit(1);
    }
}

