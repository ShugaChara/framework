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

namespace ShugaChara\Framework\Server;

use ShugaChara\Core\Helpers;
use ShugaChara\Core\Traits\Singleton;
use ShugaChara\Framework\Constant\Consts;
use ShugaChara\Framework\Contracts\SwooleManagerInterface;
use ShugaChara\Framework\Exceptions\SwooleServerException;
use ShugaChara\Framework\Traits\SwooleServerTrait;
use ShugaChara\Http\SwooleServerRequest;
use ShugaChara\Swoole\Server\Http;
use ShugaChara\Swoole\Server\WebSocket;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use swoole_server;
use swoole_http_request;
use swoole_http_response;
use Throwable;

/**
 * Swoole 服务器
 *
 * Class SwooleServer
 * @package ShugaChara\Framework\Server
 */
class SwooleServer implements SwooleManagerInterface
{
    use Singleton, SwooleServerTrait;

    const VERSION = '1.0.0';

    /**
     * Swoole 服务
     * @var
     */
    protected $swooleServer;

    /**
     * 默认 Swoole 回调事件
     * @var array
     */
    protected $defaultServerEventCallback = [
        'onStart', 'onShutdown', 'onRequest'
    ];

    /**
     * Swoole 回调事件 (重写 Swoole 服务类回调方法)
     * @var array
     */
    protected $serverEventCallback = [];

    /**
     * 服务名称
     * @var
     */
    protected $serverName;

    /**
     * Swoole 配置参数
     * @var array
     */
    protected $options = [];

    /**
     * 控制台输出对象
     * @var
     */
    public $consoleOutput;

    /**
     * 服务地址 address
     * @var
     */
    protected $host = '127.0.0.1';

    /**
     * 服务端口 port
     * @var
     */
    protected $port = 9002;

    /**
     * 运行模式 (多进程模式)SWOOLE_PROCESS | (基本模式)SWOOLE_BASE
     * @var
     */
    protected $mode = SWOOLE_PROCESS;

    /**
     * Socket 类型    TCP | UDP | TCP6 | UDP6 | UnixSocket Stream/Dgram
     * @var
     */
    protected $socket_type = SWOOLE_SOCK_TCP;

    /**
     * 服务pid
     * @var
     */
    protected $pid;

    /**
     * 服务pid文件
     * @var
     */
    protected $pid_file;

    public function initAppSwooleServer(
        $serverName = Consts::SWOOLE_SERVER_HTTP,
        array $config = [],
        OutputInterface $output = null
    ) {
        $this->serverName = $serverName;

        $this->consoleOutput = null === $output ? new ConsoleOutput() : $output;

        $this->configure($config);

        return $this;
    }

    /**
     * 初始配置
     * @param array $config
     */
    private function configure(array $config)
    {
        $this->host = Helpers::array_get($config, 'host', $this->getServerHost());
        $this->port = Helpers::array_get($config, 'port', $this->getServerPort());
        $this->options = Helpers::array_get($config, 'options', $this->getServerOptions());
        $this->setPidFile();
    }

    /**
     * 运行状态
     * @return bool
     */
    protected function isRunning()
    {
        if (file_exists($this->getPidFile())) {
            return posix_kill(file_get_contents($this->getPidFile()), 0);
        }

        if ($is_running = process_is_running("{$this->getServerName()} master")) {
            $is_running = port_is_running($this->getServerPort());
        }

        return $is_running;
    }

    /**
     * 创建Swoole服务器
     * @return Http|WebSocket
     */
    public function createAppSwooleServer()
    {
        switch (strtolower($this->getServerName())) {
            case Consts::SWOOLE_SERVER_HTTP:
                {
                    $this->swooleServer = $this->createSwooleHttpServer();
                    break;
                }
            case Consts::SWOOLE_SERVER_WEBSOCKET:
                {
                    $this->swooleServer = $this->createSwooleWebSocketServer();
                    break;
                }
            default:
        }

        // register default swoole events callback
        $this->registerDefaultServerEventCallback();

        return $this;
    }

    /**
     * 创建 Http 服务器
     * @return Http
     */
    protected function createSwooleHttpServer()
    {
        return new Http(
            $this->getServerHost(),
            $this->getServerPort(),
            $this->getServerOptions()
        );
    }

    protected function createSwooleWebSocketServer()
    {
        return;
    }

    /**
     * 注册默认 Swoole 事件回调
     * @return SwooleServer
     */
    private function registerDefaultServerEventCallback()
    {
        return $this->registerServerEventCallback($this->defaultServerEventCallback);
    }

    /**
     * 注册 Swoole 事件回调
     *
     * @param array $callback
     * @return $this
     */
    public function registerServerEventCallback(array $callback)
    {
        $this->serverEventCallback = array_merge($this->serverEventCallback, $callback);
        // 注册到 swoole server
        $this->getAppSwooleServer()->registerServerEventCallback($callback);

        return $this;
    }

    /**
     * 服务状态
     * @return bool
     */
    public function status(): bool
    {
        // TODO: Implement status() method.

        if (! $this->isRunning()) {
            return false;
        }

        $this->getSwooleServerStatusInfo();

        return true;
    }

