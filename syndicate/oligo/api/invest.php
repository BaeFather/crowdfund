<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/invest.do
## 7. 투자가능여부확인
##		중복투자 불허용
## (투자프로세스 : invest.php -> investEnd.php -> 기표시 investResultReport 실행
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST['ci']					//*** 복호화 필요
$REQUEST['user_nm']
$REQUEST['user_hp']			//*** 복호화 필요
$REQUEST['prod_cd']
$REQUEST['invest_amt']
$REQUEST['terms_list'] = array();
$REQUEST['terms_list']['terms_cd'] = 'investprovision';		// 약관코드
$REQUEST['terms_list']['agree_yn'] = 'Y';									// 동의여부
*/

// 2021-08-26 시스템 점검중
$ARR = array("code"=>'9999', "msg"=>"시스템 점검중"); echo printJson($ARR); exit;


$REQUEST['ci'] = urldecode($REQUEST['ci']);
$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }

if(!$REQUEST['prod_cd']) { $ARR = array("code"=>"9999", "msg"=>"상품코드 누락"); echo printJson($ARR); exit; }
if(!$REQUEST['invest_amt']) { $ARR = array("code"=>"9999", "msg"=>"투자금액 누락"); echo printJson($ARR); exit; }
if($REQUEST['terms_list']['terms_cd'] == 'siteprovision' && $REQUEST['terms_list']['agree_yn']!='Y') { $ARR = array("code"=>'9999', "msg"=>"헬로펀딩 서비스 이용약관 미동의 고객입니다."); echo printJson($ARR); exit; }
if($REQUEST['terms_list']['terms_cd'] == 'investprovision' && $REQUEST['terms_list']['agree_yn']!='Y') { $ARR = array("code"=>'9999', "msg"=>"헬로펀딩 투자이용약관 미동의 고객입니다."); echo printJson($ARR); exit; }

$REQUEST['user_hp']  = $crypto->deCrypt($REQUEST['user_hp']);


$MB = get_member($mb_id);
$MB['invest_able_amount'] = getInvestAbleAmountOligo($REQUEST['prod_cd'], $MB['mb_no']);	// 현재예치금을 무시한 본상품의 투자가능금액 추출

$PRDT = sql_fetch("
	SELECT
		A.idx, A.title, A.recruit_amount, A.start_datetime, A.end_datetime, invest_end_date,
		live_invest_amount AS total_invest_amount
	FROM
		cf_product A
	WHERE (1)
		AND A.idx='".$REQUEST['prod_cd']."'
		-- AND A.display='N' AND scrap_out='' AND isTest='1' AND only_vip=''
");
//echo $PRDT['recruit_need_amount']."\n";
if(!$PRDT['idx']) { $ARR = array("code"=>'9999', "msg"=>"상품정보가 없습니다."); echo printJson($ARR); exit; }
if($PRDT['start_datetime'] > DATE_YMDHIS) { $ARR = array("code"=>'9999', "msg"=>"모집 대기중인 상품입니다."); echo printJson($ARR); exit; }
if($PRDT['end_datetime'] < DATE_YMDHIS) { $ARR = array("code"=>'9999', "msg"=>"모집 기간이 지난 상품입니다."); echo printJson($ARR); exit; }
if($PRDT['invest_end_date']!='') { $ARR = array("code"=>'9999', "msg"=>"모집 종료된 상품입니다."); echo printJson($ARR); exit; }
if($REQUEST['invest_amt'] < $CONF['min_invest_limit']) { $ARR = array('code'=>'9999', 'msg'=>'최소투자금액오류'); echo printJson($ARR); exit; }
if($REQUEST['invest_amt'] > $MB['invest_able_amount']) { $ARR = array("code"=>'9999', "msg"=>"투자가능금액을 초과하였습니다. 투자가능금액(".number_format($MB['invest_able_amount']).")원"); echo printJson($ARR); exit; }

// 입금대기중인 데이터가 있을 경우
$WAIT_INVEST = sql_fetch("
	SELECT
		idx
	FROM
		cf_product_invest_detail
	WHERE 1
		AND product_idx = '".$REQUEST['prod_cd']."'
		AND member_idx = '".$MB['mb_no']."'
		AND invest_state = 'W'
		AND syndi_id = '".$_CONF['SYNDI_ID']."'
	ORDER BY
		idx DESC
	LIMIT 1");
if($WAIT_INVEST['idx']) {
	$ARR = array("code"=>'9999', "msg"=>"대기중인 투자건 있음"); echo printJson($ARR); exit;
}

// *** 중복투자 금지 *** //
$INVESTED = sql_fetch("
	SELECT
		idx, syndi_id
	FROM
		cf_product_invest
	WHERE 1
		AND product_idx = '".$REQUEST['prod_cd']."'
		AND member_idx = '".$MB['mb_no']."'
		AND invest_state = 'Y'");
if($INVESTED['idx']) {
	if($INVESTED['syndi_id']=='oligo') {
		$ARR = array("code"=>'9999', "msg"=>"중복투자오류"); echo printJson($ARR); exit;
	}
	else {
		$ARR = array("code"=>'9999', "msg"=>"중복투자오류(타 플랫폼을 통하여 이미 투자된 상품)"); echo printJson($ARR); exit;
	}
}


$ARR['code'] = '0000';
$ARR['msg'] = '투자가능합니다.';
$ARR['danger_txt'] = '헬로펀딩은 투자원금과 수익을 보장하지 않으며, 투자손실에 대한 책임은 모두 투자자에게 있습니다.';



##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>