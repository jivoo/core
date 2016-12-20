<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo;

/**
 * Event subject implementation.
 */
abstract class EventSubjectBase implements EventSubject
{
    use EventSubjectTrait;
    
    /**
     * List of event names triggered by this subject.
     *
     * @var string[]
     */
    protected $events = [];

    /**
     * Construct event subject.
     * Should always be called when extending this class.
     */
    public function __construct()
    {
        $this->e = new EventManager($this);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return $this->events;
    }
}
