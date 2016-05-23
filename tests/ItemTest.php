<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfilingTest;


use PHProfiling\Item;
use PHProfiling\Observer\FinishObserver;
use PHProfiling\Observer\PauseObserver;
use PHProfiling\Observer\ResumeObserver;
use PHProfiling\Observer\StartObserver;
use PHProfiling\Observer\StopObserver;
use PHProfiling\State\StateInit;
use PHProfiling\State\StatePaused;
use PHProfiling\State\StateRunning;
use PHProfiling\State\StateStopped;

class ItemTest extends \PHPUnit_Framework_TestCase implements StartObserver, PauseObserver, ResumeObserver, StopObserver, FinishObserver
{
    /**
     * @param Item $item
     * @return mixed
     */
    public function onFinish(Item $item)
    {
        $this->assertTrue($item->hasStatistics(self::class));
        $this->assertTrue($item->hasStatistics(self::class, 'onStart'));
        $this->assertTrue($item->getStatistic(self::class, 'onStart'));
        $this->assertTrue($item->getStatistic(self::class, 'onResume'));
        $this->assertTrue($item->getStatistic(self::class, 'onPause'));
        $ref =& $item->refScopeStatistic(self::class);
        $this->assertCount(4, $ref);
        $this->assertTrue($ref['onStop']);
        $ref['Ref'] = true;
        $this->assertTrue($item->getStatistic(self::class, 'Ref'));
        echo 'onFinish';
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function onPause(Item $item)
    {
        $item->setStatistic(self::class, 'onPause', true);
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function onResume(Item $item)
    {
        $item->setStatistic(self::class, 'onResume', true);
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function onStart(Item $item)
    {
        $item->setStatistic(self::class, 'onStart', true);
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function onStop(Item $item)
    {
        $item->setStatistic(self::class, 'onStop', true);
    }

    public function testBasic()
    {
        $item = new Item('test-simple');
        $this->assertSame('test-simple', $item->getTitle());
        $this->assertSame($item, $item->setTitle('tested'));
        $this->assertSame('tested', $item->getTitle());
        $this->assertFalse($item->isHandled());
        $this->assertInstanceOf(StateInit::class, $item->getState());
        $this->assertEmpty($item->getStatistics());
        $this->assertFalse($item->hasStatistics(self::class));
        $this->assertSame($item, $item->setStatistic(self::class, 'test1', 'a'));
        $this->assertSame('a', $item->getStatistic(self::class, 'test1'));
        $this->assertSame($item, $item->deleteStatistic(self::class, 'test1'));
        $this->assertNull($item->getStatistic(self::class, 'test1'));
        $this->expectOutputString('onFinish');
        $this->assertSame($item, $item->attach($this));
        $this->assertSame($item, $item->detach($this));
        $item->attach($this);
        $this->assertSame($item, $item->start());
        $this->assertInstanceOf(StateRunning::class, $item->getState());
        $this->assertSame($item, $item->pause());
        $this->assertInstanceOf(StatePaused::class, $item->getState());
        $this->assertSame($item, $item->resume());
        $this->assertInstanceOf(StateRunning::class, $item->getState());
        $this->assertSame($item, $item->stop());
        $this->assertInstanceOf(StateStopped::class, $item->getState());
    }

    public function testStateChanges()
    {
        $item = new Item();
        $this->assertInstanceOf(StateInit::class, $item->getState());
        $this->assertInstanceOf(StateInit::class, $item->pause()->getState());
        $this->assertInstanceOf(StateInit::class, $item->resume()->getState());
        $this->assertInstanceOf(StateInit::class, $item->stop()->getState());
        $this->assertInstanceOf(StateInit::class, $item->discard()->getState());
        $this->assertInstanceOf(StateRunning::class, $item->start()->getState());
        $this->assertInstanceOf(StateRunning::class, $item->start()->getState());
        $this->assertInstanceOf(StateRunning::class, $item->resume()->getState());
        $this->assertInstanceOf(StatePaused::class, $item->pause()->getState());
        $this->assertInstanceOf(StatePaused::class, $item->pause()->getState());
        $this->assertInstanceOf(StatePaused::class, $item->start()->getState());
        $this->assertInstanceOf(StateStopped::class, $item->stop()->getState());
        $this->assertInstanceOf(StateStopped::class, $item->start()->getState());
        $this->assertInstanceOf(StateStopped::class, $item->pause()->getState());
        $this->assertInstanceOf(StateStopped::class, $item->resume()->getState());
        $this->assertInstanceOf(StateStopped::class, $item->stop()->getState());
        $this->assertInstanceOf(StateStopped::class, $item->discard()->getState());
        $this->assertInstanceOf(StateInit::class, $item->setInitState()->getState());
        $this->assertInstanceOf(StateInit::class, $item->start()->discard()->getState());
        $this->assertInstanceOf(StateInit::class, $item->start()->pause()->discard()->getState());
        $this->assertInstanceOf(StateStopped::class, $item->start()->stop()->getState());
        $cloned = clone $item;
        $this->assertInstanceOf(StateInit::class, $cloned->getState());
    }

}
