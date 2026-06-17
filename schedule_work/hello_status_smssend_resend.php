<?php
###############################################################################
## 통계 확인 발송  /* 매시간 10 분*
## SMS발송내역을 확인하여 발송이 되지 않으면 발송처리
###############################################################################
set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', true);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/common.cli.php');
include_once($path . "/lib/sms.lib.php");

FUNCTION  fn_sms_report_check()
{
	global $_admin_sms_number;
	global $link3;

	$dtmdate = DATE("Y-m-d H");
	//$dtmdate = "2020-04-16 12";
	$intTime = TIME();

	//현시간 기준으로 2시간 이내 데이터 확인
	$Query = "SELECT t1.ridx,t2.title,t2.product, t3.cphone,t1.midx FROM cf_product_admin_report_send t1 LEFT JOIN cf_product_admin_report t2 ON t1.pidx=t2.pidx LEFT JOIN cf_product_admin_user t3 ON t1.midx=t3.midx WHERE LEFT(t2.reg_time,13)>=LEFT(DATE_ADD('".$dtmdate."', INTERVAL -2 Hour),13) AND LEFT(t2.reg_time,13)<= '".$dtmdate."' AND substring(t2.title,1,3)<>'SCF'";

	$Result = sql_query($Query,'', $connect_db);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$intRidx	=	$Row["ridx"];
		$strTitle	=	$Row["title"];
		$strProduct	=	$Row["product"];
		$strCphone	=	STR_REPLACE("-","",$Row["cphone"]);
		$intMidx	=	$Row["midx"];

		IF($strCphone)
		{
			// sms발송내역 확인
			//$QuerySms	=	"SELECT COUNT(*) as CNT FROM cf_agent_msgqueue where receiveNo='".$strCphone."'  AND message LIKE '%".$strTitle."%'";		// KP모바일 발송내역 조회
			$QuerySms	=	"SELECT COUNT(idx) as CNT FROM cf_Msg_Tran where Phone_No='".$strCphone."'  AND Message LIKE '%".$strTitle."%'";		// SMTNT 발송내역 조회 : 2020-06-03 변경
			$ResultSms	=	sql_query($QuerySms, '', $link3);

			IF($RowSms	=	sql_fetch_array($ResultSms))
			{
				$intCnt		=	$RowSms["CNT"];
				sql_free_result($ResultSms);
			}

			// 발송내역 없다면 발송
			IF(!$intCnt || $intCnt == 0)
			{
				$Q2 = "UPDATE cf_product_admin_report_send SET send_time='".$intTime."', sendyn='' WHERE ridx='".$intRidx."'";
				sql_query($Q2,'', $connect_db);

				$strSmsMsg = $strTitle."\n\n";
				$strSmsMsg .= $strProduct."\n\n";
				$strSmsMsg .= "https://www.hellofunding.co.kr/hello_report/?RT=".$intTime.$intMidx;

				//$strCphone = "010-2333-4749";

				unit_sms_send($_admin_sms_number, $strCphone, $strSmsMsg, DATE("Y-m-d H:i:s",$intTime+600));
			}
		}

		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
}

fn_sms_report_check();

sql_close($link3);
sql_close($connect_db);
?>