<?
################################################################################
## 투자 처리
## 2017-04-24 : 개인회원 상품별 투자 금액 제한 관련 내용 추가
## 2017-11-02 : 인사이드뱅크 전문처리파트 제거 -> 관리자 대출실행전 투자자등록프로세스전송 제작요망
## 2018-05-17 : 투자성공 문자발송 Disable처리
################################################################################

include_once('_common.php');
include_once(G5_LIB_PATH.'/sms.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');
include_once(G5_LIB_PATH.'/invest_queue.lib.php');

if($_REQUEST['mode']=='' && $_SERVER["REQUEST_METHOD"]!="POST") { echo "ERROR-DATA"; sql_close(); exit; }
if(!$member['mb_id']) { echo "ERROR-LOGIN"; sql_close(); exit; }
if(trim($_REQUEST['prd_idx'])=="") { echo "ERROR-DATA"; sql_close(); exit; }


// 신한은행 점검시간 진입금지 --------------------------------------------------------------
if( date('Y-m-d H:i:s') >= $CONF['BANK_STOP_SDATE'] && date('Y-m-d H:i:s') < $CONF['BANK_STOP_EDATE'] ) { echo "ERROR-BANK_PAUSE"; sql_close(); exit; }


// 금결원 점검시간 진입금지 --------------------------------------------------------------
if( date('H:i') >= $CONF['P2PCTR_PAUSE']['STIME'] || date('H:i') <= $CONF['P2PCTR_PAUSE']['ETIME'] ) { echo "ERROR-P2PCTR_PAUSE"; sql_close(); exit; }


///////////////////////////////
// KYC 현황 체크
///////////////////////////////
//if( $office_connect ) {				// if( in_array($member['mb_id'], $kyc_test_member) ) {


///////////////////////////////
// :: 투자 제한 설정 ::
///////////////////////////////
//if( in_array($member['mb_no'], array('11838','14368')) ) { echo "ERROR-CHECKED_LIMIT_MEMBER"; sql_close(); exit; }



$mb_no             = $member['mb_no'];
$prd_idx           = trim($_REQUEST["prd_idx"]);
$ajax_invest_value = trim($_REQUEST["ajax_invest_value"]);
$advance           = trim($_REQUEST['advance']);

if( ($ajax_invest_value%10000) > 0 ) { echo "ERROR-MONEY_UNIT"; sql_close(); exit; }

/*
// 투자DB처리 Queue 등록
$REQ = INVEST_QueueRegist($prd_idx, $mb_no, $ajax_invest_value);		// Queue 등록 (상품번호, 회원번호, 투자금액)
if($REQ['result'] == 'success') {
	$queue_idx = $REQ['queue_idx'];
}
*/


///////////////////////////////////////////////////////////
// :: 중복투자 제한 설정 ::
// 동일회원 동일상품 동일금액 20초내 재투자 신청시 차단
// 추가일 : 2021-12-25
///////////////////////////////////////////////////////////
$cTimeStamp = time()-20;
$cdd = date("Y-m-d", $cTimeStamp);
$cdt = date("H:i:s", $cTimeStamp);

$dupSql = "
	SELECT
		COUNT(idx) AS cnt
	FROM
		cf_product_invest_detail
	WHERE 1
		AND member_idx = '".$mb_no."' AND product_idx = '".$prd_idx."' AND invest_state = 'Y'
		AND amount = '".$ajax_invest_value."'
		AND insert_date = '".$cdd."'
		AND insert_time >= '".$cdt."'";
$DUPLICATE_INVEST = sql_fetch($dupSql);

if($DUPLICATE_INVEST['cnt'] > 0) {
	echo "ERROR-DUPLICATE_INVEST"; sql_close(); exit;
}


