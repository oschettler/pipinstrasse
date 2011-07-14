#!/bin/bash

# sudo mkdir /var/deploy
# sudo chown olav /var/deploy

script=/var/deploy/pipinstrasse.sh
log=/var/deploy/pipinstrasse.log

if [ -f $script ]; then
  grep -e '#since' $script >> $log
  sh -x $script >> $log 2>&1
  date '+#done %Y-%m-%d %H:%M:%S' >> $log

  rm $script
fi