    /**
     * 获取Swoole服务状态信息
     */
    protected function getSwooleServerStatusInfo()
    {
        exec("ps axu | grep '{$this->getServerName()}' | grep -v grep", $output);

        // list all process
        $output = get_all_process($this->getServerName());

        // combine
        $headers = ['USER', 'PID', 'RSS', 'STAT', 'START', 'COMMAND'];
        foreach ($output as $key => $value) {
            $output[$key] = array_combine($headers, $value);
        }

        $table = new Table($this->consoleOutput);
        $table
            ->setHeaders($headers)
            ->setRows($output)
        ;

        $this->consoleOutput->writeln(sprintf("Server: <info>%s</info>", $this->getAppSwooleServerName()));
        $this->consoleOutput->writeln(sprintf('App version: <info>%s</info>', $this->getServerVersion()));
        $this->consoleOutput->writeln(sprintf('Swoole version: <info>%s</info>', SWOOLE_VERSION));
        $this->consoleOutput->writeln(sprintf("PID file: <info>%s</info>, PID: <info>%s</info>", $this->getPidFile(), (int) @file_get_contents($this->getPidFile())) . PHP_EOL);
        $table->render();

        unset($table, $headers, $output);
    }

    /**
     * 服务启动
     * @return bool
     */
    public function start(): bool
    {
        // TODO: Implement start() method.

        if ($this->isRunning()) {
            $this->consoleOutput->writeln(sprintf('Server <info>[%s] %s:%s</info> address already in use', $this->getAppSwooleServerName(), $this->getServerHost(), $this->getServerPort()));
            return false;
        }

        try {
            if (! $this->getAppSwooleServer()) {
                $this->createAppSwooleServer();
            }

            if (! file_exists($dir = dirname($this->getPidFile()))) {
                mkdir($dir, 0755, true);
            }

            $this->consoleOutput->writeln(sprintf("Server: <info>%s</info> is running...", $this->getAppSwooleServerName()));

            $this->getAppSwooleServer()->start();

        } catch (Throwable $exception) {
            throw new SwooleServerException($exception->getMessage());
        }

        return true;
    }

    /**
     * 服务停止
     * @return bool
     */
    public function stop(): bool
    {
        // TODO: Implement stop() method.

        if (! $this->isRunning()) {
            $this->consoleOutput->writeln(sprintf('Server <info>%s</info> is not running...', $this->getAppServerName()));
            return false;
        }

        $pid = (int) @file_get_contents($this->getPidFile());
        if (process_kill($pid, SIGTERM)) {
            unlink($this->getPidFile());
        }

        $this->consoleOutput->writeln(sprintf('Server <info>%s</info> [<info>#%s</info>] is shutdown...', $this->getAppSwooleServerName(), $pid));
        $this->consoleOutput->writeln(sprintf('PID file %s is unlink', $this->getPidFile()), OutputInterface::VERBOSITY_DEBUG);

        return true;
    }

    /**
     * 服务平滑加载
     * @return bool
     */
    public function reload(): bool
    {
        // TODO: Implement reload() method.

        if (! $this->isRunning()) {
            $this->consoleOutput->writeln(sprintf('Server <info>%s</info> is not running...', $this->getAppSwooleServerName()));
            return false;
        }

        $pid = (int) @file_get_contents($this->getPidFile());
        posix_kill($pid, SIGUSR1);

        $this->consoleOutput->writeln(sprintf('Server <info>%s</info> [<info>%s</info>] is reloading...', $this->getAppSwooleServerName(), $pid));

        return true;
    }

    /**
     * 服务重启
     * @return bool
     */
    public function restart(): bool
    {
        $this->getAppSwooleServer()->shutdown();
        $this->getAppSwooleServer()->start();
        return true;
    }

    /**
     * Start server process
     *
     * @param swoole_server $server
     * @return bool
     */
    public function onStart(swoole_server $server)
    {
        if (version_compare(SWOOLE_VERSION, '1.9.5', '<')) {
            file_put_contents($this->getPidFile(), $server->master_pid);
            $this->pid = $server->master_pid;
        }

        process_rename($this->getServerName() . ' master');

        $this->consoleOutput->writeln(sprintf("Listen: <info>%s://%s:%s</info>", $this->getSwooleSocketTypeName(), $this->getServerHost(), $this->getServerPort()));
        $this->consoleOutput->writeln(sprintf('PID file: <info>%s</info>, PID: <info>%s</info>', $this->getPidFile(), $server->master_pid));
        $this->consoleOutput->writeln(sprintf('Server Master[<info>%s</info>] is started', $server->master_pid), OutputInterface::VERBOSITY_DEBUG);

        return true;
    }

    /**
     * Shutdown server process.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onShutdown(swoole_server $server)
    {
        if (file_exists($this->pid_file)) {
            unlink($this->pid_file);
        }

        $this->consoleOutput->writeln(sprintf('Server <info>%s</info> Master[<info>%s</info>] is shutdown ', $this->serverName, $server->master_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param swoole_http_request  $request
     * @param swoole_http_response $response
     */
    public function onRequest(swoole_http_request $swooleRequest, swoole_http_response $swooleResponse)
    {
        // 转接 Swoole request 对象
        $request = SwooleServerRequest::createServerRequestFromSwoole($swooleRequest);
        $response = app()->handleRequest($request);
        foreach ($response->getHeaders() as $key => $header) {
            $swooleResponse->header($key, $response->getHeaderLine($key));
        }
        foreach ($response->getCookieParams() as $key => $cookieParam) {
            $swooleResponse->cookie(
                $key,
                $cookieParam->getValue(),
                $cookieParam->getExpire(),
                $cookieParam->getPath(),
                $cookieParam->getDomain(),
                $cookieParam->isSecure(),
                $cookieParam->isHttpOnly()
            );
        }

        $swooleResponse->status($response->getStatusCode());
        $swooleResponse->end((string) $response->getBody());
        return;
    }
}

