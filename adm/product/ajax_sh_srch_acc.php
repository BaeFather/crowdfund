<?
################################################################################
## 예금주명 확인 => product_form.php 에서 호출됨
################################################################################

set_time_limit(10);

$base_path = "/home/crowdfund/public_html";
include_once($base_path . "/common.php");
include_once($base_path . "/lib/insidebank.lib.php");

$enc_bit = '256';

// 수취인조회(4000, 예금주명 리턴)
$ARR['REQ_NUM'] = "040";
$ARR['BANK_CD'] = $_POST['bank_code'];
$ARR['ACCT_NB'] = $_POST['acc'];

$insidebank_result = insidebank_request($enc_bit, $ARR);

echo json_encode($insidebank_result);

sql_close();
exit;

?>