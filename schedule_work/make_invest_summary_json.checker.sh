#!/bin/bash

check_service=/home/crowdfund/public_html/investment/make_invest_summary_json.php

process_count=$(ps -ef| grep $check_service | grep -v grep | wc -l)

if [ $process_count -gt 0 ]; then
	echo "[investor_deposit_checker] process already exist!!";
else
	/usr/local/php/bin/php -q $check_service > /dev/null 2>&1
fi

exit
