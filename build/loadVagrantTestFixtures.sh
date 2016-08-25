#!/usr/bin/env bash

./build/build.Linux.sh

bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/TestScenarios/Scenario_1_Lake_Example --append --env=dev
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/Scenario_6_Summer_School/ --append --env=dev