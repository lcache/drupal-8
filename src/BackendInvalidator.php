<?php

/**
 * @file
 * Contains \Drupal\lcache\BackendInvalidator.
 */

namespace Drupal\lcache;

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
