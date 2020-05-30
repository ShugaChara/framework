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

use Exception;
use LogicException;
use ShugaChara\Framework\Contracts\PoolInterface;
use ShugaChara\Redis\Redis;

/**
 * Class RedisPool
 * @package ShugaChara\Framework\Pools
 */
class RedisPool implements PoolInterface
{
    /**
     * configuration
     * @var array
     */
    protected $config;

    /**
     * connection
     * @var array
     */
    protected $connections = [];

    /**
     * RedisPool constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get connected
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function getConnection($name)
    {
        if ( (! isset($this->connections[$name]))
            || ( ($this->connections[$name] instanceof Redis) && (false === $this->connections[$name]->ping()) )
        ) {
            if (! isset($this->config[$name])) {
                throw new LogicException(sprintf('No set %s Redis', $name));
            }

            try {
                $redis = new Redis($this->config[$name]);
            } catch (Exception $e) {
                throw new Exception('redis Connection error : ' . $e->getMessage());
            }
            $this->connections[$name] = $redis->getConnection();
        }

        return $this->connections[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function initPool()
    {
        foreach (array_keys($this->config) as $name) {
            $this->getConnection($name);
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool
     */
    public function __call($name, $arguments)
    {
        switch (strtolower($name)) {
            case 'blpop' :
                if (isset($arguments[1]) && $arguments[1]) {
                    sleep($arguments[1]);
                }
                break;
        }

        return false;
    }
}
