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
 * 配置服务
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

        // 配置服务注册到容器
        $container->add('c', new FileConfig());

        // 加载基本配置
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

