#!/usr/bin/env bash

DIR=/var/www/html
cd $DIR

./build/build.sh
app/console doctrine:fixtures:load -n
