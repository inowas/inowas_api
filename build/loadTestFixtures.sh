#!/usr/bin/env bash

bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/TestScenarios/Scenario_1_Lake_Example --append --env=test
