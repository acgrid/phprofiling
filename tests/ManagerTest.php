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
use PHProfiling\Manager;
use PHProfiling\Observer\FinishObserver;
use PHProfiling\State\StateRunning;

class ManagerTest extends \PHPUnit_Framework_TestCase implements FinishObserver
{
    /**
     * @param Item $item
     * @return mixed
     */
    public function onFinish(Item $item)
    {
        echo 'onFinish';
    }

    public function testNested()
    {
        $manager = new Manager();
        $a = $manager->namedStart('A');
        $b = $manager->namedStart('B');
        $this->assertNull($a->getParent());
        $this->assertSame($a, $b->getParent());
        $this->assertSame($b, $manager->stop());
        $c = $manager->namedStart('C');
        $this->assertSame($a, $c->getParent());
        $this->assertSame($c, $manager->stop());
        $this->assertSame($a, $manager->stop());
        $this->assertSame($b, $manager->getIterator()->offsetGet(0));
        $this->assertSame($c, $manager->getIterator()->offsetGet(1));
        $this->assertSame($a, $manager->getIterator()->offsetGet(2));
    }

    public function testSimple()
    {
        $this->expectOutputString("onFinish\nonFinish");
        $item = new Item();
        $item->attach($this);
        $manager = new Manager(new Item('N'));
        $this->assertSame('N', $manager->getPrototype()->getTitle());
        $this->assertSame($manager, $manager->setPrototype($item));
        $this->assertSame($item, $manager->getPrototype());
        $this->assertInstanceOf(\SplQueue::class, $manager->getIterator());
        $this->assertNull($manager->top());
        foreach($manager as $item) $this->fail('Should be empty iteration now: ' . gettype($item));
        $newItem = $manager->create('1');
        $this->assertInstanceOf(Item::class, $newItem);
        $this->assertSame('1', $newItem->getTitle());
        $this->assertNotSame($item, $newItem);
        $this->assertSame($newItem, $manager->start());
        $this->assertInstanceOf(StateRunning::class, $newItem->getState());
        $newItem->stop();
        echo "\n";
        $this->assertNotSame($newItem, $newItem2 = $manager->namedStart('2'));
        $this->assertCount(1, $manager);
        $this->assertSame('2', $newItem2->getTitle());
        $this->assertSame($newItem2, $manager->stop());
        $this->assertCount(2, $manager);
        $item->detach($this);
        $this->assertSame('3', $manager->start()->setTitle('3')->setHandled(true)->stop()->getTitle());
        $this->assertCount(2, $manager);
    }
}
