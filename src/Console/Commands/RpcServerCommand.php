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
use ShugaChara\Console\Command;
use ShugaChara\Framework\Contracts\StatusManagerInterface;
use ShugaChara\Framework\Swoole\Server as SwooleServer;
use ShugaChara\Framework\Traits\SwooleCommand;
use ShugaChara\Framework\Swoole\Rpc\Server;
use ShugaChara\Framework\Traits\Swoole;
use ShugaChara\Swoole\SwooleHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use swoole_server;

/**
 * Class RpcServerCommand
 * @package ShugaChara\Framework\Console\Commands
 */
class RpcServerCommand extends Command implements StatusManagerInterface
{
    use Swoole, SwooleCommand;

    /**
     * @var string
     */
    protected static $name = 'server:rpc';

    /**
     * @var string
     */
    protected static $description = '创建一个 RPC 服务器';

    /**
     * 守护程序是否正在运行
     * @var bool
     */
    protected $daemon = false;

    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub

        $this
            ->setName(self::$name)
            ->setDescription(static::$description)
            ->addArgument('status', InputArgument::OPTIONAL, '服务状态')
            ->addOption('daemon', 'd', InputOption::VALUE_NONE, '是否守护进程');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setServer(new Server());
        $this->setServerName(SwooleServer::SWOOLE_SERVER);
        $this->setServerConfigName('rpc');

        $status = strtolower($input->getArgument('status')) ? : static::STATUS_NAME;
        $daemon = $input->hasParameterOption(['--daemon', '-d'], true) ? true : false;
        if (! in_array($status, self::STATUS_TYPES)) {
            throw new Exception($status . ' 服务状态未定义，请通过 --help 检查命令');
        }

        // 设置服务守护进程
        if ($daemon) {
            $this->setDaemonize($daemon);
        }

        $this->$status();

        return 1;
    }

    /**
     * @return mixed|void
     */
    public function status()
    {
        // TODO: Implement status() method.


    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function start()
    {
        // TODO: Implement start() method.

        $this->getServer()->createServer(
            SwooleServer::SWOOLE_SERVER,
            $this->getServerConfig('port'),
            $this->getServerConfig('host', '0.0.0.0'),
            $this->getServerConfig('setting', [])
        );

        // 注册默认回调事件
        $this->getServer()->registerDefaultCallback(
            $this->getServer()->getSwooleServer()
        );

        // 注册当前类回调事件
        $this->getServer()->registerClassEvents($this, $this->getServer());

        // 注册全局 Hook mainSwooleServerEventsCreate 事件
        $this->handleMainSwooleServerEventsCreate();

        // 进程 PID 文件
        $this->createSwooleSettingPidDir();

        // 设置主进程名称
        SwooleHelper::setProcessRename($this->getMasterProcessName());

        // 打印服务信息
        $this->serverInfo();

        // 添加服务
        foreach (fnc()->c()->get('swoole.rpc.services', []) as $service) {
            if ($serviceName = $this->getServer()->addService($service)) {
                $this->getIO()->writeln(sprintf('%s 服务 已注册 - <ft-magenta-bold>%s</ft-magenta-bold> 对外服务能力', $this->getServerConfigName(), $serviceName) . PHP_EOL);
            }
        }

        // 服务启动
        $this->getServer()->start();
    }

    /**
     * @return mixed|void
     */
    public function stop()
    {
        // TODO: Implement stop() method.

        $this->serverStop();
    }

    /**
     * @return mixed|void
     */
    public function reload()
    {
        // TODO: Implement reload() method.

        $this->serverReload();
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
     * 启动后在主进程（master）的主线程中调用此函数
     * @param swoole_server $server
     * @throws Exception
     */
    public function onStart(swoole_server $server)
    {
        $listeners = fnc()->c()->get('swoole.listeners', []);
        foreach ($listeners as $listener) {
            switch ($listener['sock_type']) {
                case SWOOLE_SOCK_UDP:
                    $sockType = 'udp';
                    break;
                case SWOOLE_SOCK_TCP:
                    $sockType = 'tcp';
                    break;
                default:
                    $sockType = 'sock_type:' . $listener['sock_type'];
            }

            $this->getIO()->title('<ft-red-bold>' . sprintf('Listen : %s://%s:%s', $sockType, $listener['host'], $listener['port']) . '</ft-red-bold>');
        }
    }

    /**
     * 当管理进程启动时触发此事件
     * @param swoole_server $server
     * @throws Exception
     */
    public function onManagerStart(swoole_server $server)
    {
        SwooleHelper::setProcessRename($this->getServerConfigName() . '.manager');
        $this->writelnBlock(sprintf('%s 服务管理 PID [%s] 已经启动', $this->getServerConfigName(), $server->manager_pid));
    }

    /**
     * 事件在 Worker 进程 / Task 进程启动时发生，这里创建的对象可以在进程生命周期内使用
     * @param swoole_server $server
     * @param               $workerId
     */
    public function onWorkerStart(swoole_server $server, $workerId)
    {
        $worker_name = $server->taskworker ? 'task' : 'worker';
        $tag = '%s';
        switch ($worker_name) {
            case 'task':
                $tag = '<bf-yellow>%s</bf-yellow>';
                break;
            case 'worker':
                $tag = '<bf-blue>%s</bf-blue>';
                break;
            default:
        }
        SwooleHelper::setProcessRename($this->getServerConfigName() . '.' . $worker_name . '.' . $workerId);
        $this->getIO()->writeln(sprintf('%s 服务 ' . $tag . ' <ft-blue-bold>[%s]</ft-blue-bold> 已经启动 [%s]', $this->getServerConfigName(), ucfirst($worker_name), $server->worker_pid, $workerId));
    }
}
