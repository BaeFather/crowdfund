<?
################################################################################
## /usr/local/php/bin/php -q /home/crowdfund/public_html/insidebank_test.php [000|128|256] [debug]
################################################################################

define("_GNUBOARD_", true);

error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );

include_once("_common.php");

$base_path = "/home/crowdfund/public_html";
include_once($base_path."/lib/common.lib.php");
include_once($base_path."/lib/insidebank.lib.php");

/*
if($_SERVER['HTTP_USER_AGENT']) {

}
*/


$SHISDBK['target_host']       = "222.231.31.120";
//$SHISDBK['target_host']       = "222.231.31.34";
$SHISDBK['000']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5000";  //TESTCALL
$SHISDBK['128']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5001";
$SHISDBK['128']['enc_key']    = "ECgYB1tH7pFPbDvT";
$SHISDBK['256']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5002";
$SHISDBK['256']['enc_key']    = "esYax1AADKlC7KmTjhdcd6itjLQ+2cyU";


while( list($k, $v)=@each($_REQUEST) ) { ${$k} = @trim($v); }

//print_r($_SERVER); exit;

$enc_bit = ($_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : $enc_bit;
$enc_bit = ( in_array($enc_bit, array('000','128','256')) ) ? $enc_bit : '256';
$mode    = ($_SERVER['argv']['2']) ? $_SERVER['argv']['2'] :	$mode;

//$enc_bit = "000";
//$mode = 'debug';


// TESTCALL
/*
print( insidebank_request('000') );
*/


// 결번요청
/*
$ARR['SUBMIT_GBN'] = "04";						//전문번호
$ARR['TRANDATE']   = "20170728"; //date('Ymd');
$ARR['ORI_FB_SEQ'] = "HEL9QIX6AK";
*/

//고객정보등록
/*
$ARR['REQ_NUM']     = "010";							//전문번호
$ARR['SUBMIT_GBN']  = "01";								//거래구분 (02:변경)
$ARR['CUST_ID']     = "M0000000817";			//고객ID (20자 제한이 있어 '0채운 10자리 회원번호'로 발송)
$ARR['CUST_NM']     = "배재수";						//고객명
$ARR['CUST_SUB_NM'] = "";									//고객부기명
$ARR['REP_NM']      = "";									//대표자고객명
$ARR['BIRTH_DATE']  = "19750903";					//생년월일자 YYYYMMDD
$ARR['SUP_REG_NB']  = "";									//사업자번호
$ARR['PRI_SUP_GBN'] = "1";								//개인사업자구분
$ARR['HP_NO1']      = "010";							//휴대폰지역번호
$ARR['HP_NO2']      = "6406";							//휴대폰국번호
$ARR['HP_NO3']      = "3972";							//휴대폰일련번호
$ARR['BANK_CD']     = "004";							//은행코드
$ARR['ACCT_NB']     = "59440204031532";		//은행계좌
$ARR['CMS_NB']      = "56212670605010 ";	//가상계좌번호
*/

//고객정보변경
/*
$ARR['REQ_NUM']     = "010";							//전문번호
$ARR['SUBMIT_GBN']  = "02";								//거래구분 (02:변경)
$ARR['CUST_ID']     = "0000002918";				//고객ID
$ARR['CUST_NM']     = "배재수(헬로펀딩)";						//고객명
$ARR['CUST_SUB_NM'] = "";									//고객부기명
$ARR['REP_NM']      = "";									//대표자고객명
$ARR['BIRTH_DATE']  = "19750903";					//생년월일자 YYYYMMDD
$ARR['SUP_REG_NB']  = "";									//사업자번호
$ARR['PRI_SUP_GBN'] = "1";								//개인사업자구분
$ARR['HP_NO1']      = "010";							//휴대폰지역번호
$ARR['HP_NO2']      = "6406";							//휴대폰국번호
$ARR['HP_NO3']      = "3972";							//휴대폰일련번호
$ARR['BANK_CD']     = "004";							//은행코드
$ARR['ACCT_NB']     = "59440204031532";		//은행계좌
$ARR['CMS_NB']      = "56212670605026";	  //가상계좌번호
*/

//수취인 조회
/*
$ARR['REQ_NUM']     = "040";
$ARR['BANK_CD']     = "088";
$ARR['ACCT_NB']     = "56212670605031";
*/

$INVEST = array(
	array("member_idx" => 232, "amount" => 5000000),
	array("member_idx" => 305, "amount" => 1000000),
	array("member_idx" => 327, "amount" => 5000000),
	array("member_idx" => 409, "amount" => 200000),
	array("member_idx" => 635, "amount" => 500000),
	array("member_idx" => 795, "amount" => 100000),
	array("member_idx" => 841, "amount" => 100000),
	array("member_idx" => 1166, "amount" => 1500000),
	array("member_idx" => 1186, "amount" => 100000),
	array("member_idx" => 1324, "amount" => 500000),
	array("member_idx" => 1390, "amount" => 2000000),
	array("member_idx" => 1470, "amount" => 2000000),
	array("member_idx" => 1592, "amount" => 2000000),
	array("member_idx" => 1597, "amount" => 1000000),
	array("member_idx" => 1659, "amount" => 500000),
	array("member_idx" => 1754, "amount" => 5000000),
	array("member_idx" => 1851, "amount" => 5000000),
	array("member_idx" => 2021, "amount" => 500000),
	array("member_idx" => 2168, "amount" => 5000000),
	array("member_idx" => 2192, "amount" => 3000000),
	array("member_idx" => 2254, "amount" => 100000),
	array("member_idx" => 2289, "amount" => 1000000),
	array("member_idx" => 2320, "amount" => 1000000),
	array("member_idx" => 2321, "amount" => 2000000),
	array("member_idx" => 2368, "amount" => 100000),
	array("member_idx" => 2376, "amount" => 1500000),
	array("member_idx" => 2473, "amount" => 5000000),
	array("member_idx" => 2492, "amount" => 2000000),
	array("member_idx" => 2498, "amount" => 100000),
	array("member_idx" => 2584, "amount" => 1500000),
	array("member_idx" => 2613, "amount" => 200000),
	array("member_idx" => 2620, "amount" => 2000000),
	array("member_idx" => 2674, "amount" => 5000000),
	array("member_idx" => 2722, "amount" => 500000),
	array("member_idx" => 2759, "amount" => 3000000),
	array("member_idx" => 2829, "amount" => 2000000),
	array("member_idx" => 2844, "amount" => 6000000),
	array("member_idx" => 2849, "amount" => 500000),
	array("member_idx" => 2857, "amount" => 1000000),
	array("member_idx" => 3027, "amount" => 1000000),
	array("member_idx" => 3077, "amount" => 100000),
	array("member_idx" => 3084, "amount" => 100000),
	array("member_idx" => 3102, "amount" => 5000000),
	array("member_idx" => 3109, "amount" => 5000000),
	array("member_idx" => 3140, "amount" => 100000),
	array("member_idx" => 3146, "amount" => 1000000),
	array("member_idx" => 3151, "amount" => 1000000),
	array("member_idx" => 3152, "amount" => 1000000),
	array("member_idx" => 3337, "amount" => 3000000),
	array("member_idx" => 3396, "amount" => 200000),
	array("member_idx" => 3425, "amount" => 100000),
	array("member_idx" => 3432, "amount" => 5000000),
	array("member_idx" => 3472, "amount" => 100000),
	array("member_idx" => 3500, "amount" => 200000),
	array("member_idx" => 3505, "amount" => 500000),
	array("member_idx" => 3515, "amount" => 200000),
	array("member_idx" => 3521, "amount" => 100000),
	array("member_idx" => 3547, "amount" => 2000000),
	array("member_idx" => 3605, "amount" => 5000000),
	array("member_idx" => 3612, "amount" => 10000000),
	array("member_idx" => 3641, "amount" => 500000),
	array("member_idx" => 3677, "amount" => 100000),
	array("member_idx" => 3690, "amount" => 3400000),
	array("member_idx" => 3694, "amount" => 100000),
	array("member_idx" => 3695, "amount" => 5000000),
	array("member_idx" => 3714, "amount" => 5000000),
	array("member_idx" => 3723, "amount" => 100000),
	array("member_idx" => 3726, "amount" => 5000000),
	array("member_idx" => 3729, "amount" => 100000),
	array("member_idx" => 3750, "amount" => 3000000),
	array("member_idx" => 3799, "amount" => 500000),
	array("member_idx" => 3842, "amount" => 1000000),
	array("member_idx" => 3855, "amount" => 1000000),
	array("member_idx" => 3871, "amount" => 5000000),
	array("member_idx" => 3878, "amount" => 500000),
	array("member_idx" => 3879, "amount" => 1000000),
	array("member_idx" => 3917, "amount" => 1000000),
	array("member_idx" => 3929, "amount" => 16000000),
	array("member_idx" => 3947, "amount" => 1000000),
	array("member_idx" => 3952, "amount" => 5000000),
	array("member_idx" => 3954, "amount" => 1000000),
	array("member_idx" => 3956, "amount" => 600000),
	array("member_idx" => 3966, "amount" => 500000),
	array("member_idx" => 3971, "amount" => 1000000),
	array("member_idx" => 3987, "amount" => 300000),
	array("member_idx" => 3992, "amount" => 2000000),
	array("member_idx" => 4004, "amount" => 5000000),
	array("member_idx" => 4013, "amount" => 1000000),
	array("member_idx" => 4030, "amount" => 5000000),
	array("member_idx" => 4031, "amount" => 100000),
	array("member_idx" => 4044, "amount" => 5000000),
	array("member_idx" => 4051, "amount" => 3000000),
	array("member_idx" => 4052, "amount" => 1000000),
	array("member_idx" => 4055, "amount" => 5000000),
);

for($i=0,$j=1; $i<count($INVEST); $i++,$j++) {

	//고객예치금정보조회
	$ARR['REQ_NUM']     = "041";
	$ARR['CUST_ID']     = $INVEST[$i]['member_idx'];

	$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);

	debug_flush($j." : " . $INVEST[$i]['member_idx'] . " - " . number_format($INVEST[$i]['amount']) . " ::::: " . number_format($insidebank_result['BALANCE_AMT'])."<br>\n");

}

exit;



/*
//고객정보조회
$ARR['REQ_NUM']     = "010";
$ARR['SUBMIT_GBN']  = "04";
$ARR['CUST_ID']     = "107";
*/

//고객해지 (취급주의)
/*
$ARR['REQ_NUM']     = "010";
$ARR['SUBMIT_GBN']  = "03";
$ARR['CUST_ID']     = "0000000228";
*/

//집계조회
/*
$ARR['REQ_NUM']     = "044";
$ARR['STAND_DATE']  = "20170712";
*/

$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);

exit;

?>