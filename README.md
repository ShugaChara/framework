# Framework Swoole Api 框架

[![GitHub release](https://img.shields.io/github/release/shugachara/framework.svg)](https://github.com/shugachara/czPHP/releases)
[![PHP version](https://img.shields.io/badge/php-%3E%207-orange.svg)](https://github.com/php/php-src)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](#LICENSE)

## 说明

个人基于Swoole扩展开发的一套高性能API框架。

## 包地址

[Framework](https://packagist.org/packages/shugachara/framework)

## 使用方法

**安装**

1. 新建空项目

2. 创建 composer.json 文件, 文件内容：
```
{
  "name": "shugachara/czPHP",
  "type": "project",
  "description": "this is linshan czPHP framework",
  "keywords" : [
    "swoole",
    "framework",
    "async",
    "czPHP",
    "api",
    "shugachara"
  ],
  "license": "MIT",
  "require": {
    "php": "^7.1.3",
    "shugachara/framework": "^1.0"
  },
  "require-dev": {
  },
  "extra": {
    "laravel": {
      "dont-discover": []
    }
  },
  "autoload": {
    "psr-4": {
      "App\\": "app/"
    },
    "classmap": [
    ]
  },
  "autoload-dev": {
    "psr-4": {
      "Tests\\": "tests/"
    }
  },
  "repositories": [

  ]
}
```

3. 安装 composer.json
<pre>composer install</pre>

4. 安装 Cozy 框架
<pre>php vendor/shugachara/cozy/storage/bin/console cozy:install</pre>

5. 由于框架自带了mysql和redis，所以框架安装完成后需要配置mysql和redis。redis有两个地方需要配置,
   一个是 config/cache.php 中的 default.params.dsn，容易忘记的是配置密码，比如 redis://myredispassword@127.0.0.1:6379/0,
   另一个是 config/redis.php；mysql配置在 config/database.php。当然如果不使用 mysql和redis，可以通过 config/app.php 中的
   APP_SERVICES 配置服务者去掉即可。
   
   最后,我要强烈推荐将配置写到 .env.yml 文件。
 
6. 启动Swoole服务
<pre>php bin/swoole_server start</pre>


## 更新日志

请查看 [CHANGELOG.md](CHANGELOG.md)

### 贡献

非常欢迎感兴趣，并且愿意参与其中，共同打造更好PHP生态。

* 在你的系统中使用，将遇到的问题 [反馈](https://github.com/shugachara/czPHP/issues)

### 联系

如果你在使用中遇到问题，请联系: [1099013371@qq.com](mailto:1099013371@qq.com). 博客: [kaka 梦很美](http://www.ls331.com)

## License MIT
