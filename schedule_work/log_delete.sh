#!/bin/sh

find /home/crowdfund/httpd_log/*_log -ctime +90 -exec rm -f {} \;

find /home/crowdfund/httpd_log/*_log -ctime +0 -exec gzip -9 {} \;

exit 0

