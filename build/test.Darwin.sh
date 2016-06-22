#!/usr/bin/env bash

ROOTDIR=.
SQL_DIR=$ROOTDIR/build/sql

cd $ROOTDIR
bin/console doctrine:database:drop --force --env=test
bin/console doctrine:database:create --env=test
psql inowas_test < "$SQL_DIR"/structure.sql
bin/console doctrine:schema:create --env=test

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
$DIR/loadTestFixtures.sh

cd $ROOTDIR
./vendor/bin/phpunit