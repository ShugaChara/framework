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

namespace ShugaChara\Framework\Contracts;

/**
 * Interface AliasInterface
 * @package ShugaChara\Framework\Contracts
 */
interface AliasInterface
{
    /**
     * 设置别名
     * @param string $alias     别名
     * @param string $value     别名对应值
     * @return mixed
     */
    public static function set(string $alias, $value = '');

    /**
     * 别名是否存在
     * @param string $alias
     * @return bool
     */
    public static function has(string $alias): bool;

    /**
     * 获取别名
     * @param string $alias
     * @return mixed
     */
    public static function get(string $alias);

    /**
     * 删除别名
     * @param string $alias
     * @return mixed
     */
    public static function del(string $alias);

    /**
     * 获取所有别名
     * @return array
     */
    public static function all(): array;
}