<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
if (!$product_idx) die("상품번호 오류");    // 상품번호  
if (!$member_idx) die("상품번호 오류");    // 상품번호  
if (!$canc_inv_id) die("투자신청번호 오류");    // 상품번호  


$sql = "SELECT idx, investment_register_id FROM cf_product_invest WHERE product_idx='$product_idx' AND member_idx='$member_idx' AND investment_register_id='$canc_inv_id'";
$res = sql_query($sql);
$cnt = $res->num_rows;

if (!$cnt) die("투자건을 찾을수 없습니다.");

$row = sql_fetch_array($res);

if (!$row["investment_register_id"]) die("투자신청 ID가 없습니다.");

$invest_idx = $row["idx"];

//echo "$product_idx $member_idx $invest_idx<br/>";
//echo "전산 관리자에게 연락바람<br/>";
//die();


$p2pctr_canc_result = p2pctr_invest_register_canc($member_idx, $product_idx, $invest_idx);
var_dump($p2pctr_canc_result);
if ($p2pctr_canc_result) echo "취소 성공";
?>