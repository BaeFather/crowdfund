<?php
###############################################################################
## 통계 예약 발송  /* 오전 7시*
###############################################################################
set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', true);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/common.cli.php');
include_once($path . '/lib/sms.lib.php');

FUNCTION fn_hello_status_smssend_recommend()
{
	global $_admin_sms_number;
	$intTime = TIME();

	$Query = "
		SELECT
			t1.ridx, t1.midx,
			t2.title,t2.product,
			t3.cphone
		FROM
			cf_product_admin_report_send t1
		LEFT JOIN
			cf_product_admin_report t2  ON t1.pidx=t2.pidx
		LEFT JOIN
			cf_product_admin_user t3  ON t1.midx=t3.midx
		WHERE 1=1
			AND t1.sendyn='1'";
	$Result= sql_query($Query);

	$j = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$ridx    = $Row["ridx"];
		$title   = $Row["title"];
		$product = $Row["product"];
		$cphone  = $Row["cphone"];
		$midx    = $Row["midx"];

		IF($ridx)
		{
			$Q2 = "UPDATE cf_product_admin_report_send SET send_time='".$intTime."', sendyn='' WHERE ridx='".$ridx."'";
			sql_query($Q2);

			$sms_msg = $title."\n\n";
			$sms_msg .= $product."\n\n";
			$sms_msg .= "https://www.hellofunding.co.kr/hello_report/?RT=".$intTime.$midx;

			//$cphone = "010-2333-4749";

			unit_sms_send($_admin_sms_number, $cphone, $sms_msg, DATE("Y-m-d H:i:s",$intTime+600));
		}
		IF($j > 0)
		{
			sql_free_result($Result);
		}
	}
}

fn_hello_status_smssend_recommend();
sql_close($connect_db);
?>