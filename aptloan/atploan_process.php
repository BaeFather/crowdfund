<?php

error_reporting(0);

include_once('./_common.php');
include_once('../lib/etc.lib.php');


//=============== 봇접근 제어 시작 ===============//
// 배재수 : 요청방식 및 접근경로에 따른 제어
if( $_SERVER['REQUEST_METHOD']!='POST' || !preg_match("/hellofunding\.co\.kr/i", $_SERVER['HTTP_REFERER']) ) {
	$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
	ECHO json_encode($objval, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);
	sql_close($connect_db);
	exit;
}

// 전승찬 : 동일IP 일별 등록게시글수 제한
$this_day = date("Y-m-d");
$chk_sql = "SELECT COUNT(idx) chk_count FROM cf_apat_loan_request WHERE ip='".$_SERVER['REMOTE_ADDR']."' AND regdate like '$this_day %'";
$chk_row = sql_fetch($chk_sql);
$chk_count = $chk_row["chk_count"];

if ($chk_count>3) {
	$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
	ECHO json_encode($objval, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);
	sql_close($connect_db);
	exit;
}
//=============== 봇접근 제어 끝 ===============//



$kind =& $_POST["kind"];

IF($kind == "auth")
{
	$strPost = ARRAY(
						ARRAY("si","","Y"),ARRAY("gu","","Y"),ARRAY("dong","","Y"),ARRAY("rdo_apt","","Y"),
						ARRAY("apt_name","",""),ARRAY("apt_area","",""),ARRAY("dong_num","",""),ARRAY("ho_num","",""),
						ARRAY("floor_num","",""),ARRAY("apt_name2","","")
				);
}ELSEIF($kind == "save") {
		$strPost = ARRAY(
					ARRAY("si","","Y"),ARRAY("gu","","Y"),ARRAY("dong","","Y"),ARRAY("price","","Y"),
					ARRAY("apt_name","",""),ARRAY("apt_area","",""),ARRAY("dong_num","",""),ARRAY("ho_num","",""),
					ARRAY("floor_num","",""),ARRAY("apt_name2","",""),ARRAY("rprice","","Y"),ARRAY("rdo_apt","","Y"),
					ARRAY("ramount","","Y"),ARRAY("rphone","","Y"),ARRAY("rname","","Y"),ARRAY("check01","","Y"),ARRAY("pid","","")
			);
} ELSE {
	$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
}

FOR($i=0;$i<COUNT($strPost);$i++)
{
	IF($strPost[$i][1] > 0)
	{
		//$strPostTarget = "";
		FOR($j=0;$j<COUNT($_POST[$strPost[$i][0]]);$j++)
		{
			IF($j == 0) { ${$strPost[$i][0]} = ""; }
			IF($j > 0)
			{
				${$strPost[$i][0]} .=  ",";
			}
			${$strPost[$i][0]} .= clean_xss_tags($_POST[$strPost[$i][0]][$j]);
		}
	} ELSE {
		IF($strPost[$i][2] == "Y")
		{
			//IF($_POST[$strPost[$i][0]]<>"")
			IF($_POST[$strPost[$i][0]]<>"" OR 1>0)  // 직접입력시 시구동등을 체크하는데 폼이 바뀌면서 직접입력시 시구동이 없어지면서 체크 막음 20220504 전차장
			{
				${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
			} ELSE {
				$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urldecode("값이 올바르지 않습니다. 다시 시도하여 주십시오 : ".$strPost[$i][0])),"retval"=>"");
				ECHO json_encode($objval, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);
				EXIT;
			}
		} ELSE {
			${$strPost[$i][0]} = clean_xss_tags($_POST[$strPost[$i][0]]);
		}
	}
}

$gstrNdate = DATE("Y-m-d H:i:s");

IF($kind == "auth")
{

	

	$strApt	=	new strAptPrice();

	IF($rdo_apt == "1")
	{
		$strAptNameArr	=	EXPLODE(",",$apt_name);
		$strAptAreaArr	=	EXPLODE(",",$apt_area);

		$aa = get_sise2($strAptNameArr[0], $strAptAreaArr[0]);

		$intPrice = $strApt->Apt_select($strAptNameArr[0], $strAptAreaArr[0]);

		$inrPercent = $strApt->Sale_percent($si);	// ltv

		$intRPrice = (replace_integer($intPrice)*$inrPercent) * 10000;
		IF($intPrice > 0) { $intPrice = $intPrice * 10000; }

	} ELSE {
		$intPrice = 0;
		$intRPrice = 0;
	}

	$strLinkURL = "loan_form.php";

	$objval = ARRAY("retcode"=>"OK","retkind"=>$rdo_apt,"retprice"=>$intPrice,"retrprice"=>$intRPrice,"retval"=>$strLinkURL, "aa"=>$aa);
	ECHO json_encode($objval, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);
	sql_close($connect_db);
	exit;


} ELSEIF($kind == "save") {

	// reCAPTCHA v3 적용
	$token = $_REQUEST["g-recaptcha-response"];

	//echo json_encode($token, true);

	$curlData = array(
		'secret'   => '6LdBq8UaAAAAACYrY13szIPAE4l9eqcdBCyi6RFY',
		'response' => $token
	);

	$post_field_string = http_build_query($curlData);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$curlResponse = curl_exec($ch);
	curl_close($ch);
		
	$captchaResponse = json_decode($curlResponse, true);

	$ret = array();
	$ret["capt_res"] = $captchaResponse;

	if ($captchaResponse["success"]=="true") {

	} else {
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오!!!")),"retval"=>"");
		ECHO json_encode($objval, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);
		EXIT;
	}

	//echo json_encode($ret, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);


	$strVal = ARRAY(
					"si" => $si,"gu" => $gu,"dong" => $dong,"price" => $price,"apt_name" => $apt_name,
					"apt_area" => $apt_area,"dong_num" => $dong_num,"ho_num" => $ho_num,"floor_num" => $floor_num,"apt_name2" => $apt_name2,
					"rprice" => $rprice,"ramount" => $ramount,"rphone" => $rphone,"rname" => $rname,"rdo_apt"=>$rdo_apt, "pid" => $pid
									 );

	$strApt	=	new strAptPrice();


	$strApt->Query_Save($strVal);

	$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("헬로펀딩 아파트 담보대출을 신청해주셔서 감사합니다.\n 1영업일 이내에 담당자 확인 후 연락드리겠습니다")),"retval"=>"/aptloan/loan_end.php");
	ECHO json_encode($objval, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);
	sql_close($connect_db);
	exit;

}
?>

