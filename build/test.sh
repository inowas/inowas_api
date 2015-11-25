#!/usr/bin/env bash

#/etc/init.d/postgresql stop
#/etc/init.d/postgresql start

DIR=/var/www/html
SQL_DIR=$DIR/build/sql

cd $DIR
app/console doctrine:database:drop --force --env=test
app/console doctrine:database:create --env=test

su - postgres -c "psql inowas_entities_test < "$SQL_DIR"/structure.sql"

app/console doctrine:schema:create --env=test

#su - postgres -c "psql inowas_entities_test < "$SQL_DIR"/raster.sql"

#DIR=/var/www/html
#SQL_DIR=$DIR/build/sql

cd $DIR

phpunit -c .