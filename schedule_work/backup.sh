#!/bin/sh


dat=`date +%Y%m%d`

cd /home/crowdfund/

tar zcvf BACKUP/dump.daily/hellofunding.co.kr_web_$dat.tgz public_html
find ./hellofunding.co.kr_web_*.tgz -ctime +1 -exec rm -f {} \;


cd /home/crowdfund/BACKUP/dump.daily/

mysqldump -h211.253.30.27 -P3307 -ucrowdfundXX -p'crowdfundXX^!@#$' crowdfundXX -d --default-character-set=utf8 > crowdfundXX_dump.table.dat
mysqldump -h211.253.30.27 -P3307 -ucrowdfundXX -p'crowdfundXX^!@#$' crowdfundXX -t --default-character-set=utf8 > crowdfundXX_dump.data.dat

tar zcvf crowdfundXX_db_$dat.tgz crowdfundXX_dump*

rm -f crowdfundXX_dump*
find ./*.tgz -ctime +1 -exec rm -f {} \;

exit 0

