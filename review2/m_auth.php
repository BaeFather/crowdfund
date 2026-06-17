<?php
include_once('./_common.php');
include_once('../lib/function_prc.php');
include_once('./review.class.php');

// 처리페이지
$mem_name  = $member['mb_name'];
$mem_phone = $member['mb_hp'];
$mem_no    = $member['mb_no'];
$mem_id    = $member['mb_id'];

IF($mem_no) {

	$strReviewClass = new strReviewClass();

	$regCnt = $strReviewClass->fn_registed_cnt($mem_id);

	IF($regCnt["CNT"] > 0)
	{

		$retCode = "X";
		$retval  = "";
		$retAlt  = STR_REPLACE("+"," ","이미 참여한 이벤트 입니다.");

	}
	ELSE {

		$intCnt = $strReviewClass->fn_invest_cnt($mem_no);

		IF($intCnt["CNT"] > 0) {
			$retCode = "OK";
			$retval  = ARRAY("mb_name"=>$mem_name, "mem_phone"=>$mem_phone, "mem_no"=>$mem_no);
			$retAlt  = "";
		}
		ELSE {
			$retCode = "X";
			$retval  = "";
			$retAlt  = STR_REPLACE("+"," ","투자이력이 있어야 신청이 가능합니다.");
		}

	}

}
ELSE {

	$retCode = "XX";
	$retval = "";
	$retAlt = STR_REPLACE("+"," ","로그인 후 사용이 가능합니다");

}

$objval = ARRAY(
	"retcode"  => $retCode,
	"retalert" => $retAlt,
	"retval"   => $retval
);

ECHO json_encode($objval);

sql_close($connect_for);

?>