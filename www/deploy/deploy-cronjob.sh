#!/bin/bash
script=/tmp/deploy-pipinstrasse.sh
log=/tmp/deploy-pipinstrasse.log

if [ -f $script ]; then
  grep -e '#since' >> $log
  sh -x $script >> $log 2>&1
  date '+#done %Y-%m-%d %H:%M:%S' >> $log

  rm $script
fi
