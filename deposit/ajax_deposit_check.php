<?
###############################################################################
## 가상계좌 실시간 입금 알림 (세틀뱅크)
###############################################################################
## ※ vacs_ahst 및 g5_point 테이블 데이터는 세틀뱅크 대기모듈에 의해
##    /home/crowdfund/vacs/src/EnDeServerThread.java 파일에서 데이터가 입력됨
##
## /theme/blueman1/tail.sub.php 에서 팝업(/popup/inc_deposit_check.php)으로 출력
###############################################################################
include_once("_common.php");

//if($_SERVER["REQUEST_METHOD"]!="POST") { echo "ERROR-DATA"; exit; }
if(!$member["mb_id"]) { msg_go('', '/'); }


$tr_il = date('Ymd');

$sql = "
	SELECT
		A.tr_no, A.tr_amt, A.iacct_nm,
		B.mb_no, B.mb_id
	FROM
		vacs_ahst A,
		g5_member B
	WHERE
		A.iacct_no=B.virtual_account
		AND A.bank_cd=B.va_bank_code
		AND A.tr_il='".$tr_il."'
		AND A.inp_st='1'
		AND B.mb_no='".$member['mb_no']."'
	ORDER BY
		A.tr_si DESC
	LIMIT 1";
$DATA = sql_fetch($sql);

if($DATA) {
	$LOG = sql_fetch("SELECT COUNT(tr_no) AS cnt FROM vacs_deposit_notify_daylog WHERE tr_no='".$DATA['tr_no']."'");
	if(!$LOG['cnt']) {
		$sql = "
			INSERT INTO
				vacs_deposit_notify_daylog
			SET
				tr_no='".$DATA['tr_no']."',
				mb_no='".$DATA['mb_no']."',
				mb_id='".$DATA['mb_id']."',
				amount='".$DATA['tr_amt']."',
				rdate=NOW()";
		sql_query($sql);
		$msg = "예치금 <font color='red'>".number_format($DATA['tr_amt'])."원</font>이 입금되었습니다.<br>\n입금하신 예치금으로 투자 가능합니다.";
		echo $msg;
	}
}

sql_close();
exit;

?>