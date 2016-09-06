<?php

/**
 * @file
 * Contains \Drupal\lcache\Backend.
 */

namespace Drupal\lcache;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Defines an LCache backend.
 */
class Backend implements CacheBackendInterface {

  /**
   * The cache bin to use.
   *
   * @var string
   */
  protected $bin;

  /**
   * The LCache stack, including L1 and L2.
   *
   * @var LCache\Integrated
   */
  protected $integrated;

  /**
   * Constructs a Backend object.
   * @param string $bin
   *   The bin name.
   */
  public function __construct($bin, Integrated $integrated) {
    $this->bin = $bin;
    $this->integrated = $integrated;
  }

  protected function getAddress($cid) {
    return new \LCache\Address($this->bin, $cid);
  }

  /**
   * {@inheritdoc}
   */
  public function get($cid, $allow_invalid = FALSE) {
    $address = $this->getAddress($cid);
    $entry = $this->integrated->getEntry($address);

    if (is_null($entry)) {
      return FALSE;
    }

    $response = new stdClass();
    $response->cid = $cid;
    $response->data = $entry->value;
    $response->created = $entry->created;
    $response->expire = $entry->expiration;
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(&$cids, $allow_invalid = FALSE) {
    if (empty($cids)) return;
    $cache = array();
    foreach ($cids as $cid) {
      $c = $this->get($cid);
      if (!empty($c)) {
        $cache[$cid] = $c;
      }
    }
    return $cache;
  }

  /**
   * {@inheritdoc}
   */
  public function set($cid, $data, $expire = CacheBackendInterface::CACHE_PERMANENT, array $tags = array()) {
    assert('\Drupal\Component\Assertion\Inspector::assertAllStrings($tags)');
    $tags = array_unique($tags);
    // Sort the cache tags so that they are stored consistently.
    sort($tags);

    $address = $this->getAddress($cid);
    $ttl = NULL;
    if ($expire === CACHE_TEMPORARY) {
      $ttl = 86400;  // @TODO: Use a configurable value.
    }
    else if ($expire !== CACHE_PERMANENT) {
      $ttl = $expire - REQUEST_TIME;
    }
    $this->integrated->set($address, $data, $ttl, $tags);
  }

  /**
   * {@inheritdoc}
   */
  public function setMultiple(array $items) {
    foreach ($items as $cid => $item) {
      $item += array(
        'expire' => CacheBackendInterface::CACHE_PERMANENT,
        'tags' => array(),
      );
      $this->set($cid, $item['data'], $item['expire'], $item['tags']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function delete($cid) {
    $address = $this->getAddress($cid);
    $this->integrated->delete($address);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMultiple(array $cids) {
    foreach ($cids as $cid) {
      $this->delete($cid);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
    $this->delete(NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function invalidate($cid) {
    $this->delete($cid);
  }

  /**
   * Marks cache items as invalid.
   *
   * Invalid items may be returned in later calls to get(), if the
   * $allow_invalid argument is TRUE.
   *
   * @param array $cids
   *   An array of cache IDs to invalidate.
   *
   * @see Drupal\Core\Cache\CacheBackendInterface::deleteMultiple()
   * @see Drupal\Core\Cache\CacheBackendInterface::invalidate()
   * @see Drupal\Core\Cache\CacheBackendInterface::invalidateTags()
   * @see Drupal\Core\Cache\CacheBackendInterface::invalidateAll()
   */
  public function invalidateMultiple(array $cids) {
    foreach ($cids as $cid) {
      $this->invalidate($cid);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateAll() {
    $this->delete(NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateTags(array $tags) {
    foreach ($tags as $tag) {
      $this->integrated->deleteTag($tag);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function removeBin() {
    $this->invalidateAll();
  }

  /**
   * {@inheritdoc}
   */
  public function garbageCollection() {
    $this->integrated->collectGarbage();
  }

  /**
   * (@inheritdoc)
   */
  public function isEmpty() {
    return FALSE;
  }
}