#!/usr/bin/env bash

set -e

OS=$(lsb_release -si);

if [ $OS=="Debian" ]; then

    DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
    cd $DIR
    
    sudo apt-get install -y supervisor
    service supervisor stop

    SUPERVISOR_CONF_DIR="/etc/supervisor/conf.d"

    rm -rf *.conf

    for filename in inowas_*.sh; do
        echo $filename
        programname=$(sed 's/\.[^.]*$//' <<< "$filename")
        configfilename=$programname.conf
        echo "[program:$programname]" > $configfilename
        echo "command=$DIR/$filename" >> $configfilename
        echo "stopsignal=KILL" >> $configfilename
        echo "killasgroup=true" >> $configfilename
        echo "autostart=true" >> $configfilename
        echo "autorestart=true" >> $configfilename
        echo "stderr_logfile=/var/log/supervisor/$programname.err.log" >> $configfilename
        echo "stdout_logfile=/var/log/supervisor/$programname.out.log" >> $configfilename

        if [ -L $SUPERVISOR_CONF_DIR/$configfilename ]; then
            rm -rf $SUPERVISOR_CONF_DIR/$configfilename
        fi

        ln -s $DIR/$configfilename $SUPERVISOR_CONF_DIR/$configfilename
    done

    sleep 5s

    service supervisor start
fi
