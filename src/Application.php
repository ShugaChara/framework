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
use ReflectionClass;
use ShugaChara\Container\Container;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Exceptions\DebugLogsException;
use ShugaChara\Framework\Exceptions\ResponseException;
use ShugaChara\Framework\Helpers\czHelper;
use ShugaChara\Framework\Http\Request;
use ShugaChara\Framework\ServiceProvider\ConfigServiceProvider;
use ShugaChara\Framework\Traits\Application as ApplicationTraits;
use ShugaChara\Http\Exceptions\HttpException;
use ShugaChara\Framework\Http\Response;
use Throwable;

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
        $this->getContainer()->register(new ConfigServiceProvider());
        // 加载其他服务组件
        $ServiceProviders = config()->get('SERVICE_PROVIDERS');
        foreach ($ServiceProviders as $serviceProvider) {
            $this->getContainer()->register(new $serviceProvider());
        }

        // 注册响应
        $this->getContainer()->add('response', new Response());

        // register customize exceptions
        $this->registerExceptionHandler();
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
    public function getAppName()
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
    public function getAppVersion()
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
    final public function run(): void
    {
        // TODO: Implement run() method.

        $this->isRun = true;

        // swoole 启动模式
        if ($this->getAppMode() == PHP_SWOOLE_MODE) {
            console()->run();
            exit;
        }

        // php-fpm 启动模式
        if ($this->getAppMode() == PHP_FPM_MODE) {
            $request = Request::createServerRequestFromGlobals();
            $response = $this->handleRequest($request);
            $this->handleResponse($response);
        }

        return ;
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
            $this->getContainer()->add('request', $request);

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
     *
     * @param $response
     * @return mixed
     */
    public function handleResponse($response)
    {
        return $response->send();
    }

    /**
     * 注册错误处理
     */
    protected function registerExceptionHandler()
    {
        $level = config()->get('ERROR_REPORTING', E_ALL);
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

        logs()->error($e->getMessage(), $trace);

        $status = ($e instanceof HttpException) ? $e->getStatusCode() : $e->getCode();

        if (! array_key_exists($status, Response::$statusTexts)) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $resposne = response()->json(ResponseException::getReturn($e), $status);
        if (! $this->isRun()) {
            return $this->handleResponse($resposne);
        }

        return $resposne;
    }
}

