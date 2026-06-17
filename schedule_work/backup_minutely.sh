#!/bin/sh



dat=`date +%Y%m%d_%H%M`

base_dir="/home/crowdfund/BACKUP/dump.minutely"
new_dir=$base_dir"/"$dat

if [ -d $new_dir ]; then

	echo "duplicate_dir "$new_dir
	ls -l $base_dir

else

	mkdir $new_dir
	cd $new_dir

	arr_table=(
		"g5_point"
		"g5_withdrawal"
		"IB_FB_P2P_IP"
		"KSNET_TRADE_DATA"
		"KSNET_VR_ACCOUNT"
		"finnq_deposit_check"
	)

	for (( i = 0; i < ${#arr_table[@]}; i++ )); do
		fname="crowdfundXX.${arr_table[i]}";
		mysqldump -h211.253.30.27 -P3307 -ucrowdfundXX -p'crowdfundXX^!@#$' crowdfundXX ${arr_table[i]} -t --default-character-set=utf8 > $fname
	done

fi


dat2=`date +%Y%m%d_%H%M -d '60 minute ago'`
del_dir=$base_dir"/"$dat2

if [ -d $del_dir ]; then
	rm -Rf $drop_dir
fi

exit 0

