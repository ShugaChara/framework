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
        $this->request = fn()->request();

        $this->response = fn()->response();

        $this->initialize();
    }

    /**
     * Get Request object
     * @return Request
     */
    final public function request(): Request
    {
        return $this->request;
    }

    /**
     * Get Response object
     * @return Response
     */
    final public function response(): Response
    {
        return $this->response;
    }

    /**
     * Api Json format response
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
     * data verification
     * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator|\Runner\Validator\Validator|\ShugaChara\Validation\Validator
     */
    final public function validator(): Validator
    {
        return fn()->validator();
    }

    /**
     * The initialization data method is equivalent to the use of __construct. For the management of the framework, all rewriting of the constructor is prohibited, and this method is pseudo-constructed instead
     */
    public function initialize() {}
}
