<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfilingTest\Example;


use PHProfiling\Example\TimingObserver;
use PHProfiling\Item;
use PHProfiling\Manager;

class TimingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Manager
     */
    protected $manager;

    protected function setUp()
    {
        $prototype = new Item();
        $prototype->attach(new TimingObserver());
        $this->manager = new Manager($prototype);
    }

    public function testTiming()
    {
        $this->manager->namedStart('test');
        usleep(500000);
        $this->manager->pause();
        usleep(750000);
        $this->manager->resume();
        usleep(500000);
        $item = $this->manager->stop();
        $this->assertEquals(1, $item->getStatistic(TimingObserver::class, TimingObserver::FILED_RUNTIME),
            'Time is out of tolerance, check delta first.', 0.1);
        $this->manager->namedStart('test2');
        usleep(300000);
        $this->manager->pause();
        usleep(700000);
        $item = $this->manager->stop();
        $this->assertEquals(0.3, $item->getStatistic(TimingObserver::class, TimingObserver::FILED_RUNTIME),
            'Time is out of tolerance, check delta first.', 0.1);
	    $this->assertEquals(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], TimingObserver::getGlobalT(), 'Global time is out of tolerance, check delta first.', 0.1);
    }
}
