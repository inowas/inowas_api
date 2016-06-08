#!/usr/bin/env bash

DIR=.
SQL_DIR=$DIR/build/sql

cd $DIR
bin/console doctrine:database:drop --force --env=test
bin/console doctrine:database:create --env=test
psql inowas_test < "$SQL_DIR"/structure.sql
bin/console doctrine:schema:create --env=test
bin/console doctrine:fixtures:load -n --fixtures=src/AppBundle/DataFixtures/ORM/TestScenarios/PropertyTypes --env=test
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/TestScenarios/Scenario_1_Lake_example --append --env=test
cd $DIR
./vendor/bin/phpunit --stop-on-error