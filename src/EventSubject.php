<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo;

/**
 * A producer of events.
 * Handlers and listeners can be attached.
 */
interface EventSubject
{

    /**
     * Attach an event handler to an event.
     *
     * @param string $name
     *            Name of event to handle.
     * @param callable $callback
     *            Function to call. Function must accept an
     *            {@see Event) as its first parameter.
     */
    public function attachEventHandler($name, callable $callback);

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
    public function on($name, callable $callback);

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
    public function one($name, callable $callback);

    /**
     * Attach an event listener to object (i.e.
     * multiple handlers to multiple
     * events).
     *
     * @param EventListener $listener
     *            An event listener.
     */
    public function attachEventListener(EventListener $listener);

    /**
     * Detach an already attached event handler.
     *
     * @param string $name
     *            Name of event.
     * @param callable $callback
     *            Function to detach from event.
     */
    public function detachEventHandler($name, callable $callback);

    /**
     * Detach all handlers implemented by an event listener.
     *
     * @param EventListener $listener
     *            An event listener.
     */
    public function detachEventListener(EventListener $listener);

    /**
     * Get names of all events produced by object.
     *
     * @return string[] List of event names.
     */
    public function getEvents();
}
