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

namespace ShugaChara\Framework\Pools;

use LogicException;
use ShugaChara\Databases\DB;
use ShugaChara\Framework\Contracts\PoolInterface;

/**
 * Class DatabasesPool
 * @package ShugaChara\Framework\Pools
 */
class DatabasesPool implements PoolInterface
{
    /**
     * @var DB
     */
    private $capsule;

    /**
     * 数据库配置项
     * @var array
     */
    protected $config = [];

    /**
     * 数据库连接
     * @var array
     */
    protected $connections = [];

    /**
     * DatabasesPool constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (! ($this->capsule instanceof DB)) {
            $this->capsule = DB::getInstance();
        }

        $this->config = $config;
    }

    /**
     * 获取数据库连接
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

    /**
     * @return mixed|void
     */
    public function initPool()
    {
        // TODO: Implement initPool() method.

        foreach (array_keys($this->config) as $name) {
            $this->getConnection($name);
        }
    }
}
