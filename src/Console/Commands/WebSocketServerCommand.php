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

use ShugaChara\Framework\Contracts\BaseServerCommandAbstract;
use ShugaChara\Framework\Swoole\Server;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WebSocketServerCommand
 * @package ShugaChara\Framework\Console\Commands
 */
class WebSocketServerCommand extends BaseServerCommandAbstract
{
    /**
     * @var string
     */
    protected static $name = 'server:websocket';

    /**
     * @var string
     */
    protected static $description = '创建一个 Swoole WebSocket 服务';

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initServer(Server::SWOOLE_WEBSOCKET_SERVER, $input);

        exit(0);
    }
}

