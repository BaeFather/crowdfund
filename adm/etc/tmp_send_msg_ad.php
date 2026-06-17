<?

// 특정 조건의 번호로 문자 날리기

set_time_limit(0);

include_once("_common.php");
include_once("../../lib/sms.lib.php");

if(!$is_admin) exit;

$send_msg = "(광고) 헬로펀딩 투자정보
[마감임박]
지금 500만원 투자시,
매월 수익 약 75,000원(세전)

18% / 8개월 투자상품 바로가기
https://goo.gl/gi925g

거부 0809000982";

$send_date = date("Y-m-d H:i:s", time()+300);		// 5분뒤 발송


$sql  = "
	SELECT
		A.mb_no, A.mb_id, A.mb_level, A.member_investor_type, A.mb_hp, A.mb_sms, A.mb_point
	FROM
		g5_member A
		LEFT JOIN cf_product_invest B ON A.mb_no=B.member_idx
	WHERE (1)
		AND A.member_group='F'
		AND A.mb_level='1'
		AND A.mb_sms='1'
		AND A.is_rest='N'
		AND LEFT(A.mb_hp,3) IN('010','011','016','017','018','019')
		AND (SELECT IFNULL(COUNT(idx), 0) FROM cf_product_invest WHERE product_idx IN('224','226') AND member_idx=A.mb_no AND invest_state='Y')=0
	GROUP BY
		A.mb_no
	ORDER BY
		invest_cnt DESC,
		A.mb_point DESC, A.mb_no";
$res  = sql_query($sql);
$rows = $res->num_rows;
for($i=0,$j=1; $i<$rows; $i++,$j++) {
	$LIST = sql_fetch_array($res);

	$LIST['mb_hp'] = preg_replace("/ /", "", $LIST['mb_hp']);

	if( $_REQUEST['action']==date('YmdHi') ) {
		if(unit_sms_send($_admin_sms_number, $LIST['mb_hp'], $send_msg, $send_date)) {
			debug_flush($LIST['mb_hp'] . "<br>\n");
		}
	}
	else {
		debug_flush("<pre style='font-size:12px'><b>[".$j."] ID: ".$LIST['mb_id']." / 연락처: ".$LIST['mb_hp']."</b>\n\n\n".$send_msg . "</pre><br><hr style='border:1px dotted #000'><br>\n");
	}

	if($j%300==0) {
		usleep(500000);
	}

}


debug_flush("<font color='red'>종료</font>");


exit;

?>