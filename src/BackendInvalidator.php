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

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The cache tags checksum provider.
   *
   * @var \Drupal\Core\Cache\CacheTagsChecksumInterface
   */
  protected $checksumProvider;


  protected function get_pdo_handle() {


    $db_connection_info = Database::getConnectionInfoAsUrl();
    $db_info = Database::getConnection()->getConnectionOptions();




    // @TODO: Use Drupal's connection arguments or actually pull Drupal's PDO handle.
    $dsn = 'mysql:host='. $db_info['host']. ';port='. $db_info['port'] .';dbname='. $db_info['database'];
    $options = array(\PDO::ATTR_TIMEOUT => 2, \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="ANSI_QUOTES"');
    $dbh = new \PDO($dsn, $db_info['username'], $db_info['password'], $options);
    $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $dbh;
  }

  /**
   * Constructs the BackendFactory object.
   */
  public function __construct(Connection $connection) {


    $this->connection = $connection;
    $this->checksumProvider = $checksum_provider;

    // Use the Null L1 cache for the CLI.
    $l1 = new \LCache\NullL1();
    if (php_sapi_name() !== 'cli') {
      $l1 = new \LCache\APCuL1();
    }
    $l2 = new \LCache\DatabaseL2($this->get_pdo_handle());
    $this->integrated = new \LCache\Integrated($l1, $l2);
    $this->integrated->synchronize();
  }




  /**
   * {@inheritdoc}
   */
  public function invalidateTags(array $tags) {



    $txt .= "tags \n";
    $txt .= print_r($tags, 1);

    $myfile = file_put_contents('/tmp/lcache_invalidate.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);


    foreach ($tags as $tag) {









//      if ($this->bin === 'cache_dynamic_page_cache') {


  //    }












        $this->integrated->deleteTag($tag);
    }
  }

}
