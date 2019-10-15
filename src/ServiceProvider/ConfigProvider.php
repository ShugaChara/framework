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

use ShugaChara\Config\FileConfig;
use ShugaChara\Container\Container;
use ShugaChara\Container\ServiceProviderInterface;

/**
 * 配置服务
 *
 * Class ConfigProvider
 * @package ShugaChara\Framework\ServiceProvider
 */
class ConfigProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // TODO: Implement register() method.

        $container->add('config', new FileConfig());

        $configPath = app()->getConfigPath();

        $config = $container->get('config');

        $priorityLoadFiles = ['app'];
        // 加载基础配置
        foreach ($priorityLoadFiles as $file) {
            $config->loadFile($configPath . '/' . $file . '.php');
        }
        // 设置过滤配置文件
        $config->setFilterFile($priorityLoadFiles);
        // 加载应用主配置
        $config->loadConfig($_ENV);
        // 加载组件配置
        $config->loadPath($configPath);
    }
}

