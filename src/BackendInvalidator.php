<?php

/**
 * @file
 * Contains \Drupal\lcache\BackendFactory.
 */

namespace Drupal\lcache;

use Drupal\Core\Database\Database;
use Drupal\Core\Database\Connection;
use Drupal\Core\Cache\CacheTagsChecksumInterface;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;

class BackendInvalidator implements CacheTagsInvalidatorInterface {

  protected $integrated;

  public function __construct(\LCache\Integrated $integrated) {
    $this->integrated = $integrated;
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateTags(array $tags) {
    foreach ($tags as $tag) {
        $this->integrated->deleteTag($tag);
    }
  }

}
