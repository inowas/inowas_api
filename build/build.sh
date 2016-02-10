#!/usr/bin/env bash

/etc/init.d/postgresql stop
/etc/init.d/postgresql start

DIR=/var/www/html
SQL_DIR=$DIR/build/sql

cd $DIR
bin/console doctrine:database:drop --force
bin/console doctrine:database:create

su - postgres -c "psql inowas_entities < "$SQL_DIR"/structure.sql"

bin/console doctrine:schema:create

#su - postgres -c "psql inowas_entities < "$SQL_DIR"/raster.sql"