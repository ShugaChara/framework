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

/**
 * 配置
 *
 * Class EnvProcessor
 * @package ShugaChara\Framework\Processor
 */
class EnvProcessor extends Processor
{
    public function handle(): bool
    {
        // TODO: Implement handle() method.

        $fileInfo = $this->getFileInfo($this->application->getEnvFile());
        if (! $fileInfo) {
            return true;
        }

        $envFactory = Dotenv::envFactory([
            new EnvConstAdapter,
            new PutenvAdapter,
            new ServerConstAdapter
        ]);

        // 加载.env配置,读取配置方式可以有: $_ENV \ $_SERVER \ getenv() 获取
        Dotenv::create($fileInfo['path'], $fileInfo['name'], $envFactory)->load();

        $fileInfo = $this->getFileInfo(sprintf('%s/%s.%s', $this->application->getEnvPath(), $fileInfo['name'], environment()));
        if (! $fileInfo) {
            return true;
        }

        // 加载具体的.env.n 环境配置
        Dotenv::create($fileInfo['path'], $fileInfo['name'], $envFactory)->load();

        return true;
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

