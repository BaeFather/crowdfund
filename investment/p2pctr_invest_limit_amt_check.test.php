#!/usr/local/php/bin/php -c /etc/php.ini -q
<?
///////////////////////////////////////////////////////////////////////////////
// 금결원 누적투자액 가져오기
// php -q /home/crowdfund/public_html/investment/p2pctr_invest_limit_amt_check.test.php 회원번호 상품번호
///////////////////////////////////////////////////////////////////////////////

$member_idx  = @$_SERVER['argv'][1];
$product_idx = @$_SERVER['argv'][2];

if(!$member_idx) { echo "ERROR:MEMBER_IDX"; exit; }
//if(!$product_idx) { echo "ERROR:PRODUCT_IDX"; exit; }


$base_path = "/home/crowdfund/public_html";
include_once($base_path . "/common.cli.php");
include_once($base_path . '/lib/p2pctr_svc.lib.php');


$MB = sql_fetch("SELECT mb_id FROM g5_member WHERE mb_no='".$member_idx."'");

// 금결원측 동종업계투자잔액 가져오기..
$LMT = get_p2pctr_limit($MB['mb_id'], $product_idx);
print_r($LMT);
/*
$LMT['ALL_LIMIT']		// 전체 투자잔액 (상품구분에 관계없는..)
$LMT['IMV_LIMIT']		// 부동산 투자잔액
$LMT['BRW_LIMIT']		// 동일차주 투자잔액
*/

exit;

?>