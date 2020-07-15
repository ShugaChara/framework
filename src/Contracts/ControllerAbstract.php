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
 * Controller abstract class
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
        // Set request
        $this->request = request();

        // Set response
        $this->response = response();

        // initialize controller
        $this->initialize();
    }

    /**
     * 获取请求对象
     * @return Request
     */
    final public function request(): Request
    {
        return $this->request;
    }

    /**
     * 获取响应对象
     * @return Response
     */
    final public function response(): Response
    {
        return $this->response;
    }

    /**
     * Api Json 格式响应
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
     * 初始化数据方法等效于__construct的使用。对于框架的管理，禁止所有重写构造函数，而此方法是伪构造的。
     */
    public function initialize() {}
}
