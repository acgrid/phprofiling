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

abstract class AbstractState
{
    const STATE = 0;

    /**
     * @return static
     */
    public static function getInstance()
    {
        static $instance;
        if($instance === null) $instance = new static();
        return $instance;
    }

    /**
     * Define start operation in this state
     * 
     * @param Item $item
     * @return Item
     */
    abstract public function start(Item $item);

    /**
     * Define pause operation in this state
     *
     * @param Item $item
     * @return Item
     */
    abstract public function pause(Item $item);

    /**
     * Define resume operation in this state
     *
     * @param Item $item
     * @return Item
     */
    abstract public function resume(Item $item);

    /**
     * Define stop operation in this state
     *
     * @param Item $item
     * @return Item
     */
    abstract public function stop(Item $item);

    /**
     * Define discard operation in this state
     *
     * @param Item $item
     * @return Item
     */
    abstract public function discard(Item $item);
}