///////////////////////////////////////////////////////////////////////
// 중앙기록관리 API를 통하여 회원DB 투자한도 업데이트 실행 1
///////////////////////////////////////////////////////////////////////
$exec_str = "/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/get_p2pctr_limit_amt.exec.php " .  $member['mb_no'];
$exec_result = exec($exec_str);
if(!$exec_result) { echo "ERROR-P2PCTR-UPDATE-FAIL"; sql_close(); exit; }		// 회원DB 정상 업데이트 사인이 없는 경우


$MB_P2PCTR = sql_fetch("SELECT p2pctr_all_limit, p2pctr_imv_limit, p2pctr_mv_limit FROM g5_member WHERE mb_no='".$member['mb_no']."'");
$member['invest_possible_amount']      = $MB_P2PCTR['p2pctr_all_limit'];
$member['invest_possible_amount_prpt'] = $MB_P2PCTR['p2pctr_imv_limit'];
$member['invest_possible_amount_ds']   = $MB_P2PCTR['p2pctr_mv_limit'];
unset($MB_P2PCTR);


$is_advance_invest = ($advance==1) ? 'Y' : 'N';			// 사전투자모드 설정

$sql = "
	SELECT
		A.idx, A.gr_idx, A.category, A.title,
		A.recruit_amount, A.live_invest_amount AS total_invest_amount,
		A.invest_return, A.invest_period, A.invest_usefee,
		A.open_datetime, A.start_datetime, A.end_datetime, A.recruit_period_start, A.recruit_period_end,
		A.advance_invest, A.advance_invest_ratio, A.platform, A.only_vip, A.vip_mb_no
	FROM
		cf_product A
	WHERE 1=1
		AND A.idx = '".$prd_idx."'
		AND A.start_datetime <= '".G5_TIME_YMDHIS."' AND A.end_datetime >= '".G5_TIME_YMDHIS."'
		AND A.invest_end_date = ''";
$PRDT = sql_fetch($sql);
if($_REQUEST['mode']=='test') { echo $sql."<br>\n"; print_rr($PRDT); }
if(!$PRDT['idx']) { echo "ERROR-DATA"; sql_close(); exit; }

// 2021-02-08 : 전체 투자건수, 금액 개별쿼리로 전환
//$row = sql_fetch("SELECT IFNULL(SUM(amount),0) AS total_invest_amount FROM cf_product_invest WHERE product_idx='".$PRDT['idx']."' AND invest_state='Y'");
//$PRDT['total_invest_amount'] = $row['total_invest_amount'];
//unset($row);

$recruit_amount = $PRDT['recruit_amount'];
if(!$recruit_amount) { echo "ERROR-DATA"; sql_close(); exit; }

$YmdHis               = preg_replace("/(-|:| )/", "", G5_TIME_YMDHIS);
$recruit_period_start = preg_replace("/-/", "", $PRDT['recruit_period_start']);
$recruit_period_end   = preg_replace("/-/", "", $PRDT['recruit_period_end']);
$product_open_date    = preg_replace("/(-|:| )/", "", $PRDT['open_datetime']);		// 상점오픈 (투자시작불가)
$product_invest_sdate = preg_replace("/(-|:| )/", "", $PRDT['start_datetime']);		// 투자시작
$product_invest_edate = preg_replace("/(-|:| )/", "", $PRDT['end_datetime']);			// 상품종료 (투자마감)

if($is_advance_invest=='Y') {
	$recruit_amount = round($recruit_amount * ($PRDT['advance_invest_ratio']/100));											// 사전투자비율에 따른 사전투자전체한도액
	if($PRDT['total_invest_amount'] >= $recruit_amount) { echo "ERROR-ADVANCE-INVEST-AMOUNT"; exit; }		// 사전투자 가능한 금액을 초과 입력 하셨습니다.
	if($product_invest_sdate <= $YmdHis) { echo "ERROR-ADVANCE-INVEST-DATE"; exit; }										// 사전투자 가능 시간이 지났습니다.
}



/* 투자 수익율 */
if($PRDT['invest_return'] > 0) { $invest_return = $PRDT['invest_return']; }
else { echo "ERROR-DATA"; sql_close(); exit; }

