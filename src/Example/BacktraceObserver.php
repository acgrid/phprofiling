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

class BacktraceObserver implements StartObserver, StopObserver
{
    const FILED_START_POSITION = 'start';
    const FILED_STOP_POSITION = 'stop';

    const SUB_FILED_EXPRESSION = 'invoke';
    const SUB_FILED_FILE = 'file';
    const SUB_FILED_LINE = 'line';

    /*
     * self::collectBacktrace()
     * self::onXXX()
     * call_user_func
     * Item::notify()
     * StateXXX::XXX()
     * Item::XXX()
     * [Manager::XXX()]
     */
    const SKIP_LEVELS = 8;

    /**
     * @param Item $item
     * @param string $field
     * @param string $invoke
     * @param string $file
     * @param int $line
     * @return $this
     */
    protected function writeStatistics(Item $item, $field, $invoke, $file, $line)
    {
        $item->setStatistic(static::class, $field, [
            static::SUB_FILED_EXPRESSION => $invoke,
            static::SUB_FILED_FILE => $file,
            static::SUB_FILED_LINE => $line,
        ]);
        return $this;
    }

    protected function collectBacktrace()
    {
        $backtrace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, static::SKIP_LEVELS), 5);

        // Followed by Public Accessible Facade, i.e Index 1
        $topNamespace = strstr(__NAMESPACE__, '\\', true);
        while($trace = array_shift($backtrace)){
            if(!isset($trace['class']) || (isset($trace['class']) && (strstr($trace['class'], '\\', true) <> $topNamespace))) return $trace;
        }
        return null;
    }

    /**
     * @param Item $item
     */
    public function onStart(Item $item)
    {
        $trace = $this->collectBacktrace();
        if($trace) $this->writeStatistics($item, static::FILED_START_POSITION,
            (isset($trace['class']) && isset($trace['type']) ? $trace['class'] . $trace['type'] : '') . $trace['function'],
            isset($trace['file']) ? $trace['file'] : '',
            isset($trace['line']) ? $trace['line'] : '');
    }

    /**
     * @param Item $item
     */
    public function onStop(Item $item)
    {
        $trace = $this->collectBacktrace();
        if($trace) $this->writeStatistics($item, static::FILED_STOP_POSITION,
            (isset($trace['class']) && isset($trace['type']) ? $trace['class'] . $trace['type'] : '') . $trace['function'],
            isset($trace['file']) ? $trace['file'] : '',
            isset($trace['line']) ? $trace['line'] : '');
    }

}