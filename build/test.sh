#!/usr/bin/env bash

DIR=/var/www/html
SQL_DIR=$DIR/build/sql

cd $DIR

./build/build.sh
phpunit -c .