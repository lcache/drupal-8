## Requirements

- PHP 5.6+
- APCu 4.0.11+

For more detailed instructions on installing a memcached daemon or either of the
memcache PECL extensions, please see the documentation online at
https://www.drupal.org/node/1131458 which includes links to external
walk-throughs for various operating systems.

## Installation

These are the steps you need to take in order to use this software. Order
is important.

 1. Make sure you have PECL APCu installed and configured to have enough memory.
 2. Enable the LCache module. This will install the necessary schema.
 3. Edit settings.php to make memcache the default cache class, for example:
      $settings['cache']['default'] = 'cache.backend.lcache';
 4. Configure certain caches, like for forms, to use the standard database cache.
 
