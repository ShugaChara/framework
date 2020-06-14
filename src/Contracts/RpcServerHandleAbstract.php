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

use ShugaChara\Core\Utils\Helper\ArrayHelper;

/**
 * Class RpcServerHandleAbstract
 * @package ShugaChara\Framework\Contracts
 */
abstract class RpcServerHandleAbstract
{
    /**
     * 服务名
     * @var string
     */
    protected $service;

    /**
     * 操作方法
     * @var string
     */
    protected $action;

    /**
     * 参数
     * @var array
     */
    protected $params = [];

    /**
     * 数据解包
     * @param $data
     */
    protected function unpack($data)
    {
        $data = json_decode($data, true);
        if (is_array($data)) {
            $this->service = ArrayHelper::get($data, 'service', '');
            $this->action = ArrayHelper::get($data, 'action', '');
            if (empty($this->service) || empty($this->action)) {
                return null;
            }
            $this->params = ArrayHelper::get($data, 'params', []);
            return $this;
        }

        return null;
    }

    /**
     * 获取服务名
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * 获取操作方法
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * 获取参数
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * 获取对外服务列表
     * @return mixed
     */
    public function getServiceMap()
    {
        return fnc()->c()->get('swoole.rpc.service_map', []);
    }

    /**
     * 发送数据到客户端
     * @param     $data
     * @param int $fd
     */
    public function send($data, $fd = 0)
    {
        $this->Bean()->getServ()->send(
            $fd ? : $this->Bean()->getFd(),
            $data
        );
    }

    /**
     * 关闭客户端
     * @param int $fd
     */
    public function close($fd = 0)
    {
        $this->Bean()->getServ()->close(
            $fd ? : $this->Bean()->getFd()
        );
    }
}
