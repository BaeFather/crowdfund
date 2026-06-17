<?php
include_once('./_common.php');
include_once('../lib/function_prc.php');
include_once('./review.class.php');

// 처리페이지
$mem_name  = $member['mb_name'];
$mem_phone = $member['mb_hp'];
$mem_no    = $member['mb_no'];
$mem_id    = $member['mb_id'];

IF($mem_no) {  // 회원 번호가 있을 때 

	$strReviewClass = new strReviewClass();

	$regCnt = $strReviewClass->fn_registed_cnt($mem_id);  // epilogue_list 테이블에서 count 

	IF($regCnt["CNT"] > 0) // epilogue_list(추천평 작성하기) 테이블에 데이터가 있을 때
	{

		$retCode = "X";
		$retval  = "";
		$retAlt  = STR_REPLACE("+"," ","이미 참여한 이벤트 입니다.");

	} 
	ELSE {  // epilogue_list 테이블에 데이터가 없을 때

		$intCnt = $strReviewClass->fn_invest_cnt($mem_no);  // 투자내역 - 투자상태가 정상일 때

		IF($intCnt["CNT"] > 0) {  // 투자내역이 있으면 추가
			$retCode = "OK";
			$retval  = ARRAY("mb_name"=>$mem_name, "mem_phone"=>$mem_phone, "mem_no"=>$mem_no);
			$retAlt  = "";
		}
		ELSE {  // 투자 내역이 없을 때
			$retCode = "X";
			$retval  = "";
			$retAlt  = STR_REPLACE("+"," ","투자이력이 있어야 신청이 가능합니다.");
		}

	}

}
ELSE {  // 회원 번호가 없을 때

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