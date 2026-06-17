<?
###############################################################################
## 자동투자 완료 문자 재전송 (문자발송오류시에 사용)
###############################################################################

exit;

set_time_limit(0);

include_once("_common.php");
include_once("../../lib/sms.lib.php");

if(!$is_admin) exit;

$SMS_DATA  = sql_fetch("SELECT * FROM `g5_sms_userinfo` WHERE use_yn='1' AND idx='2'");

$product = "3153,3154";

// 직원우선, 회원번호순
$sql="
	SELECT
		A.invest_idx, A.amount, A.member_idx, A.product_idx,
		B.mb_id, B.mb_name, B.mb_hp,
		(SELECT title FROM cf_product WHERE idx= A.product_idx) AS title
	FROM
		cf_product_invest_detail A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	WHERE 1=1
		AND A.product_idx IN(".$product.")
		AND A.invest_state='Y'
		AND A.is_auto_invest='1'
	ORDER BY
		A.product_idx ASC,
		B.mb_10 DESC,
		A.idx ASC";
$res  = sql_query($sql);
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++){
	$LIST[$i] = sql_fetch_array($res);
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'],false);
}
//print_rr($LIST);exit;

$list_count = count($LIST);

if(!$list_count) exit;

if( $_REQUEST['action'] == date('YmdHi') ) {

	for($i=0,$j=1; $i<$list_count; $i++,$j++) {
		if( in_array(substr($LIST[$i]['mb_hp'], 0, 3), array('010','011','016','017','018','019')) ) {

			////////////////////////////
			// 투자신청완료 문자 발송
			////////////////////////////
			if($SMS_DATA['msg']) {
				$sms_msg = preg_replace("/\{PROJECT_NAME\}/", $LIST[$i]['title'], $SMS_DATA['msg']);
				$sms_msg = preg_replace("/\{FUNDING_PRICE\}/", price_cutting($LIST[$i]['amount']), $sms_msg);
				$rst = unit_sms_send($CONF['admin_sms_number'], $LIST[$i]['mb_hp'], $sms_msg, $send_date);

				if( unit_sms_send($CONF['admin_sms_number'], $LIST[$i]['mb_hp'], $sms_msg, $send_date) ){
					debug_flush($j . ":" . $LIST[$i]['mb_hp'] . "<br/>\n");
					usleep(1000);
				}

			}

		}
	}

}
else {

	for($i=0,$j=1; $i<$list_count; $i++,$j++) {
		if( in_array(substr($LIST[$i]['mb_hp'], 0, 3), array('010','011','016','017','018','019')) ) {

			if($SMS_DATA['msg']) {
				$sms_msg = preg_replace("/\{PROJECT_NAME\}/", $LIST[$i]['title'], $SMS_DATA['msg']);
				$sms_msg = preg_replace("/\{FUNDING_PRICE\}/", price_cutting($LIST[$i]['amount']), $sms_msg);

				debug_flush($j." : unit_sms_send({$CONF['admin_sms_number']}, {$LIST[$i]['mb_hp']}, {$sms_msg}, {$send_date});<br/><br/>\n");
			}

		}
	}

}

debug_flush("<font color='red'>종료</font>");

exit;

?>