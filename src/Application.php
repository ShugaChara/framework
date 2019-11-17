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

use App\Swoole\mainSwooleEvents;
use Exception;
use ErrorException;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ShugaChara\Container\Container;
use ShugaChara\Framework\Constant\Consts;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Contracts\MainSwooleEventsInterface;
use ShugaChara\Framework\Helpers\czHelper;
use ShugaChara\Framework\Processor\ApplicationProcessor;
use ShugaChara\Framework\ServiceProvider\ConfigServiceProvider;
use ShugaChara\Framework\ServiceProvider\ConsoleServiceProvider;
use ShugaChara\Framework\ServiceProvider\DatabaseServiceProvider;
use ShugaChara\Framework\ServiceProvider\LogsServiceProvider;
use ShugaChara\Framework\ServiceProvider\RedisServiceProvider;
use ShugaChara\Framework\ServiceProvider\RouterServiceProvider;
use ShugaChara\Framework\ServiceProvider\ValidatorServiceProvider;
use ShugaChara\Framework\Traits\ApplicationTrait;
use ShugaChara\Http\HttpException;
use ShugaChara\Http\Message\ServerRequest;
use ShugaChara\Http\Response;
use Throwable;

class Application implements ApplicationInterface
{
    use ApplicationTrait;

    /**
     * APP 启动模式
     */
    const APP_MODE = [ Consts::APP_WEB_MODE, Consts::APP_SWOOLE_MODE ];

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
     * 加载组件应用服务
     * @var array
     */
    protected $appComponentServices = [
        ConfigServiceProvider::class,
        LogsServiceProvider::class,
        ConsoleServiceProvider::class,
        RouterServiceProvider::class,
        DatabaseServiceProvider::class,
        RedisServiceProvider::class,
        ValidatorServiceProvider::class,
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
     * 应用模式 目前支持 web | swoole
     * @var string
     */
    protected $appMode = Consts::APP_WEB_MODE;

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
     * Swoole 主服务事件监听类
     * @var string
     */
    protected $mainSwooleEventsObjectName = mainSwooleEvents::class;

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

    final public function __construct()
    {
        // check runtime env
        czHelper::checkRuntime();

        $this->container = new Container();

        static::$app = $this;

        // 载入初始化处理器
        $this->handleInitialize();
    }

    /**
     * 初始化处理器
     * @throws \ReflectionException
     */
    final private function handleInitialize(): void
    {
        if (! defined('IN_PHAR')) define('IN_PHAR', false);

        // init app conf
        $this->initialize();

        $envName = $this->envFile;

        $this->basePath = $this->getBasePath();
        $this->setPathCompletion();

        if (! file_exists($this->getEnvFile())) {
            throw new Exception($this->getEnvFile() . ' 不存在！请先将 ' . $envName . '.example 文件复制为 ' . $envName);
        }

        // load app services provider register
        $this->appServiceProviderRegister($this->appComponentServices);

        $this->setDateTimezone(config()->get('APP_TIME_ZONE', 'UTC'));

        if (! $this->isGeneralMode()) {
            // 加载 Swoole 主服务事件监听对象
            if (class_exists($this->getSwooleEventsObjectName())) {
                try {
                    $ref = new ReflectionClass($this->getSwooleEventsObjectName());
                    if(! $ref->implementsInterface(MainSwooleEventsInterface::class)){
                        die('global file for MainSwooleEventsInterface is not compatible for ' . $this->getSwooleEventsObjectName());
                    }
                    unset($ref);
                } catch (Throwable $throwable){
                    die($throwable->getMessage());
                }
            } else {
                die("global events file missing!\n");
            }

            // init app handle
            $this->getSwooleEventsObjectName()::initialize();
        }

        // register exception
        $this->registerExceptionHandler();
    }

    /**
     * 初始化 Application
     */
    protected function initialize() {}

    /**
     * 运行框架
     */
    final public function run(): void
    {
        // TODO: Implement run() method.

        $this->isRun = true;

        switch ($this->getAppMode()) {
            case Consts::APP_WEB_MODE:
                {
                    $request = ServerRequest::createServerRequestFromGlobals();
                    $response = $this->handleRequest($request);
                    $this->handleResponse($response);
                    break;
                }
            case Consts::APP_SWOOLE_MODE:
                {
                    console()->run();
                    break;
                }
            default:
                die("app does not start!\n");
        }

        return ;
    }

    /**
     * 服务容器注册
     * @param array $services
     */
    final protected function appServiceProviderRegister(array $services)
    {
        foreach ($services as $service) {
            (new $service)->register($this->getContainer());
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
            $this->container->add('response', new Response());
            if (! (($response = routerDispatcher()->dispatch($request)) instanceof Response)) {
                if (! is_array($response)) {
                    $response = (array) $response;
                }
                return response()->json($response);
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

        if ($this->getAppMode() == Consts::APP_WEB_MODE) {
            throw $e;
        }

        $status = ($e instanceof HttpException) ? $e->getStatusCode() : $e->getCode();

        if (! array_key_exists($status, Response::$statusTexts)) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $resposne = (new Response())->json(call_user_func(config()->get('exception.response'), $e), $status);
        if (! $this->isRun()) {
            $this->handleResponse($resposne);
        }

        return $resposne;
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
     * 获取根目录
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
    protected function setPathCompletion()
    {
        $this->envFile = sprintf('%s/%s', $this->getBasePath(), $this->envFile);
        $this->envPath = sprintf('%s/%s', $this->getBasePath(), $this->envPath);
        $this->appPath = sprintf('%s/%s', $this->getBasePath(), $this->appPath);
        $this->configPath = sprintf('%s/%s', $this->getBasePath(), $this->configPath);
        $this->routerPath = sprintf('%s/%s', $this->getBasePath(), $this->routerPath);
        $this->runtimePath = sprintf('%s/%s', $this->getBasePath(), $this->runtimePath);
    }
}

