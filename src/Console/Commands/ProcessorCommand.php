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

namespace ShugaChara\Framework\Console\Commands;

use Exception;
use RuntimeException;
use ShugaChara\Console\Command;
use ShugaChara\Framework\Contracts\StatusManagerInterface;
use ShugaChara\Framework\Swoole\Server;
use ShugaChara\Swoole\Contracts\ProcessorAbstract;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use swoole_process;

/**
 * Class ProcessorCommand
 * @package ShugaChara\Framework\Console\Commands
 */
class ProcessorCommand extends Command implements StatusManagerInterface
{
    /**
     * @var string
     */
    protected static $name = 'processor';

    /**
     * @var Server
     */
    protected $server;

    /**
     * 字符串替换占位符
     * @var string
     */
    private $placeholder = '#placeholder#';

    /**
     * 获取所有注册进程
     * @var array
     */
    protected $processes = [];

    /**
     * 当前进程
     * @var ProcessorAbstract
     */
    protected $process;

    /**
     * 进程名称
     * @var
     */
    protected $process_name;

    /**
     * 进程PID目录
     * @var
     */
    protected $pid_path;

    /**
     * 进程PID文件
     * @var
     */
    protected $pid_file;

    /**
     * 守护程序是否正在运行
     * @var bool
     */
    protected $daemon = false;

    /**
     * ProcessorCommand constructor.
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub

        $this
            ->setName(self::$name)
            ->setDescription('Swoole 进程管理器')
            ->addArgument('process_name', InputArgument::OPTIONAL, '进程名称')
            ->addArgument('status', InputArgument::OPTIONAL, '进程服务状态')
            ->addOption('daemon', '-d', InputOption::VALUE_NONE, '守护进程')
            ->addOption('list', '-l', InputOption::VALUE_NONE, '显示进程列表');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->server = new Server();

        $this->process_name = strtolower($input->getArgument('process_name'));
        $status = strtolower($input->getArgument('status')) ? : self::STATUS_NAME;
        $this->daemon = $input->hasParameterOption(['--daemon', '-d'], true) ? true : false;
        if (in_array($status, self::STATUS_TYPES)) {
            $this->pid_path = $this->server->getProcessPidPath();
            $this->pid_file = $this->server->getProcessPidFile($this->process_name ? : $this->placeholder);

            if ($input->hasParameterOption(['--list', '-l']) || empty($this->process_name)) {
                $this->showProcesses($input, $output);
                return 1;
            }

            $this->processes = config()->get('swoole.processor.fpm_list', []);
            if (! isset($this->processes[$this->process_name])) {
                throw new RuntimeException(sprintf('%s 进程不存在', $this->process_name));
            }
            $processClassName = $this->processes[$this->process_name];
            if (! class_exists($processClassName)) {
                throw new RuntimeException(sprintf('%s 进程类不存在', $this->process_name));
            }

            $this->process = new $processClassName($this->process_name);
            if (! ($this->process instanceof ProcessorAbstract)) {
                throw new RuntimeException('进程必须是 \ShugaChara\Swoole\Manager\Processor 的实例');
            }
            if ($input->hasParameterOption(['--daemon', '-d'])) {
                $this->process->getProcessor()->daemon();
            }

            $this->$status();

            return 1;
        }

        throw new Exception($status . ' 服务状态未定义，请通过 --help 检查命令');
    }

    /**
     * @return mixed|void
     */
    public function status()
    {
        // TODO: Implement status() method.

        $info = $this->getProcessInfo($this->process_name);

        $this->getTable()->setHeaders([
            'NAME', 'CLASS', 'STATUS', 'PID'
        ]);

        $rows[] = [
            $this->process_name,
            get_class($this->process),
            $info[2],
            $info[1]
        ];

        $this->getTable()->addRows($rows)->render();
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function start()
    {
        // TODO: Implement start() method.

        $pid = $this->process->getProcessor()->start();
        file_put_contents($this->pid_file, $pid);
        $this->writelnBlock(sprintf('[SUCCESS] 进程 %s PID: %s', $this->process_name, $pid));
        $this->writelnBlock(sprintf('[SUCCESS] PID: %s', $this->pid_file));
        $this->process->getProcessor()->wait(true, function ($ret) {
            $this->writelnBlock(sprintf('进程: %s. PID: %s 已退出. code: %s. signal: %s', $this->process_name, $ret['pid'], $ret['code'], $ret['signal']));
        });
    }

    /**
     * @return mixed|void
     */
    public function stop()
    {
        // TODO: Implement stop() method.

        $pid = (int) file_get_contents($this->pid_file);
        if ($this->process->getProcessor()->kill($pid, SIGTERM)) {
            $this->writelnBlock(sprintf('进程 %s PID %s 已停止', $this->process_name, $pid));
        }
    }

    /**
     * @return mixed|void
     */
    public function reload()
    {
        // TODO: Implement reload() method.

        $this->getIO()->writeln('<ft-red-bold>还没有该功能</ft-red-bold>');
    }

    /**
     * @return mixed|void
     */
    public function restart()
    {
        // TODO: Implement restart() method.

        $this->stop();

        $this->start();
    }

    /**
     * 显示进程列表
     */
    protected function showProcesses()
    {
        $rows = [];

        $this->getTable()->setHeaders(
            [
                'PROCESS', 'PID', 'STATUS', 'START AT', 'RUNTIME'
            ]
        );

        foreach (config()->get('swoole.processor.fpm_list', []) as $name => $processor) {
            $rows[] = $this->getProcessInfo($name);
            $rows[] = $this->getTableSeparator();
        }

        array_pop($rows);

        $this->getTable()->addRows($rows)->render();
    }

    /**
     * 获取进程详情
     * @param $process_name
     * @return array
     */
    protected function getProcessInfo($process_name)
    {
        $isRunning = false;
        $pid_file = str_replace($this->placeholder, $process_name, $this->pid_file);
        $pid = file_exists($pid_file) ? (int) file_get_contents($pid_file) : '';
        if (is_numeric($pid)) {
            $isRunning = swoole_process::kill($pid, 0);
        }

        return [
            $process_name,
            $isRunning ? $pid : '',
            $isRunning ? '运行中' : '已停止',
            $isRunning ? date('Y-m-d H:i:s', filemtime($pid_file)) : '',
            $isRunning ? time() - filemtime($pid_file) : '',
        ];
    }
}

