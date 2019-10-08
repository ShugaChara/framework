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

/*
|--------------------------------------------------------------------------
| shugachara 应用服务类
|--------------------------------------------------------------------------
 */

namespace ShugaChara\Framework;

use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Helpers\czHelper;
use ShugaChara\Framework\Traits\ApplicationTrait;

class Application implements ApplicationInterface
{
    use ApplicationTrait;

    /**
     * 项目目录
     * @var string
     */
    protected $basePath = '';

    /**
     * 默认时区
     * @var string
     */
    protected $default_timezone = 'UTC';

    public function __construct()
    {
        // check runtime env
        czHelper::checkRuntime();

        $this->beforeInit();

        $this->init();

        $this->afterInit();
    }

    /**
     * 运行框架
     */
    public function run(): void
    {
        // TODO: Implement run() method.

        if (! $this->beforeRun()) {
            return;
        }
    }

    /**
     * 获取框架版本号
     * @return string
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * 初始化前置操作
     */
    protected function beforeInit(): void
    {
        if (! defined('IN_PHAR')) {
            define('IN_PHAR', false);
        }

        $this->setDateTimezone($this->default_timezone);
    }

    /**
     * 初始化操作
     */
    protected function init()
    {

    }

    /**
     * 初始化后置操作
     */
    protected function afterInit()
    {

    }
}

