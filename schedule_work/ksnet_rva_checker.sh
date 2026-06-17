#!/bin/sh

check_service=KSNET_RVA_SERVICE

process_count=$(ps -ef | grep $check_service | grep -v grep | wc -l)

if [ $process_count -eq 0 ];then
	/home/crowdfund/KSNET/RVA_P2P/RVA_unix_start.sh &
else
	echo ""
fi

exit 0
