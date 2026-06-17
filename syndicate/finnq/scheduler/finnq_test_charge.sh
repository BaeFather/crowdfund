#!/bin/sh

check_service=/home/crowdfund/public_html/syndicate/finnq/scheduler/finnq_test_charge.php

process_count=$(ps -ef | grep $check_service | grep -v grep | wc -l)

if [ $process_count -eq 0 ];then
	/usr/local/php/bin/php -c /usr/local/php/etc/php.ini -q $check_service yes;
else
	echo "[finnq_test_charge] process already exist!!";
fi

exit 0
