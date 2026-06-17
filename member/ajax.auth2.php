<?
/**********************************************************************************************
	NICE평가정보 휴대폰 인증 서비스
		step2. 입력된 인증번호의 일치 확인 - 인증 마무리

	모듈이 설치된 서버의 네트웍크 및 방확벽 관련하여 아래 IP와 Port를 오픈해 주셔야 이용 가능합니다.
	IP : 121.131.196.200 / Port : 3700 ~ 3715
**********************************************************************************************/

include_once('_common.php');
include_once(G5_LIB_PATH . '/crypt.lib.php');
include_once(G5_PATH . '/mypage/crypt.php');

while( list($k, $v) = each($_POST) ) { ${$k} = trim($v); }

/*
$_POST['ResponseSeq'];		// 응답고유번호
$_POST['Authno'];					// SMS인증번호
$_POST['RequestSeq'];			// 요청SEQ_식별값
$_POST['Mhp'];
$_POST['Mname'];
*/


$DATA = array(
	'stage'        => 'CNFM',
	'sResponseSeq' => $ResponseSeq,		// 응답SEQ
	'sAuthno'      => $Authno,					// ajax.auth1.php에서 받은 인증번호
	'sRequestSeq'  => $RequestSeq,			// 요청SEQ(option)
	'Mhp'          => $Mhp,
	'Mname'        => $Mname
);
$sResultData = curlNiceAuth($DATA);

$RSTMP = (array)json_decode($sResultData);		// object 를 array 로 변환

if($RSTMP['reqflag']=='success') {

	$RESULT = array(
		"rescode" => $RSTMP['rescode'],
		"reqseq"  => $RSTMP['reqseq'],
		"resseq"  => $RSTMP['resseq'],
		"ci"      => $RSTMP['ci'],			// CI (연계정보: 88byte)
		"di"      => $RSTMP['di'],			// DI (중복정보: 64byte)
		"mkd"			=> $R['cnt']
	);

	if($RSTMP['rescode'] == '0000') {

		sql_query("
			UPDATE
				cf_auth_nice
			SET
				auth_finish='1',
				ci='".$RESULT['ci']."'
			WHERE 1
				AND reqseq  = '".$RSTMP['reqseq']."'
				AND rescode = '".$RSTMP['rescode']."'
				AND resseq  = '".$RSTMP['resseq']."'");

	}

	echo json_encode($RESULT, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);

}

sql_close();

?>