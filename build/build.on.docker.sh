#!/usr/bin/env bash

bin/console doctrine:database:drop --force --env=prod --if-exists
bin/console doctrine:database:create --env=prod
bin/console inowas:postgis:install --env=prod
bin/console doctrine:schema:create --env=prod
bin/console inowas:es:schema:create --env=prod
bin/console inowas:projections:reset --env=prod
