# Build script for debian machine
#!/usr/bin/env bash

/etc/init.d/postgresql stop
/etc/init.d/postgresql start

DIR=.
SQL_DIR=$DIR/build/sql

cd $DIR
bin/console doctrine:database:drop --force
DBNAME=$(bin/console doctrine:database:create | grep -Po '".*?"')

su postgres -c "psql $DBNAME < "$SQL_DIR"/structure.sql"

bin/console doctrine:schema:create