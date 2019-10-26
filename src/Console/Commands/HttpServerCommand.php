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

namespace ShugaChara\Framework\Console\Commands;

use Exception;
use ShugaChara\Framework\Constant\Consts;
use ShugaChara\Framework\Swoole\SwooleServerManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HttpServerCommand
 * @package ShugaChara\Framework\Console\Commands
 */
class HttpServerCommand extends BaseServerCommand
{
    protected static $name = 'http';

    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub

        $this
            ->setName(self::$name)  // 命令的名称
            ->setDescription('创建一个swoole ' . self::$name . ' 服务器')  // 简短描述
            ->setHelp($this->help())  // 运行命令时使用 "--help" 选项时的完整命令描述
            ->addArgument('status', InputArgument::OPTIONAL, '服务状态')
            ->addOption('daemon', 'd', InputOption::VALUE_NONE, '服务守护进程')
            ->addOption('force', '', InputOption::VALUE_NONE, '是否强制终止进程');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->server_name = Consts::SWOOLE_SERVER_HTTP;

        $status = strtolower($input->getArgument('status')) ?? Consts::SWOOLE_SERVER_STATUS_NAME;
        $this->daemon = $input->hasParameterOption(['--daemon', '-d'], true) ? true : false;
        $this->force = $input->hasParameterOption(['--force'], true) ? true : false;
        if (in_array($status, $this->serverStatusType)) {
            $this->swooleServerManager = SwooleServerManager::getInstance();
            if (! $this->config = config()->get('swoole.' . $this->server_name, [])) {
                throw new Exception('请完成swoole配置才能启动服务');
            }
            if ($this->daemon) {
                $this->config['setting']['daemonize'] = $this->daemon;
            }
            $this->$status();
            return 1;
        }

        throw new Exception($status . ' 服务状态未定义,请通过 --help 查看命令');
    }

    public function status()
    {
        // TODO: Implement status() method.

        return $this->handleServerStatus();
    }

    public function start()
    {
        // TODO: Implement start() method.

        return $this->handleServerStart();
    }

    public function stop()
    {
        // TODO: Implement stop() method.

        return $this->handleServerStop();
    }

    public function reload()
    {
        // TODO: Implement reload() method.

        return $this->handleServerReload();
    }

    public function restart()
    {
        // TODO: Implement restart() method.

        return $this->handleServerRestart();
    }

    /**
     * 完整命令描述
     * @return string
     */
    public function help(): string
    {
        return '创建一个http服务器';
    }
}

