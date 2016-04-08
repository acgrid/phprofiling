<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfiling;


use PHProfiling\State\StateInit;
use PHProfiling\State\StateStopped;
use Traversable;

class Manager implements IProfilingActions, \IteratorAggregate
{
    /**
     * @var \SplStack
     */
    protected $items;
    /**
     * @var \SplQueue
     */
    protected $handled;
    /**
     * @var Item
     */
    protected $prototype;

    /**
     * Manager constructor.
     * @param Item|null $prototype
     */
    public function __construct(Item $prototype = null)
    {
        $this->items = new \SplStack();
        $this->handled = new \SplQueue();
        if(isset($prototype)){
            $this->setPrototype($prototype);
        }else{
            $this->setPrototype(new Item());
        }
    }

    /**
     * @return Item
     */
    public function getPrototype()
    {
        return $this->prototype;
    }

    /**
     * @param Item $prototype
     * @return Manager
     */
    public function setPrototype(Item $prototype)
    {
        $this->prototype = $prototype;
        return $this;
    }

    /**
     * @return \SplQueue
     */
    public function getIterator()
    {
        return $this->handled;
    }

    protected function handle(Item $item)
    {
        if(!$item->isHandled()) $this->handled->enqueue($item);
    }

    /**
     * @param bool $pop
     * @return Item|null
     */
    public function top($pop = false)
    {
        if($this->items->isEmpty()) return null;
        if($pop) return $this->items->pop();
        while($top = $this->items->top()){
            /** @var Item $top */
            // WARNING Non-parallel code for the stack may be modified by other code
            if($top->getState() instanceof StateStopped){
                $this->handle($this->items->pop());
                if($this->items->isEmpty()) return null;
            }else{
                return $top;
            }
        }
        return null;
    }

    /**
     * @param string $title
     * @return Item
     */
    public function create($title = '')
    {
        $instance = clone $this->prototype; // Shallow copy deliberately
        $instance->setTitle($title);
        $this->items->push($instance);
        return $instance;
    }

    /**
     * @param string $title
     * @return Item
     */
    public function namedStart($title = '')
    {
        if(($top = $this->top()) && ($top->getState() instanceof StateInit)) return $top->setTitle($title)->start();
        return $this->create($title)->start();
    }
    
    /**
     * @return Item
     */
    public function start()
    {
        if(($top = $this->top()) && ($top->getState() instanceof StateInit)) return $top->start();
        return $this->create()->start();
    }

    /**
     * @return Item
     */
    public function pause()
    {
        if($top = $this->top()) $top->pause();
        return $top;
    }

    /**
     * @return Item
     */
    public function resume()
    {
        if($top = $this->top()) $top->resume();
        return $top;
    }

    /**
     * @return Item
     */
    public function stop()
    {
        if($top = $this->top(true)) $this->handle($top->stop());
        return $top;
    }

    /**
     * @return Item
     */
    public function discard()
    {
        if($top = $this->top()) $top->discard();
        return $top;
    }
}