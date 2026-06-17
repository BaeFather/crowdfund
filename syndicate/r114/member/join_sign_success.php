<?php
//==============================================================================
// SESSION 설정
//------------------------------------------------------------------------------
@ini_set("session.use_trans_sid", 0);	// PHPSESSID를 자동으로 넘기지 않음
@ini_set("url_rewriter.tags",""); // 링크에 PHPSESSID가 따라다니는것을 무력화함 (해뜰녘님께서 알려주셨습니다.)

session_save_path($_SERVER['DOCUMENT_ROOT']."/data/session");

if (isset($SESSION_CACHE_LIMITER))
	@session_cache_limiter($SESSION_CACHE_LIMITER);
else
	@session_cache_limiter("no-cache, must-revalidate");

ini_set("session.cache_expire", 180); // 세션 캐쉬 보관시간 (분)
ini_set("session.gc_maxlifetime", 10800); // session data의 garbage collection 존재 기간을 지정 (초)
ini_set("session.gc_probability", 1); // session.gc_probability는 session.gc_divisor와 연계하여 gc(쓰레기 수거) 루틴의 시작 확률을 관리합니다. 기본값은 1입니다. 자세한 내용은 session.gc_divisor를 참고하십시오.
ini_set("session.gc_divisor", 100); // session.gc_divisor는 session.gc_probability와 결합하여 각 세션 초기화 시에 gc(쓰레기 수거) 프로세스를 시작할 확률을 정의합니다. 확률은 gc_probability/gc_divisor를 사용하여 계산합니다. 즉, 1/100은 각 요청시에 GC 프로세스를 시작할 확률이 1%입니다. session.gc_divisor의 기본값은 100입니다.

session_set_cookie_params(0, '/');
//ini_set("session.cookie_domain", G5_COOKIE_DOMAIN);

@session_start();
//==============================================================================
header("Content-Type: text/html; charset=euc-kr");

/*****************************
//아파치에서 모듈 로드가 되지 않았을경우 동적으로 모듈을 로드합니다.
if(!extension_loaded('CPClient')) {
	dl('CPClient.' . PHP_SHLIB_SUFFIX);
}
$module = 'CPClient';
*****************************/

$sitecode   = "AB917";						// NICE로부터 부여받은 사이트 코드
$sitepasswd = "8vJBrEtmUvdb";			// NICE로부터 부여받은 사이트 패스워드


$enc_data = $_POST["EncodeData"];		// 암호화된 결과 데이타
$sReserved1 = $_POST['param_r1'];
$sReserved2 = $_POST['param_r2'];
$sReserved3 = $_POST['param_r3'];

//////////////////////////////////////////////// 문자열 점검///////////////////////////////////////////////
if(preg_match('~[^0-9a-zA-Z+/=]~', $enc_data, $match)) {echo "입력 값 확인이 필요합니다 : ".$match[0]; exit;} // 문자열 점검 추가.
if(base64_encode(base64_decode($enc_data))!=$enc_data) {echo "입력 값 확인이 필요합니다"; exit;}

