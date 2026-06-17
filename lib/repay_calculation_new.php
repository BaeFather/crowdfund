<?
#################################################
## 통합 원리금정산 함수 (DB테이블 이용)
## 최종수정일 2022-03-24
#################################################

//** 개별 정산 라이브러리는 common.php 의 investStatement() 참조 **
function repayCalculationNew($product_idx, $invest_member_id='', $only_summary='') {

	global $CONF, $BANK, $VBANK, $member, $office_connect;

	if(!$product_idx) return false;

	// 2021-02-05일 이전 종료된 상품 중 연체기록이 있었던 상품 배열화 (기존 연체정산방식 유지를 위하여 설정한 값임)
	$OVERDUE_RECORD_PRODUCT = array(
		144,145,146,171,175,176,1945,3008,3023,3063,
		3079,3086,3110,3118,3131,3132,3157,3167,3187,3194,
		3201,3215,3223,3224,3239,3249,3256,3263,3270,3278,
		3279,3299,3306,3308,3315,3324,3334,3341,3359,3382,
		3391,3422,3442,3452,3461,3471,3481,3515,3538,3575,
		3638,3721,3732,3753
	);

	$invest_member_id   = trim($invest_member_id);				// 특정 투자자만 조회 할 경우
	if($invest_member_id) { $invest_member_idx = sql_fetch("SELECT mb_no FROM g5_member WHERE mb_id='".$invest_member_id."'")['mb_no']; }

	$where = " 1=1 ";
	$where.= " AND A.product_idx='$product_idx' ";


	///////////////////
	// 상품정보
	///////////////////
	$prdt_sql = "
		SELECT
			A.idx, A.gr_idx, A.ai_grp_idx, A.state, A.category, A.title,
			A.invest_return, A.withhold_tax_rate, A.loan_interest_rate, A.overdue_rate, A.loan_usefee, A.invest_usefee, A.invest_usefee_type,
			A.invest_period, A.invest_days, A.recruit_period_start, A.recruit_period_end, A.recruit_amount, A.repay_type,
			A.ib_trust, A.ib_product_regist, A.loan_mb_no, A.repay_acct_no,
			(SELECT ref_no FROM KSNET_VR_ACCOUNT WHERE VR_ACCT_NO=A.repay_acct_no) AS ref_no,
			A.loan_dep_bank_cd1, A.loan_dep_acct_nb1, A.loan_dep_amt1, A.loan_dep_acct_memo1,
			A.loan_dep_bank_cd2, A.loan_dep_acct_nb2, A.loan_dep_amt2, A.loan_dep_acct_memo2,
			A.loan_dep_bank_cd3, A.loan_dep_acct_nb3, A.loan_dep_amt3, A.loan_dep_acct_memo3,
			A.loan_dep_bank_cd4, A.loan_dep_acct_nb4, A.loan_dep_amt4, A.loan_dep_acct_memo4,
			A.loan_dep_bank_cd5, A.loan_dep_acct_nb5, A.loan_dep_amt5, A.loan_dep_acct_memo5,
			A.open_datetime, A.open_date, A.start_datetime, A.start_date, A.end_datetime, A.end_date, A.display,
			A.loan_name, A.loan_contact, A.loan_address,
			A.insert_date, A.invest_end_date, A.loan_start_date, A.loan_end_date, A.loan_end_date_orig, A.advanced_payment, A.advance_invest, A.advance_invest_ratio,
			A.down_date, A.kakaopay_product_id,
			(SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_count,
			(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_principal,
			(SELECT IFNULL(MAX(turn), 0) FROM cf_product_give WHERE product_idx=A.idx AND banking_date IS NOT NULL) AS max_gived_turn
		FROM
			cf_product A
		WHERE
			A.idx='".$product_idx."'";

	$PRDT = sql_fetch($prdt_sql);


	// 특별처리상품 플래그 (초기상품중 종료일이 5일 이전일때 이전회차와 최종상환회차를 동일회차로 처리한 상품 구분 : 110,115,126,127,149,151,157)
	$exceptionProduct = ($PRDT['idx'] <= 172  && substr($PRDT['loan_end_date'],-2) <= '05') ? 1 : 0;
	$shortTermProduct = ($PRDT['invest_days']>0) ? 1 : 0;

	// 대출자 정보
	if($PRDT['loan_mb_no']) {
		$loaner_sql = "SELECT mb_no, mb_id, member_type, mb_name, mb_co_name, mb_hp, account_num FROM g5_member WHERE mb_no='".$PRDT['loan_mb_no']."'";
		$LOANER = sql_fetch($loaner_sql);
		$LOANER['mb_hp']       = masterDecrypt($LOANER['mb_hp'], false);
		$LOANER['account_num'] = masterDecrypt($LOANER['account_num'], false);
	}


	// 상환대상일수
	$total_invest_days = repayDayCount($PRDT['loan_start_date'], $PRDT['loan_end_date']);

	// 전체상환차수
	$total_repay_turn  = repayTurnCount($PRDT['loan_start_date'], $PRDT['loan_end_date'], $exceptionProduct, $shortTermProduct);

	$bill_table = getBillTable($product_idx);		// 정산내역 기록 테이블 (** 테이블명에 상품번호대역(100단위) 붙음 **)

  $INI['bill_table']          = $bill_table;
	$INI['static_repay_day']    = 5;
	$INI['loan_start_date']     = $PRDT['loan_start_date'];
  $INI['loan_end_date']       = $PRDT['loan_end_date'];
  $INI['total_day_count']     = $total_invest_days;
  $INI['repay_turn']          = $PRDT['total_repay_turn'] = $total_repay_turn;
  $INI['invest_count']        = $PRDT['invest_count'];


	$sql_add = ($invest_member_idx) ? "AND member_idx='$invest_member_idx'" : "";

	/////////////////////////////////////////////////////////////////////////////
	// 투자자 및 투자정보 배열화
	/////////////////////////////////////////////////////////////////////////////
	$sql = "
		SELECT
			*
		FROM
			cf_product_invest
		WHERE 1
			AND product_idx='".$product_idx."'
			AND invest_state IN('Y','R')
			$sql_add
		ORDER BY
			idx DESC";
	//if($office_connect) print_rr($sql, "font-size:12px");
	$result = sql_query($sql);
	$invest_count = $result->num_rows;

	for($i=0; $i<$invest_count; $i++) {
		$r = sql_fetch_array($result);

		$sql2 = "
			SELECT
				mb_no, mb_id, member_type, member_investor_type, is_creditor,
				mb_name, mb_co_name, mb_co_reg_num,
				is_owner_operator, receive_method, remit_fee,
				bank_code, account_num, bank_private_name, bank_private_name_sub,
				va_bank_code2, virtual_account2, va_private_name2,
				insidebank_after_trans_target
			FROM
				g5_member
			WHERE
				mb_no = '".$r['member_idx']."'";
		//if($office_connect) print_rr($sql2, "font-size:12px");
		$r2 = sql_fetch($sql2);

		$r['mb_no']                 = $r2['mb_no'];
		$r['mb_id']                 = $r2['mb_id'];
		$r['member_type']           = $r2['member_type'];
		$r['member_investor_type']  = $r2['member_investor_type'];
		$r['is_creditor']           = $r2['is_creditor'];
		$r['mb_name']               = $r2['mb_name'];
		$r['mb_co_name']            = $r2['mb_co_name'];
		$r['mb_co_reg_num']         = $r2['mb_co_reg_num'];
		$r['is_owner_operator']     = $r2['is_owner_operator'];
		$r['receive_method']        = $r2['receive_method'];
		$r['remit_fee']             = $r2['remit_fee'];
		$r['bank_code']             = $r2['bank_code'];
		$r['account_num']           = $r2['account_num'];
		$r['bank_private_name']     = $r2['bank_private_name'];
		$r['bank_private_name_sub'] = $r2['bank_private_name_sub'];
		$r['va_bank_code2']         = $r2['va_bank_code2'];
		$r['virtual_account2']      = $r2['virtual_account2'];
		$r['va_private_name2']      = $r2['va_private_name2'];
		$r['insidebank_after_trans_target'] = $r2['insidebank_after_trans_target'];
		$r['account_num']           = masterDecrypt($r['account_num'], false);

		$INVEST[$i] = $r;


		$sql2 = "
			SELECT
				member_idx,
				COUNT(idx) AS cnt_idx,
				IFNULL(SUM(amount),0) AS sum_amount
			FROM
				cf_product_invest
			WHERE 1=1
				AND member_idx='".$r['member_idx']."'
				AND invest_state IN('Y','R')";
		//if($office_connect) print_rr($sql2, "font-size:12px");
		$r2 = sql_fetch($sql2);

		/*
		$MTOTAL_INVEST_SUM[$r2['member_idx']] = array(
			'count'=>$r2['cnt_idx'],
			'amount'=>$r2['sum_amount']
		);
		*/

	}
	sql_free_result($result);
	unset($r); unset($r2);
	/////////////////////////////////////////////////////////////////////////////



	/////////////////////////////////////////////////////////////////////////////
	// 대표적인 투자자 1인의 데이터로 기준자료(상환회차, 시작일, 종료일, 일수) 를 뽑는다.
	// - 연체회차데이터 제외
	// - 부분상환데이터 제외
	/////////////////////////////////////////////////////////////////////////////
	$sql = "
		SELECT
			MIN(bill_date) AS sdate,
			MAX(bill_date) AS edate,
			COUNT(dno) AS day_count,
			turn
		FROM
			".$INI['bill_table']."
		WHERE 1
			AND product_idx = '".$product_idx."'
			AND member_idx = '".$INVEST[0]['member_idx']."'
			AND is_overdue = 'N'
		GROUP BY
			turn
		ORDER BY
			turn ASC";
	//if($office_connect) print_rr($sql, "font-size:12px");
	$res = sql_query($sql);
	$turn_count = $res->num_rows;

	for($i=0,$j=1; $i<$turn_count; $i++,$j++) {
		$RX = sql_fetch_array($res);
		$REPAY[$i]['repay_num']    = $RX['turn'];

		// 종료회차가 아닌데 정산일이 종료일자보다 큰 회차의 정산일은 대출종료일로 강제 변경
		$REPAY[$i]['repay_date'] = ($RX['turn'] < $INI['repay_turn']) ? date("Y-m", strtotime("first day of " . $RX['edate'] . " +1 month"))."-".sprintf("%02d", $INI['static_repay_day']) : $INI['loan_end_date'];
		if($REPAY[$i]['repay_date'] > $INI['loan_end_date']) $REPAY[$i]['repay_date'] =  $INI['loan_end_date'];


		$REPAY[$i]['target_sdate'] = $RX['sdate'];
		$REPAY[$i]['target_edate'] = $RX['edate'];
		$REPAY[$i]['day_count']    = $RX['day_count'];
		$REPAY[$i]['turn']         = $RX['turn'];
		$REPAY[$i]['turn_sno']     = '0';
		$REPAY[$i]['repay_schedule_date'] = $REPAY[$i]['repay_date'];


		$succ_sql = "
			SELECT
				idx,
				loan_interest_state, loan_principal_state, ib_request_ready,
				invest_give_state, invest_principal_give,
				overdue_receive, overdue_ib_request_ready, overdue_give, overdue_start_date, overdue_end_date,
				`date`
			FROM
				cf_product_success
			WHERE 1
				AND product_idx = '".$product_idx."'
				AND turn = '".$RX['turn']."'
				AND turn_sno = '0'";
		//if($office_connect) print_rr($ssql, "font-size:12px");
		$SROW = sql_fetch($succ_sql);

		$REPAY[$i]['SUCCESS'] = $SROW;
		//if($office_connect) print_rr($REPAY[$i]['SUCCESS'], "font-size:12px");
		if($REPAY[$i]['SUCCESS']['overdue_start_date'] && $REPAY[$i]['SUCCESS']['overdue_start_date'] > '0000-00-00') {
			$REPAY[$i]['SUCCESS']['overdue_finished'] = ($REPAY[$i]['SUCCESS']['overdue_end_date'] && $REPAY[$i]['SUCCESS']['overdue_end_date']=='0000-00-00') ? 'Y' : 'N';
		}

		$REPAY[$i]['LIST'] = array();

	}
	sql_free_result($res);

	/////////////////////////////////////////////////////////////////////////////


	/////////////////////////////////////
	// 정상/일부/연체 지급 합계 배열화
	/////////////////////////////////////

	// 전체 정기상환 정산합계
	$REPAY_SUM = array(
		'repay_principal' => 0,
		'invest_interest' => 0,
		'TAX'             => array('interest_tax' => 0, 'local_tax' => 0, 'sum' => 0),
		'invest_usefee'   => 0,
		'withhold'        => 0,
		'interest'        => 0
	);

	// 전체 정기상환 지급합계
	$PAIED_SUM = array(
		'repay_principal' => 0,
		'invest_interest' => 0,
		'TAX'             => array('interest_tax' => 0, 'local_tax' => 0, 'sum' => 0),
		'invest_usefee'   => 0,
		'withhold'        => 0,
		'interest'        => 0,
		'last_turn'       => ''			// 정기상환 기지급 최종회차
	);


	// 부분상환 정산합계
	$PTL_REPAY_SUM = array(
		'partial_principal'=> 0,
		'repay_principal'  => 0,
		'remain_principal' => 0,
		'invest_interest'  => 0,
		'TAX'              => array('interest_tax' => 0, 'local_tax' => 0, 'sum' => 0),
		'invest_usefee'    => 0,
		'withhold'         => 0,
		'interest'         => 0
	);

	// 부분상환 지급합계
	$PTL_PAIED_SUM = array(
		'partial_principal'=> 0,
		'repay_principal'  => 0,
		'remain_principal' => 0,
		'invest_interest'  => 0,
		'TAX'              => array('interest_tax' => 0, 'local_tax' => 0, 'sum' => 0),
		'invest_usefee'    => 0,
		'withhold'         => 0,
		'interest'         => 0
	);

	// 연체상환 정산합계
	$OVD_REPAY_SUM = array(
		'repay_principal' => 0,
		'invest_interest' => 0,
		'TAX'             => array('interest_tax' => 0, 'local_tax' => 0, 'sum' => 0),
		'invest_usefee'   => 0,
		'withhold'        => 0,
		'interest'        => 0
	);

	// 연체상환 지급합계
	$OVD_PAIED_SUM = array(
		'repay_principal' => 0,
		'invest_interest' => 0,
		'TAX'             => array('interest_tax' => 0, 'local_tax' => 0, 'sum' => 0),
		'invest_usefee'   => 0,
		'withhold'        => 0,
		'interest'        => 0
	);

	// 전체상환 정산합계
	$TOTAL_REPAY_SUM = array(
		'repay_principal' => 0,
		'invest_interest' => 0,
		'TAX'             => array('interest_tax' => 0, 'local_tax' => 0, 'sum' => 0),
		'invest_usefee'   => 0,
		'withhold'        => 0,
		'interest'        => 0
	);

	// 전체상환 정산합계
	$TOTAL_PAIED_SUM = array(
		'repay_principal' => 0,
		'invest_interest' => 0,
		'TAX'             => array('interest_tax' => 0, 'local_tax' => 0, 'sum' => 0),
		'invest_usefee'   => 0,
		'withhold'        => 0,
		'interest'        => 0
	);


	/////////////////////////////////////////////////////////////////////////////
	// $REPAY[$i]['LIST'] 배열 넣기
	/////////////////////////////////////////////////////////////////////////////
	for($i=0,$turn=1; $i<$turn_count; $i++,$turn++) {

		for($j=0; $j<$invest_count; $j++) {

			//--------------------------------------------------------------------------------------------
			// 이자정산지급일에 따른 세율 변환
			// 세율조정 발생시기를 단정할 수 없으므로 조건시을 대입한다.
			// 2021-08-21 온투법 승인일
			// 2021-10-21 헬로핀테크 헬로크라우드대부 합병일
			// 법인은 무조건 27.5%, 개인은 정산일 기준 다르게 적용
			//--------------------------------------------------------------------------------------------
			if($PRDT['loan_start_date'] >= '2021-08-27') {
				$interest_tax_ratio = ($INVEST[$j]['member_type']=='2') ? 0.25 : 0.14;
			}
			else {
				if( $REPAY[$i]['repay_schedule_date'] < '2021-10-21' ) {

					$interest_tax_ratio = ($INVEST[$j]['member_type']=='2') ? 0.25 : 0.25;

					// 0.14로 정산된것 상품회차 예외처리
					if( $product_idx == '6281' && $turn >= 3) {
						if($INVEST[$j]['member_type']=='1') $interest_tax_ratio = 0.14;
					}

					// 0.14로 정산된 상품 예외처리
					if( in_array($product_idx, array('6561','6573','6584','6596','6607')) ) {
						if($INVEST[$j]['member_type']=='1') $interest_tax_ratio = 0.14;
					}

				}
				else {
					$interest_tax_ratio = ($INVEST[$j]['member_type']=='2') ? 0.25 : 0.14;
				}
			}

			$local_tax_ratio = 0.1;		// 소득세: interest_tax_ratio의 10%
			//--------------------------------------------------------------------------------------------


			$sql = "
				SELECT
					turn, turn_sno, invest_idx, member_idx, invest_amount, partial_principal, remain_principal,
					IFNULL(SUM(day_interest),0) AS invest_interest,
					IFNULL(SUM(fee),0) AS fee,
					( SELECT IFNULL(MIN(remain_principal), 0) FROM {$INI['bill_table']} WHERE invest_idx='".$INVEST[$j]['idx']."' ) AS min_remain_amount
				FROM
					{$INI['bill_table']}
				WHERE 1
					AND invest_idx = '".$INVEST[$j]['idx']."'
					AND turn = '".$REPAY[$i]['turn']."'
					AND is_overdue = 'N'
				ORDER BY
					idx DESC
				LIMIT 1";
			//if($office_connect) print_rr($sql,'font-size:11px');
			$row = sql_fetch($sql);

			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// "광주광역시 우산동 주상복합 신축" 상품 매각으로 인한 원금만 상환처리 (마지막회차 정상 이자 및 연체이자 무시)
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if( ($product_idx=='6830' && $turn=='4') || ($product_idx=='6853' && $turn=='4') || ($product_idx=='6854' && $turn=='4') ) {
				$row['invest_interest'] = $row['fee'] = 0;
			}
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			$row['invest_interest'] = floor(customRoundOff($row['invest_interest']));											// 원단위미만 절사이나 무한소수 .9999...... 일때는 반올림 처리한다.
			$row['fee']             = floor(customRoundOff($row['fee']));																	// 원단위미만 절사이나 무한소수 .9999...... 일때는 반올림 처리한다.
			$row['interest_tax']    = floor( ($row['invest_interest'] * $interest_tax_ratio) / 10 ) * 10;		// 당월 이자소득세 = 이자수익 * 0.25
			$row['local_tax']       = floor( ($row['interest_tax'] * $local_tax_ratio) / 10 ) * 10;					// 당월 지방소득세(원단위 절사)


			$REPAY[$i]['LIST'][$j]['invest_idx']  = $INVEST[$j]['idx'];
			$REPAY[$i]['LIST'][$j]['prin_rcv_no'] = $INVEST[$j]['prin_rcv_no'];
			$REPAY[$i]['LIST'][$j]['mb_no']       = $REPAY[$i]['LIST'][$j]['member_idx'] = $row['member_idx'];
			$REPAY[$i]['LIST'][$j]['mb_id']       = $INVEST[$j]['mb_id'];
			$REPAY[$i]['LIST'][$j]['mb_name']     = ($INVEST[$j]['member_type']=='2') ? $INVEST[$j]['mb_co_name'] : $INVEST[$j]['mb_name'];
			$REPAY[$i]['LIST'][$j]['jumin']       = ($INVEST[$j]['member_type']=='2') ? $INVEST[$j]['mb_co_reg_num'] : getJumin($INVEST[$j]['member_idx']);

			$REPAY[$i]['LIST'][$j]['member_type']          = $INVEST[$j]['member_type'];
			$REPAY[$i]['LIST'][$j]['member_investor_type'] = $INVEST[$j]['member_investor_type'];
			$REPAY[$i]['LIST'][$j]['is_creditor']          = $INVEST[$j]['is_creditor'];
			$REPAY[$i]['LIST'][$j]['is_owner_operator']    = $INVEST[$j]['is_owner_operator'];

			$REPAY[$i]['LIST'][$j]['insidebank_after_trans_target'] = $INVEST[$j]['insidebank_after_trans_target'];		//신한 예치금 이전 대상자 플래그

			$REPAY[$i]['LIST'][$j]['invest_amount']     = $row['invest_amount'];					// 최초투자원금
			$REPAY[$i]['LIST'][$j]['partial_principal'] = $row['partial_principal'];			// 상환원금누적액
			$REPAY[$i]['LIST'][$j]['remain_principal']  = $row['remain_principal'];			// 잔여원금

			// 지급예정원금 처리 : 2020-09-17 수정
			//$REPAY[$i]['LIST'][$j]['repay_principal']  = ($REPAY[$i]['turn'] < $INI['repay_turn']) ? 0 : $row['remain_principal'];			// 상환원금
			if($REPAY[$i]['turn'] < $INI['repay_turn']) {			//-- 일반회차 지급예정원금
				$REPAY[$i]['LIST'][$j]['repay_principal'] = 0;
			}
			else {																						//-- 최종회차 지급예정원금
				$REPAY[$i]['LIST'][$j]['repay_principal'] = $row['min_remain_amount'];

				// ▼ 부분원금상환 지급금액 오류가 발생한 상품의 최종 지급액에서 가감 처리 --------------------------------
				if($product_idx=='1782') {

					// 실지급된 원금 확인 (부분상환액 합계)
					$tsql = "SELECT ( ".$INVEST[$j]['amount']." - IFNULL(SUM(principal),0) ) AS real_remain_principal FROM cf_product_give WHERE invest_idx='".$INVEST[$j]['idx']."' AND turn_sno > 0 AND banking_date IS NOT NULL";
					$LAST_CHECK = sql_fetch($tsql);

					$diff_amount = $LAST_CHECK['real_remain_principal'] - $REPAY[$i]['LIST'][$j]['repay_principal'];

					if($diff_amount <> 0) {
						$REPAY[$i]['LIST'][$j]['calc_repay_principal'] = $REPAY[$i]['LIST'][$j]['repay_principal'];
						$REPAY[$i]['LIST'][$j]['revision_principal'] = $diff_amount;		// 차액 = 상환된원금을 제외한 나머지 금액 - 정산상 남은 원금
						$REPAY[$i]['LIST'][$j]['repay_principal'] = $LAST_CHECK['real_remain_principal'];
					}

				}
				// ▲ 부분원금상환 지급금액 오류가 발생한 상품의 최종 지급액에서 가감 처리 --------------------------------

			}


			$REPAY[$i]['LIST'][$j]['invest_interest']     = $row['invest_interest'];				// 세전이자
			$REPAY[$i]['LIST'][$j]['invest_usefee']       = $row['fee'];										// 플랫폼이용료

			/* 원천징수 제외 대상자의 원천징수액을 알고싶으면 주석해제
			if($INVEST[$j]['member_type']=='2' || $INVEST[$j]['is_creditor']=='Y') {
				$REPAY[$i]['LIST'][$j]['OTAX']['interest_tax'] = $row['interest_tax'];												// 이자소득세
				$REPAY[$i]['LIST'][$j]['OTAX']['local_tax']    = $row['local_tax'];														// 지방소득세
				$REPAY[$i]['LIST'][$j]['OTAX']['sum']          = $row['interest_tax'] + $row['local_tax'];		// 소득세합계
			}
			*/

			// 원천징수 제외
			if($INVEST[$j]['is_creditor']=='Y') {
				// 대부업 회원
				$row['interest_tax'] = 0;
				$row['local_tax']    = 0;
			}
			else {
				// 법인 이자소득세 1000원 미만인 경우 (소액부징수)
				if($INVEST[$j]['member_type']=='2') {
					if($row['interest_tax'] < 1000 && $REPAY[$i]['repay_date'] > '2021-11-19') {
						$row['interest_tax'] = 0;
						$row['local_tax']    = 0;
					}
				}
			}

			$REPAY[$i]['LIST'][$j]['TAX']['interest_tax'] = $row['interest_tax'];												// 이자소득세 (부징수 적용)
			$REPAY[$i]['LIST'][$j]['TAX']['local_tax']    = $row['local_tax'];													// 지방소득세 (부징수 적용)
			$REPAY[$i]['LIST'][$j]['TAX']['sum']          = $row['interest_tax'] + $row['local_tax'];		// 소득세합계 (부징수 적용)

			$REPAY[$i]['LIST'][$j]['withhold'] = $row['fee'] + $row['interest_tax'] + $row['local_tax'];						// 차감합계 (플랫폼수수료 + 소득세)
			$REPAY[$i]['LIST'][$j]['interest'] = $row['invest_interest'] - $REPAY[$i]['LIST'][$j]['withhold'];			// 최종지급될 이자

			/////////////////////////////////////////////////////
			// 인사이드뱅크 회수금(상환금) 배분 처리내역 설정
			/////////////////////////////////////////////////////
			if( $PRDT['ib_trust']=='Y' ) {
				$REPAY[$i]['LIST'][$j]['ib_regist'] = $INVEST[$j]['ib_regist'];

				$ib_sql = "
					SELECT
						TR_AMT, CTAX_AMT, FEE, JI_DATE, JI_TIME, RESP_CODE
					FROM
						IB_FB_P2P_REPAY_REQ_DETAIL
					WHERE 1
						AND invest_idx = '".$INVEST[$j]['idx']."'
						AND turn = '".$REPAY[$i]['turn']."'
						AND turn_sno = '0'
						AND is_overdue = 'N'
					ORDER BY
						rdate DESC
					LIMIT 1";
				//if($office_connect) print_rr($ib_sql, "font-size:12px");
				$IB = sql_fetch($ib_sql);

				$REPAY[$i]['LIST'][$j]['ib_withdraw'] = $IB['RESP_CODE'];		// 회수금 배분처리 성공 플래그
				if($IB['RESP_CODE']=='00000000') {
					$REPAY[$i]['LIST'][$j]['ib_withdraw_datetime'] = date('Y-m-d H:i', strtotime($IB['JI_DATE'].$IB['JI_TIME']));		// 회수금 배분처리일시
				}
			}

			///////////////////////////////////
			// 지급기록 추출 및 지급계좌 설정
			///////////////////////////////////
			$give_sql = "
				SELECT
					idx, `date`,
					invest_amount, interest, principal, interest_tax, local_tax, fee,
					remit_fee, receive_method,
					bank_name, account_num, bank_private_name,
					banking_date, mgtKey,
					mb_type, investor_type, is_creditor
				FROM
					cf_product_give
				WHERE 1
					AND invest_idx = '".$INVEST[$j]['idx']."'
					AND turn = '".$REPAY[$i]['turn']."'
					AND turn_sno = '0'
					AND is_overdue = 'N'
					AND banking_date IS NOT NULL";
			//if($office_connect) print_rr($give_sql, "font-size:12px");
			$GIVE = sql_fetch($give_sql);

			if($GIVE['idx']) {

				$REPAY[$i]['LIST'][$j]['paied']             = 'Y';
				$REPAY[$i]['LIST'][$j]['give_idx']          = $GIVE['idx'];
				$REPAY[$i]['LIST'][$j]['paied_date']        = $GIVE['date'];
				$REPAY[$i]['LIST'][$j]['paied_amount']      = $GIVE['interest'];		// 이자 실입금액
				$REPAY[$i]['LIST'][$j]['remit_fee']         = ($GIVE['remit_fee']=='1') ? $GIVE['remit_fee'] : $INVEST[$j]['remit_fee'];
				$REPAY[$i]['LIST'][$j]['mgtKey']            = $GIVE['mgtKey'];
				$REPAY[$i]['LIST'][$j]['is_creditor']       = $GIVE['is_creditor'];
				$REPAY[$i]['LIST'][$j]['receive_method']    = $GIVE['receive_method'];
				$REPAY[$i]['LIST'][$j]['bank']			        = $GIVE['bank_name'];
				$REPAY[$i]['LIST'][$j]['bank_code']			    = "";		// 이미 지급됬는데 별 필요없을듯
				$REPAY[$i]['LIST'][$j]['account_num']       = $GIVE['account_num'];
				$REPAY[$i]['LIST'][$j]['bank_private_name'] = $GIVE['bank_private_name'];
				$REPAY[$i]['LIST'][$j]['banking_date']      = $GIVE['banking_date'];

				$REPAY[$i]['LIST'][$j]['paied_fee']         = $GIVE['fee'];
				$REPAY[$i]['LIST'][$j]['paied_interest_tax']= $GIVE['interest_tax'];
				$REPAY[$i]['LIST'][$j]['paied_local_tax']   = $GIVE['local_tax'];


				////////////////////////
				// 정기상환 지급합계
				////////////////////////
				$PAIED_SUM['repay_principal']           += $GIVE['principal'];
				$PAIED_SUM['invest_interest']           += ($GIVE['interest'] + $GIVE['interest_tax'] + $GIVE['local_tax'] +  $GIVE['fee']);		// 지급기록으로 세전이자 추출
				$PAIED_SUM['TAX']['interest_tax']       += $GIVE['interest_tax'];
				$PAIED_SUM['TAX']['local_tax']          += $GIVE['local_tax'];
				$PAIED_SUM['TAX']['sum']                += ($GIVE['interest_tax'] + $GIVE['local_tax']);
				$PAIED_SUM['invest_usefee']             += $GIVE['fee'];
				$PAIED_SUM['withhold']                  += ($GIVE['interest_tax'] + $GIVE['local_tax'] + $GIVE['fee']);
				$PAIED_SUM['interest']                  += $GIVE['interest'];
				$PAIED_SUM['last_turn']                 = $REPAY[$i]['turn'];

				$TOTAL_PAIED_SUM['repay_principal']     += $GIVE['principal'];
				$TOTAL_PAIED_SUM['invest_interest']     += ($GIVE['interest'] + $GIVE['interest_tax'] + $GIVE['local_tax'] +  $GIVE['fee']);		// 지급기록으로 세전이자 추출
				$TOTAL_PAIED_SUM['TAX']['interest_tax'] += $GIVE['interest_tax'];
				$TOTAL_PAIED_SUM['TAX']['local_tax']    += $GIVE['local_tax'];
				$TOTAL_PAIED_SUM['TAX']['sum']          += ($GIVE['interest_tax'] + $GIVE['local_tax']);
				$TOTAL_PAIED_SUM['invest_usefee']       += $GIVE['fee'];
				$TOTAL_PAIED_SUM['withhold']            += ($GIVE['interest_tax'] + $GIVE['local_tax'] + $GIVE['fee']);
				$TOTAL_PAIED_SUM['interest']            += $GIVE['interest'];

			}
			else {

				$REPAY[$i]['LIST'][$j]['paied']          = 'N';
				$REPAY[$i]['LIST'][$j]['give_idx']       = '';
				$REPAY[$i]['LIST'][$j]['paied_date']     = '';
				$REPAY[$i]['LIST'][$j]['paied_amount']   = '';
				$REPAY[$i]['LIST'][$j]['remit_fee']      = $INVEST[$j]['remit_fee'];
				$REPAY[$i]['LIST'][$j]['mgtKey']         = '';
				$REPAY[$i]['LIST'][$j]['is_creditor']    = $INVEST[$j]['is_creditor'];
				$REPAY[$i]['LIST'][$j]['receive_method'] = $INVEST[$j]['receive_method'];
				if($INVEST[$j]['receive_method']=='1') {
					$REPAY[$i]['LIST'][$j]['bank']              = $BANK[$INVEST[$j]['bank_code']];
					$REPAY[$i]['LIST'][$j]['bank_code']         = $INVEST[$j]['bank_code'];
					$REPAY[$i]['LIST'][$j]['account_num']       = $INVEST[$j]['account_num'];
					$REPAY[$i]['LIST'][$j]['bank_private_name'] = $INVEST[$j]['bank_private_name'];
					$REPAY[$i]['LIST'][$j]['bank_private_name'].= ($INVEST[$j]['bank_private_name_sub']) ? "(".$INVEST[$j]['bank_private_name_sub'].")" : "";
				}
				else {		// 예치금환급 선택회원은 제3자 예치시스템적용상품 여부와 상관없이 무조건 신한가상계좌로 입금받도록 수정 : 2018-04-05
					$REPAY[$i]['LIST'][$j]['bank']              = $BANK[$INVEST[$j]['va_bank_code2']];
					$REPAY[$i]['LIST'][$j]['bank_code']         = $INVEST[$j]['va_bank_code2'];
					$REPAY[$i]['LIST'][$j]['account_num']       = $INVEST[$j]['virtual_account2'];
					$REPAY[$i]['LIST'][$j]['bank_private_name'] = $INVEST[$j]['va_private_name2'];
				}
				$REPAY[$i]['LIST'][$j]['banking_date']        = "";

			}


			/////////////////////////
			//회차별(당월) 합산
			/////////////////////////
			$REPAY[$i]['SUM']['caption'] = $REPAY[$i]['turn'] . "회차 합계";
			$REPAY[$i]['SUM']['amount']              += $REPAY[$i]['LIST'][$j]['remain_principal'];
			$REPAY[$i]['SUM']['invest_interest']     += $REPAY[$i]['LIST'][$j]['invest_interest'];
			$REPAY[$i]['SUM']['invest_usefee']       += $REPAY[$i]['LIST'][$j]['invest_usefee'];
			$REPAY[$i]['SUM']['TAX']['interest_tax'] += $REPAY[$i]['LIST'][$j]['TAX']['interest_tax'];
			$REPAY[$i]['SUM']['TAX']['local_tax']    += $REPAY[$i]['LIST'][$j]['TAX']['local_tax'];
			$REPAY[$i]['SUM']['TAX']['sum']          += $REPAY[$i]['LIST'][$j]['TAX']['sum'];
			$REPAY[$i]['SUM']['withhold']            += $REPAY[$i]['LIST'][$j]['withhold'];
			$REPAY[$i]['SUM']['interest']            += $REPAY[$i]['LIST'][$j]['interest'];
			$REPAY[$i]['SUM']['repay_principal']     += $REPAY[$i]['LIST'][$j]['repay_principal'];

			unset($row);

		}		// 투자자 루프 끝


		//unset($TMP);  //배열 비움


		///////////////////
		// 전체합계
		///////////////////
		$REPAY_SUM['invest_interest']     += $REPAY[$i]['SUM']['invest_interest'];
		$REPAY_SUM['invest_usefee']       += $REPAY[$i]['SUM']['invest_usefee'];
		$REPAY_SUM['TAX']['interest_tax'] += $REPAY[$i]['SUM']['TAX']['interest_tax'];
		$REPAY_SUM['TAX']['local_tax']    += $REPAY[$i]['SUM']['TAX']['local_tax'];
		$REPAY_SUM['TAX']['sum']          += $REPAY[$i]['SUM']['TAX']['sum'];
		$REPAY_SUM['withhold']            += $REPAY[$i]['SUM']['withhold'];
		$REPAY_SUM['interest']            += $REPAY[$i]['SUM']['interest'];
		$REPAY_SUM['repay_principal']     += $REPAY[$i]['SUM']['repay_principal'];


		if($i==0) {
			//if($office_connect) print_rr($REPAY[$i],'font-size:11px;line-height:12px');
		}

		/////////////////////////////////////////////////////////////////////////
		// 부분상환내역 배열화
		/////////////////////////////////////////////////////////////////////////
		$ptl_sql = "
			SELECT
				turn, turn_sno, amount, account_day
			FROM
				cf_partial_redemption
			WHERE 1
				AND product_idx = '".$product_idx."'
				AND turn = '".$REPAY[$i]['turn']."'
				AND turn_sno > '0'
			ORDER BY
				turn_sno ASC";
		if($REPAY[$i]['turn']=='3') {
			//if($office_connect) print_rr($REPAY[$i]['turn'] . "회차 부분상환내역" . $ptl_sql, "font-size:12px");
		}
		$ptl_res  = sql_query($ptl_sql);
		$ptl_rows = $ptl_res->num_rows;
		if($ptl_rows) {

			for($k=0; $k<$ptl_rows; $k++) {

				$PARTIAL = sql_fetch_array($ptl_res);

				if($PARTIAL['turn_sno']) {

					$REPAY[$i]['PARTIAL'][$k]['turn_sno']   = $PARTIAL['turn_sno'];
					$REPAY[$i]['PARTIAL'][$k]['amount']     = $PARTIAL['amount'];
					$REPAY[$i]['PARTIAL'][$k]['account_day']= $PARTIAL['account_day'];

					$succ_sql = "
						SELECT
							idx, loan_principal_state, ib_request_ready, invest_principal_give, `date`
						FROM
							cf_product_success
						WHERE 1
							AND product_idx = '".$product_idx."'
							AND turn = '".$REPAY[$i]['turn']."'
							AND turn_sno = '".$PARTIAL['turn_sno']."'";
					//if($office_connect) print_rr($succ_sql,'font-size:12px');
					$SROWX = sql_fetch($succ_sql);
					$REPAY[$i]['PARTIAL'][$k]['SUCCESS']   = $SROWX;

					for($j=0; $j<$invest_count; $j++) {

						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j] = array();
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['invest_idx']        = $INVEST[$j]['idx'];
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['prin_rcv_no']       = $INVEST[$j]['prin_rcv_no'];
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['mb_no']             = $REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['member_idx'] = $INVEST[$j]['member_idx'];
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['mb_id']             = $INVEST[$j]['mb_id'];
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['mb_name']           = ($INVEST[$j]['member_type']=='2') ? $INVEST[$j]['mb_co_name'] : $INVEST[$j]['mb_name'];
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['jumin']             = ($INVEST[$j]['member_type']=='2') ? $INVEST[$j]['mb_co_reg_num'] : getJumin($INVEST[$j]['member_idx']);
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['member_type']       = $INVEST[$j]['member_type'];
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['is_owner_operator'] = $INVEST[$j]['is_owner_operator'];

						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['invest_amount']     = $INVEST[$j]['amount'];

						$ptl_sql2 = "
							SELECT
								repay_date, invest_importance, partial_principal, remain_principal,
								(
									SELECT
										COUNT(idx)
									FROM
										".$INI['bill_table']."
									WHERE 1
										AND invest_idx = '".$INVEST[$j]['idx']."'
										AND turn = '".$REPAY[$i]['turn']."'
										AND turn_sno = '".$PARTIAL['turn_sno']."'
										AND is_overdue = 'N'
								) AS dayCount
							FROM
								".$INI['bill_table']."
							WHERE 1
								AND invest_idx = '".$INVEST[$j]['idx']."'
								AND turn = '".$REPAY[$i]['turn']."'
								AND turn_sno = '".$PARTIAL['turn_sno']."'
								AND is_overdue = 'N'
							ORDER BY
								idx DESC
							LIMIT 1";
						$PTLDETAIL = sql_fetch($ptl_sql2);

						if( !$PTLDETAIL['invest_importance'] ) {

							// 첫날부터 장난질(부분상환) 발생시
							$ptl_sql2 = "
								SELECT
									repay_date, invest_importance, partial_principal, remain_principal,
									(
										SELECT
											COUNT(idx)
										FROM
											".$INI['bill_table']."
										WHERE 1
											AND invest_idx = '".$INVEST[$j]['idx']."'
											AND turn = '".$REPAY[$i]['turn']."'
											AND turn_sno = '0'
											AND is_overdue = 'N'
									) AS dayCount
								FROM
									".$INI['bill_table']."
								WHERE 1
									AND invest_idx = '".$INVEST[$j]['idx']."'
									AND turn = '".$REPAY[$i]['turn']."'
									AND turn_sno = '0'
									AND is_overdue = 'N'
								ORDER BY
									idx DESC
								LIMIT 1";

							$PTLDETAIL = sql_fetch($ptl_sql2);

						}
						//if($office_connect) print_rr($ptl_sql2,'font-size:12px');



						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['invest_importance'] = $PTLDETAIL['invest_importance'];		// 총투자금대비 본인투자비중
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['partial_principal'] = $PTLDETAIL['partial_principal'];		// 상환원금누적액
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['remain_principal']  = $PTLDETAIL['remain_principal'];		// 원금잔액
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['repay_principal']   = floor($PARTIAL['amount'] * ($PTLDETAIL['invest_importance'] / 100));
						//if($office_connect) echo $PARTIAL['amount']." * (".$PTLDETAIL['invest_importance']." / 100)<br>\n";

						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['invest_interest']     = 0;		// 세전이자
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['invest_usefee']       = 0;		// 플랫폼이용료
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['TAX']['interest_tax'] = 0;		// 이자소득세
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['TAX']['local_tax']    = 0;		// 지방소득세
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['TAX']['sum']          = 0;		// 소득세합계
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['withhold']            = 0;		// 원천징수액 (플랫폼수수료 + 소득세)
						$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['interest']            = 0;		// 최종지급될 이자


						/////////////////////////////////////////////////////
						// 인사이드뱅크 회수금(상환금) 배분 처리내역 설정
						/////////////////////////////////////////////////////
						if( $PRDT['ib_trust']=='Y' ) {
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['ib_regist'] = $INVEST[$j]['ib_regist'];

							$ib_sql = "
								SELECT
									TR_AMT_P, CTAX_AMT, FEE, JI_DATE, JI_TIME, RESP_CODE
								FROM
									IB_FB_P2P_REPAY_REQ_DETAIL
								WHERE 1
									AND invest_idx = '".$INVEST[$j]['idx']."'
									AND turn = '".$REPAY[$i]['turn']."'
									AND turn_sno = '".$PARTIAL['turn_sno']."'
									AND is_overdue = 'N'
								ORDER BY
									rdate DESC
								LIMIT 1";
							//if($office_connect) print_rr($ib_sql, "font-size:12px");
							$IB = sql_fetch($ib_sql);

							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['ib_withdraw'] = $IB['RESP_CODE'];		// 회수금 배분처리 성공 플래그
							if($IB['RESP_CODE']=='00000000') {
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['ib_withdraw_datetime'] = date('Y-m-d H:i', strtotime($IB['JI_DATE'].$IB['JI_TIME']));		// 회수금 배분처리일시
							}
						}

						///////////////////////////////////
						// 부분상환 지급기록 추출 및 지급계좌 설정
						///////////////////////////////////
						$ptl_give_sql = "
							SELECT
								idx, `date`, principal,
								is_creditor, remit_fee, receive_method,
								bank_name, account_num, bank_private_name,
								banking_date, mgtKey,
								mb_type, investor_type, is_creditor
							FROM
								cf_product_give
							WHERE 1
								AND invest_idx = '".$INVEST[$j]['idx']."'
								AND turn = '".$REPAY[$i]['turn']."'
								AND turn_sno = '".$PARTIAL['turn_sno']."'
								AND is_overdue = 'N'
								AND banking_date IS NOT NULL";
						$PTL_GIVE = sql_fetch($ptl_give_sql);

						if($PTL_GIVE['idx']) {

							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['paied']             = 'Y';
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['give_idx']          = $PTL_GIVE['idx'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['paied_date']        = $PTL_GIVE['date'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['paied_amount']      = $PTL_GIVE['principal'];		// 부분상환원금
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['remit_fee']         = ($PTL_GIVE['remit_fee']=='1') ? $PTL_GIVE['remit_fee'] : $INVEST[$j]['remit_fee'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['mgtKey']            = $PTL_GIVE['mgtKey'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['is_creditor']       = $PTL_GIVE['is_creditor'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['receive_method']    = $PTL_GIVE['receive_method'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank']			         = $PTL_GIVE['bank_name'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank_code']			   = '';
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['account_num']       = $PTL_GIVE['account_num'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank_private_name'] = $PTL_GIVE['bank_private_name'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['banking_date']      = $PTL_GIVE['banking_date'];

							////////////////////////////
							// 부분상환 지급합계
							////////////////////////////
							$PTL_PAIED_SUM['repay_principal']       += $PTL_GIVE['principal'];
							$PTL_PAIED_SUM['invest_interest']       += ($PTL_GIVE['interest'] + $PTL_GIVE['interest_tax'] + $PTL_GIVE['local_tax'] +  $PTL_GIVE['fee']);		// 지급기록으로 세전이자 추출
							$PTL_PAIED_SUM['TAX']['interest_tax']   += $PTL_GIVE['interest_tax'];
							$PTL_PAIED_SUM['TAX']['local_tax']      += $PTL_GIVE['local_tax'];
							$PTL_PAIED_SUM['TAX']['sum']            += ($PTL_GIVE['interest_tax'] + $PTL_GIVE['local_tax']);
							$PTL_PAIED_SUM['invest_usefee']         += $PTL_GIVE['fee'];
							$PTL_PAIED_SUM['withhold']              += ($PTL_GIVE['interest_tax'] + $PTL_GIVE['local_tax'] + $PTL_GIVE['fee']);
							$PTL_PAIED_SUM['interest']              += $PTL_GIVE['interest'];

							$TOTAL_PAIED_SUM['repay_principal']     += $PTL_GIVE['principal'];
							$TOTAL_PAIED_SUM['invest_interest']     += ($PTL_GIVE['interest'] + $PTL_GIVE['interest_tax'] + $PTL_GIVE['local_tax'] +  $PTL_GIVE['fee']);		// 지급기록으로 세전이자 추출
							$TOTAL_PAIED_SUM['TAX']['interest_tax'] += $PTL_GIVE['interest_tax'];
							$TOTAL_PAIED_SUM['TAX']['local_tax']    += $PTL_GIVE['local_tax'];
							$TOTAL_PAIED_SUM['TAX']['sum']          += ($PTL_GIVE['interest_tax'] + $PTL_GIVE['local_tax']);
							$TOTAL_PAIED_SUM['invest_usefee']       += $PTL_GIVE['fee'];
							$TOTAL_PAIED_SUM['withhold']            += ($PTL_GIVE['interest_tax'] + $PTL_GIVE['local_tax'] + $PTL_GIVE['fee']);
							$TOTAL_PAIED_SUM['interest']            += $PTL_GIVE['interest'];

						}
						else {
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['paied']          = 'N';
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['give_idx']       = '';
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['paied_date']     = '';
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['paied_amount']   = '';		// 부분상환원금
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['remit_fee']      = $INVEST[$j]['remit_fee'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['mgtKey']         = '';
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['is_creditor']    = $INVEST[$j]['is_creditor'];
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['receive_method'] = $INVEST[$j]['receive_method'];
							if($INVEST[$j]['receive_method']=='1') {
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank']         = $BANK[$INVEST[$j]['bank_code']];
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank_code']    = $INVEST[$j]['bank_code'];
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['account_num']  = $INVEST[$j]['account_num'];
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank_private_name'] = $INVEST[$j]['bank_private_name'];
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank_private_name'].= ($INVEST[$j]['bank_private_name_sub']) ? "(".$INVEST[$j]['bank_private_name_sub'].")" : "";
							}
							else {		// 예치금환급 선택회원은 제3자 예치시스템적용상품 여부와 상관없이 무조건 신한가상계좌로 입금받도록 수정 : 2018-04-05
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank']         = $BANK[$INVEST[$j]['va_bank_code2']];
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank_code']    = $INVEST[$j]['va_bank_code2'];
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['account_num']  = $INVEST[$j]['virtual_account2'];
								$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['bank_private_name'] = $INVEST[$j]['va_private_name2'];
							}
							$REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['banking_date']   = "";
						}

						// 단일 회원 데이터가 아닌 경우, 합계 계산
						$REPAY[$i]['PARTIAL'][$k]['SUM']['invest_amount']     += $REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['invest_amount'];
						$REPAY[$i]['PARTIAL'][$k]['SUM']['partial_principal'] += $REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['partial_principal'];
						$REPAY[$i]['PARTIAL'][$k]['SUM']['remain_principal']  += $REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['remain_principal'];
						$REPAY[$i]['PARTIAL'][$k]['SUM']['repay_principal']   += $REPAY[$i]['PARTIAL'][$k]['LIST'][$j]['repay_principal'];

					}	// end for($j=0; $j<$invest_count; $j++)

					$PTL_REPAY_SUM['partial_principal'] += $REPAY[$i]['PARTIAL'][$k]['SUM']['partial_principal'];
					$PTL_REPAY_SUM['remain_principal']  += $REPAY[$i]['PARTIAL'][$k]['SUM']['remain_principal'];
					$PTL_REPAY_SUM['repay_principal']   += $REPAY[$i]['PARTIAL'][$k]['SUM']['repay_principal'];

				}		// end if($R1['turn_sno'])

			}
		}

		sql_free_result($ptl_res);

		///////////////////////////////////////////////////////////////////////////
		// 연체내역 배열화
		///////////////////////////////////////////////////////////////////////////
		if( $REPAY[$i]['SUCCESS']['overdue_start_date'] > '0000-00-00' ) {

			$REPAY[$i]['OVERDUE']['rate'] = $PRDT['overdue_rate'];			// 연체이자율	(기간에 따른 정책적 연체율 변동사항이 있을 경우, 이곳에 조건문을 달것!!!!)

			$overdue_bill_setted = false;

			// 최종회차 연체 빌링자료가 있는 경우
			if($REPAY[$i]['turn']==$total_repay_turn) {

				$overdue_bill_sql = "
					SELECT
						IFNULL(MIN(bill_date),0) AS overdue_sdate,
						IFNULL(MAX(bill_date),0) AS overdue_edate,
						COUNT(bill_date) AS day_count
					FROM
						{$INI['bill_table']}
					WHERE
						invest_idx = '".$INVEST[0]['idx']."' AND turn='".$REPAY[$i]['turn']."' AND is_overdue='Y'";
				$LAST_TURN_OVERDUE_BILL = sql_fetch($overdue_bill_sql);
				//if($office_connect) print_rr($LAST_TURN_OVERDUE_BILL);

				if($LAST_TURN_OVERDUE_BILL['day_count']) {

					$REPAY[$i]['OVERDUE']['start_date'] = $LAST_TURN_OVERDUE_BILL['overdue_sdate'];
					$REPAY[$i]['OVERDUE']['end_date']   = $LAST_TURN_OVERDUE_BILL['overdue_edate'];
					$REPAY[$i]['OVERDUE']['day_count']  = $LAST_TURN_OVERDUE_BILL['day_count'];

					//if($office_connect) print_rr($REPAY[$i]['OVERDUE'], 'color:red');

					$overdue_bill_setted = true;

				}

			}


			if(!$overdue_bill_setted) {
				$REPAY[$i]['OVERDUE']['start_date'] = $REPAY[$i]['SUCCESS']['overdue_start_date'];
				$REPAY[$i]['OVERDUE']['end_date']   = ($REPAY[$i]['SUCCESS']['overdue_end_date']=='' || $REPAY[$i]['SUCCESS']['overdue_end_date']=='0000-00-00') ? G5_TIME_YMD : $REPAY[$i]['SUCCESS']['overdue_end_date'];
				$REPAY[$i]['OVERDUE']['day_count']  = repayDayCount($REPAY[$i]['OVERDUE']['start_date'], $REPAY[$i]['OVERDUE']['end_date']);		// 연체대상일수
				//if($office_connect) print_rr($REPAY[$i]['OVERDUE'], 'color:blue');
			}


			$REPAY[$i]['OVERDUE']['SUCCESS'] = array(
				'idx'                      => $REPAY[$i]['SUCCESS']['idx'],
				'overdue_receive'          => $REPAY[$i]['SUCCESS']['overdue_receive'],
				'overdue_ib_request_ready' => $REPAY[$i]['SUCCESS']['overdue_ib_request_ready'],
				'overdue_give'             => $REPAY[$i]['SUCCESS']['overdue_give']
			);

			$daysOfYear = ( in_array(substr($REPAY[$i]['target_sdate'],0,4), $CONF['LEAP_YEAR']) ) ? 366 : 365;		// ★★★ 일별이자 산출 변수 (윤년구분) ★★★

			$REPAY[$i]['OVERDUE']['LIST'] = array();

			for($j=0; $j<$invest_count; $j++) {

				$REPAY[$i]['OVERDUE']['LIST'][$j]['invest_idx'] = $REPAY[$i]['LIST'][$j]['invest_idx'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['prin_rcv_no']= $REPAY[$i]['LIST'][$j]['prin_rcv_no'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['member_idx'] = $REPAY[$i]['LIST'][$j]['member_idx'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['mb_no']      = $REPAY[$i]['LIST'][$j]['member_idx'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['mb_id']      = $REPAY[$i]['LIST'][$j]['mb_id'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['mb_name']    = $REPAY[$i]['LIST'][$j]['mb_name'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['jumin']      = $REPAY[$i]['LIST'][$j]['jumin'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['member_type']= $REPAY[$i]['LIST'][$j]['member_type'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['is_owner_operator']= $REPAY[$i]['LIST'][$j]['is_owner_operator'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['insidebank_after_trans_target']= $REPAY[$i]['LIST'][$j]['insidebank_after_trans_target'];

				$REPAY[$i]['OVERDUE']['LIST'][$j]['invest_amount']    = $REPAY[$i]['LIST'][$j]['invest_amount'];

				if($overdue_bill_setted) {

					// 연체정산자료가 존재하는 상품이면

					$overdue_bill_sql2 = "
						SELECT
							partial_principal,
							remain_principal,
							IFNULL(FLOOR(SUM(day_interest)),0) AS invest_interest,
							IFNULL(FLOOR(SUM(fee)),0) AS fee
						FROM
							{$INI['bill_table']}
						WHERE
							invest_idx = '".$REPAY[$i]['LIST'][$j]['invest_idx']."' AND turn='".$REPAY[$i]['turn']."' AND is_overdue='Y'";
					$LTOB_DATA = sql_fetch($overdue_bill_sql2);		// LAST_TURN_OVERDUE_BILL_DATA

					$REPAY[$i]['OVERDUE']['LIST'][$j]['remain_principal'] = $LTOB_DATA['remain_principal'];
					$REPAY[$i]['OVERDUE']['LIST'][$j]['invest_interest']  = $LTOB_DATA['invest_interest'];
					$REPAY[$i]['OVERDUE']['LIST'][$j]['invest_usefee']    = ($INVEST[$j]['remit_fee']=='1') ? 0 : $LTOB_DATA['fee'];

				}
				else {

					if( in_array($product_idx, $OVERDUE_RECORD_PRODUCT) ) {
						$day_invest_interest = ($REPAY[$i]['LIST'][$j]['remain_principal'] * ($PRDT['overdue_rate']/100)) / $daysOfYear;		// 일별 투자자 수익금 (연체이자율 대입)
						$day_invest_usefee   = ($REPAY[$i]['LIST'][$j]['remain_principal'] * ($PRDT['invest_usefee']/100)) / $daysOfYear;		// 일별 플랫폼이용료 (365일 기준)
					}
					else {
						if($turn < $total_repay_turn) {
							// 이자상환회차
							$day_invest_interest = ($REPAY[$i]['LIST'][$j]['invest_interest'] * ($PRDT['overdue_rate']/100)) / $daysOfYear;		// 일별 투자자 수익금 (연체이자율 대입)
							$day_invest_usefee   = ($REPAY[$i]['LIST'][$j]['invest_interest'] * ($PRDT['invest_usefee']/100)) / $daysOfYear;		// 일별 플랫폼이용료 (365일 기준)
						}
						else {
							// 원금상환회차(최종회차)
							$day_invest_interest = ($REPAY[$i]['LIST'][$j]['remain_principal'] * ($PRDT['overdue_rate']/100)) / $daysOfYear;		// 일별 투자자 수익금 (연체이자율 대입)
							$day_invest_usefee   = ($REPAY[$i]['LIST'][$j]['remain_principal'] * ($PRDT['invest_usefee']/100)) / $daysOfYear;		// 일별 플랫폼이용료 (365일 기준)
						}
					}

					$REPAY[$i]['OVERDUE']['LIST'][$j]['remain_principal'] = $REPAY[$i]['LIST'][$j]['remain_principal'];
					$REPAY[$i]['OVERDUE']['LIST'][$j]['invest_interest'] = floor($day_invest_interest * $REPAY[$i]['OVERDUE']['day_count']);
					$REPAY[$i]['OVERDUE']['LIST'][$j]['invest_usefee']   = ($INVEST[$j]['remit_fee']=='1') ? 0 : floor($day_invest_usefee * $REPAY[$i]['OVERDUE']['day_count']);

				}

				// 이자정산지급일에 따른 세율 변환
				if($PRDT['loan_start_date'] >= '2021-08-27') {
					$interest_tax_ratio = ($INVEST[$j]['member_type']=='2') ? 0.25 : 0.14;
				}
				else {
					if( $REPAY[$i]['repay_schedule_date'] < '2021-10-21' ) {

						$interest_tax_ratio = ($INVEST[$j]['member_type']=='2') ? 0.25 : 0.25;

						// 0.14로 정산된것 상품회차 예외처리
						if( $product_idx == '6281' && $turn >= 3) {
							if($INVEST[$j]['member_type']=='1') $interest_tax_ratio = 0.14;
						}

						// 0.14로 정산된 상품 예외처리
						if( in_array($product_idx, array('6561','6573','6584','6596','6607')) ) {
							if($INVEST[$j]['member_type']=='1') $interest_tax_ratio = 0.14;
						}

					}
					else {
						$interest_tax_ratio = ($INVEST[$j]['member_type']=='2') ? 0.25 : 0.14;
					}
				}

				$local_tax_ratio = 0.1;		// interest_tax_ratio의 10%
				//--------------------------------------------------------------------------------------------

				$ovd_interest_tax = floor( ($REPAY[$i]['OVERDUE']['LIST'][$j]['invest_interest'] * $interest_tax_ratio) / 10 ) * 10;				// 당월 이자소득세 = 이자수익 * 0.25
				$ovd_local_tax    = floor( ($ovd_interest_tax * $local_tax_ratio) / 10 ) * 10;				// 당월 지방소득세(원단위 절사)

				// 원천징수 제외
				if($INVEST[$j]['is_creditor']=='Y') {
					// 대부업 회원
					$ovd_interest_tax = 0;
					$ovd_local_tax    = 0;
				}
				else {
					// 법인 이자소득세 1000원 미만인 경우 (소액부징수)
					if($INVEST[$j]['member_type']=='2') {
						if($ovd_interest_tax < 1000 && $REPAY[$i]['repay_date'] > '2021-11-19') {
							$ovd_interest_tax = 0;
							$ovd_local_tax    = 0;
						}
					}
				}

				$REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['interest_tax'] = $ovd_interest_tax;											// 당월 이자소득세 = 이자수익 * 0.25
				$REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['local_tax']    = $ovd_local_tax;												// 당월 지방소득세(원단위 절사)
				$REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['interest_tax'] = $ovd_interest_tax;											// 당월 이자소득세 = 이자수익 * 0.25
				$REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['local_tax']    = $ovd_local_tax;												// 당월 지방소득세(원단위 절사)
				$REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['sum']          = $ovd_interest_tax + $ovd_local_tax;		// 당월 세금 합계
				$REPAY[$i]['OVERDUE']['LIST'][$j]['withhold']   = $REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['sum'] + $REPAY[$i]['OVERDUE']['LIST'][$j]['invest_usefee'];								// 당월 징수할 금액 (세금 + 플랫폼이용료)
				$REPAY[$i]['OVERDUE']['LIST'][$j]['interest']   = $REPAY[$i]['OVERDUE']['LIST'][$j]['invest_interest'] - $REPAY[$i]['OVERDUE']['LIST'][$j]['withhold'];								// 실 수령액


				/////////////////////////////////////////////////////
				// 인사이드뱅크 회수금(상환금) 배분 처리내역 설정
				/////////////////////////////////////////////////////
				if( $PRDT['ib_trust']=='Y' ) {
					$REPAY[$i]['OVERDUE']['LIST'][$j]['ib_regist'] = $INVEST[$j]['ib_regist'];

					$ib_sql = "
						SELECT
							TR_AMT, CTAX_AMT, FEE, JI_DATE, JI_TIME, RESP_CODE
						FROM
							IB_FB_P2P_REPAY_REQ_DETAIL
						WHERE 1
							AND invest_idx = '".$INVEST[$j]['idx']."'
							AND turn = '".$REPAY[$i]['turn']."'
							AND turn_sno = '0'
							AND is_overdue='Y'
						ORDER BY
							rdate DESC
						LIMIT 1";
					//if($office_connect) print_rr($ib_sql, "font-size:12px");
					$IB = sql_fetch($ib_sql);

					$REPAY[$i]['OVERDUE']['LIST'][$j]['ib_withdraw'] = $IB['RESP_CODE'];		// 회수금 배분처리 성공 플래그
					if($IB['RESP_CODE']=='00000000') {
						$REPAY[$i]['OVERDUE']['LIST'][$j]['ib_withdraw_datetime'] = date('Y-m-d H:i', strtotime($IB['JI_DATE'].$IB['JI_TIME']));		// 회수금 배분처리일시
					}
				}

				///////////////////////////////////
				// 지급기록 추출 및 지급계좌 설정
				///////////////////////////////////
				$ovd_give_sql = "
					SELECT
						idx, `date`,
						invest_amount, interest, principal, interest_tax, local_tax, fee,
						is_creditor, remit_fee, receive_method,
						bank_name, account_num, bank_private_name,
						banking_date, mgtKey
					FROM
						cf_product_give
					WHERE 1
						AND invest_idx = '".$INVEST[$j]['idx']."'
						AND turn = '".$REPAY[$i]['turn']."'
						AND turn_sno = '0'
						AND is_overdue = 'Y'
						AND banking_date IS NOT NULL";
				$OVD_GIVE = sql_fetch($ovd_give_sql);

				$REPAY[$i]['OVERDUE']['LIST'][$j]['paied'] = ($OVD_GIVE['idx']) ? 'Y' : 'N';

				$REPAY[$i]['OVERDUE']['LIST'][$j]['give_idx']     = ($OVD_GIVE['idx']) ? $OVD_GIVE['idx'] : '';
				$REPAY[$i]['OVERDUE']['LIST'][$j]['paied_date']   = $OVD_GIVE['date'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['paied_amount'] = $OVD_GIVE['interest'];		// 이자 실입금액
				$REPAY[$i]['OVERDUE']['LIST'][$j]['remit_fee']    = ($OVD_GIVE['remit_fee']=='1') ? $OVD_GIVE['remit_fee'] : $INVEST[$j]['remit_fee'];
				$REPAY[$i]['OVERDUE']['LIST'][$j]['mgtKey']       = $OVD_GIVE['mgtKey'];

				if($REPAY[$i]['OVERDUE']['LIST'][$j]['paied']=='Y') {
					$REPAY[$i]['OVERDUE']['LIST'][$j]['is_creditor']       = $OVD_GIVE['is_creditor'];
					$REPAY[$i]['OVERDUE']['LIST'][$j]['receive_method']	   = $OVD_GIVE['receive_method'];
					$REPAY[$i]['OVERDUE']['LIST'][$j]['bank']			         = $OVD_GIVE['bank_name'];
					$REPAY[$i]['OVERDUE']['LIST'][$j]['bank_code']			   = "";		// 이미 지급됬는데 별 필요없을듯
					$REPAY[$i]['OVERDUE']['LIST'][$j]['account_num']       = $OVD_GIVE['account_num'];
					$REPAY[$i]['OVERDUE']['LIST'][$j]['bank_private_name'] = $OVD_GIVE['bank_private_name'];
					$REPAY[$i]['OVERDUE']['LIST'][$j]['banking_date']      = $OVD_GIVE['banking_date'];

					////////////////////////////
					// 연체 지급합계
					////////////////////////////
					$OVD_PAIED_SUM['repay_principal']       += $OVD_GIVE['principal'];
					$OVD_PAIED_SUM['invest_interest']       += ($OVD_GIVE['interest'] + $OVD_GIVE['interest_tax'] + $OVD_GIVE['local_tax'] +  $OVD_GIVE['fee']);		// 지급기록으로 세전이자 추출
					$OVD_PAIED_SUM['TAX']['interest_tax']   += $OVD_GIVE['interest_tax'];
					$OVD_PAIED_SUM['TAX']['local_tax']      += $OVD_GIVE['local_tax'];
					$OVD_PAIED_SUM['TAX']['sum']            += ($OVD_GIVE['interest_tax'] + $OVD_GIVE['local_tax']);
					$OVD_PAIED_SUM['invest_usefee']         += $OVD_GIVE['fee'];
					$OVD_PAIED_SUM['withhold']              += ($OVD_GIVE['interest_tax'] + $OVD_GIVE['local_tax'] + $OVD_GIVE['fee']);
					$OVD_PAIED_SUM['interest']              += $OVD_GIVE['interest'];

					$TOTAL_PAIED_SUM['repay_principal']     += $OVD_GIVE['principal'];
					$TOTAL_PAIED_SUM['invest_interest']     += ($OVD_GIVE['interest'] + $OVD_GIVE['interest_tax'] + $OVD_GIVE['local_tax'] +  $OVD_GIVE['fee']);		// 지급기록으로 세전이자 추출
					$TOTAL_PAIED_SUM['TAX']['interest_tax'] += $OVD_GIVE['interest_tax'];
					$TOTAL_PAIED_SUM['TAX']['local_tax']    += $OVD_GIVE['local_tax'];
					$TOTAL_PAIED_SUM['TAX']['sum']          += ($OVD_GIVE['interest_tax'] + $OVD_GIVE['local_tax']);
					$TOTAL_PAIED_SUM['invest_usefee']       += $OVD_GIVE['fee'];
					$TOTAL_PAIED_SUM['withhold']            += ($OVD_GIVE['interest_tax'] + $OVD_GIVE['local_tax'] + $OVD_GIVE['fee']);
					$TOTAL_PAIED_SUM['interest']            += $OVD_GIVE['interest'];

			}
				else {
					$REPAY[$i]['OVERDUE']['LIST'][$j]['is_creditor']    = $INVEST[$j]['is_creditor'];
					$REPAY[$i]['OVERDUE']['LIST'][$j]['receive_method'] = $INVEST[$j]['receive_method'];
					if($INVEST[$j]['receive_method']=='1') {
						$REPAY[$i]['OVERDUE']['LIST'][$j]['bank']              = $BANK[$INVEST[$j]['bank_code']];
						$REPAY[$i]['OVERDUE']['LIST'][$j]['bank_code']         = $INVEST[$j]['bank_code'];
						$REPAY[$i]['OVERDUE']['LIST'][$j]['account_num']       = $INVEST[$j]['account_num'];
						$REPAY[$i]['OVERDUE']['LIST'][$j]['bank_private_name'] = $INVEST[$j]['bank_private_name'];
						$REPAY[$i]['OVERDUE']['LIST'][$j]['bank_private_name'].= ($INVEST[$j]['bank_private_name_sub']) ? "(".$INVEST[$j]['bank_private_name_sub'].")" : "";
					}
					else {		// 예치금환급 선택회원은 제3자 예치시스템적용상품 여부와 상관없이 무조건 신한가상계좌로 입금받도록 수정 : 2018-04-05
						$REPAY[$i]['OVERDUE']['LIST'][$j]['bank']              = $BANK[$INVEST[$j]['va_bank_code2']];
						$REPAY[$i]['OVERDUE']['LIST'][$j]['bank_code']         = $INVEST[$j]['va_bank_code2'];
						$REPAY[$i]['OVERDUE']['LIST'][$j]['account_num']       = $INVEST[$j]['virtual_account2'];
						$REPAY[$i]['OVERDUE']['LIST'][$j]['bank_private_name'] = $INVEST[$j]['va_private_name2'];
					}

					$REPAY[$i]['OVERDUE']['LIST'][$j]['banking_date']        = "";
				}

				// 단일 회원 데이터가 아닌 경우, 합계 계산
				$REPAY[$i]['OVERDUE']['SUM']['invest_amount']       += $REPAY[$i]['OVERDUE']['LIST'][$j]['invest_amount'];
				$REPAY[$i]['OVERDUE']['SUM']['invest_interest']     += $REPAY[$i]['OVERDUE']['LIST'][$j]['invest_interest'];
				$REPAY[$i]['OVERDUE']['SUM']['invest_usefee']       += $REPAY[$i]['OVERDUE']['LIST'][$j]['invest_usefee'];
				$REPAY[$i]['OVERDUE']['SUM']['TAX']['interest_tax'] += $REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['interest_tax'];
				$REPAY[$i]['OVERDUE']['SUM']['TAX']['local_tax']    += $REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['local_tax'];
				$REPAY[$i]['OVERDUE']['SUM']['TAX']['sum']          += $REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['sum'];
				$REPAY[$i]['OVERDUE']['SUM']['withhold']            += $REPAY[$i]['OVERDUE']['LIST'][$j]['withhold'];
				$REPAY[$i]['OVERDUE']['SUM']['interest']            += $REPAY[$i]['OVERDUE']['LIST'][$j]['interest'];

				////////////////////////////
				// 연체 합계
				////////////////////////////
				$OVD_REPAY_SUM['invest_interest']     += $REPAY[$i]['OVERDUE']['LIST'][$j]['invest_interest'];
				$OVD_REPAY_SUM['invest_usefee']       += $REPAY[$i]['OVERDUE']['LIST'][$j]['invest_usefee'];
				$OVD_REPAY_SUM['TAX']['interest_tax'] += $REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['interest_tax'];
				$OVD_REPAY_SUM['TAX']['local_tax']    += $REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['local_tax'];
				$OVD_REPAY_SUM['TAX']['sum']          += $REPAY[$i]['OVERDUE']['LIST'][$j]['TAX']['sum'];
				$OVD_REPAY_SUM['withhold']            += $REPAY[$i]['OVERDUE']['LIST'][$j]['withhold'];
				$OVD_REPAY_SUM['interest']            += $REPAY[$i]['OVERDUE']['LIST'][$j]['interest'];
				$OVD_REPAY_SUM['repay_principal']     += $REPAY[$i]['OVERDUE']['LIST'][$j]['repay_principal'];

				//if($office_connect) print_rr($OVD_REPAY_SUM, 'font-size:12px;color:#FF2222;');

			}

		}

	}		// 회차 루프 끝
	/////////////////////////////////////////////////////////////////////////////

	// 전체 정산 합계 : 정상상환 + 연체상환 + 부분상환
	$TOTAL_REPAY_SUM['repay_principal']     = $REPAY_SUM['repay_principal'] + $PTL_REPAY_SUM['repay_principal'] + $OVD_REPAY_SUM['repay_principal'];
	$TOTAL_REPAY_SUM['invest_interest']     = $REPAY_SUM['invest_interest'] + $PTL_REPAY_SUM['invest_interest'] + $OVD_REPAY_SUM['invest_interest'];
	$TOTAL_REPAY_SUM['TAX']['interest_tax'] = $REPAY_SUM['TAX']['interest_tax'] + $PTL_REPAY_SUM['TAX']['interest_tax'] + $OVD_REPAY_SUM['TAX']['interest_tax'];
	$TOTAL_REPAY_SUM['TAX']['local_tax']    = $REPAY_SUM['TAX']['local_tax'] + $PTL_REPAY_SUM['TAX']['local_tax'] + $OVD_REPAY_SUM['TAX']['local_tax'];
	$TOTAL_REPAY_SUM['TAX']['sum']          = $REPAY_SUM['TAX']['sum'] + $PTL_REPAY_SUM['TAX']['sum'] + $OVD_REPAY_SUM['TAX']['sum'];
	$TOTAL_REPAY_SUM['invest_usefee']       = $REPAY_SUM['invest_usefee'] + $PTL_REPAY_SUM['invest_usefee'] + $OVD_REPAY_SUM['invest_usefee'];
	$TOTAL_REPAY_SUM['withhold']            = $REPAY_SUM['withhold'] + $PTL_REPAY_SUM['withhold'] + $OVD_REPAY_SUM['withhold'];
	$TOTAL_REPAY_SUM['interest']            = $REPAY_SUM['interest'] + $PTL_REPAY_SUM['interest'] + $OVD_REPAY_SUM['interest'];


	//if( $only_summary ) {
		$return_arr = array(
			'PRDT'            => $PRDT,
			'LOANER'          => $LOANER,
			'INI'             => $INI,
			'INVEST'          => $INVEST,
			'REPAY'           => $REPAY,
			'REPAY_SUM'       => $REPAY_SUM,
			'PAIED_SUM'       => $PAIED_SUM,
			'PTL_REPAY_SUM'   => $PTL_REPAY_SUM,
			'PTL_PAIED_SUM'   => $PTL_PAIED_SUM,
			'OVD_REPAY_SUM'   => $OVD_REPAY_SUM,
			'OVD_PAIED_SUM'   => $OVD_PAIED_SUM,
			'TOTAL_REPAY_SUM' => $TOTAL_REPAY_SUM,
			'TOTAL_PAIED_SUM' => $TOTAL_PAIED_SUM
		);
		return $return_arr;
	//}


}

?>