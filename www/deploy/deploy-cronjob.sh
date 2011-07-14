#!/bin/bash

# sudo mkdir /var/deploy
# sudo chown olav /var/deploy
# sudo chmod 4711 /var/deploy
# ls -la /var/deploy/
##drws--x--x  2 olav root 4096 2011-07-14 11:48 .

script=/var/deploy/pipinstrasse.sh
log=/var/deploy/pipinstrasse.log

if [ -f $script ]; then
  grep -e '#since' $script >> $log
  sh -x $script >> $log 2>&1
  date '+#done %Y-%m-%d %H:%M:%S' >> $log

  rm $script
fi
