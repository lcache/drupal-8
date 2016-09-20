#!/bin/bash

set -ex


for i in $(seq 15); do
  echo "Peformance test pass $i with LCache"
  ./../../vendor/bin/behat --config=../behat/behat-local.yml ../behat/features/create-node-view-all-nodes.feature
  sleep 1
done
