#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
unamestr=`uname`

if [[ "$unamestr" == 'Linux' ]]; then
    $DIR/build.debian.sh
fi

bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/PropertyTypes --append
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/TestScenarios/Scenario_1_Lake_Example --append
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/Scenario_1/ --append
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/Scenario_2/ --append
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/Scenario_3/ --append
bin/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/ORM/Scenarios/Scenario_4/ --append
