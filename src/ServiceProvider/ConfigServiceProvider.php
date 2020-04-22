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

use Dotenv\Environment\Adapter\EnvConstAdapter;
use Dotenv\Environment\Adapter\PutenvAdapter;
use Dotenv\Environment\Adapter\ServerConstAdapter;
use ShugaChara\Config\FileConfig;
use ShugaChara\Config\Repositories\Dotenv;
use ShugaChara\Container\Container;
use ShugaChara\Container\Contracts\ServiceProviderInterface;

/**
 * 配置服务
 *
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

        $fileInfo = $this->getFileInfo(app()->getEnvFile());
        if (! $fileInfo) {
            logs()->error('[system] Application configuration file does not exist :' . $fileInfo);
            return true;
        }

        $envFactory = Dotenv::envFactory([
            new EnvConstAdapter,
            new PutenvAdapter,
            new ServerConstAdapter
        ]);

        // 加载.env配置,读取配置方式可以有: $_ENV \ $_SERVER \ getenv() 获取
        Dotenv::create($fileInfo['path'], $fileInfo['name'], $envFactory)->load();

        $fileInfo = $this->getFileInfo(sprintf('%s/%s.%s', app()->getEnvPath(), $fileInfo['name'], environment()));
        if (! $fileInfo) {
            logs()->error('[environment] Application configuration file does not exist :' . $fileInfo);
            return true;
        }

        // 加载具体的.env.n 环境配置
        Dotenv::create($fileInfo['path'], $fileInfo['name'], $envFactory)->load();

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

    protected function getFileInfo($file_name)
    {
        if (! file_exists($file_name)) {
            return null;
        }
        return [
            'path'      =>      dirname($file_name),
            'name'      =>      basename($file_name),
        ];
    }
}

