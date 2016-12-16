#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
unamestr=`uname`
$DIR/build.$unamestr.sh

#bin/console doctrine:fixtures:load --fixtures=src/Inowas/AppBundle/DataFixtures/Modflow/Hanoi -n
bin/console doctrine:fixtures:load --fixtures=src/Inowas/AppBundle/DataFixtures/Modflow/RioPrimero -n
