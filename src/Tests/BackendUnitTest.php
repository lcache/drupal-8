<?php

/**
 * @file
 * Contains \Drupal\lcache\Tests\BackendUnitTest.
 */

namespace Drupal\lcache\Tests;

use Drupal\lcache\BackendFactory;
use Drupal\system\Tests\Cache\GenericCacheBackendUnitTestBase;

/**
 * Tests the LCache Backend.
 *
 * @group lcache
 */
class BackendUnitTest extends GenericCacheBackendUnitTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['system', 'lcache'];

  /**
   * Creates a new instance of an LCache Backend.
   *
   * @return \Drupal\lcache\Backend
   *   A new LCache Backend object.
   */
  protected function createCacheBackend($bin) {
    $factory = new BackendFactory();
    return $factory->get($bin);
  }
}
