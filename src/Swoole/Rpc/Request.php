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

use ShugaChara\Core\Utils\Helper\ArrayHelper;

/**
 * Class Request
 * @package ShugaChara\Framework\Swoole\Rpc
 */
class Request
{
    const SERVICE_NAME_PARAMETER = 'service';

    const SERVICE_METHOD_PARAMETER = 'method';

    const SERVICE_PARAMS_PARAMETER = 'params';

    /**
     * @var
     */
    protected $data;

    /**
     * 服务名称
     * @var
     */
    protected $serviceName;

    /**
     * 服务方法
     * @var
     */
    protected $serviceMethod;

    /**
     * 服务参数
     * @var array
     */
    protected $serviceParams = [];

    /**
     * Request constructor.
     * @param string $data
     */
    public function __construct(string $data)
    {
        $data = json_decode($data, true);

        if ($data) {
            $this->data = $data;
            $this->serviceName = ArrayHelper::get($data, static::SERVICE_NAME_PARAMETER);
            $this->serviceMethod = ArrayHelper::get($data, static::SERVICE_METHOD_PARAMETER);
            $serviceParams = ArrayHelper::get($data, static::SERVICE_PARAMS_PARAMETER);
            if (is_array($serviceParams)) {
                $this->serviceParams = $serviceParams;
            }
        }
    }

    /**
     * 获取解析数据
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取服务名称
     * @return mixed
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * 获取服务方法
     * @return mixed
     */
    public function getServiceMethod()
    {
        return $this->serviceMethod;
    }

    /**
     * 获取服务参数
     * @return array
     */
    public function getServiceParams(): array
    {
        return $this->serviceParams;
    }
}

