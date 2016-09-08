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

class TaggingObserver implements StartObserver
{
    const FILED_TAG = 'tag';

    protected $tag;

    /**
     * TaggingObserver constructor.
     * @param string $tag
     */
    public function __construct($tag = '')
    {
        $this->setTag($tag);
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     * @return TaggingObserver
     */
    public function setTag($tag)
    {
        $this->tag = strval($tag);
        return $this;
    }

    /**
     * @param Item $item
     */
    public function onStart(Item $item)
    {
        $item->setStatistic(static::class, static::FILED_TAG, $this->tag);
    }

}