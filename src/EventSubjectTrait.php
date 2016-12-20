<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo;

/**
 * {@see EventSubject} implementation.
 */
trait EventSubjectTrait
{

    /**
     * Event manager.
     *
     * @var EventManager
     */
    protected $e;

    /**
     * Attach an event handler to an event.
     *
     * @param string $name
     *            Name of event to handle.
     * @param callable $callback
     *            Function to call. Function must accept an
     *            {@see Event) as its first parameter.
     */
    public function attachEventHandler($name, callable $callback)
    {
        $this->e->attachHandler($name, $callback);
    }

    /**
     * Attach an event handler to an event (shorter alternative to
     * {@see attachEventHandler}.
     *
     * @param string $name
     *            Name of event to handle.
     * @param callable $callback
     *            Function to call. Function must accept an
     *            {@see Event) as its first parameter.
     */
    public function on($name, callable $callback)
    {
        $this->e->attachHandler($name, $callback);
    }

    /**
     * Attach an event handler to an event (shorter alternative to
     * {@see attachEventHandler}.
     * If the event is triggered more than once,
     * the handler is only invoked once.
     *
     * @param string $name
     *            Name of event to handle.
     * @param callable $callback
     *            Function to call. Function must accept an
     *            {@see Event) as its first parameter.
     */
    public function one($name, callable $callback)
    {
        $this->e->attachHandler($name, $callback, true);
    }

    /**
     * Attach an event listener to object (i.e.
     * multiple handlers to multiple
     * events).
     *
     * @param EventListener $listener
     *            An event listener.
     */
    public function attachEventListener(EventListener $listener)
    {
        $this->e->attachListener($listener);
    }

    /**
     * Detach an already attached event handler.
     *
     * @param string $name
     *            Name of event.
     * @param callable $callback
     *            Function to detach from event.
     */
    public function detachEventHandler($name, callable $callback)
    {
        $this->e->detachHandler($name, $callback);
    }

    /**
     * Detach all handlers implemented by an event listener.
     *
     * @param EventListener $listener
     *            An event listener.
     */
    public function detachEventListener(EventListener $listener)
    {
        $this->e->detachListener($listener);
    }

    /**
     * Trigger an event on this object.
     *
     * @param string $name
     *            Name of event.
     * @param Event $event
     *            Event object.
     * @return bool False if event was stopped, true otherwise.
     */
    public function triggerEvent($name, Event $event = null)
    {
        return $this->e->trigger($name, $event);
    }
}
