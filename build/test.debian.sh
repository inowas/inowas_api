#!/usr/bin/env bash

/etc/init.d/postgresql stop
/etc/init.d/postgresql start

DIR=.
SQL_DIR=$DIR/build/sql

cd $DIR
bin/console doctrine:database:drop --force --env=test
DBNAME=$(bin/console doctrine:database:create --env=test | grep -Po '".*?"')

su postgres -c "psql $DBNAME < "$SQL_DIR"/structure.sql"

bin/console doctrine:schema:create --env=test
bin/console doctrine:fixtures:load -n --fixtures=src/AppBundle/DataFixtures/ORM/TestScenarios/PropertyTypes --env=test
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/TestScenarios/Scenario_1 --append --env=test

cd $DIR

./vendor/bin/phpunit
