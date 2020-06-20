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

namespace ShugaChara\Framework\Swoole\Rpc;

use Throwable;
use ShugaChara\Core\Traits\Singleton;
use ShugaChara\Framework\Contracts\RpcHandleInterface;

/**
 * Class RpcHandle
 * @package ShugaChara\Framework\Swoole\Rpc
 */
class RpcHandle implements RpcHandleInterface
{
    use Singleton;

    /**
     * @var DataBean
     */
    protected $bean;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param DataBean $bean
     * @return mixed|void
     */
    final public function new(DataBean $bean)
    {
        // TODO: Implement new() method.

        $this->bean = $bean;

        $this->__hook();
    }

    /**
     * @return DataBean
     */
    public function bean(): DataBean
    {
        return $this->bean;
    }

    /**
     * @return mixed|void
     */
    public function handle()
    {
        // TODO: Implement handle() method.

        if (isset($this->getRpcServer()->getServiceList()[$this->getServiceName()])) {
            $service = $this->getRpcServer()->getServiceList()[$this->getServiceName()];
            $method = $this->getServiceMethod();
            if (! in_array($method, $service->getAllowMethods())) {
                $this->handleException('服务方法不存在', Response::CODE_NOT_FUNCTION);
                return ;
            }

            try {
                $result = $service->$method(...$this->getServiceParams());
            } catch (Throwable $exception) {
                $result = $exception->getMessage();
                $code = Response::CODE_ERROR;
            }

            return $this->getServ()->send(
              $this->getFd(),
              $this->getResponse()->build($result, isset($code) ? $code : Response::CODE_SUCCESS)->beJson()
            );
        }

        return $this->handleException('服务不存在', Response::CODE_NOT_SERVER);
    }

    /**
     * @return mixed|void
     */
    public function __hook()
    {
        $request = new Request($this->getData());
        if (! $request->getData()) {
            return $this->handleException('服务参数格式错误');
        }

        $this->request = $request;

        $this->response = new Response();

        $this->handle();
    }

    /**
     * @return \swoole_server
     */
    public function getServ()
    {
        return $this->bean()->getServ();
    }

    /**
     * @return mixed
     */
    public function getFd()
    {
        return $this->bean()->getFd();
    }

    /**
     * @return mixed
     */
    public function getReactorId()
    {
        return $this->bean()->getReactorId();
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->bean()->getData();
    }

    /**
     * @return Server
     */
    public function getRpcServer()
    {
        return $this->bean()->getRpcServer();
    }

    /**
     * 获取请求数据类
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * 获取响应数据类
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 获取服务名称
     * @return mixed
     */
    public function getServiceName()
    {
        return $this->getRequest()->getServiceName();
    }

    /**
     * 获取服务方法
     * @return mixed
     */
    public function getServiceMethod()
    {
        return $this->getRequest()->getServiceMethod();
    }

    /**
     * 获取服务参数
     * @return array
     */
    public function getServiceParams()
    {
        return $this->getRequest()->getServiceParams();
    }

    /**
     * Rpc 接口异常处理
     * @param string $msg
     * @param int    $code
     */
    public function handleException($msg = '', $code = Response::CODE_EXCEPTION)
    {
        $this->getServ()->send(
            $this->getFd(),
            $this->getResponse()->build('', $code, $msg)->beJson()
        );

        $this->getServ()->close($this->getFd());
    }
}

