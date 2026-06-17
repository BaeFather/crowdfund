#!/usr/local/php/bin/php -q
<?
exit;

################################################################
## 올리고 회원 투자금 입금 체크
################################################################

set_time_limit(0);

$base_path = "/home/crowdfund/public_html";
$syndi_base_path = $base_path . "/syndicate/oligo";
include_once($base_path . "/common.cli.php");
include_once($syndi_base_path . "/syndication_config.php");

$action = (@$_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : 'debug';


$limit_second = 300;

$x = true;

while($x > 0) {

	if($g5['connect_db']) {

		if($action=='debug') { debug_flush("[" . date('Y-m-d H:i:s') . "]\n"); }

		$sdate  = date("Y-m-d H:i", time()-$limit_second);
		$edate  = date("Y-m-d H:i");

		$sdate2 = preg_replace("/(-| |:)/", "", $sdate);
		$edate2 = preg_replace("/(-| |:)/", "", $edate);

		// 투자내역중 투자금입금확인이 되지 않은 투자건 추출
		$sql = "
			SELECT
				A.*
			FROM
				oligo_deposit_check A
			INNER JOIN
				cf_product_invest B  ON A.invest_idx=B.idx
			WHERE 1
				AND A.deposit='N'
				AND B.syndi_id='".$_CONF['SYNDI_ID']."'
				AND B.invest_state='Y'
				AND LEFT(A.rdate, 16) BETWEEN '".$sdate."' AND '".$edate."'
			ORDER BY
				A.idx ASC";
		if($action=='debug') { debug_flush($sql."\n"); }
		$res  = sql_query($sql);
		$rows = sql_num_rows($res);
		for($i=0; $i<$rows; $i++) {

			$CHECK_LIST = sql_fetch_array($res);

			if($CHECK_LIST['idx']) {

				$check_amount = sprintf('%.2f', $CHECK_LIST['amount']);		// 인사이드뱅크 입금레포팅 테이블에 기록되는 금액은 소숫점이하 2자리 까지 임에 주의할 것!!

				$sql2 = "
					SELECT
						ERP_TRANS_DT
					FROM
						IB_FB_P2P_IP
					WHERE 1
						AND CUST_ID='".$CHECK_LIST['member_idx']."'
						AND TR_AMT>='".$check_amount."'
						AND TR_AMT_GBN='10'
						AND LEFT(ERP_TRANS_DT, 12) BETWEEN '".$sdate2."' AND '".$edate2."'
					ORDER BY
						ERP_TRANS_DT ASC LIMIT 1";
				if($action=='debug') { debug_flush($sql2."\n"); }
				$DEPOSIT_IB = sql_fetch($sql2);

				if($DEPOSIT_IB['ERP_TRANS_DT']) {

					$check_date = substr($DEPOSIT_IB['ERP_TRANS_DT'], 0, 4).'-'.substr($DEPOSIT_IB['ERP_TRANS_DT'], 4, 2).'-'.substr($DEPOSIT_IB['ERP_TRANS_DT'], 6, 2).' '.substr($DEPOSIT_IB['ERP_TRANS_DT'], 8, 2).':'.substr($DEPOSIT_IB['ERP_TRANS_DT'], 10, 2).':'.substr($DEPOSIT_IB['ERP_TRANS_DT'], 12, 2);

					// 입금확인 플래그 등록
					$sqlx = "UPDATE oligo_deposit_check SET deposit='Y', check_date='".$check_date."' WHERE idx='".$CHECK_LIST['idx']."'";
					if($action=='debug') {
						debug_flush($sqlx."\n");
					}
					else {
						sql_query($sqlx);
					}


					// 입금알림을 띄우지 않도록 함.
					$sqlx2 = "DELETE FROM IB_deposit_notify_daylog WHERE mb_no='".$CHECK_LIST['member_idx']."' AND amount='".$CHECK_LIST['amount']."' AND LEFT(rdate, 10)='".date('Y-m-d')."'";
					if($action=='debug') {
						debug_flush($sqlx2."\n");
					}
					else {
						sql_query($sqlx2);
					}

					if($action=='debug') { echo "\n"; }

				}
			}

		}

		sql_free_result($res);

	}
	else {
		sql_close();
		break;
		exit;
	}

	sleep(10);

}

?>