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

namespace ShugaChara\Framework\ServiceProvider;

use ShugaChara\Container\Container;
use ShugaChara\Container\Contracts\ServiceProviderInterface;
use ShugaChara\Framework\Helpers\FHelper;
use ShugaChara\Validation\Validator;

/**
 * 验证服务
 *
 * Class ValidatorServiceProvider
 * @package ShugaChara\Framework\ServiceProvider
 */
class ValidatorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // TODO: Implement register() method.

        $container->add('validator', Validator::getInstance()->boot(
            FHelper::c()->get('validator.lang_path'),
            FHelper::c()->get('validator.lang')
        ));
    }
}

