#!/bin/sh

check_service=/home/crowdfund/public_html/syndicate/finnq/scheduler/finnq_deposit_check.php

process_count=$(ps -ef | grep $check_service | grep -v grep | wc -l)

if [ $process_count -eq 0 ];then
	/usr/local/php/bin/php -c /usr/local/php/etc/php.ini -q $check_service yes;
else
	echo "[finnq_deposit_checker] process already exist!!";
fi

exit 0
