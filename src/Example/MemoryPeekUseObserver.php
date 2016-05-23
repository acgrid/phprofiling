<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfiling\Example;


use PHProfiling\Item;
use PHProfiling\Observer\StartObserver;
use PHProfiling\Observer\StopObserver;

class MemoryPeekUseObserver implements StartObserver, StopObserver
{
    const FILED_MEMORY_USAGE = 'usage';
    
    protected $realUsage;

    /**
     * MemoryPeekUseObserver constructor.
     * @param bool $real_usage See param of memory_get_peak_usage()
     */
    public function __construct($real_usage = false)
    {
        $this->realUsage = $real_usage;
    }
    
    /**
     * @param Item $item
     * @return mixed
     */
    public function onStart(Item $item)
    {
        $item->setStatistic(static::class, static::FILED_MEMORY_USAGE, memory_get_peak_usage($this->realUsage));
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function onStop(Item $item)
    {
        $item->setStatistic(static::class, static::FILED_MEMORY_USAGE,
            memory_get_peak_usage($this->realUsage) - $item->getStatistic(static::class, static::FILED_MEMORY_USAGE));
    }

}