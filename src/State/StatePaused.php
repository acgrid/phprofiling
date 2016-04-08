<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfiling\State;

use PHProfiling\Item;
use PHProfiling\Observer\FinishObserver;
use PHProfiling\Observer\ResumeObserver;
use PHProfiling\Observer\StopObserver;

class StatePaused extends AbstractState
{
    const STATE = 3;
    
    /**
     * @param Item $item
     * @return Item
     */
    public function start(Item $item)
    {
        return $item;
    }

    /**
     * @param Item $item
     * @return Item
     */
    public function pause(Item $item)
    {
        return $item;
    }

    /**
     * @param Item $item
     * @return Item
     */
    public function resume(Item $item)
    {
        return $item->setRunningState()->notify(ResumeObserver::class);
    }

    /**
     * @param Item $item
     * @return Item
     */
    public function stop(Item $item)
    {
        return $item->setStoppedState()->notify(StopObserver::class)->notify(FinishObserver::class);
    }

    /**
     * @param Item $item
     * @return Item
     */
    public function discard(Item $item)
    {
        return $item->setInitState();
    }
}