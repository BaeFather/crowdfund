<?
//인사이드뱅크 집계 실시간 수집실행

$reqdate = @trim($_POST['reqdate']);

$exec_string = "/usr/local/php/bin/php -q /home/crowdfund/public_html/shinhan/bank_deal_check.php direct";

if($reqdate) $exec_string.= " " . $reqdate;

//echo $exec_string;

$exec_result = exec($exec_string);
if(trim($exec_result) == 'success') {
	echo "OK";
}
else {
	echo "FAIL";
}

exit;

?>