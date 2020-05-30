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

namespace ShugaChara\Framework\ServiceProvider;

use ShugaChara\Console\Console;
use ShugaChara\Container\Container;
use ShugaChara\Container\Contracts\ServiceProviderInterface;

/**
 * Console service
 * Class ConsoleServiceProvider
 * @package ShugaChara\Framework\ServiceProvider
 */
class ConsoleServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return mixed|void
     */
    public function register(Container $container)
    {
        // TODO: Implement register() method.

        $consoleApplication = new Console();

        if ($commands = fn()->c()->get('console')) {
            foreach ($commands as $key => $command) {
                $consoleApplication->add(new $command['name']($key));
            }
        }

        $container->add('console', $consoleApplication);
    }
}

