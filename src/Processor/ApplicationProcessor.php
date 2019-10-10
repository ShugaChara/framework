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

    public function handle(): bool
    {
        // TODO: Implement handle() method.

        foreach ($this->processors as $processor) {
            $class = get_class($processor);

            // If is disabled, skip handle.
            if (isset($disabled[$class])) {
                continue;
            }

            $processor->handle();
        }

        return true;
    }

    /**
     * Add first processor
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
     * Add last processor
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

