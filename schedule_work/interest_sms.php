<?php
###############################################################################
## 차주 이자 납입 안내 문자 발송
###############################################################################

set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', true);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/common.cli.php');
//include_once(G5_LIB_PATH.'/repay_calculation_new.php');		// 월별 정산내역 추출함수 호출
include_once($path . "/lib/sms.lib.php");
include_once($path . "/adm/mortgage/mortgage_common.php");
?>
<?
$today = $argv[1];
if (!$today) die("오늘 날짜 오류");

if ($argv[2]) $dtimes = $argv[2];
else $dtimes = 1;

if ($argv[3]) $sms_send=$argv[3];
else $sms_send="N";

if ($dtimes==2) {
	$kijun  = "16:00:00";
	$totime = "17:00:52";
} else {
	$kijun  = "10:00:00";
	$totime = "11:00:52";
}


echo "\n\n".$today. " " . $dtimes . "번째(".$totime.") 문자발송=> ".$sms_send."\n\n";
/*
$sql = "SELECT A.* , B.virtual_account2, C.title
		  FROM cf_loaner_push_schedule A
	 LEFT JOIN g5_member B ON(B.mb_no = A.mb_no)
	 LEFT JOIN cf_product C ON(C.idx = A.product_idx)
		 WHERE A.send_yn='Y' AND send_date = '$today' AND send_time<='$totime'
		   AND A.msg_id='0'
		   AND C.state='1'
		   AND send_status='0'
		   AND A.send_time = '$kijun'
		   order by product_idx";
*/
$sql = "SELECT A.* , B.virtual_account2, C.title
		  FROM cf_loaner_push_schedule A
	 LEFT JOIN g5_member B ON(B.mb_no = A.mb_no)
	 LEFT JOIN cf_product C ON(C.idx = A.product_idx)
		 WHERE A.send_yn='Y' AND send_date = '$today' AND send_time<='$totime'
		   AND A.msg_id='0'
		   AND C.state='1'
		   AND send_status='0'
		   AND A.send_time = '$kijun'
		   AND A.msg_gubun <> '만기 1달전'
		   order by product_idx";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

echo "대상자 총 ".number_format($cnt)." 건\r\n\r\n" ;

//sql_close();
//die("safe die\n");

$send_cnt=0;

for ($i=0 ; $i<$cnt ; $i++) {

	$sms_id = "";

	$row = sql_fetch_array($res);

	echo "==============================================================================================\r\n";
	echo "$i idx ".$row['product_idx']." " . $row['title'].' '.$row['turn']."회차\n";

	$remain_amt = get_chaju_remain_amt("$row[mb_no]"  , "$row[virtual_account2]"  , "$row[product_idx]" );  //$mb_no, $acct_no, $prd_idx 계좌잔액

	echo "계좌잔액 $remain_amt   ";

	if ($row["eja"]) $eja = $row["eja"];
	else $eja = get_eja($row['product_idx'],$row['turn']);  //$prd_idx, $turn 이자

	echo "이자 $eja   ";

	$chung_amt = $eja - $remain_amt;  // 이자 - 계좌잔액 = 청구액
	echo "청구액 $chung_amt\r\n\r\n\r\n";

	if ($chung_amt<=0) {
		echo "청구액 없음\n\n";
		continue;
	}

	// SMS 발송 시작
	$chaju = get_chaju_info($row['product_idx']);  //$prd_idx 해당 상품 차주 정보


	// 임시
	/*
	$hp = masterEncrypt($chaju[0]["mb_hp"], false);
	$hpk = substr($chaju[0]["mb_hp"], -4);
	$imsi_sql = "update cf_loaner_push_schedule set mb_hp='$hp' , mb_hp_key='$hpk' where idx='$row[idx]'";
	sql_query($imsi_sql);
	echo "$imsi_sql\n";
	*/


	$from_hp = "15885210";
	$to_hp   = $chaju[0]["mb_hp"];
	$send_msg = $row["msg"];
	$send_msg = str_replace("{PRICE}", number_format($chung_amt), $send_msg);
	$send_date = $row["send_date"]." ".$row["send_time"];

	echo "전화번호 : ".$to_hp."\r\n";
	echo $send_msg."\r\n";

	if ($to_hp) {

		if ($sms_send=="Y") {  //수신자 값이 있고 발송구분 값이 Y일때

			if( $to_hp=='01056070130' && in_array(date('Y-m-d'), array('2021-12-29','2021-12-31','2022-01-03')) ) {

				// 차주 신동기 문자발송 무효처리 (임시)

			}
			else {

				//$to_hp = "01086246176";  // 전승찬
				//$to_hp = "01086672630";  // 이동심
				$sms_id = unit_sms_send_v2($from_hp, $to_hp, $send_msg, $send_date=null,$send_id=null);
				$send_cnt = $send_cnt +1;
				echo "sms id = ".$sms_id."\r\n";
				//echo "$send_msg\n";

			}

		}

	}

	if ($sms_id) {
		$up_sql = "UPDATE cf_loaner_push_schedule SET msg_id='$sms_id' WHERE idx='$row[idx]'";
		//echo $up_sql."\r\n";
		sql_query($up_sql);
	}

	echo "\r\n\r\n";
	//die();

}

sql_close();

echo "총건수 ".$cnt."건중 발송 ".$send_cnt."건\r\n\r\n\r\n";
?>