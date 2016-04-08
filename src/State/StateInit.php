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
use PHProfiling\Observer\StartObserver;

class StateInit extends AbstractState
{
    const STATE = 1;

    /**
     * @param Item $item
     * @return mixed
     */
    public function start(Item $item)
    {
        return $item->setRunningState()->notify(StartObserver::class);
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function pause(Item $item)
    {
        return $item;
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function resume(Item $item)
    {
        return $item;
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function stop(Item $item)
    {
        return $item;
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function discard(Item $item)
    {
        return $item;
    }
    
}