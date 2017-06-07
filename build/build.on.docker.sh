#!/usr/bin/env bash

# Set folder permissions
rm -rf var/cache/*
rm -rf var/logs/*
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var

bin/console doctrine:database:drop --force --env=prod --if-exists
bin/console doctrine:database:create --env=prod
bin/console inowas:postgis:install --env=prod
bin/console doctrine:schema:create --env=prod
bin/console inowas:es:schema:create --env=prod
bin/console inowas:projections:reset --env=prod
