<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/investEnd.do
## 8. 투자처리
##	2020-10-08 사전투자 관련 항목 제거
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");
@include_once($base_path . "/lib/sms.lib.php");

/*
$REQUEST['ci']            = 'INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==';		//*** 복호화 필요
$REQUEST['prod_cd']       = '2134';
$REQUEST['agree_yn']	    = 'Y';			// '동의함'텍스트 입력여부
$REQUEST['inve_agree_yn'] = 'Y';			// 투자자이용약관 동의여부
$REQUEST['invest_amt']    = '5000000';
*/

$REQUEST['ci'] = urldecode($REQUEST['ci']);
$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }

if(!$REQUEST['prod_cd'])           { $ARR = array("code"=>"9999", "msg"=>"상품코드 누락"); echo printJson($ARR); exit; }
if($REQUEST['agree_yn']!='Y')      { $ARR = array("code"=>'9999', "msg"=>"동의함 미입력"); echo printJson($ARR); exit; }
if($REQUEST['inve_agree_yn']!='Y') { $ARR = array("code"=>'9999', "msg"=>"투자자이용약관 미동의"); echo printJson($ARR); exit; }
if(!$REQUEST['invest_amt'])        { $ARR = array("code"=>"9999", "msg"=>"투자금액 누락"); echo printJson($ARR); exit; }


$MB = get_member($mb_id);
$MB['invest_able_amount'] = getInvestAbleAmountOligo($REQUEST['prod_cd'], $MB['mb_no']);	// 현재예치금을 무시한 본상품의 투자가능금액 추출

