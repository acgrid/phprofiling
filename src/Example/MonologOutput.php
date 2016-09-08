<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfiling\Example;


use Monolog\Logger;
use PHProfiling\Item;
use PHProfiling\Observer\FinishObserver;

class MonologOutput implements FinishObserver
{
    protected $logger;
    protected $level;

    /**
     * MonologOutput constructor.
     * @param Logger $logger
     * @param int $level
     */
    public function __construct(Logger $logger, $level = Logger::INFO)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    /**
     * @param Item $item
     */
    public function onFinish(Item $item)
    {
        $this->logger->addRecord($this->level, $item->getTitle(), $item->getStatistics());
        $item->setHandled(true);
    }


}