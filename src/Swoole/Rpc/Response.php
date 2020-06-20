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

/**
 * Class Response
 * @package ShugaChara\Framework\Swoole\Rpc
 */
class Response
{
    // 成功
    const CODE_SUCCESS = 0;

    // 失败
    const CODE_ERROR = -1;

    // 异常
    const CODE_EXCEPTION = 1001;

    // 服务不存在
    const CODE_NOT_SERVER = 1002;

    // 服务方法不存在
    const CODE_NOT_FUNCTION = 1003;

    /**
     * 数据包
     * @var
     */
    protected $data;

    /**
     * 状态码
     * @var
     */
    protected $code;

    /**
     * 信息体
     * @var
     */
    protected $msg;

    /**
     * 数据打包体
     * @var
     */
    protected $buildData;

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @return mixed
     */
    public function getBuildData()
    {
        return $this->buildData;
    }

    /**
     * 组合数据包
     * @param        $data
     * @param int    $code
     * @param string $msg
     * @return $this
     */
    public function build($data, $code = self::CODE_SUCCESS, $msg = '')
    {
        $this->data = $data;
        $this->code = $code;
        $this->msg = $msg;

        return $this->buildData();
    }

    /**
     * 响应数据构建
     * @return $this
     */
    public function buildData()
    {
        $this->buildData = [
            'result'    =>      $this->getData(),
            'code'      =>      $this->getCode(),
            'msg'       =>      $this->getMsg()
        ];

        return $this;
    }

    /**
     * 将数据打包体转为Json
     * @return false|string
     */
    public function beJson()
    {
        return json_encode($this->getBuildData());
    }
}
