<?
###############################################################################
## 투자상품 상세보기
###############################################################################

include_once('./_common.php');
include_once(G5_LIB_PATH.'/review.lib.php');

$g5['title'] = '투자상품 상세보기';
$g5['top_bn'] = "/images/investment/sub_investment.jpg";
$g5['top_bn_alt'] = "투자하기 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";


while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k)) ${$k} = addslashes(clean_xss_tags(trim($v))); }

if($co['co_include_head']) @include_once($co['co_include_head']);
else include_once('./_head.php');
?>



	<!-- 법정공시정보 필수 확인 -->
	<? 
	//if($is_member || $is_admin) {
		include_once('gongsi_popup2.php');
	//} 
	?>