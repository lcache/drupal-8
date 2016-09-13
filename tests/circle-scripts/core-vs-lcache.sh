#!/bin/bash

set -ex

# Create a drush alias file so that Behat tests can be executed against Pantheon.
terminus sites aliases
# Drush Behat driver fails without this option.
echo "\$options['strict'] = 0;" >> ~/.drush/pantheon.aliases.drushrc.php
export BEHAT_PARAMS='{"extensions" : {"Behat\\MinkExtension" : {"base_url" : "http://'$TERMINUS_ENV'-'$TERMINUS_SITE'.pantheonsite.io/"}, "Drupal\\DrupalExtension" : {"drush" :   {  "alias":  "@pantheon.'$TERMINUS_SITE'.'$TERMINUS_ENV'" }}}}'
terminus site set-connection-mode  --mode=git

cd ${TERMINUS_SITE}
git checkout master
git branch -D $TERMINUS_ENV
git pull
git checkout -b $TERMINUS_ENV

echo "php_version: 7.0 " >> pantheon.yml

git add pantheon.yml
git commit -m 'Clean install of Drupal Core'
git push  --set-upstream origin $TERMINUS_ENV -f

sleep 30
{
  terminus drush "si -y"
} &> /dev/null
terminus site clear-cache

./../../../vendor/bin/behat --config=../../behat/behat-pantheon.yml ../../behat/features/
sleep 15

# Set up LCache repo
#composer config repositories.drupal composer https://packages.drupal.org/8
composer config repositories.d8lcache vcs git@github.com:lcache/drupal-8.git
composer require drupal/lcache:dev-master#$CIRCLE_SHA1

# A .git directory might in modules/lcache/
git add modules/lcache/*
git add .
git commit -m 'Adding LCache'
git push

sleep 60
{
  terminus drush "si -y"
} &> /dev/null
terminus drush "en lcache -y"
terminus site clear-cache

echo "\$settings['cache']['default'] = 'cache.backend.lcache';" >> sites/default/settings.php
git add .
git commit -m 'LCache in settings.php'
git push origin $TERMINUS_ENV

./../../../vendor/bin/behat --config=../../behat/behat-pantheon.yml ../../behat/features/

terminus site clear-cache
