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

use ShugaChara\Console\Command;
use ShugaChara\Framework\Server\SwooleServer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SwooleCommand
 * @package ShugaChara\Framework\Console\Commands
 */
class SwooleCommand extends Command
{
    protected static $name = 'swoole';

    protected function configure()
    {
        parent::configure(); // TODO: Change the autogenerated stub

        $this
            ->setName(self::$name)  // 命令的名称
            ->setDescription('创建一个swoole服务器')  // 简短描述
            ->setHelp('创建一个http服务器:支持 http/websocket')  // 运行命令时使用 "--help" 选项时的完整命令描述
            ->addArgument('server_name', InputArgument::OPTIONAL, '服务名称')  // 配置可选参数
            ->addArgument('handle', InputArgument::OPTIONAL, '服务状态');  // 配置可选参数
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server_name = $input->getArgument('server_name') ?? 'http';
        $handle = $input->getArgument('handle') ?? 'status';

        new SwooleServer($server_name, $handle);
    }
}

