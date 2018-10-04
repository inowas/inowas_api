#!/usr/bin/env bash

set -e

ARGS=("$@")
FOLDER=${ARGS[0]}
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`

echo "Set permissions for user ${HTTPDUSER} at folder ${FOLDER}"
chown ${HTTPDUSER}:${HTTPDUSER} ${FOLDER} -R
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX ${FOLDER}
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX ${FOLDER}

echo "You're running php version:"
php -v

echo "Start php-fpm"
php-fpm -F -R
