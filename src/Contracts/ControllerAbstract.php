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
use ShugaChara\Http\Response;
use ShugaChara\Validation\Validator;

/**
 * Class ControllerAbstract
 * @package ShugaChara\Framework\Contracts
 */
abstract class ControllerAbstract
{
    /**
     * @var ServerRequest
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
     * @return ServerRequest
     */
    final public function request(): ServerRequest
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
     * 响应API Json数据
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    final public function responseAPI($data = [], $status = Response::HTTP_OK, array $headers = [])
    {
        return responseAPI($data, $status, $headers);
    }

    /**
     * 数据验证
     * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator|\Runner\Validator\Validator|\ShugaChara\Validation\Validator
     */
    final public function validator(): Validator
    {
        return validator();
    }

    abstract function index();

    /**
     * 初始化数据方法,相当于__construct使用,为了框架的管理,所有禁止了构造函数的重写,该方法伪构造替代
     */
    public function initialize() {}
}
