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

/**
 * Class SwooleServer
 * @package ShugaChara\Framework\Server
 */
class SwooleServer implements SwooleManagerInterface
{
    const VERSION = '1.0.0';

    protected $swoole;

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
     * 创建Swoole服务器
     *
     * @return Http|WebSocket
     */
    public function createSwooleServer()
    {
        switch (strtoupper($this->serverName)) {
            case Consts::SWOOLE_SERVER_HTTP:
                {
                    $this->swoole = new Http($this->host, $this->port, $this->options);
                    break;
                }
            case Consts::SWOOLE_SERVER_WEBSOCKET:
                {
                    $this->swoole = new WebSocket($this->host, $this->port, $this->options);
                    break;
                }
            default:
        }

        return $this->swoole;
    }

    /**
     * 获取swoole服务
     * @return mixed
     */
    protected function swoole()
    {
        return $this->swoole;
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
                $this->pid_file = '/tmp/' . str_replace(' ', '-', $this->serverName) . '.pid';
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

    public function restart(): bool
    {
        $this->swoole->shutdown();
        $this->swoole->start();
        return true;
    }
}

