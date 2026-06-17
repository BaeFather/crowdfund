<?
###############################################################################
## 2022-05-04: 투자상품 상태값 변경처리(기표처리) 중 cf_product 업데이트 SQL오류로
## 진행이 안된 상품을 본 파일을 이용하여 데이터복구 처리 하였음.
## 대상상품 : 8770, 8776
###############################################################################

exit;
set_time_limit(0);

include_once('./_common.php');

include_once(G5_LIB_PATH.'/repay_calculation_new.php');		// 월별 정산내역 추출함수 호출
include_once(G5_LIB_PATH.'/insidebank.lib.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$prd_idx = trim($_REQUEST['prd_idx']);
$change_state = 1;

if(!$prd_idx) { exit; }
if(!$change_state) { exit; }


$PRDT = sql_fetch("SELECT * FROM cf_product WHERE idx='".$prd_idx."'");
if(!$PRDT['idx']) { exit; }

$ib_trust = ($PRDT['ib_trust']=='Y' && $PRDT['ib_product_regist']=='Y') ? true : false;

//print_rr($PRDT);exit;


/////////////////////////////////////////////////
// state=1 : 대출실행 - 이자상환중 처리
// 10억을 초과한 투자금의 대출입금계좌는 10억단위로 분리하여야 한다. 단 신한은행계좌일 경우 10억이상도 가능
// 다른 투자상품간 대출지급계좌가 동일할 경우에는 정상대출이 된다.
/////////////////////////////////////////////////
if($change_state=='1') {

	$tmp_loan_start_date = '2022-05-04';

	// ▼ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------
	if($ib_trust) {

		$start_date = new DateTime($tmp_loan_start_date);
		if($PRDT['invest_period']==1 && $PRDT['invest_days'] > 0) {
			$end_date = new DateTime(date("Y-m-d", strtotime($tmp_loan_start_date." +".$PRDT['invest_days']." day")));
		}
		else {
			$end_date = new DateTime(date("Y-m-d", strtotime($tmp_loan_start_date." +".$PRDT['invest_period']." month")));
		}
		$loan_end_date = $end_date->format('Y-m-d');


		// 차주정보
		$LMB  = sql_fetch("SELECT mb_id, mb_name, mb_co_name, member_type FROM g5_member WHERE mb_no='".$PRDT['loan_mb_no']."'");

		$LOAN_EXEC_DATE = preg_replace("/-| /", "", $tmp_loan_start_date);	// 대출실행일
		$LOAN_EXP_DATE  = preg_replace("/-| /", "", $loan_end_date);	// 대출만기일
		$LOAN_CUST_ID   = $PRDT['loan_mb_no'];												// 대출자 아이디는 회원번호로 설정함.
		$LOAN_CUST_NM   = ($LMB['member_type']=='2') ? $LMB['mb_co_name'] : $LMB['mb_name'];	// 대출자고객명
		$CMS_NB         = $PRDT['repay_acct_no'];											// 상환용 가상계좌번호

		$LOAN_DEP_CNT = 0;
		for($i=0,$j=1; $i<5; $i++,$j++) {
			if($PRDT['loan_dep_bank_cd'.$j] && $PRDT['loan_dep_acct_nb'.$j]) {
				$LOAN_DEP_CNT += 1;
			}
		}

		// 투자정보 업데이트 요청전문 (2500) 데이터 구성
		$INVEST  = sql_fetch("SELECT COUNT(idx) AS cnt, SUM(amount) AS amount FROM cf_product_invest WHERE product_idx='".$prd_idx."' AND invest_state='Y' AND ib_regist='1'");
		$INV_CNT = $INVEST['cnt'];

		$ARR['REQ_NUM']           = '020';											// 전문번호
		$ARR['SUBMIT_GBN']        = '05';												// 거래구분: 투자등록
		$ARR['LOAN_SEQ']          = $prd_idx;										// 대출식별번호
		$ARR['LOAN_AMT']          = $PRDT['recruit_amount'];		// 총대출금
		$ARR['LOAN_FEE']	        = 0;													// 취급수수료 (강제 0으로 처리 : 이상규대리 요청)  //$ARR['LOAN_FEE'] = (int)$PRDT['loan_usefee'];
		$ARR['LOAN_EXEC_DATE']    = $LOAN_EXEC_DATE;						// 대출실행일
		$ARR['LOAN_EXP_DATE']     = $LOAN_EXP_DATE;							// 대출만기일
		$ARR['LOAN_CUST_ID']      = $LOAN_CUST_ID;							// 대출자고객ID
		$ARR['LOAN_CUST_NM']      = $LOAN_CUST_NM;							// 대출자고객명
		$ARR['CMS_NB']            = $CMS_NB;										// 가상계좌번호 (모계좌 : 헬로크라우드대부 업체코드로 배당된 가상계좌)
		$ARR['LOAN_DEP_CNT']      = $LOAN_DEP_CNT;							// 대출입금계좌건수
		$ARR['INV_CNT']           = $INV_CNT;										// 투자자수
		for($i=0,$j=1; $i<5; $i++,$j++) {
			$ARR['LOAN_DEP_BANK_CD'.$j] = $PRDT['loan_dep_bank_cd'.$j];		// 대출금입금은행코드$j
			$ARR['LOAN_DEP_ACCT_NB'.$j] = $PRDT['loan_dep_acct_nb'.$j];		// 대출금입금계좌번호$j
			$ARR['LOAN_DEP_AMT'.$j]     = ($PRDT['loan_dep_amt'.$j] > 0) ? $PRDT['loan_dep_amt'.$j] : '';		// 대출금입금금액$j
		}

		// 다중차수 대출상품일 경우 첫회차 대출번호. 본 대출건이 최초대출이면 공백처리
		if($PRDT['gr_idx'] > 0 && $PRDT['idx'] > $PRDT['gr_idx']) {
			$INV_CUST_ID = $PRDT['gr_idx'];
		}
		else {
			$INV_CUST_ID = '';
		}
		$ARR['INV_CUST_ID'] = $INV_CUST_ID;

		/*
		// 대출실행(2300) 등록전문 데이터 구성
		$ARR2['REQ_NUM']    = "020";
		$ARR2['SUBMIT_GBN'] = "03";
		$ARR2['LOAN_SEQ']   = $prd_idx;		// 대출식별번호

		$RETURN_ARR2 = insidebank_request('256', $ARR2);		// 인사이드뱅크 대출실행 등록전문 (2300) 발송
		*/

		/*
		if($RETURN_ARR2['RCODE']!='00000000') {

			$RETURN_ARR = array('result'=>'FAIL', 'message'=>$RETURN_ARR2['ERRMSG']);
			echo json_encode($RETURN_ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES); sql_close(); exit;

		}
		else {
		*/

			// 그룹 상품중 두번째 상품 대출실행시 첫번째 상품을 해당 그룹상품의 상환계좌 참조번호로 강제 설정한다. (참조번호 미설정시 발생할 입금처 미확인 사태를 방지하기 위함)
			$grp_prdt_count = sql_fetch("SELECT COUNT(idx) AS cnt FROM cf_product WHERE gr_idx='".$PRDT['gr_idx']."'");
			if($grp_prdt_count['cnt']==2) {
				$sql = "
					UPDATE
						KSNET_VR_ACCOUNT
					SET
						REF_NO='".$prd_idx."'
					WHERE 1
						AND USE_FLAG='Y'
						AND VR_ACCT_NO='".$CMS_NB."'";
				print_rr($sql);
				sql_query($sql);
			}

			$ib_loan_start = "S";		// 대출실행플래그(R:대기|S:실행됨|C:실행후취소됨)

			// 일별 이자,수수료 명세서 생성시작
			$exec_path   = "/usr/local/php/bin/php -q " . G5_ADMIN_PATH . "/repayment/make_bill_exec.php " . $prd_idx;
			$exec_result = shell_exec($exec_path);

		/*
		}

		unset($ARR2);
		unset($RETURN_ARR2);
		*/

	}
	// ▲ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------


	$exceptionProduct = '';
	$shortTermProduct = ($PRDT['invest_period']==1 && $PRDT['invest_days'] > 0) ? true : false;

	$turn_cnt  = repayTurnCount($tmp_loan_start_date, $loan_end_date, $exceptionProduct, $shortTermProduct, $PRDT['calc_type']);
	$turn_cnt_orig  = $turn_cnt;

	$sql_add = ($ib_loan_start) ? ", ib_loan_start='$ib_loan_start'" : "";

	//상품정보값 수정
	$update_sql = "
		UPDATE
			cf_product
		SET
			state = '".$change_state."',
			loan_start_date = '".$tmp_loan_start_date."',
			loan_end_date   = '".$loan_end_date."',
			loan_end_date_orig = '".$loan_end_date."',
			turn_cnt = '".$turn_cnt."',
			turn_cnt_orig = '".$turn_cnt_orig."'
			$sql_add
		WHERE
			idx = '".$prd_idx."'";
	print_rr($update_sql);
	if( sql_query($update_sql) ) {

		$RETURN_ARR = array('result'=>'SUCCESS', 'message'=>'');
		echo json_encode($RETURN_ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);

		// 대출자 수수료 지급스케쥴 등록
		$exec_path2   = "/usr/local/php/bin/php -q " . G5_ADMIN_PATH . "/repayment/make_loaner_fee_collect_schedule.php " . $prd_idx;
		$exec_result2 = shell_exec($exec_path2);

		// 차주 이자 입금 안내 스케줄 생성
		$exec_path3    = "/usr/local/php/bin/php -q " . G5_ADMIN_PATH . "/mortgage/make_loaner_interest_sms_schedule.php " . $prd_idx;
		$exec_result3 = shell_exec($exec_path3);


		// 기표 안내 문자 차주에게 발송 (주담대 상품만) 2022-03-25 전차장
		if ($PRDT["category"]=="2" AND $PRDT["mortgage_guarantees"]=="1") {
			$exec_path4    = "/usr/local/php/bin/php -q " . G5_ADMIN_PATH . "/mortgage/chaju_sms.php " . $prd_idx . " 1";
			$exec_result4  = shell_exec($exec_path4);
		}

	}

}


sql_close();

exit;

?>