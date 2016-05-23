<?php
/**
 * This file is part of the acgrid/PHProfiling.
 * @package acgrid/PHProfiling
 * @link http://www.github.com/acgrid/PHProfiling
 * @copyright Copyright (c) 2016 acgrid
 * @license This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace PHProfiling;


use PHProfiling\Observer\IObserver;
use PHProfiling\State\AbstractState;
use PHProfiling\State\StateInit;
use PHProfiling\State\StatePaused;
use PHProfiling\State\StateRunning;
use PHProfiling\State\StateStopped;

class Item implements IProfilingActions
{
    /**
     * @var AbstractState
     */
    protected $state;
    /**
     * @var \SplObjectStorage
     */
    protected $observers;
    /**
     * @var array ['FQN' => Custom Data]
     */
    protected $statistics = [];
    /**
     * Set true to escape the manager storing handled instance 
     * @var bool
     */
    protected $handled = false;
    /**
     * Point to parent item in nested context
     * @var Item|null
     */
    protected $parent;

    /**
     * @var string
     */
    protected $title;

    /**
     * Item constructor.
     * @param string $title
     */
    public function __construct($title = '')
    {
        $this->observers = new \SplObjectStorage();
        $this->setInitState();
        $this->title = $title;
    }

    /**
     * Automatically restore initial state upon cloned
     */
    function __clone()
    {
        $this->setInitState();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Determine whether statistic data exists for specified scope(class) and even specific key
     * @param string $scope
     * @param string|null $key
     * @return bool
     */
    public function hasStatistics($scope, $key = null)
    {
        return isset($key) ? isset($this->statistics[$scope][$key]) : isset($this->statistics[$scope]);
    }

    /**
     * @return array
     */
    public function getStatistics()
    {
        return $this->statistics;
    }

    /**
     * @param string $scope
     * @param string $key
     * @return mixed
     */
    public function getStatistic($scope, $key)
    {
        return isset($this->statistics[$scope][$key]) ? $this->statistics[$scope][$key] : null;
    }

    /**
     * @param string $scope
     * @param string $key
     * @return $this
     */
    public function deleteStatistic($scope, $key)
    {
        if(isset($this->statistics[$scope][$key])) unset($this->statistics[$scope][$key]);
        return $this;
    }

    /**
     * @param string $scope
     * @param string $key
     * @param mixed $data
     * @return Item
     */
    public function setStatistic($scope, $key, $data)
    {
        $this->statistics[$scope][$key] = $data;
        return $this;
    }

    public function &refScopeStatistic($scope)
    {
        if(!isset($this->statistics[$scope])) $this->statistics[$scope] = [];
        return $this->statistics[$scope];
    }

    /**
     * @param string $title
     * @return Item
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return AbstractState
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param AbstractState $state
     * @return Item
     */
    public function setState(AbstractState $state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHandled()
    {
        return $this->handled;
    }

    /**
     * @param boolean $handled
     * @return Item
     */
    public function setHandled($handled)
    {
        $this->handled = (bool) $handled;
        return $this;
    }

    /**
     * @return null|Item
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param null|Item $parent
     * @return Item
     */
    public function setParent(Item $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    public function setInitState()
    {
        $this->statistics = [];
        $this->handled = false;
        $this->parent = null;
        return $this->setState(StateInit::getInstance());
    }

    public function setRunningState()
    {
        return $this->setState(StateRunning::getInstance());
    }

    public function setPausedState()
    {
        return $this->setState(StatePaused::getInstance());
    }

    public function setStoppedState()
    {
        return $this->setState(StateStopped::getInstance());
    }

    /**
     * @param IObserver $observer
     * @return $this
     */
    public function attach(IObserver $observer)
    {
        $this->observers->attach($observer);
        return $this;
    }

    /**
     * @param IObserver $observer
     * @return $this
     */
    public function detach(IObserver $observer)
    {
        $this->observers->detach($observer);
        return $this;
    }

    /**
     * @param string $interface
     * @return $this
     */
    public function notify($interface)
    {
        static $handlers;
        if(!is_array($handlers)) $handlers = [];
        $handler = isset($handlers[$interface]) ? $handlers[$interface] :
            ($handlers[$interface] = 'on' . strstr((new \ReflectionClass($interface))->getShortName(), 'Observer', true));
        foreach ($this->observers as $observer){
            /** @var IObserver $observer */
            if(method_exists($observer, $handler)) call_user_func([$observer, $handler], $this);
        }
        return $this;
    }

    /**
     * Start this profiling and collect onStart hooks.
     *
     * @return static
     */
    public function start()
    {
        return $this->state->start($this);
    }

    /**
     * Pause this profiling and notify onPause hooks
     *
     * @return static
     */
    public function pause()
    {
        return $this->state->pause($this);
    }

    /**
     * Resume this profiling and notify onResume hooks
     *
     * @return static
     */
    public function resume()
    {
        return $this->state->resume($this);
    }

    /**
     * Stop this profiling and notify onStop hooks
     *
     * @return static
     */
    public function stop()
    {
        return $this->state->stop($this);
    }

    /**
     * Discard all collected data and reset the profiling to initial state
     *
     * @return static
     */
    public function discard()
    {
        return $this->state->discard($this);
    }

}