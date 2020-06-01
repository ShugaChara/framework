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

namespace ShugaChara\Framework\ServiceProvider;

use ShugaChara\Config\FileConfig;
use ShugaChara\Container\Container;
use ShugaChara\Container\Contracts\ServiceProviderInterface;

/**
 * Configuration service
 * Class ConfigServiceProvider
 * @package ShugaChara\Framework\ServiceProvider
 */
class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return bool|mixed
     */
    public function register(Container $container)
    {
        // TODO: Implement register() method.

        // Register the configuration service to the container
        $container->add('c', new FileConfig());

        // Load basic configuration
        foreach (
            [
                dirname(dirname(__DIR__)) . '/c.php'
            ]
            as
            $file
        ) {
            $container->get('c')->loadFile($file);
        }
    }
}

