#!/bin/bash

# Bring the code down to Circle so that modules can be added via composer.
git clone $(terminus site connection-info --field=git_url) $TERMINUS_SITE
cd $TERMINUS_SITE
git checkout $TERMINUS_ENV

#composer config repositories.drupal composer https://packages.drupal.org/8
composer config repositories.d8lcache vcs git@github.com:lcache/drupal-8.git
composer require drupal/lcache:dev-master#$CIRCLE_SHA1

# Make sure submodules are not committed.
rm -rf modules/lcache/.git/
# Make a git commit
git add .
git commit -m 'Result of build step'
git push --set-upstream origin $TERMINUS_ENV

# Instal Drupal and Enable LCache.
{
  terminus drush "si -y"
} &> /dev/null
terminus drush "en lcache -y"

# Set LCache to be the default cache service.

echo "\$settings['cache']['default'] = 'cache.backend.lcache';" >> sites/default/settings.php

git add .
git commit -m 'LCache in settings.php'
git push origin $TERMINUS_ENV
