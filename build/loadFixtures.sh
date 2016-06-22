#!/usr/bin/env bash

bin/console doctrine:fixtures:load -n --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/PropertyTypes
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/TestScenarios/Scenario_1_Lake_Example --append
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/Scenario_1/ --append
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/Scenario_2/ --append
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/Scenario_3/ --append
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/Scenario_4/ --append