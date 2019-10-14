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

use function get_class;

/**
 * Class ApplicationProcessor
 * @package ShugaChara\Framework\Processor
 */
class ApplicationProcessor extends Processor
{
    /**
     * @var array
     */
    private $processors = [];

    /**
     * @var array
     */
    private $disabledProcessors = [];

    public function handle(): bool
    {
        // TODO: Implement handle() method.

        foreach ($this->processors as $processor) {
            $class = get_class($processor);

            // If is disabled, skip handle.
            if (isset($this->disabledProcessors[$class])) {
                continue;
            }

            $processor->handle();
        }

        return true;
    }

    /**
     * 添加 禁止/过滤 Processor
     *
     * @param Processor ...$processor
     * @return bool
     */
    public function addDisabledProcessors($processor): bool
    {
        foreach ($processor as $item) {
            $class = get_class($item);
            if (! isset($this->disabledProcessors[$class])) {
                $this->disabledProcessors[$class] = $item;
            }
        }

        return true;
    }

    /**
     * 将 processor 添加到第一个
     *
     * @param Processor[] $processor
     * @return bool
     */
    public function addFirstProcessor(Processor ...$processor): bool
    {
        array_unshift($this->processors, ... $processor);

        return true;
    }

    /**
     * 将 processor 添加到最后
     *
     * @param Processor[] $processor
     *
     * @return bool
     */
    public function addLastProcessor(Processor ...$processor): bool
    {
        array_push($this->processors, ... $processor);

        return true;
    }
}

