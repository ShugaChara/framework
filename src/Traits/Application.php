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

namespace ShugaChara\Framework\Traits;

use ShugaChara\Core\Utils\Helper\ArrayHelper;
use ShugaChara\Framework\Components\Alias;

/**
 * Trait Application
 * @package ShugaChara\Framework\Traits
 */
trait Application
{
    /**
     * 获取应用根目录
     * @return mixed
     */
    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }

    /**
     * 设置命令行参数
     * @param $argv
     */
    protected function setArgv($argv)
    {
        $this->argv['exec'] = ArrayHelper::get($argv, 0);
        $this->argv = [
            'exec'  =>  ArrayHelper::get($argv, 0),
            'name'  =>  ArrayHelper::get($argv, 1),
        ];

        unset($argv[0], $argv[1]);

        $this->argv['param'] = array_values($argv);

        Alias::set('argv', $this->argv);
    }

    /**
     * 获取命令行参数
     * @return mixed
     */
    public function getArgv()
    {
        return Alias::get('argv');
    }

    /**
     * 服务容器注册
     * @param array $services
     */
    public function serviceProviderRegister(array $services)
    {
        foreach ($services as $service) {
            (new $service)->register(container());
        }
    }

    /**
     * 应用是否已运行
     * @return bool
     */
    protected function isExecute(): bool
    {
        return (bool) $this->isExecute;
    }
}