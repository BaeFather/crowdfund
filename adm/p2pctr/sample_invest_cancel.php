<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$prd_idx    = "7063";    // 상품번호  
$mb_no      = "5450";   // 회원번호
$invest_idx = "331882";  // 투자번호  // 신길순 투자신청 ID K210500031_210917_IR_0000000399
//$p2pctr_canc_result = p2pctr_invest_register_canc($mb_no, $prd_idx, $invest_idx);
var_dump($p2pctr_canc_result);
?>