/* 투자기간 */
if($PRDT['invest_period'] > 0) { $invest_period = $PRDT['invest_period']; }
else { echo "ERROR-DATA"; sql_close(); exit; }

/* 투자자 플랫폼 이용료 */
$invest_usefee = ($PRDT['invest_usefee'] > 0) ? $PRDT['invest_usefee'] : 0;



if($is_advance_invest!='Y') {
	if( $product_open_date < $YmdHis && $product_invest_edate > $YmdHis ) {
		if($product_invest_sdate < $YmdHis) {
			if($recruit_amount > $PRDT['total_invest_amount']) {  // 투자액 세팅
				// 투자 가능
			}
			else {
				echo "ERROR-INVEST-END"; sql_close(); exit;
			}
		}
		else {
			echo "ERROR-DATE"; sql_close(); exit;
		}
	}
	else {
		echo "ERROR-DATE"; sql_close(); exit;
	}
}


$min_invest_limit = $CONF['min_invest_limit'];
$max_invest_limit = $CONF['max_invest_limit'];

if($ajax_invest_value < $min_invest_limit) {
	echo "ERROR-MIN-PRICE"; sql_close(); exit;
}
if($max_invest_limit!="") {
	if($ajax_invest_value > $max_invest_limit) {
		echo "ERROR-MAX-PRICE"; sql_close(); exit;
	}
}


// 투자데이터에 신디케이션ID 기록
if( preg_match("/r114/i", $_SERVER['HTTP_HOST']) ) {
	$cookie_syndi_id = "r114";
}
else if( preg_match("/itembay/i", $_SERVER['HTTP_HOST'])) {
	$cookie_syndi_id = "itembay";
}
else {
	if( preg_match("/wowstar/i", $_COOKIE['PHPSESSID'])) {
		$cookie_syndi_id = "hktvwowstar";
	}
	else if( preg_match("/chosun/i", $_COOKIE['PHPSESSID']) ) {
		$cookie_syndi_id = "chosun";
	}
}


///////////////////////////////////////////////////////////////////////////////
// 2020-10-05: 자체어플리케이션을 운영하는 신디케이션사를 통한 투자내역이 존재하는 투자건의 경우 추가 투자 불가 처리함.
//  신디케이션사측 투자내역과 차이가 발생하여 생길 수 있는 정산상의 혼선를 방어하기 위함.
//	대상신디케이션사 : finnq, oligo, kakaopay
///////////////////////////////////////////////////////////////////////////////
/*
if($member['finnq_userid'] || $member['oligo_userid'] || $member['kakaopay_userid']) {
	$SYNDI_INVESTED = sql_fetch("
		SELECT
			idx, syndi_id
		FROM
			cf_product_invest_detail
		WHERE 1
			AND product_idx = '".$prd_idx."'
			AND member_idx = '".$mb_no."'
			AND invest_state = 'Y'
			AND syndi_id IN('finnq','oligo','kakaopay')
		ORDER BY
			idx DESC
		LIMIT 1");
	if($SYNDI_INVESTED['idx']) {
		if($SYNDI_INVESTED['syndi_id']=='oligo') { echo "ERROR-INVESTEDFROM-OLIGO"; }
		else if($SYNDI_INVESTED['syndi_id']=='kakaopay') { echo "ERROR-INVESTEDFROM-KAKAOPAY"; }
		else { echo "ERROR-INVESTEDFROM-FINNQ"; }
		sql_close();
		exit;
	}
}
*/



