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

use ShugaChara\Core\Helpers;
use ShugaChara\Core\Utils\Helper\ArrayHelper;
use ShugaChara\Framework\Contracts\AliasInterface;

/**
 * Class Alias
 * @package ShugaChara\Framework\Components
 */
class Alias implements AliasInterface
{
    /**
     * 别名前缀
     */
    const ALIAS_PREFIX = '@';

    /**
     * @var array
     */
    protected static $alias = [];

    /**
     * @param string $alias
     * @param string $value
     * @return mixed|void
     */
    public static function set(string $alias, $value = '')
    {
        // TODO: Implement set() method.

        static::$alias[static::alias($alias)] = $value;
    }

    /**
     * @param string $alias
     * @return mixed|void
     */
    public static function has(string $alias): bool
    {
        // TODO: Implement has() method.

        return isset(static::$alias[static::alias($alias)]) ? true : false;
    }

    /**
     * @param string $alias
     * @return mixed|void
     */
    public static function get(string $alias)
    {
        // TODO: Implement get() method.

        return ArrayHelper::get(static::$alias, static::alias($alias), '');
    }

    /**
     * @param string $alias
     * @return mixed|void
     */
    public static function del(string $alias)
    {
        // TODO: Implement del() method.

        unset(static::$alias[static::alias($alias)]);
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        // TODO: Implement all() method.

        return static::$alias;
    }

    /**
     * 生成别名
     * @param string $alias
     * @return string
     */
    protected static function alias(string $alias)
    {
        $alias = strtolower($alias);

        if ($alias && ($alias[0] != static::ALIAS_PREFIX)) {
            $alias = static::ALIAS_PREFIX . $alias;
        }

        return $alias;
    }
}