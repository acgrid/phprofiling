<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfilingTest\Example;


use PHProfiling\Example\TaggingObserver;
use PHProfiling\Manager;

class TaggingTest extends \PHPUnit_Framework_TestCase
{

    public function testTag()
    {
        $manager = new Manager();
        $tag = new TaggingObserver('SQL');
        $this->assertSame('SQL', $tag->getTag());
        $manager->getPrototype()->attach($tag);
        $manager->namedStart('foo');
        $item = $manager->stop();
        $this->assertSame('foo', $item->getTitle());
        $this->assertSame('SQL', $item->getStatistic(TaggingObserver::class, TaggingObserver::FILED_TAG));
    }
}
