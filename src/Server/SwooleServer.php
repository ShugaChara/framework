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
use ShugaChara\Framework\Traits\SwooleServerEventTrait;
use ShugaChara\Framework\Traits\SwooleServerTrait;
use ShugaChara\Swoole\Server\Http;
use ShugaChara\Swoole\Server\WebSocket;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Swoole 服务器
 *
 * Class SwooleServer
 * @package ShugaChara\Framework\Server
 */
class SwooleServer implements SwooleManagerInterface
{
    use Singleton, SwooleServerTrait, SwooleServerEventTrait;

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
        'onWorkerStart', 'onStart', 'onShutdown', 'onRequest'
    ];

    /**
     * Swoole 回调事件 (重写 Swoole 服务类回调方法 - 关联数组[事件=>类名])
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
        $this->registerServerEventCallback($this->defaultServerEventCallback);

        return $this;
    }

    /**
     * 创建 Http 服务器
     * @return Http
     */
    protected function createSwooleHttpServer()
    {
        return (new Http())->initSwooleServer(
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
     * 注册 Swoole 事件回调
     *
     * @param array $callback   例如 ['onStart', 'onRequest'] or ['onStart' => 'ShugaChara\Framework\Server\SwooleServer', 'onRequest' => 'ShugaChara\Framework\Console\Commands\HttpServerCommand']
     * @param null  $class      回调事件来源类 比如 static::class
     * @return $this
     */
    public function registerServerEventCallback(array $callback, $class = null)
    {
        if (! $class) {
            $class = get_called_class();
        }

        $cloneCallback = $callback;
        unset($callback);
        foreach ($cloneCallback as $key => $value) {
            if (is_numeric($key)) {
                $callback[$value] = $class;
            } else {
                $callback[$key] = $value;
            }
        }

        $this->serverEventCallback = array_merge($this->serverEventCallback, $callback);

        // 注册到 swoole server 对象
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
            $this->consoleOutput->writeln(sprintf('当前 Swoole 服务 <info>%s</info> 未在运行状态...', $this->getAppSwooleServerName()));
            return false;
        }

        // 获取 Swoole 服务状态信息
        $this->getSwooleServerStatusInfo();

        $this->consoleOutput->writeln(sprintf('当前 Swoole 服务 <info>%s</info> 已在运行状态...', $this->getAppSwooleServerName()));

        return true;
    }

    /**
     * 服务启动
     * @return bool
     */
    public function start(): bool
    {
        // TODO: Implement start() method.

        if ($this->isRunning()) {
            $this->consoleOutput->writeln(sprintf('当前使用的 Swoole 服务 <info>[%s] %s:%s</info> 地址已被使用,请确认服务是否已在运行中', $this->getAppSwooleServerName(), $this->getServerHost(), $this->getServerPort()));
            return false;
        }

        try {
            if (! $this->getAppSwooleServer()) {
                $this->createAppSwooleServer();
            }

            if (! file_exists($dir = dirname($this->getPidFile()))) {
                mkdir($dir, 0755, true);
            }

            $this->consoleOutput->writeln(sprintf("当前 Swoole 服务 <info>%s</info> 正在启动中...", $this->getAppSwooleServerName()));

            // swoole 服务注入
            container()->add('swoole', $this);

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
            $this->consoleOutput->writeln(sprintf('当前 Swoole 服务 <info>%s</info> 未在运行状态...', $this->getAppServerName()));
            return false;
        }

        $pid = (int) @file_get_contents($this->getPidFile());
        if (process_kill($pid, SIGTERM)) {
            unlink($this->getPidFile());
        }

        $this->consoleOutput->writeln(sprintf('当前 Swoole 服务 <info>%s</info> [<info>#%s</info>] 已停止运行...', $this->getAppSwooleServerName(), $pid));
        $this->consoleOutput->writeln(sprintf('PID 进程文件 %s 已被删除', $this->getPidFile()), OutputInterface::VERBOSITY_DEBUG);

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
            $this->consoleOutput->writeln(sprintf('当前 Swoole 服务 <info>%s</info> 未在运行状态...', $this->getAppSwooleServerName()));
            return false;
        }

        $pid = (int) @file_get_contents($this->getPidFile());
        posix_kill($pid, SIGUSR1);

        $this->consoleOutput->writeln(sprintf('当前 Swoole 服务 <info>%s</info> [<info>%s</info>] 已执行平滑加载...', $this->getAppSwooleServerName(), $pid));

        return true;
    }

    /**
     * 服务重启
     * @return bool
     */
    public function restart(): bool
    {
        $this->stop();
        $this->start();
        return false;
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

        $this->consoleOutput->writeln(sprintf("服务名: <info>%s</info>", $this->getAppSwooleServerName()));
        $this->consoleOutput->writeln(sprintf('App 服务应用版本: <info>%s</info>', $this->getServerVersion()));
        $this->consoleOutput->writeln(sprintf('Swoole 版本: <info>%s</info>', SWOOLE_VERSION));
        $this->consoleOutput->writeln(sprintf("PID 进程文件: <info>%s</info>, PID: <info>%s</info>", $this->getPidFile(), (int) @file_get_contents($this->getPidFile())) . PHP_EOL);
        $table->render();

        unset($table, $headers, $output);
    }
}

