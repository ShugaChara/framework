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

        $envFile = $this->application->getEnvFile();
        $envPath = dirname($envFile);
        $envFileName = basename($envFile);

        if (! file_exists($envFile)) {
            return true;
        }

        $envFactory = Dotenv::envFactory([
            new EnvConstAdapter,
            new PutenvAdapter,
            new ServerConstAdapter
        ]);

        // 加载env配置,读取配置方式可以有: $_ENV \ $_SERVER \ getenv() 获取
        Dotenv::create($envPath, $envFileName, $envFactory)->load();

        return true;
    }
}

