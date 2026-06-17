<?
/**********************************************************************************************
	NICE평가정보 휴대폰 인증 서비스
		step1. 개인정보를 받아 인증번호를 발송한다.

	모듈이 설치된 서버의 네트웍크 및 방확벽 관련하여 아래 IP와 Port를 오픈해 주셔야 이용 가능합니다.
	IP : 121.131.196.200 / Port : 3700 ~ 3715
**********************************************************************************************/

include_once('_common.php');
include_once(G5_LIB_PATH . '/crypt.lib.php');
include_once(G5_PATH . '/mypage/crypt.php');

while( list($k, $v) = each($_POST) ) { ${$k} = trim($v); }

/*
$_POST['Mbirth']
$_POST['Msex']
$_POST['Mname']
$_POST['Mhp']
$_POST['Mhpcomp']
$_POST['Muse']
*/

$key         = 'jumin';

$iBirth      = $Mbirth;														// 생년월일 19880609
$iSex        = $Msex;
$sBirth      = substr($iBirth, 2, 6) . $iSex;								// 주민번호 앞 7자리

$oName       = sql_real_escape_string($Mname);

$sHP         = $Mhp;																// 휴대폰번호
$sHPComp     = $Mhpcomp;														// 이통사
$sRequestSeq = date('YmdHis')."-".rand(100,999);						// 요청SEQ_식별값
$use         = $Muse;


$DATA = array(
	'stage'       => 'AUTH',
	'sBirth'      => $sBirth,
	'sName'       => $oName,		//'sName' => $sName,
	'sHPComp'     => $sHPComp,
	'sHP'         => $sHP,
	'sRequestSeq' => $sRequestSeq
);
$sResultData = curlNiceAuth($DATA);

$RSTMP = (array)json_decode($sResultData);		// object 를 array 로 변환

if($RSTMP['reqflag']=='success') {

	$sql = "
		INSERT INTO
			cf_auth_nice
		SET
			auth_name      = '".$oName."',
			auth_birth     = '". encrypt($Mbirth, $key)."',
			auth_sex       = '".$iSex."',
			auth_telecom   = '".$sHPComp."',
			auth_phone_num = '".$sHP."',
			reqseq         = '".$RSTMP['reqseq']."',
			rescode        = '".$RSTMP['rescode']."',
			resseq         = '".$RSTMP['resseq']."',
			insert_date    = NOW()";
	sql_query($sql);
	$insert_idx = sql_insert_id();

	$RESULT = array(
	  "src"     => $nice_send_data,
		"res"     => $sResultData,
		"reqseq"  => $RSTMP['reqseq'],
		"rescode" => $RSTMP['rescode'],
		"resseq"  => $RSTMP['resseq'],
		"already" => $already,
		"sql_id"  => $insert_idx
	);

	echo json_encode($RESULT, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);

}

sql_close();

?>