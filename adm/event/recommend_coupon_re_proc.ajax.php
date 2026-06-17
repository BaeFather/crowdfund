<?php
set_time_limit(0);

include_once('./_common.php');
include_once('../../lib/sms.lib.php');

$cnumber = $_POST["cnumber"];

$Query  = "SELECT cphone, msg FROM hloan_partner_event_log WHERE cnumber='".$cnumber."'";
$row= sql_fetch($Query);

$cphone	=	$row["cphone"];
$msg	=	$row["msg"];

fn_partner_event_sms_send_re($row["cphone"], $row["msg"]);

FUNCTION fn_partner_event_sms_send_re($strCphone,$sms_msg)
{
	global $_admin_sms_number;

	IF(!$_admin_sms_number)
	{

	} ELSE {

		$intTime = "";
		unit_sms_send($_admin_sms_number, $strCphone, $sms_msg, $intTime);

//		$Query = "INSERT INTO hloan_partner_event_log (ridx, cnumber, cphone, msg, reg_date) VALUES ('".$intRidx."','".$strCNumber."', '".$strCphone."','".$sms_msg."',now())";
//		sql_query($Query);
	}
}
?>