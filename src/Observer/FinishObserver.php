<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfiling\Observer;


use PHProfiling\Item;

interface FinishObserver extends IObserver
{
    /**
     * Return true to let manager remove this item if the item is no longer needed.
     * 
     * @param Item $item
     * @return bool
     */
    public function onFinish(Item $item);
}