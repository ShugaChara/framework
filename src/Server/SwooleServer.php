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
use ShugaChara\Framework\Constant\Consts;
use ShugaChara\Framework\Contracts\SwooleManagerInterface;
use ShugaChara\Swoole\Server\Http;
use ShugaChara\Swoole\Server\WebSocket;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use swoole_server;
use swoole_http_request;
use swoole_http_response;

/**
 * Class SwooleServer
 * @package ShugaChara\Framework\Server
 */
class SwooleServer implements SwooleManagerInterface
{
    const VERSION = '1.0.0';

    /**
     * 实例化的 Http | WebSocket Swoole外层封装服务
     * @var
     */
    protected $swoole;

    /**
     * Swoole 服务
     * @var
     */
    protected $server;

    /**
     * Swoole 回调事件 (重写 ShugaChara\Swoole\Traits 服务类回调方法及Http WebSocket服务回调)
     * @var array
     */
    protected $callback = [
        'onStart', 'onShutdown', 'onRequest'
    ];

    /**
     * 服务名称 http | websocket
     * @var
     */
    protected $serverName;

    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    /**
     * Swoole 参数配置
     * @var array
     */
    protected $options = [];

    /**
     * 控制台输出
     * @var
     */
    protected $consoleOutput;

    /**
     * 服务地址 address
     * @var
     */
    protected $host;

    /**
     * 服务端口 port
     * @var
     */
    protected $port;

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

    public function __construct(
        $serverName = Consts::SWOOLE_SERVER_HTTP,
        array $config = [],
        OutputInterface $output = null
    )
    {
        $this->serverName = $serverName;
        $this->config = $config;

        $this->consoleOutput = null === $output ? new ConsoleOutput() : $output;

        $this->configure();
    }

    private function configure()
    {
        $this->host = Helpers::array_get($this->config, 'host', '127.0.0.1');
        $this->port = Helpers::array_get($this->config, 'port');
        $this->options = Helpers::array_get($this->config, 'options', []);
        $this->setPidFile();
    }

    /**
     * Swoole 运行模式设置
     * @param $mode
     * @return $this
     */
    public function setSwooleMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Swoole 运行模式获取
     * @return mixed
     */
    public function getSwooleMode()
    {
        return $this->mode;
    }

    /**
     * Swoole Socket类型设置
     * @param $socket_type
     * @return $this
     */
    public function setSwooleSocketType($socket_type)
    {
        $this->socket_type = $socket_type;
        return $this;
    }

    /**
     * Swoole Socket类型获取
     * @return mixed
     */
    public function getSwooleSocketType()
    {
        return $this->socket_type;
    }

    /**
     * 获取Swoole Socket类型名称
     * @return string
     */
    public function getSwooleSocketTypeName()
    {
        switch ($this->socket_type) {
            case 1: return Consts::SWOOLE_SERVER_SCHEME_TCP;
            case 2: return Consts::SWOOLE_SERVER_SCHEME_UDP;
            case 3: return Consts::SWOOLE_SERVER_SCHEME_TCP6;
            case 4: return Consts::SWOOLE_SERVER_SCHEME_UDP6;
            case 5: return Consts::SWOOLE_SERVER_SCHEME_UNIX_DGRAM;
            case 6: return Consts::SWOOLE_SERVER_SCHEME_UNIX_STREAM;
        }
    }

    /**
     * 创建Swoole服务器
     *
     * @return Http|WebSocket
     */
    public function createSwooleServer()
    {
        switch (strtoupper($this->serverName)) {
            case Consts::SWOOLE_SERVER_HTTP:
                {
                    $this->swoole = new Http(
                        $this->host,
                        $this->port,
                        $this->options,
                        $this->mode,
                        $this->socket_type
                    );

                    break;
                }
            case Consts::SWOOLE_SERVER_WEBSOCKET:
                {
                    $this->swoole = new WebSocket($this->host, $this->port, $this->options);
                    break;
                }
            default:
        }

        $this->server = $this->swoole->getServer();

        $this->onCallback();

        return $this;
    }

    /**
     * 获取swoole外层服务
     * @return mixed
     */
    protected function swoole()
    {
        return $this->swoole;
    }

    /**
     * Swoole 服务
     * @return mixed
     */
    protected function getServer()
    {
        return $this->server;
    }

    /**
     * Swoole 事件回调
     * @return $this
     */
    protected function onCallback()
    {
        foreach ($this->callback as $event) {
            $this->server->on(lcfirst(substr($event, 2)), [$this, $event]);
        }

        return $this;
    }

