<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo\Store;

/**
 * A configuration is a document used primarily for reading (but with support
 * for the occasional write as well).
 *
 * Unlike {@see State}, the associated {@see Store} is
 * only exclusively locked while writing, so the same configuration can be
 * opened multiple times in by different instances. However it does not ensure
 * durability, since changes made in one instance can be overwritten by changes
 * made in another.
 */
class Config extends Document
{

    /**
     * @var Store
     */
    private $store = null;

    /**
     * {@inheritdoc}
     */
    protected $saveDefaults = true;

    /**
     * Construct conifguration.
     *
     * @param Store $store
     *            Optional store to load/save data from/to.
     * @throws AccessException If file could not be read.
     */
    public function __construct(Store $store = null)
    {
        parent::__construct();
        if (isset($store)) {
            $this->store = $store;
            $this->reload();
        }
    }

    /**
     * Reload configuration document from store.
     * @throws AccessException If file could not be read.
     */
    public function reload()
    {
        if ($this->root !== $this) {
            $this->root->reload();
            return;
        }
        if (! isset($this->store)) {
            return;
        }
        if (! $this->store->touch()) {
            return;
        }
        try {
            $this->store->open(false);
            $this->data = $this->store->read();
        } catch (AccessException $e) {
            throw new AccessException('Could not read configration: ' . $e->getMessage(), null, $e);
        }
        $this->store->close();
    }

    /**
     * Save configuration.
     * If this is not the root configuration, the root configuration will be
     * saved instead.
     *
     * @return boolean True if the configuration was saved.
     */
    public function save()
    {
        if ($this->root !== $this) {
            return $this->root->save();
        }
        if (! isset($this->store)) {
            return false;
        }
        if (! $this->updated) {
            return true;
        }
        $this->store->open(true);
        $this->store->write($this->data);
        $this->store->close();
        $this->updated = false;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function createEmpty()
    {
        return new Config();
    }
}
