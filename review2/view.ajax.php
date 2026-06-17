<?php
include_once('./_common.php');
include_once('../lib/function_prc.php');
include_once('./review.class.php');

	$SE		=	$_POST["SE"];

	IF(!$SE)
	{
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
		ECHO json_encode($objval);
		EXIT;
	}

	$strVal = new strReviewClass();
	$strView = $strVal->fn_recommend_view($SE);

	IF($strView["id"])
	{
		$retval = "<div class='btn'><img src='img/close.png' class='btnServiceClose' alt='닫기'></div>
		<div class='user'>
			<span class='name'>투자자 ".$strView["mem_name"]."</span> /
			<span class='info'>".$strVal->fn_general_txt($strView["mb_sex"],$strVal->fn_mb_sex())."</span>
		</div>
		<div class='investinfo'>
			<span class='num'>투자참여 : ".$strView["CNT"]."회</span>
			<span class='favo'>선호상품 : ".$strView["content2txt"]."</span>
		</div>
		<div class='contents'>
			<p class='pop_text'>".nl2br($strView["contents"])."</p>
		</div>";

		$retCode = "OK";
		$retAlt = "";
	} ELSE {
		$retCode = "X";
		$retAlt = STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오"));
	}

	$objval = ARRAY("retcode"=>$retCode,"retalert"=>$retAlt,"retval"=>$retval);
	ECHO json_encode($objval);
	sql_close($connect_for);
?>