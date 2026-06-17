<?
##########################################################################################################
## 가상계좌 실시간 입금 알림 (KSNET 가상계좌 및 일반 거래 입출금 내역 기준으로 적용시)
## /theme/blueman1/tail.sub.php 에서 팝업(/popup/inc_deposit_check_ksnet.php)으로 출력
##########################################################################################################
include_once("_common.php");

//if($_SERVER["REQUEST_METHOD"]!="POST") { echo "ERROR-DATA"; exit; }
if(!$member["mb_id"]) { msg_go('', '/'); }


$deal_date = date('Ymd');

// 가상계좌(모계좌:헬로핀테크 마킹) 입금내역 가져오기 (KSNET 데이터 기준)
// DEAL_SELE 거래구분 (20:입금, 30:출금, 51:입금취소, 52:출금취소)
$sql = "
	SELECT
		SEQ_NO, TOTAL_AMT
	FROM
		KSNET_TRADE_DATA
	WHERE	1
		AND COMP_CODE = '".$COMP_CODE['hellofintech']."'
		AND VR_ACCT_NO = '".$member['virtual_account2']."'
		AND DEAL_SELE = '20'
		AND DEAL_DATE = '".$deal_date."'
	ORDER BY
		DEAL_DATE DESC, DEAL_TIME DESC
	LIMIT 1";
$TRADE = sql_fetch($sql);
if($TRADE) {

	$LOG = sql_fetch("SELECT COUNT(SEQ_NO) AS cnt FROM KSNET_deposit_notify_daylog WHERE SEQ_NO='".$TRADE['SEQ_NO']."' AND mb_no='".$member['mb_no']."'");

	if(!$LOG['cnt']) {
		// 로그에 알림 마킹 처리
		$sql2 = "
			INSERT INTO
				KSNET_deposit_notify_daylog
			SET
				SEQ_NO = '".$TRADE['SEQ_NO']."',
				mb_no  = '".$member['mb_no']."',
				mb_id  = '".$member['mb_id']."',
				amount = '".$TRADE['TOTAL_AMT']."',
				rdate  = NOW()";
		sql_query($sql2);
		$msg = "예치금 <font color='red'>".number_format($TRADE['TOTAL_AMT'])."원</font>이 입금되었습니다.<br>\n입금하신 예치금으로 투자 가능합니다.";
		echo $msg;
	}

}


sql_close();
exit;

?>