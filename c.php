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

use ShugaChara\Framework\Swoole\Server;
use ShugaChara\Framework\Middlewares\DispatchMiddleware;
use ShugaChara\Framework\ServiceProvider\LogsServiceProvider;
use ShugaChara\Framework\ServiceProvider\ConsoleServiceProvider;
use ShugaChara\Framework\ServiceProvider\CacheServiceProvider;
use ShugaChara\Framework\ServiceProvider\RouterServiceProvider;
use ShugaChara\Framework\ServiceProvider\DatabaseServiceProvider;
use ShugaChara\Framework\ServiceProvider\ValidatorServiceProvider;
use ShugaChara\Framework\Tools\StatusCode;
use ShugaChara\Framework\Swoole\MainSwooleEvents;
use ShugaChara\Framework\Console\Commands\ApplicationCommand;
use ShugaChara\Framework\Console\Commands\HttpServerCommand;

return [
    // Application Name
    'app_name'  =>  'framework',

    // Application version
    'app_version'   =>  'v1.0.0',

    // Whether to debug mode
    'is_debug'  =>  true,

    // Error level
    'error_reporting'   =>  E_ALL,

    // Time zone
    'timezone'  =>  'PRC',

    // Interface status code
    'apicode'   =>  StatusCode::class,

    // Controller namespace
    'controller_namespace'  =>  '\\App\\Http\\Controllers\\',

    // Application middleware
    'middlewares'   =>  [
        'dispatch'      =>  DispatchMiddleware::class,
    ],

    // Application services
    'service_providers' =>  [
        LogsServiceProvider::class,
        ConsoleServiceProvider::class,
        CacheServiceProvider::class,
        RouterServiceProvider::class,
        DatabaseServiceProvider::class,
        ValidatorServiceProvider::class,
    ],

    // Routing configuration
    'router'    =>  [
        //  Directory path
        'path'      =>  fnc()->app()->getRootDirectory() . '/router/',
        //  Routing file suffix
        'ext'       =>  '.php',
    ],

    // Log configuration
    'logs'      =>  [
        //  Directory path
        'path'      =>  fnc()->app()->getRootDirectory() . '/runtime/logs/',
        //  Maximum number of files
        'maxFiles'  =>  30,
        //  Log file suffix
        'ext'       =>  '.log',
    ],

    // Command line script
    'console'   =>  [
        'application' => [
            'name'  =>  ApplicationCommand::class
        ],
        'httpserver' => [
            'name'  =>  HttpServerCommand::class
        ],
    ],

    // Database configuration
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

    // Cache configuration
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

    // Swoole configuration
    'swoole'    =>  [
        // Main event monitoring class
        'main_events'   =>  MainSwooleEvents::class,
        'processor' =>  [
            'pid_path'  =>  fnc()->app()->getRootDirectory() . '/processes',
        ],
        'http' => [
            'host' => '127.0.0.1',
            'port' => 9002,
            'setting' => [
                'worker_num' => 8,
                'task_worker_num' => 8,
                'task_tmpdir' => fnc()->app()->getRootDirectory() . '/swoole/' . Server::SWOOLE_HTTP_SERVER . '/task',
                'log_file' => fnc()->app()->getRootDirectory() . '/swoole/' . Server::SWOOLE_HTTP_SERVER . '.log',
                'pid_file' => fnc()->app()->getRootDirectory() . '/swoole/' . Server::SWOOLE_HTTP_SERVER . '.pid',
                'daemonize' => false,
                'backlog' => 128,
                'open_cpu_affinity' => true,
                'dispatch_mode' => 2
            ]
        ],
    ]
];