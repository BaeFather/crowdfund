<?

set_time_limit(0);

$sub_menu = '700000';
include_once('./_common.php');

ini_set('memory_limit','256M');



auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

include_once(G5_LIB_PATH.'/repay_calculation.php');		// 월별 정산내역 추출함수 호출
include_once(G5_LIB_PATH.'/insidebank.lib.php');

// 올리고 레포팅 전송
function oligoSendReport($product_idx, $syndi_platform) {
	if($product_idx && $syndi_platform) {
		$PLATFORM = explode("|", $syndi_platform);
		if( in_array("oligo", $PLATFORM) )  {
			@shell_exec("/usr/local/php/bin/php -q " . G5_SYNDICATE_PATH . "/oligo/report/productStateReport.php " . $product_idx);
			$return_value = true;
		}
		else {
			$return_value = false;
		}
	}
	else {
		$return_value = false;
	}
	return $return_value;
}


$prd_idx              = trim($_REQUEST['idx']);											// 상품번호기준
$mb_id                = trim($_REQUEST['mb_id']);										// 특정 투자자만 조회 할 경우
//$invest_period      = trim($_REQUEST['invest_period']);						// (시뮬레이션용) 투자개월수
//$loan_start_date    = trim($_REQUEST['loan_start_date']);					// (시뮬레이션용) 투자시작일
//$loan_end_date      = trim($_REQUEST['loan_end_date']);						// (시뮬레이션용) 투자만기일
//$invest_usefee      = trim($_REQUEST['invest_usefee']);						// (시뮬레이션용) 플랫폼이용료율
//$invest_usefee_type = trim($_REQUEST['invest_usefee_type']);			// (시뮬레이션용) 플랫폼이용료 징수방식
//$turn               = trim($_REQUEST['turn']);

// 올리고 투자유무 확인 (취소무관)
$TMP = sql_fetch("SELECT platform FROM cf_product WHERE display='Y' AND scrap_out='' AND isTest='' AND only_vip='' AND idx='".$prd_idx."'");
$platform = $TMP['platform'];


$INV_ARR   = repayCalculation($prd_idx, $mb_id);

$INI       = $INV_ARR['INI'];
$PRDT      = $INV_ARR['PRDT'];
$LOANER    = $INV_ARR['LOANER'];
$INVEST    = $INV_ARR['INVEST'];
$MTOTAL_INVEST_SUM = $INV_ARR['MTOTAL_INVEST_SUM'];
$REPAY     = $INV_ARR['REPAY'];
$REPAY_SUM = $INV_ARR['REPAY_SUM'];
//print_rr($REPAY, 'font-size:0.85em'); exit;

$ib_trust = ($PRDT['ib_trust']=='Y' && $PRDT['ib_product_regist']=='Y') ? true : false;

if($ib_trust) {
	$DEPOSITED = sql_fetch("SELECT MAX(ERP_TRANS_DT) AS ERP_TRANS_DT, SUM(TR_AMT) AS TR_AMT FROM IB_FB_P2P_IP WHERE ACCT_NB='".$PRDT['repay_acct_no']."'");		// 입금총액
}

