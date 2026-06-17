#!/usr/local/php/bin/php -q
<?
exit;

###############################################################################
## 핀크 투자발생시 가상의 입금내역 등록
###############################################################################
## ★★★★ 실서비스에서는 실행금지함 ★★★★
## * * * * * /home/crowdfund/public_html/syndicate/oligo/scheduler/oligo_test_charge.sh &
###############################################################################

set_time_limit(0);

$base_path = "/home/crowdfund/public_html";
$syndi_base_path = $base_path . "/syndicate/oligo";
include_once($base_path . "/common.cli.php");
include_once($syndi_base_path . "/syndication_config.php");


$action = (@$_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : 'debug';


$deposit_checktime_limit = 300;		// 투자건 발생후 입금확인 제한시간

$x = true;

while($x > 0) {

	if($action=='debug') { debug_flush("[".date('Y-m-d H:i:s')."]\n"); }


	// 최초 자료등록은 investEnd.php 에서 등록됨.
	$sql = "
		SELECT
			A.idx, A.invest_idx, A.member_idx, A.product_idx, A.amount, A.rdate,
			(SELECT mb_id FROM g5_member WHERE mb_no=A.member_idx) AS mb_id
		FROM
			oligo_deposit_check A
		WHERE 1
			AND A.deposit='N'
		ORDER BY
			A.idx ASC";
	$res = sql_query($sql);
	//debug_flush($sql."\n");

	$rows = sql_num_rows($res);

	if($rows) {

		for($i=0; $i<$rows; $i++) {

			$DEPOSIT[$i] = sql_fetch_array($res);

			$MB = get_member($DEPOSIT[$i]['mb_id']);

			$rdateTimeStamp = strtotime($DEPOSIT[$i]['rdate']);
			$timeGap = time() - $rdateTimeStamp;

			// 핀크 신규 투자가 등록 되었을 경우 해당 투자금 만큼 예치금 입금 통지내역 테이블에 데이터를 넣어준다.
			if($timeGap <= $deposit_checktime_limit && $DEPOSIT[$i]['idx'] && $MB['mb_no'] && $MB['bank_code'] && $MB['account_num']) {

				if($action=='yes') {
					usleep(rand(10,30));	// 가상계좌 이체시간 시뮬레이션 (10~30초 무작위 설정)
				}

				$fb_seq = sprintf('%10d', rand(0, 9999999999));

				$sql2 = "
					INSERT INTO
						IB_FB_P2P_IP
					SET
						PARTNER_CD   = 'P0012',
						SR_DATE      = '".date('Ymd')."',
						FB_SEQ       = '".$fb_seq."',
						CUST_ID      = '".$DEPOSIT[$i]['member_idx']."',
						BANK_ID      = '".$MB['bank_code']."',
						ACCT_NB      = '".$MB['account_num']."',
						TR_AMT       = '".sprintf('%.2f', $DEPOSIT[$i]['amount'])."',
						TR_AMT_DCH   = '".sprintf('%.2f', $DEPOSIT[$i]['amount'])."',
						REMITTER_NM  = '".$DEPOSIT[$i]['bank_private_name']."',
						MEDIA_GBN    = '10',
						TR_AMT_GBN   = '10',
						ERP_TRANS_DT = '".date('YmdHis')."',
						memo         = 'test'";
				//debug_flush($sql2."\n");

				if($action=='yes') {
					$res2 = sql_query($sql2);
					if($res2) {
						$sql3 = "UPDATE oligo_deposit_check SET deposit='Y', check_date=NOW() WHERE idx='".$DEPOSIT[$i]['idx']."'";
						$res3 = sql_query($sql3);
					}
				}

			}

		}		// end for

	}

	sleep(10);

}


exit;

?>