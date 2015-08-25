<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo\Core\Units;

use Jivoo\Core\UnitBase;
use Jivoo\Core\App;
use Jivoo\Core\Store\Document;
use Jivoo\Core\Cache\Cache;
use Jivoo\Models\Enum;
use Jivoo\Core\ModuleLoader;
use Jivoo\Core\LoadableModule;
use Jivoo\Helpers\Helpers;
use Jivoo\Models\Models;

/**
 * Initializes application logic such as controllers, helpers, models, etc.
 */
class AppLogicUnit extends UnitBase {
  /**
   * {@inheritdoc}
   */
  public function run(App $app, Document $config) {
    Enum::addSearchPrefix($app->n('Enums') . '\\');
    $app->m->Helpers = new Helpers($app);
    $app->m->Helpers->runInit();
    $app->m->Models = new Models($app);
    $app->m->Models->runInit();
  }
}