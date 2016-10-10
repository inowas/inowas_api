#!/usr/bin/env bash

/etc/init.d/postgresql stop
/etc/init.d/postgresql start

ROOTDIR=.
SQL_DIR=$ROOTDIR/build/sql

cd $ROOTDIR
bin/console doctrine:database:drop --force --env=dev
DBNAME=$(bin/console doctrine:database:create --env=dev | grep -Po '".*?"')
su postgres -c "psql $DBNAME < "$SQL_DIR"/structure.sql"
bin/console doctrine:schema:create --env=dev

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
$DIR/loadVagrantTestFixtures.sh

cd $ROOTDIR
./vendor/bin/phpunit --group integration_tests
