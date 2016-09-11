<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo\Store;

/**
 * Stores data in PHP sessions.
 */
class PhpSessionStore implements Store
{

    /**
     * Whether or not session is open.
     *
     * @var bool
     */
    private $open = false;

    /**
     * Whether or not session is mutable.
     *
     * @var bool
     */
    private $mutable = false;

    /**
     * Session subkey.
     *
     * @var string
     */
    public $key = null;

    /**
     * Session cookie name.
     *
     * @var string
     */
    public $name = null;

    /**
     * Whether to enable Secure flag on session cookie.
     *
     * @var bool
     */
    public $secure = false;

    /**
     * Whether to enable HttpOnly flag on session cookie.
     *
     * @var bool
     */
    public $httpOnly = true;

    /**
     * Session cookie path.
     *
     * @var string
     */
    public $path = '/';

    /**
     * Session cookie domain.
     *
     * @var string
     */
    public $domain = '';
    
    /**
     * @var array
     */
    private $data = null;

    /**
     * {@inheritdoc}
     */
    public function open($mutable = false)
    {
        $params = session_get_cookie_params();
        session_set_cookie_params(
            $params['lifetime'],
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnly
        );
        if (isset($this->name)) {
            session_name($this->name);
        }
        if (! session_start()) {
            throw new AccessException('Could not start PHP session');
        }
        $this->open = true;
        $this->mutable = $mutable;
        if (isset($this->key)) {
            $this->data = array();
            if (isset($_SESSION[$this->key])) {
                $this->data = $_SESSION[$this->key];
            }
        } else {
            $this->data = $_SESSION;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (! $this->open) {
            return;
        }
        session_write_close();
        $this->open = false;
        $this->mutable = false;
        $this->data = null;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (isset($this->data)) {
            return $this->data;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $data)
    {
        if (! $this->open) {
            return;
        }
        if (! $this->mutable) {
            throw new AccessException('Not mutable');
        }
        $this->data = $data;
        if (isset($this->key)) {
            $_SESSION[$this->key] = $this->data;
        } else {
            $_SESSION = $this->data;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isOpen()
    {
        return $this->open;
    }

    /**
     * {@inheritdoc}
     */
    public function isMutable()
    {
        return $this->mutable;
    }
}
