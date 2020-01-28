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

use ShugaChara\Framework\Http\Request;
use ShugaChara\Framework\Http\Response;
use ShugaChara\Validation\Validator;

/**
 * 控制器抽象类
 *
 * Class ControllerAbstract
 * @package ShugaChara\Framework\Contracts
 */
abstract class ControllerAbstract
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * ControllerAbstract constructor.
     */
    final public function __construct()
    {
        $this->request = request();

        $this->response = response();

        $this->initialize();
    }

    /**
     * 获取Request请求对象
     * @return Request
     */
    final public function request(): Request
    {
        return $this->request;
    }

    /**
     * 获取Response响应对象
     * @return Response
     */
    final public function response(): Response
    {
        return $this->response;
    }

    /**
     * Api Json格式响应
     *
     * @param null  $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    final public function api($data = null, $status = Response::HTTP_OK, array $headers = [])
    {
        return $this->response()->api($data, $status, $headers);
    }

    /**
     * 数据验证
     * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator|\Runner\Validator\Validator|\ShugaChara\Validation\Validator
     */
    final public function validator(): Validator
    {
        return validator();
    }

    /**
     * 初始化数据方法,相当于__construct使用,为了框架的管理,所有禁止了构造函数的重写,该方法伪构造替代
     */
    public function initialize() {}
}
