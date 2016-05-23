<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfilingTest\Example;


use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHProfiling\Example\MonologOutput;
use PHProfiling\Example\TaggingObserver;
use PHProfiling\Item;
use PHProfiling\Manager;

class MonologOutputTest extends \PHPUnit_Framework_TestCase
{

    public function testBasic()
    {
        $this->expectOutputRegex('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}] test\.DEBUG: 1 \{"PHProfiling\\\\\\\\Example\\\\\\\\TaggingObserver":\{"tag":"EX"\}\} \[\]/');
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler('php://output'));
        $prototype = new Item();
        $prototype->attach(new MonologOutput($logger, Logger::DEBUG));
        $prototype->attach(new TaggingObserver('EX'));
        $manager = new Manager($prototype);
        $manager->namedStart('1');
        $this->assertTrue($manager->stop()->isHandled());
    }

}
