#!/bin/sh



dat=`date +%Y%m%d_%H -d '1 hour ago'`

baseDir="/home/crowdfund/BACKUP/dump.hourly"
newDir=$baseDir"/"$dat

if [ -d $newDir ]; then

  echo "duplicate_dir "$newDir
  ls -l $baseDir

else

  mkdir $newDir
  cd $newDir

  arr_table=(
		"cf_product"
		"cf_product_give"
		"cf_product_invest"
		"cf_product_invest_detail"
		"cf_product_success"
		"g5_member"
		"g5_member_drop"
		"g5_point"
		"g5_withdrawal"
		"IB_deal_daylog"
		"IB_FB_P2P_DC_IP"
		"IB_FB_P2P_IP"
		"IB_FB_P2P_REPAY_REQ"
		"IB_FB_P2P_REPAY_REQ_DETAIL"
		"IB_FB_P2P_REPAY_REQ_ready"
		"IB_make_fbseq"
		"IB_request_log"
		"IB_vact"
		"IB_vact_hellocrowd"
		"KSNET_TRADE_DATA"
		"KSNET_TRADE_ERR"
		"KSNET_VR_ACCOUNT"
		"TaxinvoiceLog"
	)

  for (( i = 0; i < ${#arr_table[@]}; i++ )); do
    fname="crowdfundXX.${arr_table[i]}";
    mysqldump -h211.253.30.27 -P3307 -ucrowdfundXX -p'crowdfundXX^!@#$' crowdfundXX ${arr_table[i]} -t --default-character-set=utf8 > $fname
  done

  cd $baseDir

  tar zcvf $dat.tgz $dat
  rm -Rf $dat

  dat2=`date +%Y%m%d_%H -d '25 hour ago'`
  dropFile=$dat2.tgz

  if [ -f $dropFile ]; then
    rm -f $dropFile
  fi

fi

exit 0
