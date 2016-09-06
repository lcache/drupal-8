## IMPORTANT NOTE ##

This file contains installation instructions for the 8.x-1.x version of the
Drupal LCache module.

## REQUIREMENTS ##

- PHP 5.6 or greater
- APCu 4.0

For more detailed instructions on installing a memcached daemon or either of the
memcache PECL extensions, please see the documentation online at
https://www.drupal.org/node/1131458 which includes links to external
walk-throughs for various operating systems.

## INSTALLATION ##

These are the steps you need to take in order to use this software. Order
is important.

 1. Make sure you have one of the PECL memcache packages installed.
 2. Enable the memcache module.
    You need to enable the module in Drupal before you can configure it to run
    as the default backend. This is so Drupal knows where to find everything.
 2. Edit settings.php to configure the servers, clusters and bins that memcache
    is supposed to use. You do not need to set this if the only memcache backend
    is localhost on port 11211. By default the main settings will be:
      $settings['memcache']['servers'] = ['127.0.0.1:11211' => 'default'];
      $settings['memcache']['bins'] = ['default' => 'default'];
      $settings['memcache']['key_prefix'] => '';
 7. Edit settings.php to make memcache the default cache class, for example:
      $settings['cache']['default'] = 'cache.backend.memcache';