if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved1, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved2, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved3, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($enc_data != "") {

	//if (extension_loaded($module)) {// 동적으로 모듈 로드 했을경우
		$plaindata = get_decode_data($sitecode, $sitepasswd, $enc_data);// 암호화된 결과 데이터의 복호화
	//} else {
	//	$plaindata = "Module get_response_data is not compiled into PHP";
	//}

	//echo "[plaindata]  " . $plaindata . "<br>";

	if ($plaindata == -1){
		$returnMsg  = "암/복호화 시스템 오류";
	}else if ($plaindata == -4){
		$returnMsg  = "복호화 처리 오류";
	}else if ($plaindata == -5){
		$returnMsg  = "HASH값 불일치 - 복호화 데이터는 리턴됨";
	}else if ($plaindata == -6){
		$returnMsg  = "복호화 데이터 오류";
	}else if ($plaindata == -9){
		$returnMsg  = "입력값 오류";
	}else if ($plaindata == -12){
		$returnMsg  = "사이트 비밀번호 오류";
	}else{
		// 복호화가 정상적일 경우 데이터를 파싱합니다.
		$requestnumber  = GetValue($plaindata , "REQ_SEQ");
		$responsenumber = GetValue($plaindata , "RES_SEQ");
		$authtype       = GetValue($plaindata , "AUTH_TYPE");
		$name           = GetValue($plaindata , "NAME");
		$birthdate      = GetValue($plaindata , "BIRTHDATE");
		$gender         = GetValue($plaindata , "GENDER");
		$nationalinfo   = GetValue($plaindata , "NATIONALINFO");	//내/외국인정보(사용자 매뉴얼 참조)
		$dupinfo        = GetValue($plaindata , "DI");
		$conninfo       = GetValue($plaindata , "CI");
		$MOBILE_NO      = GetValue($plaindata , "MOBILE_NO");

		if($MOBILE_NO){
			$mb_hp  = str_replace("-","",$MOBILE_NO);
			$mb_hp1 = substr($mb_hp,0,3);
			$mb_hp2 = substr($mb_hp,3,-4);
			$mb_hp3 = substr($mb_hp,-4);
		}

		if(strcmp($_SESSION["REQ_SEQ"], $requestnumber) != 0)
		{
			echo "세션값이 다릅니다. 올바른 경로로 접근하시기 바랍니다.<br>";
			$requestnumber = "";
			$responsenumber = "";
			$authtype = "";
			$name = "";
			$birthdate = "";
			$gender = "";
			$nationalinfo = "";
			$dupinfo = "";
			$conninfo = "";
		}
	}
}



function GetValue($str , $name)
{
	$pos1 = 0;  //length의 시작 위치
	$pos2 = 0;  //:의 위치

	while( $pos1 <= strlen($str) )
	{
		$pos2 = strpos( $str , ":" , $pos1);
		$len = substr($str , $pos1 , $pos2 - $pos1);
		$key = @substr($str , $pos2 + 1 , $len);
		$pos1 = $pos2 + $len + 1;
		if( $key == $name )
		{
			$pos2 = strpos( $str , ":" , $pos1);
			$len = substr($str , $pos1 , $pos2 - $pos1);
			$value = @substr($str , $pos2 + 1 , $len);
			return $value;
		}
		else
		{
			// 다르면 스킵한다.
			$pos2 = strpos( $str , ":" , $pos1);
			$len = substr($str , $pos1 , $pos2 - $pos1);
			$pos1 = $pos2 + $len + 1;
		}
	}
}


$todate = date("Ymd");

$now_old = $todate - $birthdate;

if($now_old < 190000) {
?>
<html>
<head>
<title>NICE평가정보 - CheckPlus 본인인증</title>
</head>
<body>
<script>
alert('19세 미만의 미성년자는 법정 대리인 정보를 등록하여 서비스 사용승인을 받으셔야 합니다.');
window.opener.location.href='/member/join_info_none_auth.php?user=junior';
window.self.close();
</script>
</body>
</html>
<?
}
else {
?>
<html>
<head>
<title>NICE평가정보 - CheckPlus 본인인증</title>
</head>
<body>
<script>
	window.opener.document.getElementById('member_sign').innerHTML = "<span style='color:green'>정상 인증 되었습니다.</span> <span style='color:#aaa'>(<?=$name?> " + "<?=$mb_hp1?>" + "-" + "<?=$mb_hp2?>" + "-" + "<?=$mb_hp3?>)</span>";
	window.opener.document.getElementById('is_sign').value = 'Y';
	window.opener.document.getElementById('mb_dupinfo').value = '<?=$responsenumber?>';
//window.opener.document.getElementById('mb_dupinfo').value = '<?=$dupinfo?>';

	window.opener.document.getElementById('auth_mb_hp').value = '<?=$mb_hp1.$mb_hp2.$mb_hp3?>';
	window.opener.document.getElementById('auth_mb_name').value = '<?=$name?>';

	window.opener.document.getElementById('mb_name').value = '<?=$name?>';
	window.opener.document.getElementById('mb_hp1').value = '<?=$mb_hp1?>';
	window.opener.document.getElementById('mb_hp2').value = '<?=$mb_hp2?>';
	window.opener.document.getElementById('mb_hp3').value = '<?=$mb_hp3?>';

	window.opener.document.getElementById('btn_certi').style.display="none";

	window.self.close();
</script>
</body>
</html>
<?
}
?>