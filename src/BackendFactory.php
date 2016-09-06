<?php

/**
 * @file
 * Contains \Drupal\lcache\BackendFactory.
 */

namespace Drupal\lcache;

class BackendFactory {

  protected $integrated;

  protected function get_pdo_handle() {
    // @TODO: Use Drupal's connection arguments or actually pull Drupal's PDO handle.
    $dsn = 'mysql:host='. $_ENV['DB_HOST']. ';port='. $_ENV['DB_PORT'] .';dbname='. $_ENV['DB_NAME'];
    $options = array(\PDO::ATTR_TIMEOUT => 2, \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="ANSI_QUOTES"');
    $dbh = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $options);
    $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $dbh;
  }

  /**
   * Constructs the BackendFactory object.
   */
  public function __construct() {
    // Use the Null L1 cache for the CLI.
    $l1 = new \LCache\NullL1();
    if (php_sapi_name() !== 'cli') {
      $l1 = new \LCache\APCuL1();
    }
    $l2 = new \LCache\DatabaseL2($this->get_pdo_handle());
    $this->integrated = new \LCache\Integrated($l1, $l2);
    $this->synchronize();
  }

  /**
   * Gets an LCache Backend for the specified cache bin.
   *
   * @param $bin
   *   The cache bin for which the object is created.
   *
   * @return \Drupal\lcache\Backend
   *   The cache backend object for the specified cache bin.
   */
  public function get($bin) {
    return new Backend($bin, $this->integrated);
  }
}
