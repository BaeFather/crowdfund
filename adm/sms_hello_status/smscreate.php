<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//아이디 처리
?>
<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
@include_once("../../lib/sms.lib.php");

	$p_id =& $_POST["p_id"];

	IF(!$p_id)
	{
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
		ECHO json_encode($objval);
		EXIT;
	}

	/* 리포트 데이터 검증*/

	$strColumn		= ARRAY("idx");
	$strTable		= "cf_product";
	$strWhere		= " WHERE idx='".add_str($p_id)."'";
	$strOrderBy		= " idx ASC";
	$strLimit1		= 0;
	$strLimit2		= 1;
	$strLen			= 10000;

	$rowView = fr_board_view($strColumn,$strTable,"",$strWhere,$strOrderBy,$strLimit1,$strLimit2,$strLen,$connect_db);

	IF(!@$rowView[0]["idx"])
	{
		$strRetCode = "X";
		$strRetAlt = STR_REPLACE("+"," ",urlencode("대상 상품이 존재하지 않습니다. 상품번호를 확인하여 주십시오."));
	} ELSE {
		/* 리포트 데이터 생성*/
		fn_cf_product_admin_report($p_id);
		/* sms전송 */
		fn_hello_status_smssend($p_id);

		$strRetCode = "OK";
		$strRetAlt = STR_REPLACE("+"," ",urlencode("리포트가 정상 등록 되었으며 문자가 발송 되었습니다."));

	}

	sql_close($connect_db);

	$objval = ARRAY("retcode"=>$strRetCode,"retalert"=>$strRetAlt,"retval"=>"/adm/sms_hello_status/");
	ECHO json_encode($objval);
?>