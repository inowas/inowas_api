#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

/usr/bin/python3 -u $DIR/../py/pyModelling/inowas.flopy.read_data.rpc.server.py $1
