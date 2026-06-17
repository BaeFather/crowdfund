<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/checkJoin.do
## 3. 가입여부체크
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST['ci'];							// 본인인증 CI 값 전송
$REQUEST['prod_cd'];				// 상품코드 --> 필수값 아님
$REQUEST['acc_user_nm'];		// 예금주
$REQUEST['bank_cd'];				// 은행코드
$REQUEST['acc_no'];					// 계좌번호 (AES256)

$ARR['code']					// 결과코드
$ARR['msg']						// 결과메시지
$ARR['join_yn']				// 가입여부
$ARR['min_amt']				// 최소투자가능금액
$ARR['max_amt']				// 최대투자가능금액
$ARR['min_unit']			// 최소투자가능단위
$ARR['bank_cd']				// 가상계좌은행코드
$ARR['acc_no']				// 가상계좌정보(AES256 암호화 필요)

*/

$REQUEST['ci'] = urldecode($REQUEST['ci']);

if(!$REQUEST['ci'])          { $ARR = array("code"=>"9999", "msg"=>"CI 누락"); echo printJson($ARR); exit; }
//if(!$REQUEST['acc_user_nm']) { $ARR = array("code"=>"9999", "msg"=>"예금주 누락"); echo printJson($ARR); exit; }		// 필수값 아님
//if(!$REQUEST['bank_cd'])     { $ARR = array("code"=>"9999", "msg"=>"은행코드 누락"); echo printJson($ARR); exit; }	// 필수값 아님
//if(!$REQUEST['acc_no'])      { $ARR = array("code"=>"9999", "msg"=>"계좌번호 누락"); echo printJson($ARR); exit; }	// 필수값 아님

$mb_id = memberCheck($REQUEST['ci']);
if($mb_id) {
	$MB = get_member($mb_id);
	$is_member = true;
}


if($REQUEST['prod_cd']) {

	$prod_sql = "
		SELECT
			A.idx, A.gr_idx, A.category, A.state, A.invest_end_date,
			( A.recruit_amount - (SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') ) AS need_recruit_amount
		FROM
			cf_product A
		WHERE 1
			AND A.idx='".$REQUEST['prod_cd']."'";
	$PRDT = sql_fetch($prod_sql);
	if(!$PRDT['idx']) { $ARR = array("code"=>"9999", "msg"=>"상품코드 오류"); echo printJson($ARR); exit; }

	//////////////////////
	// 투자가능금액 계산
	//////////////////////
	$site_limit = ($is_member) ? $INDI_INVESTOR[$MB['member_investor_type']]['site_limit'] : $INDI_INVESTOR['1']['site_limit'];

	if($PRDT['idx'] == $PRDT['gr_idx']) {
		$product_limit = ($is_member) ? $INDI_INVESTOR[$MB['member_investor_type']]['single_product_limit'] : $INDI_INVESTOR['1']['single_product_limit'];
	}
	else {
		$product_limit = ($is_member) ? $INDI_INVESTOR[$MB['member_investor_type']]['group_product_limit'] : $INDI_INVESTOR['1']['group_product_limit'];
	}

	if($PRDT['category']=='2') {
		$ca_limit = ($is_member) ? $INDI_INVESTOR[$MB['member_investor_type']]['prpt_limit'] : $INDI_INVESTOR['1']['prpt_limit'];
		$max_amt = min( array($PRDT['need_recruit_amount'], $site_limit, $product_limit, $ca_limit) );
	}
	else {
		$max_amt = min( array($PRDT['need_recruit_amount'], $site_limit, $product_limit) );
	}

	$min_amt = min( array($CONF['min_invest_limit'], $PRDT['need_recruit_amount'], $max_amt) );


}

$acc_no = preg_replace("/(-| )/", "", $crypto->deCrypt($REQUEST['acc_no']));			// 값이 있을경우에만 출력


$mbSql = "
	SELECT
		mb_no, mb_ci, oligo_userid, va_bank_code2, virtual_account2
	FROM
		g5_member
	WHERE 1
		AND mb_level='1' AND member_group='F' AND member_type='1'
		AND mb_ci='".$REQUEST['ci']."'";
//echo iconv('utf-8', 'euc-kr', $mbSql);
$MB = sql_fetch($mbSql);

if($MB['mb_no']) {

	//if($MB['mb_ci']=='') {
	//	sql_query("UPDATE g5_member SET mb_ci='".$REQUEST['ci']."' WHERE mb_no='".$MB['mb_no']."'");		// ci값이 없으면 올리고에서 주는 ci값으로 대체
	//}

	if($MB['oligo_userid']=='') {
		// 기존 회원인데 올리고 플래그가 없을 경우 미가입자로 분류하여 join.php 에서 정보를 업데이트 받도록 처리한다.
		$ARR['code']     = "0000";
		$ARR['msg']      = "정상처리되었습니다.";
		$ARR['join_yn']  = "N";			// 미가입자
	}
	else {
		if($MB['virtual_account2']) {
			$ARR['code']     = "0000";
			$ARR['msg']      = "정상처리되었습니다";
			$ARR['join_yn']  = "Y";			// 기가입자
		}
		else {
			// 가상계좌 없을 경우 미가입자 처리해서 join.php에서 가상계좌를 발급받을 수 있도록 유도
			$ARR['code']     = "0000";
			$ARR['msg']      = "정상처리되었습니다.";
			$ARR['join_yn']  = "N";			// 미가입자
		}
	}

}
else {

	$ARR['code']     = "0000";
	$ARR['msg']      = "정상처리되었습니다.";
	$ARR['join_yn']  = "N";			// 미가입자

}



if($PRDT['idx']) {
	$ARR['min_amt']  = (string)$min_amt;
	$ARR['max_amt']  = (string)$max_amt;
	$ARR['min_unit'] = '1';														// 최소투자단위 : 만원(1)/십만원(2)
}


if($ARR['join_yn']=='Y') {
	$ARR['bank_cd'] = $MB['va_bank_code2'];
	$ARR['acc_no']  = $crypto->enCrypt($MB['virtual_account2']);		// 가상계좌번호 AES256 암호화처리
}
else {
	$ARR['bank_cd'] = '';
	$ARR['acc_no']  = '';
}


##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>