    /**
     * 设置PidFile
     * @param string $pidFile
     * @return bool
     */
    public function setPidFile(string $pidFile = '')
    {
        if ($pidFile) {
            $this->pid_file = $pidFile;
        } else {
            if (isset($this->options['pid_file'])) {
                $this->pid_file = $this->options['pid_file'];
            }
            if (empty($this->pid_file)) {
                $this->options['pid_file'] = $this->pid_file = '/tmp/' . str_replace(' ', '-', $this->serverName) . '.pid';
            }
        }

        return true;
    }

    /**
     * 获取PidFile
     * @return mixed
     */
    public function getPidFile()
    {
        return $this->pid_file;
    }

    /**
     * 获取APP服务项目名称
     * @return string
     */
    public function getAppServerName()
    {
        return config()->get('APP_NAME') . '.' . $this->serverName;
    }

    /**
     * 运行状态
     * @return bool
     */
    protected function isRunning()
    {
        if (file_exists($this->pid_file)) {
            return posix_kill(file_get_contents($this->pid_file), 0);
        }

        if ($is_running = process_is_running("{$this->serverName} master")) {
            $is_running = port_is_running($this->port);
        }

        return $is_running;
    }

    /**
     * 服务状态
     * @return bool
     */
    public function status(): bool
    {
        // TODO: Implement status() method.

        if (! $this->isRunning()) {
            $this->consoleOutput->writeln(sprintf('Server <info>%s</info> is not running...', $this->getAppServerName()));
            return false;
        }

        exec("ps axu | grep '{$this->serverName}' | grep -v grep", $output);

        // list all process
        $output = get_all_process($this->serverName);

        // combine
        $headers = ['USER', 'PID', 'RSS', 'STAT', 'START', 'COMMAND'];
        foreach ($output as $key => $value) {
            $output[$key] = array_combine($headers, $value);
        }

        $table = new Table($this->consoleOutput);
        $table
            ->setHeaders($headers)
            ->setRows($output);

        $this->consoleOutput->writeln(sprintf("Server: <info>%s</info>", $this->getAppServerName()));
        $this->consoleOutput->writeln(sprintf('App version: <info>%s</info>', SwooleServer::VERSION));
        $this->consoleOutput->writeln(sprintf('Swoole version: <info>%s</info>', SWOOLE_VERSION));
        $this->consoleOutput->writeln(sprintf("PID file: <info>%s</info>, PID: <info>%s</info>", $this->pid_file, (int) @file_get_contents($this->pid_file)) . PHP_EOL);
        $table->render();

        unset($table, $headers, $output);

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
            $this->consoleOutput->writeln(sprintf('Server <info>[%s] %s:%s</info> address already in use', $this->getAppServerName(), $this->host, $this->port));
        } else {
            try {
                if (! $this->swoole) {
                    $this->createSwooleServer();
                }

                if (! file_exists($dir = dirname($this->pid_file))) {
                    mkdir($dir, 0755, true);
                }

                $this->consoleOutput->writeln(sprintf("Server: <info>%s</info> is running...", $this->getAppServerName()));

                $this->swoole->start();

            } catch (\Exception $exception) {
                $this->consoleOutput->write("<error>{$exception->getMessage()}</error>\n");
            }
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
            unlink($this->pid_file);
        }

        $this->consoleOutput->writeln(sprintf('Server <info>%s</info> [<info>#%s</info>] is shutdown...', $this->getAppServerName(), $pid));
        $this->consoleOutput->writeln(sprintf('PID file %s is unlink', $this->pid_file), OutputInterface::VERBOSITY_DEBUG);

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
            $this->consoleOutput->writeln(sprintf('Server <info>%s</info> is not running...', $this->getAppServerName()));
            return false;
        }

        $pid = (int) @file_get_contents($this->getPidFile());
        posix_kill($pid, SIGUSR1);

        $this->consoleOutput->writeln(sprintf('Server <info>%s</info> [<info>%s</info>] is reloading...', $this->getAppServerName(), $pid));

        return true;
    }

    /**
     * 服务重启
     * @return bool
     */
    public function restart(): bool
    {
        $this->swoole->shutdown();
        $this->swoole->start();
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
            file_put_contents($this->pid_file, $server->master_pid);
            $this->pid = $server->master_pid;
        }

        process_rename($this->serverName . ' master');

        $this->consoleOutput->writeln(sprintf("Listen: <info>%s://%s:%s</info>", $this->getSwooleSocketTypeName(), $this->host, $this->port));
        $this->consoleOutput->writeln(sprintf('PID file: <info>%s</info>, PID: <info>%s</info>', $this->pid_file, $server->master_pid));
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

    public function onRequest(swoole_http_request $request, swoole_http_response $response)
    {

    }
}

