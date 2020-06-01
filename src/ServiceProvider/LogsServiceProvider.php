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

use Monolog\Handler\RotatingFileHandler;
use ShugaChara\Container\Container;
use ShugaChara\Container\Contracts\ServiceProviderInterface;
use ShugaChara\Framework\Helpers\FHelper;
use ShugaChara\Logs\Logger;

/**
 * Logs service
 * Class LogsServiceProvider
 * @package ShugaChara\Framework\ServiceProvider
 */
class LogsServiceProvider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    private $logs = [];

    /**
     * @param Container $container
     * @return mixed|void
     */
    public function register(Container $container)
    {
        // TODO: Implement register() method.

        $container->add('logs', function () {
            return function ($key, $level = Logger::DEBUG) {
                if (! isset($this->logs[$key])) {
                    $logHandler = new RotatingFileHandler(
                        fnc()->c()->get('logs.path') . $key . fnc()->c()->get('logs.ext'),
                        fnc()->c()->get('logs.maxFiles'),
                        $level
                    );
                    $this->logs[$key] = new Logger($key, [$logHandler]);
                }

                return $this->logs[$key];
            };
        });
    }
}

