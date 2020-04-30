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

use Exception;
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

        $fileInfo = $this->getFileInfo(app()->getEnvFilePath());
        if (is_null($fileInfo)) {
            throw new Exception('[system] Application configuration file does not exist :' . $fileInfo);
        }

        $envFactory = Dotenv::envFactory([
            new EnvConstAdapter,
            new PutenvAdapter,
            new ServerConstAdapter
        ]);

        // 加载.env配置,读取配置方式可以有: $_ENV \ $_SERVER \ getenv() 获取
        Dotenv::create($fileInfo['path'], $fileInfo['name'], $envFactory)->load();

        // 将配置服务注册到容器
        $container->add('c', new FileConfig());

        // 加载基础配置
        foreach (
            [
                dirname(dirname(__DIR__)) . '/c.php'
            ]
            as
            $file
        ) {
            $container->get('c')->loadFile($file);
        }

        // 加载应用主配置
        $container->get('c')->loadConfig($_ENV);
    }

    /**
     * 获取文件基础信息
     * @param $file_name
     * @return array|null
     */
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

