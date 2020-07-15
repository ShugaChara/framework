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

namespace ShugaChara\Framework\Components;

use ShugaChara\Core\Traits\Singleton;
use ShugaChara\Core\Utils\Helper\ArrayHelper;
use ShugaChara\Framework\Contracts\AliasInterface;

/**
 * Class Alias
 * @package ShugaChara\Framework\Components
 */
class Alias implements AliasInterface
{
    use Singleton;

    /**
     * 别名前缀
     */
    const ALIAS_PREFIX = '@';

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @param string $alias
     * @param string $value
     * @return mixed|void
     */
    public function set(string $alias, $value = '')
    {
        // TODO: Implement set() method.

        $this->alias[$this->alias($alias)] = $value;
    }

    /**
     * @param string $alias
     * @return mixed|void
     */
    public function has(string $alias): bool
    {
        // TODO: Implement has() method.

        return isset($this->alias[$this->alias($alias)]) ? true : false;
    }

    /**
     * @param string $alias
     * @return mixed
     */
    public function get(string $alias)
    {
        // TODO: Implement get() method.

        return ArrayHelper::get($this->alias, static::alias($alias), null);
    }

    /**
     * @param string $alias
     * @return mixed|void
     */
    public function del(string $alias)
    {
        // TODO: Implement del() method.

        unset($this->alias[$this->alias($alias)]);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        // TODO: Implement all() method.

        return $this->alias;
    }

    /**
     * 生成别名 (注意：设置别名必须全小写)
     * @param string $alias
     * @return string
     */
    protected function alias(string $alias)
    {
        $alias = strtolower($alias);

        if ($alias && ($alias[0] != static::ALIAS_PREFIX)) {
            $alias = static::ALIAS_PREFIX . $alias;
        }

        return $alias;
    }
}