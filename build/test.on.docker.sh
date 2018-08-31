#!/usr/bin/env bash

bin/console doctrine:database:drop --force --env=test --if-exists
bin/console doctrine:database:create --env=test
bin/console inowas:postgis:install --env=test
bin/console doctrine:schema:create --env=test
bin/console inowas:es:schema:create --env=test
bin/console inowas:projections:reset --env=test

/usr/local/bin/composer test