$PRDT = sql_fetch("
	SELECT
		A.idx, A.category, A.mortgage_guarantees, A.title, A.recruit_amount, A.open_datetime, A.start_datetime, A.end_datetime, A.invest_end_date,
		A.live_invest_amount AS total_invest_amt
	FROM
		cf_product A
	WHERE (1)
		AND A.idx='".$REQUEST['prod_cd']."'
		-- AND A.display='Y' AND A.scrap_out='' AND A.isTest='' AND A.only_vip=''
");
//echo $PRDT['recruit_need_amount']."\n";
if(!$PRDT['idx']) { $ARR = array("code"=>'9999', "msg"=>"상품정보가 없습니다."); echo printJson($ARR); exit; }
if($PRDT['start_datetime'] > DATE_YMDHIS) { $ARR = array("code"=>'9999', "msg"=>"모집 대기중인 상품입니다."); echo printJson($ARR); exit; }
if($PRDT['end_datetime'] < DATE_YMDHIS) { $ARR = array("code"=>'9999', "msg"=>"모집 기간이 지난 상품입니다."); echo printJson($ARR); exit; }
if($PRDT['invest_end_date']!='') { $ARR = array("code"=>'9999', "msg"=>"모집 종료된 상품입니다."); echo printJson($ARR); exit; }
if($REQUEST['invest_amt'] < $CONF['min_invest_limit']) { $ARR = array('code'=>'9999', 'msg'=>'최소투자금액오류'); echo printJson($ARR); exit; }
if($REQUEST['invest_amt'] > $MB['invest_able_amount']) { $ARR = array("code"=>'9999', "msg"=>"투자가능금액을 초과하였습니다. 투자가능금액(".number_format($MB['invest_able_amount']).")원"); echo printJson($ARR); exit; }


$prd_idx        = $REQUEST['prod_cd'];
$invest_amount  = $REQUEST['invest_amt'];


// 중복투자 금지
$LAST_INVEST = sql_fetch("
	SELECT
		idx
	FROM
		cf_product_invest_detail
	WHERE 1
		AND product_idx='".$REQUEST['prod_cd']."'
		AND member_idx='".$MB['mb_no']."'
		AND invest_state='Y'
		AND syndi_id='".$_CONF['SYNDI_ID']."'
	ORDER BY
		idx DESC
	LIMIT 1");
if($LAST_INVEST['idx']) { $ARR = array("code"=>'9999', "msg"=>"중복투자는 허용하지 않습니다."); echo printJson($ARR); exit; }

/*
// 부동산PF 상품일 경우 최대투자제한금액 설정
if($PRDT['category']=='2' && $PRDT['mortgage_guarantees']=='') {
	// 최대투자금액 초과 체크

	echo $invest_amount.":".$_CONF['pf_max_invest_limit']."\n"; exit;

	if( $invest_amount > $_CONF['pf_max_invest_limit'] ) {
		$ARR = array('code'=>'9999', 'message'=>'부동산PF 최대투자금액초과'); echo printJson($ARR); exit;
	}
}
*/

$nowDateTime = preg_replace('/(-| |:)/', '', G5_TIME_YMDHIS);

$recruit_amount      = $PRDT['recruit_amount'];
$investStartDateTime = preg_replace('/(-| |:)/', '', $PRDT['start_datetime']);
$investEndDatetime   = preg_replace('/(-| |:)/', '', $PRDT['end_datetime']);


// 잔여 모집금액
$need_recruit_amount = $recruit_amount - $PRDT['total_invest_amt'];

// 투자 가능금액 설정
$invest_possible_amount = $need_recruit_amount;

//--- 개인회원의 경우 투자등급에 따른 투자 가능금액 산출 -------------------
if($MB['member_type']=='1') {

	if($MB['member_investor_type']=='1') {
		// 투자금과 카테고리별 투자한도 비교
		if($PRDT['category']=='2') {
			if($invest_amount > $MB['invest_possible_amount_prpt']) { $ARR = array('code'=>'9999', 'message'=>'부동산 투자한도초과'); echo printJson($ARR); exit; }		//부동산 투자제한 금액 초과
		}
		else {
			if($invest_amount > $MB['invest_possible_amount']) { $ARR = array('code'=>'9999', 'message'=>'동산담보대출 투자한도초과'); echo printJson($ARR); exit; }			//동산 투자제한 금액 초과
		}
	}

	if( in_array($MB['member_investor_type'], array('1','2')) ) {
		$limit_amount = ($is_group_product) ? $INDI_INVESTOR[$MB['member_investor_type']]['group_product_limit'] : $INDI_INVESTOR[$MB['member_investor_type']]['single_product_limit'];
		$_invest_possible_amount = $limit_amount - $INVEST_PRDT['sum_invest_amount'];

		if($_invest_possible_amount > $MB['invest_possible_amount']) {
			$invest_possible_amount = ($MB['member_investor_type']=='1' && $PRDT['category']=='2') ? $MB['invest_possible_amount_prpt'] : $MB['invest_possible_amount'];
		}
		else {
			$invest_possible_amount = ($MB['member_investor_type']=='1' && $PRDT['category']=='2') ? $MB['invest_possible_amount_prpt'] : $_invest_possible_amount;
		}
	}

}

// 투자 가능금액이 잔여 모집액보다 크면 투자 가능금액 = 잔여모집액
if($invest_possible_amount >= $need_recruit_amount) {
	$invest_possible_amount = $need_recruit_amount;
}

// 예치금과 투자 할 금액 비교 :::: 헬로펀딩에 예치금을 먼저 충전하고 투자하는 방식이 아니므로 주석처리함.
//if($MB['mb_point'] < $invest_amount) { echo "ERROR-BALANCE"; exit; }

// 투자 가능 금액과 투자 할 금액 비교
if($invest_possible_amount < $invest_amount) { $ARR = array('code'=>'9999', 'message'=>'투자금액초과'); echo printJson($ARR); exit; }


###################################
## 투자내역 등록
###################################
$input_datetime = date('Y-m-d H:i:s');
$IDT = explode(" ", $input_datetime);
$input_dt = $IDT[0];
$input_tm = $IDT[1];

$MB['mb_point'] = get_point_sum($MB['mb_id']);

$invest_state = ($invest_amount <= $MB['mb_point']) ? 'Y' : 'W';		// 정상투자 : 대기투자

// 헬로펀딩 예치금이 충분하면 투자내역 직접 등록
$sqlx = "
	INSERT INTO
		 cf_product_invest
	 SET
		 amount            = '".$invest_amount."',
		 member_idx        = '".$MB['mb_no']."',
		 product_idx       = '".$prd_idx."',
		 invest_state      = '".$invest_state."',
		 insert_date       = '".$input_dt."',
		 insert_time       = '".$input_tm."',
		 insert_datetime   = '".$input_datetime."',
		 syndi_id          = '".$_CONF['SYNDI_ID']."'";
$resx = sql_query($sqlx);
$invest_idx = sql_insert_id();

if($invest_idx) {

	// 원리금 수취권 번호 업데이트
	$prin_rcv_no = 'M' . $MB['mb_no'] . 'P' . $prd_idx . 'I' . $invest_idx;								//원리금 수취권 번호: M회원번호P상품번호I투자번호
	sql_query("UPDATE cf_product_invest SET prin_rcv_no='".$prin_rcv_no."' WHERE idx='".$invest_idx."'");

	// 투자건별내역 등록
	$sqlx2 = "
		INSERT INTO
			cf_product_invest_detail
		SET
			invest_idx        = '".$invest_idx."',
			amount            = '".$invest_amount."',
			member_idx        = '".$MB['mb_no']."',
			product_idx       = '".$prd_idx."',
			invest_state      = '".$invest_state."',
			insert_date       = '".$input_dt."',
			insert_time       = '".$input_tm."',
			syndi_id          = '".$_CONF['SYNDI_ID']."'";
	$resx2 = sql_query($sqlx2);

	if($invest_state=='Y') {

		// 포인트 차감
		$po_content = $PRDT['title']. "-투자(".$_CONF['SYNDI_ID'].")";
		insert_point($MB['mb_id'], $invest_amount * (-1), $po_content, '@invest', $MB['mb_id'], $MB['mb_id'].'-'.uniqid(''), 0);

		//////////////////////////////////////////////////////////////////
		// 상품관리테이블에 실시간 모집금액 반영하기 :: 2021-02-15 추가
		//////////////////////////////////////////////////////////////////
		sql_query("UPDATE cf_product SET live_invest_amount = live_invest_amount + {$invest_amount} WHERE idx = '".$prd_idx."'");
		//////////////////////////////////////////////////////////////////

		// 최종 모집총액 확인
		$FINAL_INVEST = sql_fetch("SELECT IFNULL(SUM(amount),0) AS sum_amount FROM cf_product_invest WHERE product_idx='".$prd_idx."' AND invest_state='Y'");

		// 투자금 모집완료시 투자종료일 표기. 투자마무리
		if( $PRDT['recruit_amount'] <= $FINAL_INVEST['sum_amount']) {

			sql_query("UPDATE cf_product SET invest_end_date='".$input_dt."' WHERE idx='".$prd_idx."'");		// 투자종료처리

			$invest_finished = true;

			IF(!preg_match("/소상공인 확정매출채권/i", $PRDT['title']))
			{
				/* 리포트 데이터 생성*/
				fn_cf_product_admin_report($prd_idx);
				/* sms전송 */
				fn_hello_status_smssend($prd_idx);
			}

		}

		// 올리고에 결과 전송 --------------------
		@shell_exec("/usr/local/php/bin/php -q " . $syndi_base_path . "/report/investResultReport.php " . $invest_idx);
		@shell_exec("/usr/local/php/bin/php -q " . $syndi_base_path . "/report/productStateReport.php " . $prd_idx);
		// ---------------------------------------

	}

	$complete_msg = ($invest_state=='Y') ? "정상투자완료" : "입금대기투자등록 완료";

	$status  = ($invest_finished) ? '04' : getProductStatOligo($prd_idx);
	$inve_yn = ($status=='02') ? 'Y' : 'N';		// 모집중일때만 Y

	$INVEST_CHECK = sql_fetch("
		SELECT
			COUNT(idx) AS cnt,
			IFNULL(SUM(amount),0) AS amount
		FROM
			cf_product_invest
		WHERE (1)
			AND product_idx='".$prd_idx."'
			AND invest_state='Y'");

	$inve_rate = @floor(($INVEST_CHECK['amount'] / $PRDT['recruit_amount']) * 100);
	if($inve_rate >= 100) { $inve_yn = 'N'; }

	$ARR['code']      = '0000';
	$ARR['msg']       = $complete_msg;
	$ARR['inve_yn']   = $inve_yn;														// 투자가능여부 (이거뭔데??)
	$ARR['status']    = $status;														// 상태값
	$ARR['inve_num']  = (string)$INVEST_CHECK['cnt'];				// 투자자수
	$ARR['inve_amt']  = (string)$INVEST_CHECK['amount'];		// 투자모집금액
	$ARR['inve_rate'] = (string)$inve_rate;									// 투집율(숫자만)


	// 테스트 서버인 경우 임의 입금데이터를 등록하기 위해 oligo_deposit_check 에 데이터 기록
	if( preg_match("/dev\.hello/", $_CONF['host_domain']) ) {
		if($invest_state=='W') {

			$need_amount = $invest_amount - $MB['mb_point'];

			// 투자금 후납 체크용 데이터 기록 (/syndicate/oligo/oligo_deposit_check.php 를 통하여 IB_FB_P2P_IP테이블에 입금등록시 oligo_deposit_check테이블 deposit필드값 변경)
			$sqlx3 = "
				INSERT INTO
					oligo_deposit_check
				SET
					invest_idx   = '".$invest_idx."',
					member_idx   = '".$MB['mb_no']."',
					product_idx  = '".$prd_idx."',
					amount       = '".$need_amount."',
					rdate        = '".$input_datetime."',
					deposit      = 'N'";
			$result3 = sql_query($sqlx3);

		}
	}

}
else {
	$ARR = array('code'=>'9999', 'message'=>'투자등록오류'); echo printJson($ARR); exit;
}



##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>