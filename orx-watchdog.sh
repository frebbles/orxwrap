#!/bin/bash
# OpenWEBRX Watchdog script
#
# If there are no connections in 30 seconds, kill openwebrx processes.

TMRFILE=/tmp/orx_watchdog_tmr
NTSDSTMP=$(expr `date +%s` + 30)
DSTMP=`date +%s`

CONNECTIONS=`netstat -n | grep ":8073 " | grep EST | wc -l`

# Check to see if OPENWEBRX is running at all...
if [ ! `ps -efh | grep -v grep | egrep "openwebrx|csdr|rtl_sdr" | wc -l` ]
then
exit
fi

if [ "0" == $CONNECTIONS ]
then
  # We mark a file if it doesnt exist with thirty seconds from now...
  if [ -f $TMRFILE ]
  then
    # Have we passed the datetimestamp and should clear the processes?
    LASTDSTMP=`cat $TMRFILE`
    if [ $LASTDSTMP -lt $DSTMP ]
      then
      killall -9 openwebrx openwebrx.py csdr rtl_sdr rtl_mus
      rm $TMRFILE
    fi
  else # Mark a future comparison datetime stamp
    echo $NTSDSTMP > $TMRFILE
  fi
else
  # We have connections, reset the timer/remove the datetimestamp file
  if [ -f $TMRFILE ]
  then 
    rm $TMRFILE
  fi
fi

