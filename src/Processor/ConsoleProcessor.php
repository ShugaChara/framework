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

namespace ShugaChara\Framework\Processor;

use Dotenv\Environment\Adapter\EnvConstAdapter;
use Dotenv\Environment\Adapter\PutenvAdapter;
use Dotenv\Environment\Adapter\ServerConstAdapter;
use ShugaChara\Config\Repositories\Dotenv;
use ShugaChara\Console\Console;

/**
 * 控制台命令
 *
 * Class ConsoleProcessor
 * @package ShugaChara\Framework\Processor
 */
class ConsoleProcessor extends Processor
{
    public function handle(): bool
    {
        // TODO: Implement handle() method.

        $consoleApplication = new Console();

        if ($commands = config()->get('APP_CONSOLE_COMMANDS')) {
            foreach ($commands as $key => $command) {
                $consoleApplication->add(new $command['name']($key));
            }
        }

        $consoleApplication->run();

        return true;
    }
}

