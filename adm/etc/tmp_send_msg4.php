<?

exit;

// 상품취소시 투자자에게 문자 날리기

set_time_limit(0);

include_once("_common.php");
include_once("../../lib/sms.lib.php");

if(!$is_admin) exit;

$prd_idx='1768';

$send_msg = "[헬로펀딩 공지]

헬로펀딩 투자상품 제1585호 부산 명지국제신도시 오피스텔 브릿지 상품은 차주 측 인출선행조건 미충족(채권보전에 관한 사항)으로 인하여 대출금 기표 진행되지 않으며 모집된 펀딩 자금은 투자자분들에게 예치금으로 반환되었으므로 참고부탁드립니다.

헬로펀딩을 믿고 투자해주신만큼 더욱 안전하게 운영할 수 있도록 최선을 다하겠습니다.
감사합니다.";


//$send_date = date("Y-m-d H:i:s", time()+300);


$sql  = "
	SELECT
		A.idx AS invest_idx,
		B.mb_no, B.mb_hp
	FROM
		cf_product_invest A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	WHERE 1
		AND A.product_idx='".$prd_idx."'
		AND A.invest_state='R'
	ORDER BY
		invest_idx ASC";
$res  = sql_query($sql);
$rows = $res->num_rows;
for($i=0,$j=1; $i<$rows; $i++,$j++) {
	$LIST = sql_fetch_array($res);

	$LIST['mb_hp'] = masterDecrypt($LIST['mb_hp'], false);

	$print_name = ( $LIST['member_type']=='2' ) ? $LIST['mb_co_name'] : $LIST['mb_name'];

	if( $_REQUEST['action']==date('YmdHi') ) {
		if(unit_sms_send($_admin_sms_number, $LIST['mb_hp'], $send_msg, $send_date)) {
			debug_flush($LIST['mb_hp'] . "<br>\n");
		}
	}
	else {
		debug_flush("$j : unit_sms_send($_admin_sms_number, {$LIST['mb_hp']}, $send_msg, $send_date)<br/>\n");
	}

	if($j%300==0) {
		sleep(2);
	}

}


debug_flush("<font color='red'>종료</font>");


exit;

?>