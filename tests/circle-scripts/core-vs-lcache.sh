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



# Make a custom admin user because the normal Drush/Behat way of creating users
# 1) is slow and 2) would require rerunning with each Behat run and 3)
# would result nodes getting deleted at the end of each run.
{
 terminus drush "user-create $DRUPAL_ADMIN_USERNAME"
 terminus drush "user-add-role  administrator $DRUPAL_ADMIN_USERNAME"
 terminus drush "upwd $DRUPAL_ADMIN_USERNAME  --password=$DRUPAL_ADMIN_PASSWORD"
} &> /dev/null


for i in $(seq 50); do
  echo "Peformance test pass $i with Core"
  ./../../../vendor/bin/behat --config=../../behat/behat-pantheon.yml ../../behat/features/create-node-view-all-nodes.feature
done




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

# echo "\$settings['cache']['default'] = 'cache.backend.lcache';" >> sites/default/settings.php

# Swap services for the bins otherwise using ChainedFastBackend
#echo "\$settings['cache']['bins']['bootstrap'] = 'cache.backend.lcache';" >> sites/default/settings.php
#echo "\$settings['cache']['bins']['config']    = 'cache.backend.lcache';" >> sites/default/settings.php
#echo "\$settings['cache']['bins']['discovery'] = 'cache.backend.lcache';" >> sites/default/settings.php

# Swap services for render, and dynamic page cache.
#echo "\$settings['cache']['bins']['render']             = 'cache.backend.lcache';" >> sites/default/settings.php
#echo "\$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.lcache';" >> sites/default/settings.php
 echo "\$settings['cache']['bins']['toolbar']            = 'cache.backend.lcache';" >> sites/default/settings.php
# echo "\$settings['cache']['bins']['menu']               = 'cache.backend.lcache';" >> sites/default/settings.php
#echo "\$settings['cache']['bins']['entity']             = 'cache.backend.lcache';" >> sites/default/settings.php#
# echo "\$settings['cache']['bins']['data']               = 'cache.backend.lcache';" >> sites/default/settings.php

git add .
git commit -m 'LCache for toolbar bin'
git push origin $TERMINUS_ENV


# Make a custom admin user because the normal Drush/Behat way of creating users
# 1) is slow and 2) would require rerunning with each Behat run and 3)
# would result nodes getting deleted at the end of each run.
{
 terminus drush "user-create $DRUPAL_ADMIN_USERNAME"
 terminus drush "user-add-role  administrator $DRUPAL_ADMIN_USERNAME"
 terminus drush "upwd $DRUPAL_ADMIN_USERNAME  --password=$DRUPAL_ADMIN_PASSWORD"
} &> /dev/null


for i in $(seq 50); do
  echo "Peformance test pass $i with LCache"
  ./../../../vendor/bin/behat --config=../../behat/behat-pantheon.yml ../../behat/features/create-node-view-all-nodes.feature
done


terminus site clear-cache
