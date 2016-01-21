<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo;

/**
 * An event.
 */
class Event
{

    /**
     * Has event been stopped.
     *
     * @var bool
     */
    public $stopped = false;

    /**
     * Name of event.
     *
     * @var string|null
     */
    public $name = null;

    /**
     * Sender of event.
     *
     * @var object|null
     */
    public $sender = null;

    /**
     * Event parameters.
     *
     * @var array
     */
    public $parameters = array();

    /**
     * Construct event.
     *
     * @param object|null $sender
     *            Sender of event.
     * @param array $parameters
     *            Additional event paramters.
     */
    public function __construct($sender = null, $parameters = array())
    {
        $this->sender = $sender;
        $this->parameters = $parameters;
    }

    /**
     * Stop propagation of event.
     */
    public function stopPropagation()
    {
        $this->stopped = true;
    }
}
