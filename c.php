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
use ShugaChara\Framework\Swoole\TaskDispatcher;
use ShugaChara\Framework\Swoole\Rpc\RpcHandle;
use ShugaChara\Framework\Console\Commands\ApplicationCommand;
use ShugaChara\Framework\Console\Commands\HttpServerCommand;
use ShugaChara\Framework\Console\Commands\ProcessorCommand;
use ShugaChara\Framework\Console\Commands\RpcServerCommand;

return [
    // 应用名称
    'app_name'  =>  'framework',

    // 应用版本
    'app_version'   =>  'v1.0.0',

    // 是否调试模式
    'is_debug'  =>  true,

    // 错误级别
    'error_reporting'   =>  E_ALL,

    // 应用时区
    'timezone'  =>  'PRC',

    // 接口状态码
    'apicode'   =>  StatusCode::class,

    // 控制器命名空间
    'controller_namespace'  =>  '\\App\\Http\\Controllers\\',

    // 应用中间件
    'middlewares'   =>  [
        'dispatch'      =>  DispatchMiddleware::class,
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

    // 路由配置
    'router'    =>  [
        //  目录文件存放位置
        'path'      =>  fnc()->app()->getRootDirectory() . '/router/',
        //  路由文件前缀
        'ext'       =>  '.php',
    ],

    // 日志配置
    'logs'      =>  [
        //  目录文件存放位置
        'path'      =>  fnc()->app()->getRootDirectory() . '/runtime/logs/',
        //  最大文件数量
        'maxFiles'  =>  30,
        //  日志文件前缀
        'ext'       =>  '.log',
    ],

    // 命令行脚本
    'console'   =>  [
        'application' => [
            'name'  =>  ApplicationCommand::class
        ],
        'httpserver' => [
            'name'  =>  HttpServerCommand::class
        ],
        'processor'  =>  [
            'name'  =>  ProcessorCommand::class
        ],
        'rpcserver' => [
            'name'  =>  RpcServerCommand::class
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

    // Swoole 配置
    'swoole'    =>  [
        // 主事件监控类
        'main_events'   =>  MainSwooleEvents::class,
        // 服务 热更新/重启配置
        'hotreload'     =>  [
            //  启动状态
            'status'        =>  false,
            //  进程名称
            'name'          =>  'HotReload',
            //  指定目录
            'monitorDir'    =>  fnc()->app()->getRootDirectory(),
            //  文件扩展名
            'monitorExt'    =>  ['php'],
            //  是否打开 inotify
            'disableInotify'=>  false,
        ],
        'task'  =>  [
            'dispatcher_class'  =>  TaskDispatcher::class
        ],
        'processor' =>  [
            'pid_path'  =>  fnc()->app()->getRootDirectory() . '/processes',
            'swoole_list' => [],
            'fpm_list'    => []
        ],
        'listeners' => [
            /*[
                'host'  =>  '127.0.0.1',
                'port'  =>  9999,
                'sock_type' =>  SWOOLE_SOCK_TCP,
                'setting'   =>  [],
                'events'    =>  BaseListener::class,
            ]*/
        ],
        'http' => [
            'host' => '127.0.0.1',
            'port' => 9002,
            'setting' => [
                'worker_num' => 8,
                'task_worker_num' => 8,
                'task_tmpdir' => fnc()->app()->getRootDirectory() . '/swoole/' . Server::SWOOLE_HTTP_SERVER . '/' . Server::SWOOLE_HTTP_SERVER . '/task',
                'log_file' => fnc()->app()->getRootDirectory() . '/swoole/' . Server::SWOOLE_HTTP_SERVER . '/' . Server::SWOOLE_HTTP_SERVER . '.log',
                'pid_file' => fnc()->app()->getRootDirectory() . '/swoole/' . Server::SWOOLE_HTTP_SERVER . '/' . Server::SWOOLE_HTTP_SERVER . '.pid',
                'daemonize' => false,
                'backlog' => 128,
                'open_cpu_affinity' => true,
                'dispatch_mode' => 2
            ]
        ],
        'rpc'  =>  [
            'handle_class' => RpcHandle::class,
            'services' => [],
            'host' => '127.0.0.1',
            'port' => 9012,
            'setting' => [
                'worker_num' => 8,
                'task_worker_num' => 8,
                'task_tmpdir' => fnc()->app()->getRootDirectory() . '/swoole/rpc/task',
                'log_file' => fnc()->app()->getRootDirectory() . '/swoole/rpc.log',
                'pid_file' => fnc()->app()->getRootDirectory() . '/swoole/rpc.pid',
                'daemonize' => false,
                'backlog' => 128,
                'open_cpu_affinity' => true,
                'dispatch_mode' => 2,
            ]
        ],
    ]
];