if( $member['member_type']=='1' && in_array($member['member_investor_type'], array('1','2')) ) {
	// 모집중이거나 이자상환중인 (원금상환이 완료되지 않은) 동일차주상품 SELECT (현재 투자하려는 상품도 포함)
	$sql2 = "SELECT idx FROM cf_product WHERE state IN ('', '1') AND gr_idx='".$PRDT['gr_idx']."' AND idx > '".$CONF['old_type_end_prdt_idx']."' ORDER BY idx";
	//echo $sql2."<br>\n";
	$res  = sql_query($sql2);
	$rcnt = $res->num_rows;
	if($rcnt) {
		if($rcnt > 1) {
			$is_group_product = true;
		}
		$prd_idx_arr = '';
		for($i=0,$j=1; $i<$rcnt; $i++,$j++) {
			$r = sql_fetch_array($res);
			$prd_idx_arr.="'".$r['idx']."'";
			$prd_idx_arr.= ($j<$rcnt) ? "," : "";
		}

		$sql3 = "SELECT IFNULL(SUM(amount), 0) AS sum_invest_amount FROM cf_product_invest WHERE member_idx='".$mb_no."' AND product_idx IN (".$prd_idx_arr.") AND invest_state='Y'";
		$GROUP_INVESTED = sql_fetch($sql3);
	}
}


// 동일차주상품 투자금액 체크 (개인-일반투자자, 개인-소득적격투자자 일때만 체크)
if( $is_group_product && $member['member_type']=='1' && in_array($member['member_investor_type'], array('1','2')) ) {

	$group_invest_balance = $INDI_INVESTOR[$member['member_investor_type']]['group_product_limit'] - $GROUP_INVESTED['sum_invest_amount'];

	if($group_invest_balance < $ajax_invest_value) { echo "ERROR-GROUP-INVEST-AMOUNT-LIMITED"; sql_close(); exit; }				// 동일차주 투자한도 초과

}


// 상품의 잔여 모집금액
$need_recruit_amount = $recruit_amount - $PRDT['total_invest_amount'];

// 상품의 투자 가능금액 설정
$invest_possible_amount = $need_recruit_amount;

//--- 개인회원의 경우 투자등급에 따른 투자 가능금액 산출 -------------------
if($member['member_type']=='1') {

	if($member['member_investor_type']=='1') {
		// 투자금과 카테고리별 투자한도 비교
		if($PRDT['category']=='2') {
			if($ajax_invest_value > $member['invest_possible_amount_prpt']) { echo "ERROR-INVEST-AMOUNT-LIMITED-PRPT"; sql_close(); exit; }		// 부동산 투자제한 금액 초과
		}
		else {
			if($ajax_invest_value > $member['invest_possible_amount']) { echo "ERROR-INVEST-AMOUNT-LIMITED"; sql_close(); exit; }							// 동산 투자제한 금액 초과
		}
	}

	if( in_array($member['member_investor_type'], array('1','2')) ) {
		$limit_amount = ($is_group_product) ? $INDI_INVESTOR[$member['member_investor_type']]['group_product_limit'] : $INDI_INVESTOR[$member['member_investor_type']]['single_product_limit'];
		$invest_possible_amount_tmp = $limit_amount - $GROUP_INVESTED['sum_invest_amount'];

		if($invest_possible_amount_tmp > $member['invest_possible_amount']) {
			$invest_possible_amount = ($member['member_investor_type']=='1' && $PRDT['category']=='2') ? $member['invest_possible_amount_prpt'] : $member['invest_possible_amount'];
		}
		else {
			$invest_possible_amount = ($member['member_investor_type']=='1' && $PRDT['category']=='2') ? $member['invest_possible_amount_prpt'] : $invest_possible_amount_tmp;
		}
	}

}

// 투자 가능금액이 잔여 모집액보다 크면 투자 가능금액 = 잔여모집액
if($invest_possible_amount >= $need_recruit_amount) {
	$invest_possible_amount = $need_recruit_amount;
}

// 예치금과 투자 할 금액 비교
if($member['mb_point'] < $ajax_invest_value) {
	echo "ERROR-BALANCE";
	sql_close();
	exit;
}

// 투자 가능 금액과 투자 할 금액 비교
if($invest_possible_amount < $ajax_invest_value) {
	echo ($is_advance_invest=='Y') ? "ERROR-ADVANCE-INVEST-AMOUNT" : "ERROR-INVEST";
	sql_close();
	exit;
}



