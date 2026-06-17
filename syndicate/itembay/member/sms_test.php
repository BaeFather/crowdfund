<?
include_once('./_common.php');

//include_once('../lib/common.lib.php');
include_once(HF_PATH.'/lib/sms.lib.php');

$mb_hp = "01086246176";
$sms_msg = "111";

$rst = unit_sms_send($_admin_sms_number, $mb_hp, $sms_msg);
echo "$rst";
?>