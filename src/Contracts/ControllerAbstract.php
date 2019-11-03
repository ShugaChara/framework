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

namespace ShugaChara\Framework\Contracts;

use ShugaChara\Http\Message\ServerRequest;

/**
 * Class ControllerAbstract
 * @package ShugaChara\Framework\Contracts
 */
abstract class ControllerAbstract
{
    /**
     * @var object
     */
    private $request;

    /**
     * ControllerAbstract constructor.
     */
    final public function __construct()
    {
        $this->request = request();

        $this->initialize();
    }

    /**
     * 获取Request请求对象
     * @return ServerRequest
     */
    final public function request(): ServerRequest
    {
        return $this->request;
    }

    /**
     * 数据验证
     * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator|\Runner\Validator\Validator|\ShugaChara\Validation\Validator
     */
    final public function validator()
    {
        return validator();
    }

    abstract function index();

    /**
     * 初始化数据方法,相当于__construct使用,为了框架的管理,所有禁止了构造函数的重写,该方法伪构造替代
     */
    public function initialize() {}
}
