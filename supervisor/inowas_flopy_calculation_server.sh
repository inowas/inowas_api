#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

/usr/bin/python3 -u $DIR/../py/pyModelling/inowas.flopy.calculation.server.py /tmp/data
