# LCache

This module provides a combination L1/L2 cache using a combination
of APCu as L1 with a database as L2 (and for coherency management
among the L1 caches).

Currently only supported on Pantheon, but there's nothing that
inherently relies on anything Pantheon-specific.

Upstream library: https://github.com/lcache/lcache

## Usage

 1. Upload the module to your site.
    a. If using Composer to manage your sites modules:  
       composer require drupal/lcache [* TBD]
    b. If not using Composer:
       cd sites/all/modules
       git clone git@github.com:lcache/drupal-8.git lcache
       lcache
       composer install
 2. Install the module (so Drupal creates the schema).
 3. Edit settings.php to make memcache the default cache class, for example:
      $settings['cache']['default'] = 'cache.backend.lcache';