//**** 법인 및 전문투자자일 경우 단일상품 최대 투자가능금액은 전체모집금액의 40% 까지만 허용한다. (온투법관련 변경사항) ****//
if(G5_TIME_YMD >= $CONF['online_invest_policy_sdate']) {
	if($member['member_type']=='2' || ($member['member_type']=='1' && $member['member_investor_type']=='3')) {
		$sqlx = "SELECT IFNULL(SUM(amount), 0) AS invest_amount FROM cf_product_invest WHERE member_idx='".$mb_no."' AND product_idx='".$prd_idx."' AND invest_state='Y'";
		$PRO_INVESTOR = sql_fetch($sqlx);

		$total_invest_amount = $PRO_INVESTOR['invest_amount'] + $ajax_invest_value;
		$pro_max_limit_amount = floor(($PRDT['recruit_amount'] * $INDI_INVESTOR['3']['invest_able_perc'])/10000) * 10000;


		// 지정투자자 상품이며, 투자자가 본인이며, 법인인 경우 투자가능금액은 제한을 받지 않도록
		if( $PRDT['only_vip']=='1' && in_array($member['mb_no'], explode(",", $PRDT['vip_mb_no'])) && $member['member_type']=='2' ) {
			$pro_max_limit_amount = $invest_possible_amount;
		}


		if($total_invest_amount > $pro_max_limit_amount) {
			echo "ERROR-MAX-INVEST-AMOUNT-OVER";
			sql_close();
			exit;
		}
	}
}



## 투자내역 등록 ########################################################
// [투자내역 관리 테이블 추가 : 2016-10-25]
// cf_product_invest : 합산금액 및 최종 처리일시 데이터 취급
// cf_product_invest_detail : 상세내역 전체 등록

$input_datetime = date('Y-m-d H:i:s');
$INPUT_DATE = explode(" ", $input_datetime);
$input_day  = $INPUT_DATE[0];
$input_time = $INPUT_DATE[1];

$INVEST = sql_fetch("SELECT idx FROM cf_product_invest WHERE member_idx='".$mb_no."' AND product_idx='".$prd_idx."' AND invest_state='Y' ORDER BY idx DESC LIMIT 1");

$first_invest = ($INVEST['idx']) ? false : true;		// 첫 투자 여부

// 동일한 투자번호를 가진 투자시도수 (정상/취소 무관)
$invest_try_count = sql_fetch("SELECT COUNT(idx) AS cnt FROM cf_product_invest_detail WHERE invest_idx='".$INVEST['idx']."' AND member_idx='".$mb_no."'")['cnt'];


////////////////////////////////////////////////////////
// 최종 잔여모집액 체크
////////////////////////////////////////////////////////
//if( $need_recruit_amount <= ($PRDT['recruit_amount'] * 0.1) ) { usleep(rand(0, 1500000)); } 지연시간 셋팅 -> 오버펀딩 됨

$RECHECK = sql_fetch("SELECT IFNULL(SUM(amount),0) AS total_invest_amount FROM cf_product_invest WHERE product_idx='".$prd_idx."' AND invest_state='Y'");
$remain_recruit_amount = $recruit_amount - $RECHECK['total_invest_amount'];		// 잔여모집액

if( $ajax_invest_value > $remain_recruit_amount ) {  // 투자액 세팅
	echo "ERROR-INVEST";		// 잔여모집액 초과
	sql_close();
	exit;
}
////////////////////////////////////////////////////////

