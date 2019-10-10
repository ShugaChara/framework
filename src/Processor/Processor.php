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

namespace ShugaChara\Framework\Processor;

use ShugaChara\Framework\Contracts\ApplicationInterface;
use ShugaChara\Framework\Contracts\ProcessorInterface;

abstract class Processor implements ProcessorInterface
{
    /**
     * 应用容器
     * @var ApplicationInterface
     */
    protected $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }
}

