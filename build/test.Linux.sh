#!/usr/bin/env bash

/etc/init.d/postgresql stop
/etc/init.d/postgresql start

ROOTDIR=.
SQL_DIR=$ROOTDIR/build/sql

cd $ROOTDIR
bin/console doctrine:database:drop --force --env=test
DBNAME=$(bin/console doctrine:database:create --env=test | grep -Po '".*?"')
su postgres -c "psql $DBNAME < "$SQL_DIR"/structure.sql"
bin/console doctrine:schema:create --env=test

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
$DIR/loadTestFixtures.sh

cd $ROOTDIR
./vendor/bin/phpunit --exclude-group integration_tests