if($first_invest) {

	// 최초 투자 -----------------------
	$query = "
		INSERT INTO
			cf_product_invest
		SET
			  amount            = '".$ajax_invest_value."'
			, member_idx        = '".$mb_no."'
			, product_idx       = '".$prd_idx."'
			, invest_state      = 'Y'
			, insert_date       = '".$input_day."'
			, insert_time       = '".$input_time."'
			, insert_datetime   = '".$input_datetime."'
			, is_advance_invest = '".$is_advance_invest."'
			, mb_type           = '".$member['member_type']."'
			, investor_type     = '".$member['member_investor_type']."'";
	//print_rr($query);
	if($cookie_syndi_id) { $query.= ", syndi_id = '".$cookie_syndi_id."'";	}
	$result = sql_query($query);

	$invest_idx = sql_insert_id();
	$prin_rcv_no = 'I' . $invest_idx;			// 원리금 수취권 번호: I투자번호 (패턴변경 : 2022-03-21 배부장)	//$prin_rcv_no(기존패턴) = 'M' . $mb_no . 'P' . $prd_idx . 'I' . $invest_idx;
	if($invest_try_count > 0) $prin_rcv_no.= '_' . ($invest_try_count+1);
	sql_query("UPDATE cf_product_invest SET prin_rcv_no='".$prin_rcv_no."' WHERE idx='".$invest_idx."'");

}
else {

	$invest_idx = $INVEST['idx'];
	$prin_rcv_no = 'I' . $invest_idx . '_' . ($invest_try_count+1);

	// 중복 투자 -----------------------
	$query = "
		UPDATE
			cf_product_invest
		SET
			  amount        = amount + ".$ajax_invest_value."
			, insert_date   = '".$input_day."'
			, insert_time   = '".$input_time."'
			, mb_type       = '".$member['member_type']."'
			, investor_type = '".$member['member_investor_type']."'
			, prin_rcv_no   = '".$prin_rcv_no."'
		WHERE
			idx = '".$invest_idx."'";
	$result = sql_query($query);

}


