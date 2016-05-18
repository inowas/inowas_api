#!/usr/bin/env bash

/etc/init.d/postgresql stop
/etc/init.d/postgresql start

DIR=.
SQL_DIR=$DIR/build/sql

cd $DIR
bin/console doctrine:database:drop --force --env=test
bin/console doctrine:database:create --env=test
su postgres -c "psql inowas_dev_test < "$SQL_DIR"/structure.sql"
bin/console doctrine:schema:create --env=test
cd $DIR
./vendor/bin/phpunit
