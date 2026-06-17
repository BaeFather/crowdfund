#!/bin/sh

check_service=KSNET_RFB_SERVICE

process_count=$(ps -ef | grep $check_service | grep -v grep | wc -l)

if [ $process_count -eq 0 ];then
	/home/crowdfund/KSNET/RFB_1.0/RFB_unix_start.sh &
else
	echo ""
fi

exit 0
