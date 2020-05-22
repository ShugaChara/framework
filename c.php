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

use ShugaChara\Framework\Tools\StatusCode;
use ShugaChara\Framework\Swoole\MainSwooleEvents;
use ShugaChara\Framework\Middlewares\DispatchMiddleware;
use ShugaChara\Framework\ServiceProvider\LogsServiceProvider;
use ShugaChara\Framework\ServiceProvider\ConsoleServiceProvider;
use ShugaChara\Framework\ServiceProvider\CacheServiceProvider;
use ShugaChara\Framework\ServiceProvider\RouterServiceProvider;
use ShugaChara\Framework\ServiceProvider\DatabaseServiceProvider;
use ShugaChara\Framework\ServiceProvider\ValidatorServiceProvider;
use ShugaChara\Framework\Console\Commands\ApplicationCommand;
use ShugaChara\Framework\Console\Commands\HttpServerCommand;
use ShugaChara\Framework\Console\Commands\ProcessorCommand;
use ShugaChara\Framework\Swoole\Processor\BaseProcess;
use ShugaChara\Swoole\Server;
use ShugaChara\Framework\Helpers\FHelper;

return [
    // 应用名称
    'app_name'  =>  'framework',

    // 错误级别
    'error_reporting'   =>  E_ALL,

    // 是否调试模式
    'is_debug'  =>  true,

    // 控制器命名空间
    'controller_namespace'  =>  '\\App\\Http\\Controllers\\',

    // 应用中间件
    'middlewares'   =>  [
        'dispatch'      =>  DispatchMiddleware::class,
    ],

    // 接口状态码类
    'apicode'       =>  StatusCode::class,

    // 路由配置
    'router'    =>  [
        //  存放目录路径
        'path'      =>  FHelper::app()->getRootDirectory() . '/router/',
        //  路由文件后缀
        'ext'       =>  '.php',
    ],

    // 日志配置
    'logs'      =>  [
        //  存放目录路径
        'path'      =>  FHelper::app()->getRootDirectory() . '/runtime/logs/',
        //  最大文件数量
        'maxFiles'  =>  30,
        //  日志文件后缀
        'ext'       =>  '.log',
    ],

    // 命令行脚本
    'console'   =>  [
        'application' => [
            'name'  =>  ApplicationCommand::class
        ],
        'processor' =>  [
            'name'  =>  ProcessorCommand::class
        ],
        'http'  =>  [
            'name'  =>  HttpServerCommand::class
        ],
    ],

    // 数据库配置
    'databases' =>  [
        'default' => [
            'driver'    =>  'mysql',
            'host'      =>  '127.0.0.1',
            'port'      =>  3306,
            'username'  =>  '',
            'password'  =>  '',
            'database'  =>  '',
            'charset'   =>  'utf8mb4',
            'prefix'    =>  ''
        ],
    ],

    // 缓存配置
    'cache'     =>  [
        'redis'     =>      [
            'default'       =>      [
                'scheme'        =>   'tcp',
                'host'          =>   '127.0.0.1',
                'database'      =>   0,
                'password'      =>   '',
                'port'          =>   6379,
                'time_out'      =>   5,
            ],
        ]
    ],

    // 应用服务
    'service_providers' =>  [
        LogsServiceProvider::class,
        ConsoleServiceProvider::class,
        CacheServiceProvider::class,
        RouterServiceProvider::class,
        DatabaseServiceProvider::class,
        ValidatorServiceProvider::class,
    ],

    // 时区
    'timezone'  =>  'PRC',

    // 验证配置
    'validator' =>  [
        // 语言包存放目录路径
        'lang_path' =>      'data/lang',
        // 所使用的语言包
        'lang'      =>      'zh',
    ],

    // 进程配置
    'processor' =>  [
        'base' => [
            'process' => BaseProcess::class,
            'options' => [],
        ],
    ],

    // Swoole 配置
    'swoole'    =>  [
        // 主事件监听类
        'main_events'   =>  MainSwooleEvents::class,
        'processor' =>  [
            'pid_path'  =>  FHelper::app()->getRootDirectory() . '/processes',
        ],
        'http' => [
            'host' => '127.0.0.1',
            'port' => 9002,
            'setting' => [
                'worker_num' => 8,
                'task_worker_num' => 8,
                'task_tmpdir' => FHelper::app()->getRootDirectory() . '/swoole/' . Server::SWOOLE_HTTP_SERVER . '/task',
                'log_file' => FHelper::app()->getRootDirectory() . '/swoole/' . Server::SWOOLE_HTTP_SERVER . '.log',
                'pid_file' => FHelper::app()->getRootDirectory() . '/swoole/' . Server::SWOOLE_HTTP_SERVER . '.pid',
                'daemonize' => false,
                'backlog' => 128,
                'open_cpu_affinity' => true,
            ]
        ],
    ]
];