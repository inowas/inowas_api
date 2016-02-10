#!/usr/bin/env bash

DIR=/var/www/html
cd $DIR

./build/build.sh
bin/console doctrine:fixtures:load -n
