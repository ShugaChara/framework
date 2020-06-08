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

namespace ShugaChara\Framework\Swoole;

use swoole_server;

/**
 * Class Task
 * @package ShugaChara\Framework\Swoole
 */
class Task
{
    /**
     * @var swoole_server
     */
    protected $serv;

    /**
     * @var
     */
    protected $task_id;

    /**
     * @var
     */
    protected $src_worker_id;

    /**
     * @var
     */
    protected $data;

    /**
     * Task constructor.
     * @param swoole_server $serv
     * @param int           $task_id
     * @param int           $src_worker_id
     * @param               $data
     */
    public function __construct(swoole_server $serv, int $task_id, int $src_worker_id, $data)
    {
        $this->serv = $serv;
        $this->task_id = $task_id;
        $this->src_worker_id = $src_worker_id;
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
    public function getTaskId()
    {
        return $this->task_id;
    }

    /**
     * @return mixed
     */
    public function getSrcWorkerId()
    {
        return $this->src_worker_id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}

