#!/usr/bin/env bash

/etc/init.d/postgresql stop
/etc/init.d/postgresql start

bin/console doctrine:database:drop --force --env=test
bin/console inowas:postgis:install --env=test
bin/console doctrine:schema:create --env=test
bin/console inowas:es:schema:create --env=test
bin/console inowas:projections:reset --env=test
bin/phpunit
