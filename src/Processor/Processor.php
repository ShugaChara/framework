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

use ShugaChara\Framework\Contracts\ProcessorInterface;
use ShugaChara\Swoole\Manager\ProcessManager;

abstract class Processor extends ProcessManager implements ProcessorInterface
{

}
