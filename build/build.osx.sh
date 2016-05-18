# Build-Script for osX-Machine
#!/usr/bin/env bash

DIR=.
SQL_DIR=$DIR/build/sql

cd $DIR
bin/console doctrine:database:drop --force
bin/console doctrine:database:create
psql inowas < "$SQL_DIR"/structure.sql
bin/console doctrine:schema:create

bin/console doctrine:database:drop --force --env=test
bin/console doctrine:database:create --env=test
psql inowas_test < "$SQL_DIR"/structure.sql
bin/console doctrine:schema:create --env=test
