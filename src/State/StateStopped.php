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

class StateStopped extends AbstractState
{
    const STATE = 4;

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
        return $item;
    }

    /**
     * @param Item $item
     * @return Item
     */
    public function stop(Item $item)
    {
        return $item;
    }

    /**
     * @param Item $item
     * @return Item
     */
    public function discard(Item $item)
    {
        return $item;
    }

}