# LCache

This module provides a combination L1/L2 cache using a combination
of APCu as L1 with a database as L2 (and for coherency management
among the L1 caches).

Currently only supported on Pantheon, but there's nothing that
inherently relies on anything Pantheon-specific.

Upstream library: https://github.com/lcache/lcache

## Usage

 1. Upload the module to your site.
 2. Update your Composer dependencies to ensure that the
    lcache/lcache module is available to your Drupal site.
    See: https://www.drupal.org/node/2514612
 3. Install the module (so Drupal creates the schema).
 4. Edit settings.php to make LCache the default cache class, for example:
      $settings['cache']['default'] = 'cache.backend.lcache';
 5. Configure write-heavy caches like the form cache to use a backend like
    the default database one or another write-friendly cache.
 
