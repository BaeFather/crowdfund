#!/bin/sh

check_service=lguplus-uagent

process_count=$(ps -ef | grep $check_service | grep -v grep | wc -l)

if [ $process_count -eq 0 ];then
        /usr/local/lguplus/LGUPlus_Agent_2.2.2/bin/uagent.sh start;
else
        echo "[SMS_AGENT] process already exist!!";
fi
