#!/bin/bash

git clone $(terminus site connection-info --field=git_url) $TERMINUS_SITE
cd $TERMINUS_SITE
git checkout $TERMINUS_ENV
