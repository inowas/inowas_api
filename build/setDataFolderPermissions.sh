#!/usr/bin/env bash

set -e

ARGS=("$@")
FOLDER=${ARGS[0]}
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`

chown ${HTTPDUSER}:${HTTPDUSER} ${FOLDER} -R
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX ${FOLDER}
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX ${FOLDER}
