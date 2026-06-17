#!/usr/local/php/bin/php -q
<?
################################################################
## 핀크 투자후 미입금자 투자 취소처리
## 투자후 10분 동안 입금 로그가 입력되지 않으면 투자 취소 처리함.
## php -q /home/crowdfund/public_html/syndicate/finnq/scheduler/finnq_deposit_fail_check.php
## * * * * * /home/crowdfund/public_html/syndicate/finnq/scheduler/finnq_deposit_fail_checker.sh &
################################################################

set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', false);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/data/dbconfig.php');
include_once($path . '/lib/common.lib.php');


$action = (@$_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : 'debug';
$deposit_checktime_limit_second = 600;
$syndi_id = "finnq";
$cancel_by = "system";

$x = true;

while($x > 0) {

	if($action=='debug') { echo ("[" . date('Y-m-d H:i:s') . "]\n"); }

	//---------------------------------------------------------------------------
	$link = sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD, G5_MYSQL_DB);
	sql_set_charset("UTF8", $link);
	//---------------------------------------------------------------------------


	$target_date  = date("Y-m-d H:i", time()-$deposit_checktime_limit_second);

	// 핀크를 통한 투자내역중 투자금입금확인이 되지 않은 투자건 추출
	$sql = "
		SELECT
			A.*
			-- B.invest_state,
			-- C.syndi_userid, C.mb_name, C.virtual_account2,
		FROM
			finnq_deposit_check A
		INNER JOIN
			cf_product_invest B  ON A.invest_idx=B.idx
		WHERE 1
			AND A.deposit='N'
			AND B.syndi_id='".$syndi_id."'
			AND B.invest_state='Y'
			AND LEFT(A.rdate, 16)='".$target_date."'
		ORDER BY
			A.idx ASC";
	if($action=='debug') { echo ($sql."\n"); }
	$res  = sql_query($sql, true, $link);
	$rows = sql_num_rows($res);
	for($i=0; $i<$rows; $i++) {

		$CHECK_LIST = sql_fetch_array($res);
		//print_r($CHECK_LIST);

		if($CHECK_LIST['idx']) {

			$PRDT   = sql_fetch("SELECT idx, title, invest_end_date FROM cf_product WHERE idx='".$CHECK_LIST['product_idx']."' AND state=''", true, $link);		// 투자완료는 되었으나 대출실행전인 상품만 가져온다.
			$MB     = sql_fetch("SELECT mb_no, mb_id FROM g5_member WHERE mb_no='".$CHECK_LIST['member_idx']."' AND mb_level='1'", true, $link);
			$INVEST = sql_fetch("SELECT idx, amount FROM cf_product_invest WHERE idx='".$CHECK_LIST['invest_idx']."' AND invest_state='Y'", true, $link);

			if($PRDT['idx'] && $MB['mb_id'] && $INVEST['idx']) {

				$update_sql1 = "UPDATE cf_product_invest SET invest_state='N', cancel_date=NOW(), cancel_by='".$cancel_by."' WHERE idx='".$CHECK_LIST['invest_idx']."' AND invest_state='Y'";
				$update_sql2 = "UPDATE cf_product_invest_detail SET invest_state='N', cancel_date=NOW() WHERE invest_idx='".$CHECK_LIST['invest_idx']."' AND invest_state='Y'";

				$po_content = $PRDT['title']. "-투자 취소 (핀크 투자금 입금내역 미확인)";

				if($action=='yes') {
					// 투자내역 취소
					if( sql_query($update_sql1, true, $link) ) {
						sql_query($update_sql2, true, $link);			// 상세투자내역 취소
						insert_point($MB['mb_id'], $INVEST['amount'] , $po_content, '@cancel', $MB['mb_id'], $MB['mb_id'].'-'.uniqid(''), 0);		// 투자금액 예치금으로 돌려줌

						// 상품관리테이블에 실시간 모집금액 반영하기 :: 2021-02-15 추가
						sql_query("UPDATE cf_product SET live_invest_amount = live_invest_amount - {$INVEST['amount']} WHERE idx = '".$CHECK_LIST['product_idx']."'", true, $link);

					}
				}
				else {
					echo ($update_sql1."\n");
					echo ($update_sql2."\n");
					echo ("insert_point(".$MB['mb_id'].", ".$INVEST['amount'].", ".$po_content.", '@cancel', ".$MB['mb_id'].", ".$MB['mb_id']."-".uniqid('').", 0);\n");
				}

				// 투자모집성공 처리된 상품일 경우 투자모집성공 플래그 초기화
				if($PRDT['invest_end_date']) {
					$update_sql3 = "UPDATE cf_product SET invest_end_date='' WHERE idx='".$CHECK_LIST['product_idx']."'";
					if($action=='yes') {
						sql_query($update_sql3, true, $link);
					}
					else {
						echo ($update_sql3."\n");
					}
				}

				// 핀크 투자의뢰내역에 미입금 처리
				$update_sql4 = "UPDATE finnq_deposit_check SET deposit='C', check_date=NOW() WHERE idx='".$CHECK_LIST['idx']."'";
				if($action=='yes') {
					sql_query($update_sql4, true, $link);
				}
				else {
					echo ($update_sql4."\n\n");
				}

				unset($PRDT); unset($MB); unset($INVEST);

			}

		}


	}

	sql_free_result($res);

	sql_close($link);

	sleep(10);

}

?>