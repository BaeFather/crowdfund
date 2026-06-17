<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/termsList.do
## 5. 약관보기
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST['terms_type'];			// 약관종류 (1:회원가입 2:투자자이용약관)
$REQUEST['prod_cd'];			// 상품코드

// 결과코드 예시
// {"code":"0000","msg":"정상처리되었습니다." ,"terms_list":[ {"terms_nm":"이용약관',"terms_cd":"xxxxxxx","terms_mand_yn":"Y","terms_url":"https://www.xxxxxxx"} ]}
*/

if($REQUEST['terms_type']=='1') {
	$provision_path  = $base_path . "/company/provision.php";
	$provision_url   = $_CONF['host_domain'] . "/company/provision.php";
	$provision_title = "이용약관";
	$provision_code  = "siteprovision";
}
else if($REQUEST['terms_type']=='2') {
	$provision_path  = $base_path . "/company/provision2.php";
	$provision_url   = $_CONF['host_domain'] . "/company/provision2.php";
	$provision_title = "투자이용약관";
	$provision_code  = "investprovision";
}
else {
	$ARR = array("code"=>"9999","msg"=>"정책분류 전송오류"); echo printJson($ARR); exit;
}


if( in_array($REQUEST['terms_type'], array('1','2')) ) {

	$ARR['code'] = "0000";
	$ARR['msg']  = "정상처리되었습니다";
	$ARR['terms_list'] = array();

	$array = array(
		'terms_nm'      => $provision_title,
		'terms_mand_yn' => 'Y',
		'terms_url'     => $provision_url,
		'terms_cd'      => $provision_code
	);

	array_push($ARR['terms_list'], $array);

}


##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>