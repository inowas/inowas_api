#!/bin/bash

set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd $DIR/../
php bin/console inowas:es:schema:create
php bin/console inowas:es:truncate
php bin/console inowas:projections:reset
php bin/console inowas:es:migrate 1
