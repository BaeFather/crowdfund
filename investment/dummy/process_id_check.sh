#!/bin/bash

check_process=/home/crowdfund/public_html/investment/dummy/invest.sh

if [ $1 ]; then
	check_process="$check_process $1"
fi

if [ $2 ]; then
	check_process="$check_process $2"
fi

if [ $3 ]; then
	check_process="$check_process $3"
fi

#echo $check_process

ps -ax | grep -v grep | grep "$check_process" | awk '{print $1}'

exit 0

