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
use Throwable;
use ErrorException;
use Psr\Http\Message\ServerRequestInterface;
use ShugaChara\Http\Exceptions\HttpException;
use ShugaChara\Container\Container;
use ShugaChara\Framework\Components\Alias;
use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Exceptions\DebugLogsException;
use ShugaChara\Framework\Exceptions\ResponseException;
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
     * Application root
     * @var string
     */
    protected $rootDirectory;

    /**
     * Whether the application framework is running
     * @var bool
     */
    protected $isExecute = false;

    /**
     * Application constructor.
     * @param $argv
     */
    final public function __construct($argv)
    {
        // check runtime env
        fn()->checkRuntime();

        // set root directory
        $this->setRootDirectory();
        if ($this->rootDirectory[strlen($this->rootDirectory) - 1] == '/') {
            $this->rootDirectory = rtrim($this->rootDirectory, '/');
        }

        // set argv
        Alias::set('argv', $argv);

        // load container
        Alias::set('container', new Container());

        // load static application
        static::$application = $this;

        // container add aplication
        container()->add('application', static::$application);

        // register c (Configuration Center)
        container()->register(new ConfigServiceProvider());

        // load initialize
        $this->handleInitialize();
    }

    /**
     * Initialize the processor Application
     */
    final protected function handleInitialize()
    {
        // init application
        $this->initialize();

        if (! $this->getRootDirectory()) {
            throw new Exception('Please configure the application root directory first.');
        }

        // load service providers
        $this->registerServiceProviders(fn()->c()->get('service_providers'));

        // set timezone
        date_default_timezone_set(fn()->c()->get('timezone', 'PRC'));

        // register response
        container()->add('response', new Response());

        // register exception
        $this->registerExceptionHandler();
    }

    /**
     * Registration error handling
     */
    protected function registerExceptionHandler()
    {
        $level = fn()->c()->get('error_reporting');
        error_reporting($level);

        set_exception_handler([$this, 'handleException']);

        set_error_handler(function ($level, $message, $file, $line) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }, $level);
    }

    /**
     * Error handling
     * @param $e
     * @return Response
     * @throws FatalThrowableErro
     */
    public function handleException($e)
    {
        // Exception catch conversion
        if (!$e instanceof Exception) {
            $e = new ErrorException($e);
        }

        // Throws an exception that does not create a request
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

        fn()->logs()->error($e->getMessage(), $trace);

        $status = ($e instanceof HttpException) ? $e->getStatusCode() : $e->getCode();

        if (! array_key_exists($status, Response::$statusTexts)) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $resposne = fn()->response()->api(ResponseException::getReturn($e), $status);
        if (! $this->isExecute()) {
            return $this->handleResponse($resposne);
        }

        return $resposne;
    }

    /**
     * Request processing
     * @param ServerRequestInterface $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function handleRequest(ServerRequestInterface $request)
    {
        try {
            container()->add('request', $request);

            if ((! (($response = fn()->routerDispatcher()->dispatch($request)) instanceof Response))) {
                return fn()->response()->json($response);
            }

            return $response;

        } catch (Exception $exception) {
            return $this->handleException($exception);
        } catch (Throwable $exception) {
            return $this->handleException(new ErrorException($exception));
        }
    }

    /**
     * Response processing
     * @param $response
     * @return mixed
     */
    public function handleResponse($response)
    {
        return $response->send();
    }

    /**
     * Set application root directory
     * @return mixed
     */
    abstract protected function setRootDirectory();

    /**
     * Frame pre-operation
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

        if (Alias::get('argv')) {
            fn()->console()->run();
        }

        exit(1);
    }
}

