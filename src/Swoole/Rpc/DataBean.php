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
    protected $reactorId;

    /**
     * @var
     */
    protected $data;

    /**
     * DataBean constructor.
     * @param swoole_server $serv
     * @param int           $fd
     * @param int           $reactorId
     * @param               $data
     */
    public function __construct(swoole_server $serv, int $fd, int $reactorId, $data)
    {
        $this->serv = $serv;
        $this->fd = $fd;
        $this->reactorId = $reactorId;
        $this->data = $data;
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
        return $this->reactorId;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}