//////////////////////
//투자건별내역 등록
//////////////////////
if($result) {

	$query2 = "
		INSERT INTO
			 cf_product_invest_detail
		 SET
			   invest_idx   = '".$invest_idx."'
			 , amount       = '".$ajax_invest_value."'
			 , member_idx   = '".$mb_no."'
			 , product_idx  = '".$prd_idx."'
			 , invest_state = 'Y'
			 , insert_date  = '".$input_day."'
			 , insert_time  = '".$input_time."'
			 , is_advance_invest = '".$is_advance_invest."'";
	if($cookie_syndi_id) { $query2.= ", syndi_id = '".$cookie_syndi_id."'";	}
	$result2 = sql_query($query2);
	$invest_detail_idx = sql_insert_id();

	$after_remain_amount = $remain_recruit_amount - $ajax_invest_value;		// 본 투자건이 정상투자로 등록되면 남을 모집잔여금액

	// 투자액 모집완료시 투자종료일 표기
	//if( $recruit_amount <= ($PRDT['total_invest_amount'] + $ajax_invest_value) ) {  // 2021-06-02일까지 적용됬던 룰
	if( $after_remain_amount <= 0 ) {
		if($is_advance_invest == 'N') {
			$product_update = "UPDATE cf_product SET invest_end_date='".date('Y-m-d')."' WHERE idx = '".$prd_idx."'";
			sql_query($product_update);


			//IF(!preg_match("/소상공인 확정매출채권/i", $PRDT['title']))
			if ($PRDT['category']<>"3")
			{
				fn_cf_product_admin_report($prd_idx);		// 리포트 데이터 생성
				fn_hello_status_smssend($prd_idx);			// SMS전송
			}

			/*
			if (($PRDT['category']=="3") {

				$chk_so_sql = "SELECT COUNT(*) scf_not_end FROM cf_product WHERE category='3' AND start_date='$input_day' AND invest_end_date=''";
				$chk_so_row = sql_fetch($chk_so_sql);
				if ($chk_so_row["scf_not_end"]==0) {
					$report_idx = fn_cf_product_admin_report_scf($input_day);		// 리포트 데이터 생성
					fn_hello_status_smssend_scf($report_idx);			// SMS전송
				}

			} else {
				fn_cf_product_admin_report($prd_idx);		// 리포트 데이터 생성
				fn_hello_status_smssend($prd_idx);			// SMS전송
			}
			*/

			// 캐시파일 초기화
			@unlink(G5_DATA_PATH."/cache/productList-active.php");
			@unlink(G5_DATA_PATH."/cache/productList-latest.php");
		}
	}

	$po_content = $PRDT['title']. "-투자";
	insert_point($member['mb_id'], $ajax_invest_value * (-1), $po_content, '@invest', $member['mb_id'], $member['mb_id'].'-'.uniqid(''), 0);

	//////////////////////////////////////////////////////////////////////
	// (!중요) 상품관리테이블에 실시간 모집금액 반영하기 :: 2021-02-15 추가
	//////////////////////////////////////////////////////////////////////
	$last_update_sql = "UPDATE cf_product SET live_invest_amount = live_invest_amount + {$ajax_invest_value} WHERE idx = '".$prd_idx."'";
	sql_query($last_update_sql);
	//////////////////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////////////////
	// 올리고에 상품진행현황 전송 (2020-01-24)
	//////////////////////////////////////////////////////////////////////
	//if( preg_match("/oligo/i", $PRDT['platform']) ) {
	//	@shell_exec("/usr/local/php/bin/php -q " . G5_SYNDICATE_PATH . "/oligo/report/productStateReport.php " . $prd_idx);
	//}
	//////////////////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////////////////
	// 투자완료 문자 발송 (특정회원에게만, 이정환차장 요청. 2018-05-04)
	//////////////////////////////////////////////////////////////////////
	if( in_array($member['mb_id'], array('apollon','akorea')) ) {

		$SMS_DATA = sql_fetch("SELECT * FROM `g5_sms_userinfo` WHERE idx='2' AND use_yn='1'");
		if($SMS_DATA['msg']) {
			$sms_msg = preg_replace("/\{FUNDING_PRICE\}/", price_cutting($ajax_invest_value), $SMS_DATA['msg']);
			$sms_msg = preg_replace("/\{PROJECT_NAME\}/", $PRDT['title'], $sms_msg);
			$rst = unit_sms_send($CONF['admin_sms_number'], $member['mb_hp'], $sms_msg);
		}

	}
	//////////////////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////////////////
	// 금결원 중앙기록관리 투자신청 기록
	//////////////////////////////////////////////////////////////////////
	$p2pctr_reg_result = p2pctr_invest_register($mb_no, $prd_idx);

	if($p2pctr_reg_result) {

		////////////////////////////////////
		// 투자한도 업데이트 실행 2
		////////////////////////////////////
		$exec_str = "/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/get_p2pctr_limit_amt.exec.php " .  $member['mb_no'];
		$exec_result = shell_exec($exec_str);

	}
	else {

		///////////////////////////////////////////////////////////////////////
		// 2022-01-22 추가 : 중앙기록관리 결과가 정상이 아닌 경우  투자취소실행
		///////////////////////////////////////////////////////////////////////

		$exec_str = "/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/emergency_investment_cancel.proc.php " . $invest_detail_idx . " " . $member['mb_no'];
		$exec_result = shell_exec($exec_str);

		echo "ERROR-P2PCTR-FAIL-CANCEL";
		@sql_close();
		exit;

	}


	if($is_advance_invest) {
		echo "SUCCESS-ADVANCE-INVEST";
	}
	else {
		echo "SUCCESS";
	}

	//INVEST_QueueUpdate($queue_idx, '1');		// Queue 종료처리 (투자성공:1)

}
else {

	echo "ERROR-INVEST-END";

	//INVEST_QueueUpdate($queue_idx, '2');		// Queue 종료처리 (처리실패:1)

}


@sql_close();

// 실행시간 로깅 종료
//if($log_idx) {
//	$thrSec  = get_microtime() - $sdt;
//	@shell_exec("/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/test_log_finish.exec.php {$log_idx} {$thrSec}");
//}

exit;

?>