#!/usr/bin/env bash

SQL_DIR=/var/www/html/build/sql

cd /var/www/html/
app/console doctrine:database:drop --force
app/console doctrine:database:create

su - postgres -c "psql inowas_entities < "$SQL_DIR"/structure.sql"

app/console doctrine:schema:create