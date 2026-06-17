#!/bin/bash

check_service=/home/crowdfund/public_html/syndicate/oligo/scheduler/oligo_after_invest.cli.php

process_count=$(ps -ef | grep $check_service | grep -v grep | wc -l)

if [ $process_count -gt 0 ]; then
	echo "[oligo_after_invest] process already exist!!";
else
	nohup /usr/local/php/bin/php -q $check_service yes > /dev/null 2>&1 &
fi

exit