if($_REQUEST['action']) {


	///////////////////////////////////////////////////////////////////////////////
	// (중도상환용)대출종료일자 변경
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "repay_date_change") {

		if($PRDT['state']!='1') { echo "대출만료일을 변경할 수 없는 상품입니다."; exit; }
		//if($_POST['loan_end_date'] < date('Y-m-d')) { echo "중도상환용 대출만료일은 금일을 포함한 이후 일자로만 설정 가능합니다."; exit; }

		$sql = "UPDATE cf_product SET loan_end_date='".$_POST['loan_end_date']."' WHERE idx='".$_POST['idx']."'";
		if(sql_query($sql)) {

			// 수익명세서 재생성시작
			$exec_path = "/usr/local/php/bin/php -q " . G5_ADMIN_PATH . "/repayment/make_bill_exec.php " . $prd_idx;
			@shell_exec($exec_path);

			$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
			echo json_encode($RESULT_ARR);

		}
		else {

			$RESULT_ARR = array('result' => 'ERROR', 'message' => 'UPDATE ERROR');
			echo json_encode($RESULT_ARR);

		}

	}


	///////////////////////////////////////////////////////////////////////////////
	// 대출이자 수급완료 플래그
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "loan_interest_success") {

		$turn = trim($_POST['turn']);
		$date = trim($date);

		if($ib_trust) {

			if(!$DEPOSITED['TR_AMT']) {
				$msg = "입금내역이 없습니다.";
				$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
				echo json_encode($RESULT_ARR);
				exit;
			}

			$repay_arr_no = $turn - 1;
			$need_amount  = $REPAY[$repay_arr_no]['NUJUK_SUM']['invest_interest'];		// 요청 차수까지의 누적 이자총액 (지급여부 상관없음)

			if($DEPOSITED['TR_AMT'] < $need_amount) {
				$msg = "상환 입금액이 부족합니다.\n\n부족분: " . number_format($need_amount-$DEPOSITED['TR_AMT']) . "원";
				$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
				echo json_encode($RESULT_ARR);
				exit;
			}

		}


		$DATA = sql_fetch("SELECT idx FROM cf_product_success WHERE product_idx='$prd_idx' AND turn='$turn' ORDER BY idx LIMIT 1");

		if($DATA['idx']) {
			$sql = "UPDATE cf_product_success SET loan_interest_state='Y' WHERE idx='".$DATA['idx']."'";
		}
		else {
			$sql = "INSERT INTO cf_product_success (loan_interest_state, product_idx, turn, `date`) VALUES ('Y', '$prd_idx', '$turn', '$date')";
		}

		if( sql_query($sql) ) {
			$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
			echo json_encode($RESULT_ARR);
		}

	}


	///////////////////////////////////////////////////////////////////////////////
	// 연체이자 수급완료 플래그
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "overdue_rcv_success") {

		$turn = trim($_POST['turn']);

		$DATA = sql_fetch("SELECT idx FROM cf_product_success WHERE product_idx='$prd_idx' AND turn='$turn' ORDER BY idx LIMIT 1");

		if($DATA['idx']) {
			$sql = "UPDATE cf_product_success SET overdue_receive='Y', overdue_end_date=NOW() WHERE idx='".$DATA['idx']."'";
		}
		else {
			$sql = "INSERT INTO cf_product_success (overdue_receive, product_idx, turn, overdue_end_date, date) VALUES ('Y', '$prd_idx', '$turn', CURRENT_DATE(), CURRENT_DATE())";
		}

		if( sql_query($sql) ) {
			$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
			echo json_encode($RESULT_ARR);
		}

	}


	///////////////////////////////////////////////////////////////////////////////
	// 투자수익금 지급완료 플래그
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "invest_give_success") {

		$turn = trim($_POST['turn']);
		$date = trim($date);

		$repay_arr_no = $turn - 1;

		if(date('Y-m-d') < $REPAY[$repay_arr_no]['repay_date']) {
			//echo $_POST['turn'] . "회차 투자수익금의 지급완료 처리는 「" . date('Y년 m월 d일', strtotime($REPAY[$repay_arr_no]['repay_date'])) . "」부터 가능합니다."; exit;
		}

		$ROW = sql_fetch("SELECT COUNT(idx)	AS give_count FROM cf_product_give WHERE product_idx='$prd_idx' AND turn='$turn' AND is_overdue='N'");

		$gived_count = count($REPAY[$repay_arr_no]['LIST']);

		if($ROW['give_count'] <> $gived_count) {
			$msg = "투자자수와 지급처리수가 동일하지 않습니다.\n다음 사항을 확인 하십시요.\n\n투자자: " . $ROW['give_count'] ."명\n지급수: ".number_format($gived_count)."건";
			$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
			echo json_encode($RESULT_ARR);
			exit;
		}
		else {
			$DATA = sql_fetch("SELECT idx, loan_interest_state FROM cf_product_success WHERE loan_interest_state='Y' AND product_idx='$prd_idx' AND turn='$turn' ORDER BY idx LIMIT 1");
			if($DATA['loan_interest_state']=='') {
				$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
				echo json_encode($RESULT_ARR);
				exit;
			}

			if($DATA['idx']) {
				$sql = "UPDATE cf_product_success SET invest_give_state='Y' WHERE idx='".$DATA['idx']."'";
			}
			else {
				$sql = "INSERT INTO cf_product_success (invest_give_state, product_idx, turn, `date`) VALUES ('Y', '$prd_idx', '$turn', '$date')";
			}

			if( sql_query($sql) ) {
				$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
				echo json_encode($RESULT_ARR);
			}
		}

	}

	///////////////////////////////////////////////////////////////////////////////
	// 연체이자 지급완료 플래그
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "overdue_give_success") {

		$turn = trim($_POST['turn']);

		$DATA = sql_fetch("SELECT idx, overdue_receive FROM cf_product_success WHERE product_idx='$prd_idx' AND turn='$turn' ORDER BY idx LIMIT 1");
		if($DATA['overdue_receive']=='') {
			$msg = "연체이자 수급 기록이 없습니다.";
			$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
			echo json_encode($RESULT_ARR);
			exit;
		}

		$sql = "UPDATE cf_product_success SET overdue_give='Y' WHERE idx='".$DATA['idx']."'";

		if( sql_query($sql) ) {
			$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
			echo json_encode($RESULT_ARR);
		}

	}


	///////////////////////////////////////////////////////////////////////////////
	// 대출원금 수급완료 플래그
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "loan_principal_success") {

		$turn = trim($_POST['turn']);

		if($ib_trust) {

			if(!$DEPOSITED['TR_AMT']) {
				$msg = "입금내역이 없습니다.";
				$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
				echo json_encode($RESULT_ARR);
				exit;
			}

			$repay_arr_no = $_POST['turn'] - 1;
			$need_amount  = $PRDT['recruit_amount'] + $REPAY[$repay_arr_no]['NUJUK_SUM']['invest_interest'];		// 대출원금 + 요청 차수까지의 누적 이자총액 (지급여부 상관없음)

			if($DEPOSITED['TR_AMT'] < $need_amount) {
				$msg = "상환 입금액이 부족합니다.\n\n부족분: " . number_format($need_amount-$DEPOSITED['TR_AMT']) . "원";
				$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
				echo json_encode($RESULT_ARR);
				exit;
			}

		}

		$DATA = sql_fetch("SELECT idx FROM cf_product_success WHERE product_idx='$prd_idx' AND turn='$turn' ORDER BY idx LIMIT 1");

		$sql = "UPDATE cf_product_success SET loan_principal_state='Y' WHERE idx='".$DATA['idx']."'";

		if( sql_query($sql) ) {
			$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
			echo json_encode($RESULT_ARR);
		}
		else {
			$msg = "DB UPDATE ERROR";
			$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
			echo json_encode($RESULT_ARR);
		}

	}



	///////////////////////////////////////////////////////////////////////////////
	// 투자원금 지급완료 플래그
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "invest_principal_give_success") {

		$turn = trim($_POST['turn']);

		$sql1 = "
			UPDATE
				cf_product_success
			SET
				invest_principal_give='Y'
			WHERE (1)
				AND product_idx='$prd_idx'
				AND turn='$turn'";
		$res1 = sql_query($sql1);

		$sql2 = "
			UPDATE
				cf_product_invest
			SET
				is_return = 'Y',
				return_date = NOW()
			WHERE (1)
				AND product_idx='$prd_idx'
				AND invest_state='Y'";
		$res2 = sql_query($sql2);

		if($res1 && $res2) {
			$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
			echo json_encode($RESULT_ARR);
		}
		else {
			$msg = "DB UPDATE ERROR";
			$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
			echo json_encode($RESULT_ARR);
		}

	}



	///////////////////////////////////////////////////////////////////////////////
	// 연체등록 플래그
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "overdue_start") {

		$turn = trim($_POST['turn']);
		$start_date = trim($_POST['start_date']);

		$DATA = sql_fetch("SELECT idx FROM cf_product_success WHERE product_idx='$prd_idx' AND turn='$turn' ORDER BY idx LIMIT 1");

		if($DATA['idx']) {
			$sql = "UPDATE cf_product_success SET overdue_start_date='$start_date' WHERE idx='$prd_idx'";
		}
		else {
			$sql = "INSERT INTO cf_product_success (product_idx, turn, overdue_start_date, date) VALUES ('$prd_idx', '$turn', '$start_date', CURRENT_DATE())";
		}

		if( sql_query($sql) ) {
			$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
			echo json_encode($RESULT_ARR);
		}
		else {
			$msg = "DB QUERY ERROR";
			$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
			echo json_encode($RESULT_ARR);
		}

	}




	///////////////////////////////////////////////////////////////////////////////
	// 3자예치시스템 적용상품 투자자 전송
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "ib_investor_regist") {

		if(!$ib_trust) { debug_flush('제3자 예치시스템에 등록된 대출상품이 아닙니다!!!'); }

		$sql = "
			SELECT
				A.idx AS invest_idx,
				A.amount, A.prin_rcv_no,
				B.mb_no, B.mb_id, B.mb_name, B.mb_co_name, B.member_type
			FROM
				cf_product_invest A
			LEFT JOIN
				g5_member B
			ON
				A.member_idx=B.mb_no
			WHERE 1
				AND A.product_idx='$prd_idx'
				AND invest_state='Y'
				AND ib_regist=''
			ORDER BY
				A.idx";
		//echo $sql;
		$res  = sql_query($sql);
		$rows = sql_num_rows($res);
		if($rows) {

			$CNT = array('succ'=>0, 'fail'=>0);

			for($i=0,$j=1; $i<$rows; $i++,$j++) {
				$LIST[$i] = sql_fetch_array($res);
				$print_name = ($LIST[$i]['member_type']=='2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];

				///////////////////////////////////////////////////////
				// 인사이드뱅크 전문 구성 및 발송 (전문번호:2200)
				///////////////////////////////////////////////////////
				$ARR['REQ_NUM']     = "020";											// 전문번호
				$ARR['SUBMIT_GBN']  = "02";												// 거래구분 (투자자등록:02 | 투자자변경:06 | 투자자취소:07)
				$ARR['LOAN_SEQ']    = $prd_idx;										// 대출식별번호 (대출상품번호)
				$ARR['INV_SEQ']     = $LIST[$i]['invest_idx'];		// 대출등록시 투자자건수에 대한 일련번호
				$ARR['INV_CUST_ID'] = $LIST[$i]['mb_no'];					// 투자자ID (투자자식별번호)
				$ARR['PRIN_RCV_NO'] = $LIST[$i]['prin_rcv_no'];		// 원리금 수취권 번호
				$ARR['INV_AMT']     = $LIST[$i]['amount'];				// 투자금액
				//print_r($ARR);

				$RETURN_ARR = insidebank_request('256', $ARR);	/*** 인사이드뱅크 전문(2200) 전송 ***/

				if($RETURN_ARR['RCODE']=='00000000') {
					sql_query("UPDATE cf_product_invest SET ib_regist='1' WHERE idx='".$LIST[$i]['invest_idx']."'");
					$result_text = 'SUCCESS';
					$CNT['succ']++;
				}
				else {
					$result_text = 'FAIL : ' . $RETURN_ARR['ERRMSG'];
					$CNT['fail']++;
				}
				unset($ARR);

				debug_flush('['.$j.'] ' . $LIST[$i]['mb_id']. '    ' . $print_name . ' :: ' . number_format($LIST[$i]['amount']) . "원 >>>>>>>>>> ". $result_text . " \n");

				if($j==$rows) {
					debug_flush("\n" .
						">>>>>>>>>> " . $CNT['succ'] . "건 정상\n" .
						">>>>>>>>>> " . $CNT['fail'] . "건 실패\n\n" .
						">>>>>>>>>> 페이지를 새로고침 하십시요!!!\n");
				}

			}

			sql_free_result($res);
		}
		else {
			debug_flush("전송처리 할 투자건이 없습니다!!!");
		}

	}



	///////////////////////////////////////////////////////////////////////////////
	// 상품(투자) 진행 현황 설정값 변경
	// 1:이자상환중 2:상환완료(투자종료) 3:투자금모집실패 4:부실 5:중도일시상환 6:대출실행취소
	// ※※ 제3자예치금관리 연계 대출상품등록/수정은 ajax_invest_shinhan_proc.php 에서...
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "calculate_state_update") {

		/////////////////////////////////////////////////
		// state=1 : 대출실행 - 이자상환중 처리
		// 10억을 초과한 투자금의 대출입금계좌는 10억단위로 분리하여야 한다. 단 신한은행계좌일 경우 10억이상도 가능
		// 다른 투자상품간 대출지급계좌가 동일할 경우에는 정상대출이 된다.
		/////////////////////////////////////////////////
		if($_POST['state']=='1') {

			$start_date    = new DateTime($_POST['date']);
			//$end_date      = new DateTime(date('Y-m-d', strtotime($_POST['date'].' +'.$PRDT['invest_period'].' month')));
			if($PRDT['invest_period']==1 && $PRDT['invest_days'] > 0) {
				$end_date = new DateTime(date("Y-m-d", strtotime($_POST['date']." +".$PRDT['invest_days']." day")));
			}
			else {
				$end_date = new DateTime(date("Y-m-d", strtotime($_POST['date']." +".$PRDT['invest_period']." month")));
			}

			$loan_end_date = $end_date->format('Y-m-d');
			$TOTAL_DATE    = date_diff($start_date, $end_date);
			$total_day     = $TOTAL_DATE->days;

			// ▼ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------
			if($ib_trust) {
				$LMB  = sql_fetch("SELECT mb_id, mb_name, mb_co_name, member_type FROM g5_member WHERE mb_no='".$PRDT['loan_mb_no']."'");

				$LOAN_EXEC_DATE = preg_replace("/-| /", "", $_POST['date']);	// 대출실행일
				$LOAN_EXP_DATE  = preg_replace("/-| /", "", $loan_end_date);	// 대출만기일
				$LOAN_CUST_ID   = $PRDT['loan_mb_no'];		// 대출자 아이디는 회원번호로 설정함.
				$LOAN_CUST_NM   = ($LMB['member_type']=='2') ? $LMB['mb_co_name'] : $LMB['mb_name'];	// 대출자고객명
				$CMS_NB         = $PRDT['repay_acct_no'];	// 상환용 가상계좌번호

				$LOAN_DEP_CNT = 0;
				for($i=0,$j=1; $i<5; $i++,$j++) {
					if($PRDT['loan_dep_bank_cd'.$j] && $PRDT['loan_dep_acct_nb'.$j]) {
						$LOAN_DEP_CNT += 1;
					}
				}

				// 인사이드뱅크 투자정보 업데이트 요청전문 (2500) 데이터 구성
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

				//print_rr($ARR, 'font-size:12px'); exit;
				//$RETURN_ARR = insidebank_request('256', $ARR);  // 인사이드뱅크 투자정보 업데이트 요청전문(2500) 발송

				/*
				if($RETURN_ARR['RCODE']!='00000000') {
					alert($RETURN_ARR['ERRMSG']); exit;
				}
				else {
				*/
					// 인사이드뱅크 대출실행 등록전문 데이터 구성
					$ARR2['REQ_NUM']    = "020";
					$ARR2['SUBMIT_GBN'] = "03";
					$ARR2['LOAN_SEQ']   = $prd_idx;		// 대출식별번호

					$RETURN_ARR2 = insidebank_request('256', $ARR2);		// 인사이드뱅크 대출실행 등록전문 (2300) 발송

					if($RETURN_ARR2['RCODE']!='00000000') {
						alert($RETURN_ARR2['ERRMSG']); exit;
					}
					else {

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
							sql_query($sql);
						}

						$ib_loan_start = "S";		// 대출실행플래그(R:대기|S:실행됨|C:실행후취소됨)

					}

					unset($ARR2);
					unset($RETURN_ARR2);
				/*
				}
				*/

			}
			// ▲ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------


			//상품정보값 수정
			$update_sql = "
				UPDATE
					cf_product
				SET
					state='".$_POST['state']."',
					loan_start_date='".$_POST['date']."',
					loan_end_date='".$loan_end_date."',
					loan_end_date_orig='".$loan_end_date."'";
			$update_sql.= ($ib_loan_start) ? ", ib_loan_start='$ib_loan_start'" : "";
			$update_sql.= " WHERE idx='".$prd_idx."'";
			if(sql_query($update_sql)) {

				// 수익명세서 생성시작
				$exec_path = "/usr/local/php/bin/php -q " . G5_ADMIN_PATH . "/repayment/make_bill_exec.php " . $prd_idx;
				@shell_exec($exec_path);

				oligoSendReport($prd_idx, $platform);		// 올리고 레포팅
				alert('정상적으로 처리되었습니다.', G5_ADMIN_URL.'/product_calculate.php?idx='.$prd_idx);

			}

		}

		/////////////////////////////////////////////////
		// state=2 : 상환완료(투자종료) 처리
		/////////////////////////////////////////////////
		else if($_POST['state']=='2') {

			$REPORT = sql_fetch("
				SELECT
					idx
				FROM
					cf_product_success
				WHERE 1
					AND product_idx='".$prd_idx."'
					AND turn='".$INI['repay_count']."'
					AND loan_interest_state='Y'
					AND loan_principal_state='Y'
					AND invest_give_state='Y'
					AND invest_principal_give='Y'");
			if( !$REPORT['idx'] ) {
				alert('실제 투자 상환이 마무리 되지 않았습니다.\n원리금 수급 및 상환 현황을 체크하십시요.', G5_ADMIN_URL.'/product_calculate.php?idx='.$prd_idx);
				exit;
			}

			// ▼ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------
			if($ib_trust) {
				$LOAN_AMT      = $PRDT['recruit_amount'];
				$LOAN_EXP_DATE = preg_replace("/(-| )/", "", $PRDT['loan_end_date']);

				$ARR['REQ_NUM']				= "020";
				$ARR['SUBMIT_GBN']		= "08";							// 거래구분	(대출상환완료:08)
				$ARR['LOAN_SEQ']			= $prd_idx;					// 대출식별번호
				$ARR['LOAN_AMT']			= $LOAN_AMT;				// 대출상환금액 (총대출금)
				$ARR['LOAN_EXP_DATE'] = $LOAN_EXP_DATE;		// 대출상환일자 (대출만기일)

				$RETURN_ARR = insidebank_request('256', $ARR);  // 인사이드뱅크 대출상환완료 요청전문(2700) 발송

				if($RETURN_ARR['RCODE']!='00000000') {
					alert($RETURN_ARR['ERRMSG']); exit;
				}
			}
			// ▲ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------

			//상품정보값 수정
			$update_sql = "UPDATE cf_product SET state='".$_POST['state']."' WHERE idx='".$prd_idx."'";
			if(sql_query($update_sql)) {
				oligoSendReport($prd_idx, $platform);		// 올리고 레포팅
				alert('정상적으로 처리되었습니다.', G5_ADMIN_URL.'/product_calculate.php?idx='.$prd_idx);
			}

		}


		/////////////////////////////////////////////////
		// state=3 : 투자금 모집실패
		/////////////////////////////////////////////////
		else if($_POST['state']=='3') {

			/*
			// ▼ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------
			if($ib_trust) {

				$ARR['REQ_NUM']    = "020";
				$ARR['SUBMIT_GBN'] = "04";
				$ARR['LOAN_SEQ']   = $prd_idx;		// 대출식별번호

				$RETURN_ARR = insidebank_request('256', $ARR);  // 인사이드뱅크 대출취소 요청전문(2400) 발송 -> 투자금 반환은 자동으로 처리됨

				if($RETURN_ARR['RCODE']!='00000000') {
					alert($RETURN_ARR['ERRMSG']); exit;
				}

			}
			// ▲ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------
			*/


			$sql = "
				SELECT
					A.*,
					B.mb_id,
					C.title
				FROM
					cf_product_invest A
				LEFT JOIN
					g5_member B  ON A.member_idx = B.mb_no
				LEFT JOIN
					cf_product C  ON A.product_idx = C.idx
				WHERE 1
					AND A.product_idx = '".$prd_idx."'";
			$result = sql_query($sql);

			while ($row = sql_fetch_array($result)) {
				$po_content = $row['title'] . "-투자금 반환";
				insert_point($row['mb_id'], $row['amount'], $po_content, '@return', $member['mb_id'], $member['mb_id'].'-'.uniqid(''));

				$param = array();
				$param['apiGbn']    = 'pendingCancel';
				$param['mb_id']     = $row['mb_id'];
				$param['parentTid'] = $row['tr_no'];

				$result = payGateCurl($param);
			}


			//상품정보값 수정
			$update_sql = "UPDATE cf_product SET state = '".$_POST['state']."' WHERE idx = '".$prd_idx."'";
			if(sql_query($update_sql)) {
				oligoSendReport($prd_idx, $platform);		// 올리고 레포팅
				alert('정상적으로 처리되었습니다.', G5_ADMIN_URL.'/product_calculate.php?idx='.$prd_idx);
			}

		}

		/////////////////////////////////////////////////
		// state=4 : 부실 처리
		/////////////////////////////////////////////////
		else if($_POST['state']=='4') {

			$update_sql = "UPDATE cf_product SET state = '".$_POST['state']."' WHERE idx = '".$prd_idx."'";
			if(sql_query($update_sql)) {
				oligoSendReport($prd_idx, $platform);		// 올리고 레포팅
				alert('정상적으로 처리되었습니다.', G5_ADMIN_URL.'/product_calculate.php?idx='.$prd_idx);
			}

		}


		/////////////////////////////////////////////////
		// state=5 : 중도상환 처리
		/////////////////////////////////////////////////
		else if($_POST['state']=='5') {

			$REPORT = sql_fetch("
				SELECT
					idx
				FROM
					cf_product_success
				WHERE 1
					AND product_idx='".$prd_idx."'
					AND turn='".$INI['repay_count']."'
					AND loan_interest_state='Y'
					AND loan_principal_state='Y'
					AND invest_give_state='Y'
					AND invest_principal_give='Y'");
			if( !$REPORT['idx'] ) {
				alert('실제 투자 상환이 마무리 되지 않았습니다.\n원리금 수급 및 상환 현황을 체크하십시요.', G5_ADMIN_URL.'/product_calculate.php?idx='.$prd_idx);
				exit;
			}

			// ▼ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------
			if($ib_trust) {
				$LOAN_AMT      = $PRDT['recruit_amount'];
				$LOAN_EXP_DATE = preg_replace("/(-| )/", "", $_POST['loan_end_date']);

				$ARR['REQ_NUM']				= "020";
				$ARR['SUBMIT_GBN']		= "08";							// 거래구분	(대출상환완료:08)
				$ARR['LOAN_SEQ']			= $prd_idx;					// 대출식별번호
				$ARR['LOAN_AMT']			= $LOAN_AMT;				// 대출상환금액 (메뉴얼상 중도상환전납부금액이라 명시되어있으나 원금총액을 등록해주면 된다.)
				$ARR['LOAN_EXP_DATE'] = $LOAN_EXP_DATE;		// 대출상환일자 (중도상환일)

				$RETURN_ARR = insidebank_request('256', $ARR);  // 인사이드뱅크 투자정보 대출상환완료 요청전문(2700) 발송

				if($RETURN_ARR['RCODE']!='00000000') {
					alert($RETURN_ARR['ERRMSG']); exit;
				}
			}
			// ▲ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------

			//상품정보값 수정
			$update_sql = "UPDATE cf_product SET state = '".$_POST['state']."', loan_end_date = '".$_POST['loan_end_date']."' WHERE idx = '".$prd_idx."'";
			if(sql_query($update_sql)) {
				oligoSendReport($prd_idx, $platform);		// 올리고 레포팅
				alert('정상적으로 처리되었습니다.', G5_ADMIN_URL.'/product_calculate.php?idx='.$prd_idx);
			}

		}


		/////////////////////////////////////////////////
		// state=6 : 기표전 대출계약취소 처리
		/////////////////////////////////////////////////
		else if($_POST['state']=='6') {

			// 대출계약취소로 인한 개인 투자금 반환처리
			$sql2 = "
				SELECT
					A.amount, A.member_idx, B.mb_id
				FROM
					cf_product_invest A
				INNER JOIN
					g5_member B ON A.member_idx=B.mb_no
				WHERE 1
					AND A.product_idx = '".$prd_idx."'
					AND A.invest_state ='Y'";
			$res2 = sql_query($sql2);
			while($INVEST = sql_fetch_array($res2)) {

				// 예치금으로 반환 처리. 환급계좌로 수동 이체시 본 프로세스 주석처리 할 것.
				$po_content = $PRDT['title'] . "-투자금 반환";
				insert_point($INVEST['mb_id'], $INVEST['amount'], $po_content, '@return', $member['mb_id'], $member['mb_id'].'-'.uniqid(''));

				$res3 = sql_query("UPDATE cf_product_invest        SET invest_state='R', cancel_date=NOW() WHERE product_idx='".$prd_idx."' AND member_idx='".$INVEST['member_idx']."' AND invest_state='Y'");
				$res4 = sql_query("UPDATE cf_product_invest_detail SET invest_state='R', cancel_date=NOW() WHERE product_idx='".$prd_idx."' AND member_idx='".$INVEST['member_idx']."' AND invest_state='Y'");
			}

			$update_sql = "UPDATE cf_product SET state = '".$_POST['state']."', cancel_date = '".date('Y-m-d')."' WHERE idx = '".$prd_idx."'";
			if(sql_query($update_sql)) {

				// ▼ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------
				if($ib_trust) {

					$ARR['REQ_NUM']    = "020";
					$ARR['SUBMIT_GBN'] = "04";
					$ARR['LOAN_SEQ']   = $prd_idx;		// 대출식별번호

					$RETURN_ARR = insidebank_request('256', $ARR);  // 인사이드뱅크 대출취소 요청전문(2400) 발송 -> 투자금 반환은 자동으로 처리됨

					if($RETURN_ARR['RCODE']!='00000000') {
						alert($RETURN_ARR['ERRMSG']); exit;
					}

				}
				// ▲ 제3자 예치금 관리시스템 적용 상품 처리 ----------------------

				oligoSendReport($prd_idx, $platform);		// 올리고 레포팅
				alert('정상적으로 처리되었습니다.', G5_ADMIN_URL.'/product_calculate.php?idx='.$prd_idx);
			}

		}

	}



	///////////////////////////////////////////////////////////////////////////////
	// 이자 지급요청 데이터 등록 (제3자 예치시스템)
	///////////////////////////////////////////////////////////////////////////////
	// IB_FB_P2P_REPAY_REQ, IB_FB_P2P_REPAY_REQ_DETAIL 테이블에 원리금 지금요청을
	// 등록 및 전문(B2500) 지급요청 처리됨.
	// 등록회차(REQ_SEQ)
	// ※	신한은행 파일방식 처리시간은 일3회(은행 영업일) 실행되며 실행시간은 아래와 같습니다.
	//    은행 처리시간 전 최소 30분전에 인사이드뱅크로 해당회차에 대한 요청을 하셔야 정상적인 처리가 진행됩니다.
	//    1회차: 05시, 2회차: 10시, 3회차: 17시
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "loan_interest_give_ib_request_ready") {


		if(!$ib_trust) {
			$RESULT_ARR = array('result' => 'ERROR', 'message' => '제3자 예치시스템이 적용된 대출건이 아닙니다.');
			echo json_encode($RESULT_ARR);
			exit;
		}

		$repay_arr_no = $_POST['turn'] - 1;
		$TARGET_REPAY = $REPAY[$repay_arr_no];
		$repay_count  = count($TARGET_REPAY['LIST']);

		//if(date('Y-m-d') < $TARGET_REPAY['repay_date']) { echo $_POST['turn'] . "회차 투자수익금의 지급완료 처리는 「" . date('Y년 m월 d일', strtotime($TARGET_REPAY['repay_date'])) . "」부터 가능합니다."; exit; }

		$ROW = sql_fetch("SELECT COUNT(idx) AS cnt_idx FROM cf_product_success WHERE product_idx='".$prd_idx."' AND turn='".$_POST['turn']."' AND loan_interest_state='Y'");
		if(!$ROW['cnt_idx']) {
			$msg = "'대출이자 수급완료' 처리가 되지 않아 진행 할 수 없습니다.";
			$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
			echo json_encode($RESULT_ARR);
			exit;
		}

		$SDATE = ($SDATE) ? preg_replace("/-/", "", $SDATE) : date('Ymd');

		$PARTNER_CD = 'P0012';
		$DC_NB = $prd_idx;


		//처리대기중인 데이터의 마지막 SEQ값 가져오기... SEQ필드가 문자형이라 형변환 처리 함 => CAST(SEQ AS unsigned)
		$TMP = sql_fetch("SELECT MAX(CAST(SEQ AS unsigned)) AS max_seq FROM IB_FB_P2P_REPAY_REQ_DETAIL WHERE SDATE='' AND REG_SEQ='' AND req_idx IS NULL");
		$seq = $TMP['max_seq'] + 1;
		unset($TMP);

		$dtlsql = "INSERT INTO IB_FB_P2P_REPAY_REQ_DETAIL (SEQ, PARTNER_CD, DC_NB, CUST_ID, TR_AMT, TR_AMT_P, CTAX_AMT, FEE, REPAY_RECEIPT_NB, invest_idx, turn, rdate) VALUES ";

		$insert_count = 0;
		for($j=0,$k=1; $j<$repay_count; $j++,$k++) {

			$REPAY_RECEIPT_NB = 'M' . $TARGET_REPAY['LIST'][$j]['mb_no'] . 'P' . $prd_idx . 'I' . $TARGET_REPAY['LIST'][$j]['invest_idx'];		// 원리금 수취권 번호

			// 입력된 기록이 없을때에만 등록
			$RECORDED = sql_fetch("SELECT COUNT(SEQ) AS cnt FROM IB_FB_P2P_REPAY_REQ_DETAIL WHERE REPAY_RECEIPT_NB='".$REPAY_RECEIPT_NB."' AND RESP_CODE='' AND turn='".$_POST['turn']."' AND is_overdue='N'");
			if(!$RECORDED['cnt']) {

				$SEQ		 = $seq;		// 해당 회차의 데이터 일련번호
				$CUST_ID = $TARGET_REPAY['LIST'][$j]['mb_no'];				// 투자자고객ID

				// 원금처리
				$TR_AMT_P = $TARGET_REPAY['LIST'][$j]['repay_principal'];
				/*
				if($PRDT['repay_type']=='1') {
					// 만기일시상환
					$TR_AMT_P = ($_POST['turn']==$INI['repay_count']) ? $TARGET_REPAY['LIST'][$j]['amount'] : 0;		// 세후 투자수익금중 투자자 원금금액
				}
				else {
					// 분할상환방식을 적용할 경우 정산프로세스에서 다시 계산할것.
					$TR_AMT_P = 0;
				}
				*/

				$TR_AMT   = $TARGET_REPAY['LIST'][$j]['interest'] + $TR_AMT_P;		// 입금금액 = 세후 투자수익금(세금+수수료를 제외한 재예치 대상 수익금)
				$CTAX_AMT = $TARGET_REPAY['LIST'][$j]['TAX']['sum'];							// 세금
				$FEE      = $TARGET_REPAY['LIST'][$j]['invest_usefee'];						// 수수료

				$dtlsql.= "(";
				$dtlsql.= "'".$SEQ."'";
				$dtlsql.= ",'".$PARTNER_CD."'";
				$dtlsql.= ",'".$DC_NB."'";
				$dtlsql.= ",'".$CUST_ID."'";
				$dtlsql.= ",'".$TR_AMT."'";
				$dtlsql.= ",'".$TR_AMT_P."'";
				$dtlsql.= ",'".$CTAX_AMT."'";
				$dtlsql.= ",'".$FEE."'";
				$dtlsql.= ",'".$REPAY_RECEIPT_NB."'";
				$dtlsql.= ",'".$TARGET_REPAY['LIST'][$j]['invest_idx']."'";
				$dtlsql.= ",'".$_POST['turn']."'";
				$dtlsql.= ", NOW()";
				$dtlsql.= ")";
				$dtlsql.= ($k<$repay_count) ? "," : "";

				$TOTAL_TR_AMT += $TR_AMT;

				$seq++;
				$insert_count++;

			}

		}

		//echo $dtlsql; exit;

		if($insert_count) {
			if( sql_query($dtlsql) ) {
				$sql = "UPDATE cf_product_success SET ib_request_ready='Y' WHERE product_idx='".$prd_idx."' AND turn='".$_POST['turn']."'";
				sql_query($sql);

				$msg = "대출상품명 :  " . $PRDT['title'] . "\n" .
						 "상환회차 :  " . $_POST['turn'] ."회차\n" .
						 "요청건수 : " . number_format($repay_count) . "건\n" .
						 "금액합계 : " . number_format($TOTAL_TR_AMT) . "원\n\n" .
						 "입금요청대기건으로 등록 되었습니다.";

				$RESULT_ARR = array('result' => 'SUCCESS', 'message' => $msg);
				echo json_encode($RESULT_ARR);

			}
			else {
				$msg = "DB 입력 오류가 발생하였습니다. 관리자에게 문의하십시요.";
				$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
				echo json_encode($RESULT_ARR);
			}
		}
		else {
			$msg = "기등록된 요청이거나, 등록 할 데이터가 없습니다.";
			$RESULT_ARR = array('result' => 'ERROR', 'message' => $msg);
			echo json_encode($RESULT_ARR);
		}

	}



	///////////////////////////////////////////////////////////////////////////////
	// 이자 지급요청 전송 (제3자 예치시스템)
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "loan_interest_give_ib_request_send") {

		$sdate = $_POST['req_sdate'];
		$stime = $_POST['req_stime'];
		$sdatetime = $sdate.' '.$stime;

		$SDATE = preg_replace('/-/', '', $sdate);
		$STIME = preg_replace('/:/', '', $stime);


		if( substr(G5_TIME_YMDHIS, 0, 16) >= substr($sdatetime, 0, 16) ) {
			$msg = "등록가능한 시간이 아닙니다. 다음 시간대를 이용하십시요.\n\n";
			$msg.= "현재시간 : " . substr(G5_TIME_YMDHIS, 0, 16);
			$RESULT_ARR = array("result" => "ERROR", "message" => $msg);
			echo json_encode($RESULT_ARR);
			exit;
		}

		// 중복 회차 거부설정
		$ROW_A = sql_fetch("SELECT COUNT(*) AS cnt FROM IB_FB_P2P_REPAY_REQ WHERE SDATE='".$SDATE."' AND STIME='".$STIME."' AND EXEC_STATUS='00'");  // 요청처리상태 (00:처리전,01:처리중,02:처리완료)
		$ROW_B = sql_fetch("SELECT COUNT(*) AS cnt FROM IB_FB_P2P_REPAY_REQ_ready WHERE SDATE='".$SDATE."' AND STIME='".$STIME."'");

		if( $ROW_A['cnt'] > 0 || $ROW_B['cnt'] > 0 ) {
			$msg = "해당 시간대의 예약 내역이 존재합니다. 다음 시간대를 이용하십시요.\n\n";
			$msg.= "현재시간 : " . substr(G5_TIME_YMDHIS, 0, 16);
			$RESULT_ARR = array("result" => "ERROR", "message" => $msg);
			echo json_encode($RESULT_ARR);
			exit;
		}


		$where = " SDATE='' AND REG_SEQ='' AND req_idx IS NULL";

		$sql = "
			SELECT
				COUNT(CUST_ID) AS TOTAL_CNT,
				IFNULL(SUM(TR_AMT),0) AS TOTAL_TR_AMT,
				IFNULL(SUM(TR_AMT_P),0) AS TOTAL_TR_AMT_P,
				IFNULL(SUM(CTAX_AMT),0) AS TOTAL_CTAX_AMT,
				IFNULL(SUM(FEE),0) AS TOTAL_FEE
			FROM
				IB_FB_P2P_REPAY_REQ_DETAIL
			WHERE ". $where;

		$ROW = sql_fetch($sql);

		// 다음 회차 설정
		$ROW_C = sql_fetch("SELECT COUNT(*) AS cnt FROM IB_FB_P2P_REPAY_REQ WHERE SDATE='".$SDATE."' AND STIME<='".$STIME."'");
		$ROW_D = sql_fetch("SELECT COUNT(*) AS cnt FROM IB_FB_P2P_REPAY_REQ_ready WHERE SDATE='".$SDATE."' AND STIME<='".$STIME."'");
		$reg_seq = $ROW_C['cnt'] + $ROW_D['cnt'] + 1;

		// 요청자료 금액 및 카운트 추출
		$REG_SEQ        = sprintf('%02d', $reg_seq);
		$PARTNER_CD     = 'P0012';
		$STIME					= $STIME;
		$TOTAL_CNT      = $ROW['TOTAL_CNT'];	// 해당 회차의 요청한 총 건수를 나타냅니다.
		$TOTAL_TR_AMT   = $ROW['TOTAL_TR_AMT'];
		$TOTAL_TR_AMT_P = $ROW['TOTAL_TR_AMT_P'];
		$TOTAL_CTAX_AMT = $ROW['TOTAL_CTAX_AMT'];
		$TOTAL_FEE      = $ROW['TOTAL_FEE'];

		$TOTAL_S_CNT		= '';							// 총정상처리건수
		$TOTAL_E_CNT		= '';							// 총에러처리건수
		$TRAN_DATE			= '';							// 처리일자 (YYYYMMDD)
		$TRAN_TIME			= '';							// 처리시간 (hhmmss)
		$RESP_CODE			= '';							// 응답코드
		$RESP_MSG				= '';							// 응답메세지
		$EXEC_STATUS		= '00';						// 00:처리전 01:처리중 02:처리완료

		// 펌뱅킹 원리금지급요청 및 실행정보 등록
		$sql = "
			INSERT INTO
				IB_FB_P2P_REPAY_REQ_ready
			SET
				SDATE          = '".$SDATE."',
				REG_SEQ        = '".$REG_SEQ."',
				PARTNER_CD     = '".$PARTNER_CD."',
				STIME          = '".$STIME."',
				TOTAL_CNT      = '".$TOTAL_CNT."',
				TOTAL_TR_AMT   = '".$TOTAL_TR_AMT."',
				TOTAL_TR_AMT_P = '".$TOTAL_TR_AMT_P."',
				TOTAL_CTAX_AMT = '".$TOTAL_CTAX_AMT."',
				TOTAL_FEE      = '".$TOTAL_FEE."',
				TRAN_DATE      = '".$TRAN_DATE."',
				TRAN_TIME      = '".$TRAN_TIME."',
				TOTAL_S_CNT    = '".$TOTAL_S_CNT."',
				TOTAL_E_CNT    = '".$TOTAL_E_CNT."',
				RESP_CODE      = '".$RESP_CODE."',
				RESP_MSG       = '".$RESP_MSG."',
				EXEC_STATUS    = '".$EXEC_STATUS."',
				apply          = ''";
		sql_query($sql);
		$insert_idx = sql_insert_id();

		if($insert_idx) {

			$PRDT_TURN = $_POST['PRDT_TURN'];

			//상품-차수별 상세 상환요청내역 전송정보 수정
			for($i=0; $i<count($PRDT_TURN); $i++) {

				$TMP_ARR = explode("&", trim($PRDT_TURN[$i]));
				$prd_idx = $TMP_ARR[0];
				$turn    = $TMP_ARR[1];
				$overdue = ($TMP_ARR[2]) ? $TMP_ARR[2] : 'N';

				// 펌뱅킹 원리금지급요청 상세정보 등록. 미리 등록된 상세정보내역에서 발송일자 및 실행순번 수정
				$sql2 = "
					UPDATE
						IB_FB_P2P_REPAY_REQ_DETAIL
					SET
						SDATE   = '".$SDATE."',
						REG_SEQ = '".$REG_SEQ."',
						req_idx = '".$insert_idx."'
					WHERE
						$where
						AND DC_NB='".$prd_idx."'
						AND turn='".$turn."'
						AND is_overdue='".$overdue."'
					ORDER BY
						rdate";
				//echo $sql2."\n";
				sql_query($sql2);

				// 지급상태정보 변경 *** 최후 지급완료처리 IB_FB_P2P_REPAY_REQ 테이블의 RESP_CODE 값(정상:00000000)으로 구분 -> 자동스케쥴러가 필요함. ***
				$sqlx = "UPDATE cf_product_success SET invest_give_state='W' WHERE product_idx='".$prd_idx."' AND turn='".$turn."'";
				//echo $sqlx."\n";
				sql_query($sqlx);

			}



			$cnt_sql = "
				SELECT
					COUNT(CUST_ID) AS TOTAL_CNT,
					SUM(TR_AMT) AS TOTAL_TR_AMT,
					SUM(TR_AMT_P) AS TOTAL_TR_AMT_P,
					SUM(CTAX_AMT) AS TOTAL_CTAX_AMT,
					SUM(FEE) AS TOTAL_FEE
				FROM
					IB_FB_P2P_REPAY_REQ_DETAIL
				WHERE 1
					AND SDATE = '".$SDATE."'
					AND req_idx = '".$insert_idx."'";
			//echo $cnt_sql."\n\n";
			$INPUTED = sql_fetch($cnt_sql);

			// 예약테이블 카운트 재수정
			$sql3 = "
				UPDATE
					IB_FB_P2P_REPAY_REQ_ready
				SET
					TOTAL_CNT      = '".$INPUTED['TOTAL_CNT']."',
					TOTAL_TR_AMT   = '".$INPUTED['TOTAL_TR_AMT']."',
					TOTAL_TR_AMT_P = '".$INPUTED['TOTAL_TR_AMT_P']."',
					TOTAL_CTAX_AMT = '".$INPUTED['TOTAL_CTAX_AMT']."',
					TOTAL_FEE      = '".$INPUTED['TOTAL_FEE']."'
				WHERE
					idx = '".$insert_idx."'";
			//echo $sql3."\n\n";
			sql_query($sql3);

			$RESULT_ARR = array("result" => "SUCCESS", "message" => "");
			echo json_encode($RESULT_ARR);

		}

	}



	///////////////////////////////////////////////////////////////////////////////
	// 이자 지급처리
	// 가상계좌환급도 적용되어있음 (2017-11-30 by.배차장)
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "loan_interest_give") {

		$repay_turn   = $_POST['turn'];
		$repay_arr_no = $repay_turn - 1;

		$TARGET_REPAY = $REPAY[$repay_arr_no];
		$repay_date   = $TARGET_REPAY['repay_date'];	// 약정상환일
		$repay_count  = count($TARGET_REPAY['LIST']);

		/*
		echo "예치금 충전: ".$PRDT['title']." (".$repay_turn."회차 원리금)\n\n";
		echo "INI => "; print_r($INI);
		echo "REPAY => "; print_r($REPAY);
		echo "TARGET_REPAY => "; print_r($TARGET_REPAY);
		exit;
		*/

		//if(date('Y-m-d') < $TARGET_REPAY['repay_date']) { echo $repay_turn . "회차 투자수익금의 원리금 지급 처리는 「" . date('Y년 m월 d일', strtotime($TARGET_REPAY['repay_date'])) . "」부터 가능합니다."; exit; }

		$ROW = sql_fetch("SELECT COUNT(idx) AS cnt_idx FROM cf_product_success WHERE loan_interest_state='Y' AND product_idx='".$prd_idx."' AND turn='".$repay_turn."'");
		if(!$ROW['cnt_idx']) {
			$RESULT_ARR = array("result" => "ERROR", "message" => "'대출이자 수급완료' 처리가 되지 않아 진행 할 수 없습니다.");
			echo json_encode($RESULT_ARR);
			exit;
		}

		$proc_count = 0;
		for($j=0,$k=1; $j<$repay_count; $j++,$k++) {

			// ** 기입금자 중복 지급방지 체크 **
			$cntsql = "SELECT COUNT(idx) AS cnt_idx FROM cf_product_give WHERE invest_idx='".$TARGET_REPAY['LIST'][$j]['invest_idx']."' AND product_idx='".$prd_idx."' AND turn='".$repay_turn."' AND is_overdue='N'";
			$ROW    = sql_fetch($cntsql);
			if(!$ROW['cnt_idx']) {

				$bank_code         = $TARGET_REPAY['LIST'][$j]['bank_code'];
				$bank_name         = $BANK[$bank_code];
				$bank_private_name = $TARGET_REPAY['LIST'][$j]['bank_private_name'];
				$account_num       = preg_replace("/-/", "", $TARGET_REPAY['LIST'][$j]['account_num']);

				$proc_auth_flag = true;

				if($ib_trust) {

					// 원리금 수취방식에 따른 입금계좌 설정(제3자 예치시스템 적용 상품 일 경우에만 적용됨)
					if($TARGET_REPAY['LIST'][$j]['receive_method']=='2') {		// 가상계좌환급
						$MB = sql_fetch("SELECT va_bank_code2, virtual_account2, va_private_name2, insidebank_after_trans_target FROM g5_member WHERE mb_no='".$TARGET_REPAY['LIST'][$j]['mb_no']."'");
						if($MB['insidebank_after_trans_target']=='Y') {
							$proc_auth_flag = false;
							//echo "신한 예치금 전환 대상자:\n";
							//echo "  아이디 - " . $TARGET_REPAY['LIST'][$j]['mb_id'] . "\n";
							//echo ($TARGET_REPAY['LIST'][$j]['member_type']=='2') ? "  법인명 - " . $TARGET_REPAY['LIST'][$j]['mb_name'] : "  성명 - " . $TARGET_REPAY['LIST'][$j]['mb_name'];
							//echo "\n\n";
						}
						else {
							$bank_code         = $MB['va_bank_code2'];
							$bank_name         = $BANK[$MB['va_bank_code2']];
							$bank_private_name = $MB['va_private_name2'];
							$account_num       = preg_replace("/-/", "", $MB['virtual_account2']);
						}
					}

					if($proc_auth_flag && $bank_code && $account_num) {

						if($prd_idx=='171') {
							if($repay_turn == $INI['repay_count']) {
								$TARGET_REPAY['LIST'][$j]['amount'] = 0;
							}
						}

						//---- 최종이체금액 (최종 회차일 경우 투자원금을 포함해서 이체 또는 충전) ----//
						$last_trans_amount = ($repay_turn < $INI['repay_count']) ? $TARGET_REPAY['LIST'][$j]['interest'] : $TARGET_REPAY['LIST'][$j]['amount'] + $TARGET_REPAY['LIST'][$j]['interest'];
						//---- 최종이체금액 (최종 회차일 경우 투자원금을 포함해서 이체 또는 충전) ----//

						//echo $last_trans_amount; exit;

						/////////////////////////////////////////////////////////////////////////////
						// 환급계좌로 원리금 수취하는 회원 지급을 위한 예치금 출금전문(3200) 발송
						/////////////////////////////////////////////////////////////////////////////
						if($TARGET_REPAY['LIST'][$j]['receive_method']=='1') {
							$REQ_NUM         = '032';																				// 전문번호(출금: 032)
							$CUST_ID         = $TARGET_REPAY['LIST'][$j]['mb_no'];					// 투자자고객ID (투자자번호로 처리함)
							$TRAN_BANK_CD    = $TARGET_REPAY['LIST'][$j]['bank_code'];			// 이체은행코드(출금신청한 예치금을 입금받을 은행코드)
							$TRAN_ACCT_NB    = preg_replace("/-/", "", $TARGET_REPAY['LIST'][$j]['account_num']);		// 이체계좌번호(출금신청한 예치금을 입금받을 계좌번호)
							//$bank_private_name = $member['bank_private_name'];						// 이체계좌성명 (동일계좌 동일금액의 연속된 이체요청은 받아들여지지 않아 예금주명 뒤에 숫자를 다르게 붙여준다. - 신한은행 이승우주임 김진부과장 guide)
							//$bank_private_name.= ($member['bank_private_name_sub']) ? '('.$member['bank_private_name_sub'].')' : '';
							//$TRAN_REMITEE_NM = mb_substr($bank_private_name, 0, 6) . sprintf("%02d", rand(0,99));

							$TRAN_REMITEE_NM = "";
							$TRAN_REMITEE_NM.= "헬로펀딩";
							$TRAN_REMITEE_NM.= str_f6($PRDT['title'], '[제', '호]');

							$ARR['REQ_NUM']         = $REQ_NUM;
							$ARR['CUST_ID']         = $CUST_ID;
							$ARR['TRAN_BANK_CD']    = $TRAN_BANK_CD;																			// 입금계좌은행코드
							$ARR['TRAN_ACCT_NB']    = $TRAN_ACCT_NB;																			// 입금계좌번호
							$ARR['TRAN_REMITEE_NM'] = $TRAN_REMITEE_NM."(".sprintf("%02d", rand(0,99)).")";	// 이체계좌성명 (동일 이체계좌성명 발생시 이체가 되지 않으므로 랜덤숫자붙여줌)
							$ARR['TRAN_AMT']        = $last_trans_amount;																	// 이체금액
							$ARR['TRAN_MEMO']       = $TRAN_REMITEE_NM;																		// 이체계좌통장메모
							$ARR['GUAR_MEMO']       = '원리금('.$TARGET_REPAY['LIST'][$j]['mb_no'].')';		// 예치금모계좌통장메모
							$ARR['FUND_KIND']       = '10';																								// 자금성격(10:예치금)
							//print_r($ARR);
							$RETURN_ARR = insidebank_request('256', $ARR);

							$proc_auth_flag = false;

							if($RETURN_ARR['RCODE']=='00000000') {
								$proc_auth_flag = true;
							}
							else {
								// IS0102 코드 발생시 결번요청(8400)으로 지급전문 실행 결과값 재전송받기
								if( $RETURN_ARR['RCODE']=='IS0102') {

									$LAST_REQUEST = sql_fetch("
										SELECT
											idx, request_arr
										FROM
											IB_request_log
										WHERE 1
											AND request_code='3200' AND rcode='IS0102' AND exec_path='/adm/product_calculate_proc.php'
											AND request_arr LIKE '%CUST_ID=".$CUST_ID."&%'
										ORDER BY
											idx DESC LIMIT 1");

									if( $LAST_REQUEST['idx'] ) {

										$REQUEST_ARR = explode("&", $LAST_REQUEST['request_arr']);
										$last_fbseq = preg_replace("/FB_SEQ=/", "", $REQUEST_ARR[0]);

										if($last_fbseq) {
											// 결번요청(8400)  -> 전문 실행 결과값 재전송받기
											$ARR2['SUBMIT_GBN'] = "04";						//전문번호
											$ARR2['TRAN_DATE']  = date('Ymd');		//date('Ymd');
											$ARR2['ORI_FB_SEQ'] = $last_fbseq;

											$RETURN_ARR2 = insidebank_request("000", $ARR2);
											if($RETURN_ARR2['ORI_FB_REQCODE']=='00000000') {
												sql_query("UPDATE IB_request_log SET rcode='00000000' WHERE idx='".$LAST_REQUEST['idx']."'");
												$proc_auth_flag = true;
												$RETURN_ARR['GUAR_SEQ'] = $RETURN_ARR2['GUAR_SEQ'];
											}
										}

									}

								}
							}
						}		// end if($TARGET_REPAY['LIST'][$j]['receive_method']=='1')

						/////////////////////////////////////////////////////////////////////////////
						// 가상계좌로 받는 사람은 원리금만큼 포인트 부여.
						// 출금처리 안함
						/////////////////////////////////////////////////////////////////////////////
						else if($TARGET_REPAY['LIST'][$j]['receive_method']=='2') {
							$point_subject = '예치금 충전: '.$PRDT['title'].' ('.$repay_turn.'회차 원리금)';
							insert_point($TARGET_REPAY['LIST'][$j]['mb_id'], $last_trans_amount, $point_subject, '@repay', $member['mb_id'], $member['mb_id'].'-'.uniqid(''));
						}

						/////////////////////////////////////////////////////////////////////////////
						// 환급계좌 미지정시 이체 또는 포인트 부여 차단
						/////////////////////////////////////////////////////////////////////////////
						else {
							$proc_auth_flag = false;
							//echo "환급계좌 미지정 회원:\n";
							//echo "  아이디 - " . $TARGET_REPAY['LIST'][$j]['mb_id'] . "\n";
							//echo ($TARGET_REPAY['LIST'][$j]['member_type']=='2') ? "  법인명 - " . $TARGET_REPAY['LIST'][$j]['mb_name'] : "  성명 - " . $TARGET_REPAY['LIST'][$j]['mb_name'];
							//echo "\n\n";
						}

					}
					else {
						$proc_auth_flag = false;
						//echo "원리금 지급계좌 미등록 회원:\n";
						//echo "  아이디 - " . $TARGET_REPAY['LIST'][$j]['mb_id'] . "\n";
						//echo ($TARGET_REPAY['LIST'][$j]['member_type']=='2') ? "  법인명 - " . $TARGET_REPAY['LIST'][$j]['mb_name'] : "  성명 - " . $TARGET_REPAY['LIST'][$j]['mb_name'];
						//echo "\n\n";
					}

				}

				$remit_fee = ($PRDT['invest_usefee']=='' || $PRDT['invest_usefee']=='0.00') ? '1' : '';

				if($proc_auth_flag) {
					// 원리금 입금로그 등록	(invest_amount 는 실수령 이자만 등록)
					$insert_sql = "
						INSERT INTO
							cf_product_give
						SET
							`date`            = '".$repay_date."',
							invest_amount     = '".$TARGET_REPAY['LIST'][$j]['interest']."',
							interest          = '".$TARGET_REPAY['LIST'][$j]['interest']."',
							principal         = '".$TARGET_REPAY['LIST'][$j]['repay_principal']."',
							interest_tax      = '".$TARGET_REPAY['LIST'][$j]['TAX']['interest_tax']."',
							local_tax         = '".$TARGET_REPAY['LIST'][$j]['TAX']['local_tax']."',
							fee               = '".$TARGET_REPAY['LIST'][$j]['invest_usefee']."',
							invest_idx        = '".$TARGET_REPAY['LIST'][$j]['invest_idx']."',
							member_idx        = '".$TARGET_REPAY['LIST'][$j]['mb_no']."',
							product_idx       = '".$prd_idx."',
							turn              = '".$repay_turn."',
							is_overdue        = 'N',
							is_creditor       = '".$TARGET_REPAY['LIST'][$j]['is_creditor']."',
							remit_fee         = '".$remit_fee."',
							receive_method    = '".$TARGET_REPAY['LIST'][$j]['receive_method']."',
							bank_name         = '".$bank_name."',
							bank_private_name = '".$bank_private_name."',
							account_num       = '".$account_num."',
							banking_date      = NOW(),
							GUAR_SEQ          = '".$RETURN_ARR['GUAR_SEQ']."'";
					//echo $insert_sql."\n\n";
					if(sql_query($insert_sql)) {
						$proc_count += sql_affected_rows();

						// 지급상태정보 변경 (지급완료처리)
						// $update_sql = "UPDATE cf_product_success SET invest_give_state='Y' WHERE product_idx='".$prd_idx."' AND turn='".$repay_turn."'";
						// sql_query($update_sql);
					}
					else {
						$RESULT_ARR = array("result" => "ERROR", "message" => "DB INSERT ERROR");
						echo json_encode($RESULT_ARR);
						break;
					}
				}

				$RETURN_ARR = NULL;


			}

		}	// end for

		if($proc_count) {
			$msg = $proc_count . "건 지급처리 완료";
		}
		else {
			$msg = "재지급 처리건이 없습니다.\n투자수익금 지급완료 처리 하십시요.";
		}

		$RESULT_ARR = array("result" => "SUCCESS", "message" => $msg);
		echo json_encode($RESULT_ARR);
		//exit;

	}


	///////////////////////////////////////////////////////////////////////////////
	// 연체이자 지급요청 전송 (제3자 예치시스템)
	///////////////////////////////////////////////////////////////////////////////
	if($_REQUEST['action'] == "overdue_give_ib_request_ready") {

		if(!$ib_trust) {
			$msg = "제3자 예치시스템이 적용된 대출건이 아닙니다.";
			$RESULT_ARR = array("result" => "FAIL", "message" => $msg);
			echo json_encode($RESULT_ARR);
			exit;
		}

		$repay_turn   = $_POST['turn'];
		$repay_date   = $_POST['date'];		// 약정 정산일
		$repay_arr_no = $repay_turn - 1;
		$TARGET_REPAY = $REPAY[$repay_arr_no];
		$repay_count  = count($TARGET_REPAY['OVERDUE_LIST']);

		$ROW = sql_fetch("SELECT COUNT(idx) AS cnt_idx FROM cf_product_success WHERE product_idx='".$prd_idx."' AND turn='".$repay_turn."' AND overdue_receive='Y'");
		if(!$ROW['cnt_idx']) {
			$msg = "연체이자 수급완료 처리가 되지 않아 진행 할 수 없습니다.";
			$RESULT_ARR = array("result" => "FAIL", "message" => $msg);
			echo json_encode($RESULT_ARR);
			exit;
		}

		$PARTNER_CD = 'P0012';
		$DC_NB = $prd_idx;


		//처리대기중인 데이터의 마지막 SEQ값 가져오기... SEQ필드가 문자형이라 형변환 처리 함 => CAST(SEQ AS unsigned)
		$TMP = sql_fetch("SELECT MAX(CAST(SEQ AS unsigned)) AS max_seq FROM IB_FB_P2P_REPAY_REQ_DETAIL WHERE SDATE='' AND REG_SEQ='' AND req_idx IS NULL");
		$seq = $TMP['max_seq'] + 1;
		unset($TMP);

		$dtlsql = "INSERT INTO IB_FB_P2P_REPAY_REQ_DETAIL (SEQ, PARTNER_CD, DC_NB, CUST_ID, TR_AMT, TR_AMT_P, CTAX_AMT, FEE, REPAY_RECEIPT_NB, invest_idx, turn, is_overdue, rdate) VALUES ";

		$insert_count = 0;
		for($j=0,$k=1; $j<$repay_count; $j++,$k++) {

			$REPAY_RECEIPT_NB = 'M' . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_no'] . 'P' . $prd_idx . 'I' . $TARGET_REPAY['OVERDUE_LIST'][$j]['invest_idx'];		// 원리금 수취권 번호

			// 입력된 기록이 없을때에만 등록
			$RECORDED = sql_fetch("SELECT COUNT(SEQ) AS cnt FROM IB_FB_P2P_REPAY_REQ_DETAIL WHERE REPAY_RECEIPT_NB='".$REPAY_RECEIPT_NB."' AND RESP_CODE='' AND turn='".$repay_turn."' AND is_overdue='1'");

			if(!$RECORDED['cnt']) {

				$SEQ		  = $seq;		// 해당 회차의 데이터 일련번호
				$CUST_ID  = $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_no'];									// 투자자고객ID
				$TR_AMT_P = $TARGET_REPAY['OVERDUE_LIST'][$j]['repay_principal'];				// 원금처리
				$TR_AMT   = $TARGET_REPAY['OVERDUE_LIST'][$j]['interest'] + $TR_AMT_P;	// 입금금액 = 세후 투자수익금(세금+수수료를 제외한 재예치 대상 수익금)
				$CTAX_AMT = $TARGET_REPAY['OVERDUE_LIST'][$j]['TAX']['sum'];						// 세금
				$FEE      = $TARGET_REPAY['OVERDUE_LIST'][$j]['invest_usefee'];					// 수수료

				$dtlsql.= "(";
				$dtlsql.= "'".$SEQ."'";
				$dtlsql.= ",'".$PARTNER_CD."'";
				$dtlsql.= ",'".$DC_NB."'";
				$dtlsql.= ",'".$CUST_ID."'";
				$dtlsql.= ",'".(int)$TR_AMT."'";
				$dtlsql.= ",'".(int)$TR_AMT_P."'";
				$dtlsql.= ",'".(int)$CTAX_AMT."'";
				$dtlsql.= ",'".(int)$FEE."'";
				$dtlsql.= ",'".$REPAY_RECEIPT_NB."'";
				$dtlsql.= ",'".$TARGET_REPAY['OVERDUE_LIST'][$j]['invest_idx']."'";
				$dtlsql.= ",'".$repay_turn."'";
				$dtlsql.= ",'Y'";
				$dtlsql.= ", NOW()";
				$dtlsql.= ")";
				$dtlsql.= ($k<$repay_count) ? "," : "";

				$TOTAL_TR_AMT += $TR_AMT;

				$seq++;
				$insert_count++;

			}
		}

		if($insert_count) {
			if( sql_query($dtlsql) ) {
				$sql = "UPDATE cf_product_success SET overdue_ib_request_ready='Y' WHERE product_idx='".$prd_idx."' AND turn='".$repay_turn."'";
				sql_query($sql);
				$msg = "대출상품명 :  " . $PRDT['title'] . "\n" .
						 "상환회차 :  " . $_POST['turn'] ."회차(연체이자)\n" .
						 "요청건수 : " . number_format($repay_count) . "건\n" .
						 "금액합계 : " . number_format($TOTAL_TR_AMT) . "원\n\n" .
						 "입금요청대기건으로 등록 되었습니다.";
				$RESULT_ARR = array("result" => "SUCCESS", "message" => $msg);
				echo json_encode($RESULT_ARR);
			}
			else {
				$msg = "DB 입력 오류가 발생하였습니다. 관리자에게 문의하십시요.";
				$RESULT_ARR = array("result" => "ERROR", "message" => $msg);
				echo json_encode($RESULT_ARR);
			}
		}
		else {
			$msg = "기등록된 요청이거나, 등록 할 데이터가 없습니다.";
			$RESULT_ARR = array("result" => "ERROR", "message" => $msg);
			echo json_encode($RESULT_ARR);
		}

	}


	###############################################################
	## 연체이자 지급
	###############################################################
	if($_REQUEST['action'] == "overdue_give") {

		$repay_turn   = $_POST['turn'];
		$repay_date   = $_POST['date'];		// 약정 정산일
		$repay_arr_no = $repay_turn - 1;
		$TARGET_REPAY = $REPAY[$repay_arr_no];
		$repay_count  = count($TARGET_REPAY['OVERDUE_LIST']);

		$ROW = sql_fetch("SELECT COUNT(idx) AS cnt_idx FROM cf_product_success WHERE overdue_receive='Y' AND product_idx='".$prd_idx."' AND turn='".$repay_turn."'");
		if(!$ROW['cnt_idx']) {
			$RESULT_ARR = array("result" => "ERROR", "message" => "'연체이자 수급완료' 처리가 되지 않아 진행 할 수 없습니다.");
			echo json_encode($RESULT_ARR);
			exit;
		}

		$proc_count = 0;
		for($j=0,$k=1; $j<$repay_count; $j++,$k++) {

			// ** 기입금자 중복 지급방지 체크 **
			$cntsql = "SELECT COUNT(idx) AS cnt_idx FROM cf_product_give WHERE invest_idx='".$TARGET_REPAY['OVERDUE_LIST'][$j]['invest_idx']."' AND product_idx='".$prd_idx."' AND turn='".$repay_turn."' AND is_overdue='Y'";
			$ROW    = sql_fetch($cntsql);
			if(!$ROW['cnt_idx']) {

				$bank_code         = $TARGET_REPAY['OVERDUE_LIST'][$j]['bank_code'];
				$bank_name         = $BANK[$bank_code];
				$bank_private_name = $TARGET_REPAY['OVERDUE_LIST'][$j]['bank_private_name'];
				$account_num       = preg_replace("/-/", "", $TARGET_REPAY['OVERDUE_LIST'][$j]['account_num']);

				$proc_auth_flag = true;

				if($ib_trust) {

					// 원리금 수취방식에 따른 입금계좌 설정(제3자 예치시스템 적용 상품 일 경우에만 적용됨)
					if($TARGET_REPAY['OVERDUE_LIST'][$j]['receive_method']=='2') {		// 가상계좌환급
						$MB = sql_fetch("SELECT va_bank_code2, virtual_account2, va_private_name2, insidebank_after_trans_target FROM g5_member WHERE mb_no='".$TARGET_REPAY['OVERDUE_LIST'][$j]['mb_no']."'");
						if($MB['insidebank_after_trans_target']=='Y') {
							$proc_auth_flag = false;
							//echo "신한 예치금 전환 대상자:\n";
							//echo "  아이디 - " . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_id'] . "\n";
							//echo ($TARGET_REPAY['OVERDUE_LIST'][$j]['member_type']=='2') ? "  법인명 - " . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_name'] : "  성명 - " . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_name'];
							//echo "\n\n";
						}
						else {
							$bank_code         = $MB['va_bank_code2'];
							$bank_name         = $BANK[$MB['va_bank_code2']];
							$bank_private_name = $MB['va_private_name2'];
							$account_num       = preg_replace("/-/", "", $MB['virtual_account2']);
						}
					}

					if($proc_auth_flag && $bank_code && $account_num) {

						$last_trans_amount = $TARGET_REPAY['OVERDUE_LIST'][$j]['interest'] + $TARGET_REPAY['OVERDUE_LIST'][$j]['repay_principal'];

						/////////////////////////////////////////////////////////////////////////////
						// 환급계좌로 원리금 수취하는 회원 지급을 위한 예치금 출금전문(3200) 발송
						/////////////////////////////////////////////////////////////////////////////
						if($TARGET_REPAY['OVERDUE_LIST'][$j]['receive_method']=='1') {
							$REQ_NUM         = '032';																				// 전문번호(출금: 032)
							$CUST_ID         = $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_no'];					// 투자자고객ID (투자자번호로 처리함)
							$TRAN_BANK_CD    = $TARGET_REPAY['OVERDUE_LIST'][$j]['bank_code'];			// 이체은행코드(출금신청한 예치금을 입금받을 은행코드)
							$TRAN_ACCT_NB    = preg_replace("/-/", "", $TARGET_REPAY['OVERDUE_LIST'][$j]['account_num']);		// 이체계좌번호(출금신청한 예치금을 입금받을 계좌번호)
							//$bank_private_name = $member['bank_private_name'];						// 이체계좌성명 (동일계좌 동일금액의 연속된 이체요청은 받아들여지지 않아 예금주명 뒤에 숫자를 다르게 붙여준다. - 신한은행 이승우주임 김진부과장 guide)
							//$bank_private_name.= ($member['bank_private_name_sub']) ? '('.$member['bank_private_name_sub'].')' : '';
							//$TRAN_REMITEE_NM = mb_substr($bank_private_name, 0, 6) . sprintf("%02d", rand(0,99));

							$TRAN_REMITEE_NM = "";
							$TRAN_REMITEE_NM.= "헬로펀딩";
							$TRAN_REMITEE_NM.= str_f6($PRDT['title'], '[제', '호]');

							$ARR['REQ_NUM']         = $REQ_NUM;
							$ARR['CUST_ID']         = $CUST_ID;
							$ARR['TRAN_BANK_CD']    = $TRAN_BANK_CD;																			// 입금계좌은행코드
							$ARR['TRAN_ACCT_NB']    = $TRAN_ACCT_NB;																			// 입금계좌번호
							$ARR['TRAN_REMITEE_NM'] = $TRAN_REMITEE_NM."(".sprintf("%02d", rand(0,99)).")";	// 이체계좌성명 (동일 이체계좌성명 발생시 이체가 되지 않으므로 랜덤숫자붙여줌)
							$ARR['TRAN_AMT']        = $last_trans_amount;																	// 이체금액
							$ARR['TRAN_MEMO']       = $TRAN_REMITEE_NM;																		// 이체계좌통장메모
							$ARR['GUAR_MEMO']       = '연체이자('.$TARGET_REPAY['OVERDUE_LIST'][$j]['mb_no'].')';		// 예치금모계좌통장메모
							$ARR['FUND_KIND']       = '10';																								// 자금성격(10:예치금)
							//print_r($ARR);
							$RETURN_ARR = insidebank_request('256', $ARR);
							if($RETURN_ARR['RCODE']!='00000000') {
								$proc_auth_flag = false;
							}
						}

						/////////////////////////////////////////////////////////////////////////////
						// 가상계좌로 받는 사람은 원리금만큼 포인트 부여.
						// 출금처리 안함
						/////////////////////////////////////////////////////////////////////////////
						else if($TARGET_REPAY['OVERDUE_LIST'][$j]['receive_method']=='2') {
							$point_subject = '예치금 충전: '.$PRDT['title'].' ('.$repay_turn.'회차 연체이자)';
							insert_point($TARGET_REPAY['OVERDUE_LIST'][$j]['mb_id'], $last_trans_amount, $point_subject, '@overdue_repay', $member['mb_id'], $member['mb_id'].'-'.uniqid(''));
						}

						/////////////////////////////////////////////////////////////////////////////
						// 환급계좌 미지정시 이체 또는 포인트 부여 차단
						/////////////////////////////////////////////////////////////////////////////
						else {
							$proc_auth_flag = false;
							//echo "환급계좌 미지정 회원:\n";
							//echo "  아이디 - " . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_id'] . "\n";
							//echo ($TARGET_REPAY['OVERDUE_LIST'][$j]['member_type']=='2') ? "  법인명 - " . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_name'] : "  성명 - " . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_name'];
							//echo "\n\n";
						}

					}
					else {
						$proc_auth_flag = false;
						//echo "원리금 지급계좌 미등록 회원:\n";
						//echo "  아이디 - " . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_id'] . "\n";
						//echo ($TARGET_REPAY['OVERDUE_LIST'][$j]['member_type']=='2') ? "  법인명 - " . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_name'] : "  성명 - " . $TARGET_REPAY['OVERDUE_LIST'][$j]['mb_name'];
						//echo "\n\n";
					}

				}

				$remit_fee = ($PRDT['invest_usefee']=='' || $PRDT['invest_usefee']=='0.00') ? '1' : '';

				if($proc_auth_flag) {
					// 수익금 입금로그 등록	(invest_amount 는 실수령 이자만 등록)
					$insert_sql = "
						INSERT INTO
							cf_product_give
						SET
							`date`            = '".$repay_date."',
							invest_amount     = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['interest']."',
							interest          = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['interest']."',
							principal         = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['repay_principal']."',
							interest_tax      = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['TAX']['interest_tax']."',
							local_tax         = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['TAX']['local_tax']."',
							fee               = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['invest_usefee']."',
							invest_idx        = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['invest_idx']."',
							member_idx        = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['mb_no']."',
							product_idx       = '".$prd_idx."',
							turn              = '".$repay_turn."',
							is_overdue        = 'Y',
							is_creditor       = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['is_creditor']."',
							remit_fee         = '".$remit_fee."',
							receive_method    = '".$TARGET_REPAY['OVERDUE_LIST'][$j]['receive_method']."',
							bank_name         = '".$bank_name."',
							bank_private_name = '".$bank_private_name."',
							account_num       = '".$account_num."',
							banking_date      = NOW(),
							GUAR_SEQ='".$RETURN_ARR['GUAR_SEQ']."'";
					//echo $insert_sql."\n\n";

					if(sql_query($insert_sql)) {
						$proc_count += sql_affected_rows();
					}
					else {
						echo "DB INSERT ERROR";
						break;
					}

				}

				$RETURN_ARR = NULL;


			}

		}	// end for

		if($proc_count) {
			$msg = $proc_count . "건 지급처리 완료";
		}
		else {
			$msg = "재지급 처리건이 없습니다.\n연체이자 지급완료 처리 하십시요.";
		}

		$RESULT_ARR = array("result" => "SUCCESS", "message" => $msg);
		echo json_encode($RESULT_ARR);

	}

}


sql_close();

// 최초 투자자 마킹 cf_product_invest.first_inv
shell_exec("/usr/local/php/bin/php /home/crowdfund/public_html/adm/jipyo/first_inv_cli.php" . " > /dev/null 2>/dev/null &");

exit;

?>