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
use PHProfiling\Observer\PauseObserver;
use PHProfiling\Observer\ResumeObserver;
use PHProfiling\Observer\StartObserver;
use PHProfiling\Observer\StopObserver;

/**
 * Class TimingObserver
 * Provide microsecond level execution time measurement
 * @package PHProfiling\Example
 */
class TimingObserver implements StartObserver, PauseObserver, ResumeObserver, StopObserver
{
    const FILED_START = 'start';
    const FILED_PAUSED_AT = 'pause';
    const FILED_RUNTIME = 'run';

    /**
     * @var float The timestamp on execution start
     */
    protected $start;
	/**
	 * @var float The start timestamp of latest set
	 */
	protected static $globalStart;

    /**
     * TimingObserver constructor.
     * @param float|null $startTimestamp
     */
    public function __construct(float|null $startTimestamp = null)
    {
        if(is_numeric($startTimestamp)){
            $this->start = $startTimestamp;
        }elseif(isset($_SERVER['REQUEST_TIME_FLOAT'])){
            $this->start = $_SERVER['REQUEST_TIME_FLOAT'];
        }else{
            $this->start = static::getTS();
        }
        $this->start = floatval($this->start);
	    static::$globalStart = $this->start;
    }
    
    protected function getStart(Item $item)
    {
        return $item->getStatistic(self::class, self::FILED_START);
    }
    
    protected function getPausedAt(Item $item)
    {
        return $item->getStatistic(self::class, self::FILED_PAUSED_AT);
    }
    
    protected function getRuntime(Item $item)
    {
        return $item->getStatistic(self::class, self::FILED_RUNTIME);
    }
    
    protected function setRuntime(Item $item, $runtime)
    {
        return $item->setStatistic(self::class, self::FILED_RUNTIME, $runtime);
    }

    /**
     * @param Item $item
     */
    public function onPause(Item $item)
    {
        $item->setStatistic(self::class, self::FILED_PAUSED_AT, $this->getT());
    }

    /**
     * @param Item $item
     */
    public function onResume(Item $item)
    {
        $this->setRuntime($item, $this->getRuntime($item) - ($this->getT() - $this->getPausedAt($item)))
            ->deleteStatistic(self::class, self::FILED_PAUSED_AT);
    }

    /**
     * @param Item $item
     */
    public function onStart(Item $item)
    {
        $item->setStatistic(self::class, self::FILED_START, $this->getT())
            ->setStatistic(self::class, self::FILED_RUNTIME, 0);
    }

    /**
     * @param Item $item
     */
    public function onStop(Item $item)
    {
        if($pausedAt = $this->getPausedAt($item)){
            $runtime = $pausedAt - $this->getStart($item);
        }else{
            $runtime = $this->getT() - $this->getStart($item);
        }
        $this->setRuntime($item, $this->getRuntime($item) + $runtime);
    }
    
    public function getT()
    {
        return static::getTS() - $this->start;
    }

    public static function getGlobalT()
    {
    	if(!isset(static::$globalStart)) static::$globalStart = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : $_SERVER['REQUEST_TIME'];
	    return static::getTS() - static::$globalStart;
    }

    /**
     * @return float
     */
    public static function getTS()
    {
        return microtime(true);
    }
}