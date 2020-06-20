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

use swoole_server;

/**
 * Class DataBean
 * @package ShugaChara\Framework\Swoole\Rpc
 */
class DataBean
{
    /**
     * @var Server
     */
    protected $rpc_server;

    /**
     * @var swoole_server
     */
    protected $serv;

    /**
     * @var
     */
    protected $fd;

    /**
     * @var
     */
    protected $reactor_id;

    /**
     * @var
     */
    protected $data;

    /**
     * DataBean constructor.
     * @param swoole_server $serv
     * @param int           $fd
     * @param int           $reactor_id
     * @param               $data
     * @param               $rpc_server
     */
    public function __construct(swoole_server $serv, int $fd, int $reactor_id, $data, $rpc_server)
    {
        $this->serv = $serv;
        $this->fd = $fd;
        $this->reactor_id = $reactor_id;
        $this->data = $data;
        $this->rpc_server = $rpc_server;
    }

    /**
     * @return swoole_server
     */
    public function getServ(): swoole_server
    {
        return $this->serv;
    }

    /**
     * @return mixed
     */
    public function getFd()
    {
        return $this->fd;
    }

    /**
     * @return mixed
     */
    public function getReactorId()
    {
        return $this->reactor_id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取 Rpc 服务
     * @return Server
     */
    public function getRpcServer(): Server
    {
        return $this->rpc_server;
    }
}

