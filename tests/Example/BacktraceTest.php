<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfilingTest\Example;


use PHProfiling\Example\BacktraceObserver;
use PHProfiling\Item;
use PHProfiling\Manager;
use PHProfiling\Observer\FinishObserver;

class BacktraceTest extends \PHPUnit_Framework_TestCase implements FinishObserver
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     *
     */
    protected function setUp()
    {
        $item = new Item();
        $item->attach($this);
        $item->attach(new BacktraceObserver());
        $this->manager = new Manager($item);
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function onFinish(Item $item)
    {
        $this->assertArrayHasKey(BacktraceObserver::class, $item->getStatistics());
    }

    protected function innerFunction()
    {
        $trace = $this->manager->stop()->refScopeStatistic(BacktraceObserver::class);
        $this->assertSame('PHProfilingTest\Example\BacktraceTest->innerFunction',
            $trace[BacktraceObserver::FILED_STOP_POSITION][BacktraceObserver::SUB_FILED_EXPRESSION]);
    }

    public function testInFunction()
    {
        $this->manager->namedStart('1');
        $trace = $this->manager->stop()->refScopeStatistic(BacktraceObserver::class);
        $this->assertSame('PHProfilingTest\Example\BacktraceTest->testInFunction',
            $trace[BacktraceObserver::FILED_START_POSITION][BacktraceObserver::SUB_FILED_EXPRESSION]);
        $this->assertEquals($trace[BacktraceObserver::FILED_START_POSITION], $trace[BacktraceObserver::FILED_STOP_POSITION]);
        $this->manager->namedStart('2');
        $this->innerFunction();
    }

    public function testInClosure()
    {
        $finish = function(Item $item){
            $trace = $item->stop()->refScopeStatistic(BacktraceObserver::class);
            $this->assertSame('PHProfilingTest\Example\BacktraceTest->testInClosure',
                $trace[BacktraceObserver::FILED_START_POSITION][BacktraceObserver::SUB_FILED_EXPRESSION]);
            $this->assertRegExp('/closure(\$|\})/i',
                $trace[BacktraceObserver::FILED_STOP_POSITION][BacktraceObserver::SUB_FILED_EXPRESSION]);
        };
        $finish($this->manager->namedStart('3'));
    }
}
