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

namespace ShugaChara\Framework\Pools;

use LogicException;
use ShugaChara\Databases\DB;
use ShugaChara\Framework\Contracts\PoolInterface;

class DatabasesPool implements PoolInterface
{
    /**
     * @var DB
     */
    private $capsule;

    /**
     * DB 配置项
     * @var array
     */
    protected $config = [];

    /**
     * DB连接
     * @var array
     */
    protected $connections = [];

    public function __construct(array $config)
    {
        if (! ($this->capsule instanceof DB)) {
            $this->capsule = DB::getInstance();
        }

        $this->config = $config;
    }

    /**
     * 获取连接
     * @param $name
     * @return mixed
     */
    public function getConnection($name)
    {
        if (! isset($this->connections[$name])) {
            if (! isset($this->config[$name])) {
                throw new LogicException(sprintf('No set %s database', $name));
            }

            $this->connections[$name] = $this->capsule->addConnection($this->config[$name], $name);
        }

        $getCapsule = ($this->connections[$name])->getCapsule();
        $getCapsule->setConnection($name);

        return $getCapsule;
    }

    public function initPool()
    {
        // TODO: Implement initPool() method.

        foreach (array_keys($this->config) as $name) {
            $this->getConnection($name);
        }
    }
}
