<?
###############################################################################
## 신한 인사이드뱅크에 대출관리 프로세스 (AJAX방식)
## 제작시작: 2017-08-21
###############################################################################
## ?prd_idx=149
## 1. 대출정보등록 전문번호: 2100
## 2. 대출정보변경 전문번호: 2500
###############################################################################
# 2018-05-09 : 참조번호(REF_NO) 모듈적용. KSNET_VR_ACCOUNT
###############################################################################

/*
// 자사 도메인이 아닌곳에서 호출된 경우 exit
$allow_domain = "hellofunding.co.kr";
if(isset($_SERVER['HTTP_REFERER'])) {
	if(!preg_match("/$allow_domain/i", $_SERVER['HTTP_REFERER'])) {
		header('HTTP/1.1 404 Not Found');
	}
}
*/

include_once("_common.php");

// 로그인 체크
if(!$_SESSION['ss_mb_id']) { echo "ERROR:LOGIN"; exit; }

while(list($k, $v)=each($_REQUEST)) { $REQ[$k] = trim($v); }

if($REQ['prd_idx']=='') { echo "ERROR:NONE_PARAM_DATA"; exit; }

include_once(G5_PATH.'/lib/insidebank.lib.php');


// 투자상품정보의 대출 정보 가져오기
$sql = "
	SELECT
		idx, gr_idx, category, recruit_amount, loan_usefee, invest_period, invest_days,
		loan_start_date, loan_end_date, ib_product_regist, loan_mb_no, repay_acct_no
	FROM
		cf_product
	WHERE
		idx='".$REQ['prd_idx']."'";
$PRDT = sql_fetch($sql);

if(!$PRDT['idx'])						{ echo "ERROR:NONE_PRODUCT"; exit; }				// 대출상품정보 없음
if(!$PRDT['loan_mb_no'])		{ echo "ERROR:EMPTY_LOANER_INFO"; exit; }		// 대출회원 정보 없음

// 동일차주 상품 XXXXXX 수정할것
/*
	if($PRDT['idx'] > $PRDT['gr_idx']) {
		$PARENT = sql_fetch("SELECT repay_acct_no FROM cf_product WHERE idx='".$PRDT['gr_idx']."'");
		$PRDT['repay_acct_no'] = $PARENT['repay_acct_no'];
	}
*/

$SUBMIT_GBN = ($PRDT['ib_product_regist']=='Y') ? '05' : '01';

$LOAN_DEP_CNT = 0;
$LOAN_DEP_AMT_SUM = 0;
for($i=0,$j=1; $i<5; $i++,$j++) {
	$LOAN_DEP_AMT_SUM += $REQ['loan_dep_amt'.$j];
	if($REQ['loan_dep_bank_cd'.$j] && $REQ['loan_dep_acct_nb'.$j]) {
		$LOAN_DEP_CNT += 1;
	}
}


if($LOAN_DEP_CNT==0) { echo "ERROR:EMPTY_LOANER_ACCT_INFO"; exit; }		// 대출금 입금은행정보 없음
if($LOAN_DEP_AMT_SUM <> $PRDT['recruit_amount']) { echo "ERROR:DIFFRENT_DEPOSIT_AMOUNT"; exit; }		// 모집금액과 계좌입금액합계가 다를경우


$loan_exec_date = ($PRDT['loan_start_date'] > '0000-00-00') ? $PRDT['loan_start_date'] : date('Y-m-d');		// 정해진 대출일이 없으면 금일자로 등록
$LOAN_EXEC_DATE = preg_replace("/(-|:| )/", "", $loan_exec_date);

if($PRDT['invest_period']==1 && $PRDT['invest_days'] > 0) {
	$EDATE_OBJ = new DateTime(date("Y-m-d", strtotime($loan_exec_date." +".$PRDT['invest_days']." day")));
}
else {
	$EDATE_OBJ = new DateTime(date("Y-m-d", strtotime($loan_exec_date." +".$PRDT['invest_period']." month")));
}
$LOAN_EXP_DATE = $EDATE_OBJ->format('Y-m-d');		// 대출 종료일
$LOAN_EXP_DATE = preg_replace("/(-|:| )/", "", $LOAN_EXP_DATE);

if($PRDT['ib_product_regist']=='Y') {
	if($PRDT['loan_end_date'] > '0000-00-00') $LOAN_EXP_DATE = preg_replace("/-/", "", $PRDT['loan_end_date']);
}

//print_r($LOAN_EXP_DATE); exit;


// 대출회원 정보
$LMB = sql_fetch("SELECT mb_id, mb_name, mb_co_name, member_type FROM g5_member WHERE mb_no='".$PRDT['loan_mb_no']."' AND member_group='L'");
if(!$LMB) { echo "ERROR:EMPTY_LOANER_INFO"; exit; }		// 대출회원 정보 없음

$LOAN_CUST_ID = $PRDT['loan_mb_no'];		// 대출자 아이디는 회원번호로 설정함.
$LOAN_CUST_NM = ($LMB['member_type']=='2') ? $LMB['mb_co_name'] : $LMB['mb_name'];

// 가상계좌번호 설정
if($PRDT['repay_acct_no']=='') {
	// 유휴가상계좌 가져오기
	$VACT = sql_fetch("SELECT acct_no FROM IB_vact_hellocrowd WHERE acct_st=0 ORDER BY acct_no ASC LIMIT 1");
	if(!$VACT || $VACT['acct_no']=='') { echo "ERROR:SH_VA_INSUFFICIENCY"; exit; }

	$vact_first_set = true;
	$CMS_NB = $VACT['acct_no'];
}
else {
	$CMS_NB = $PRDT['repay_acct_no'];
}

// 투자자수 가져오기
$INVEST = sql_fetch("SELECT COUNT(idx) AS cnt, SUM(amount) AS amount FROM cf_product_invest WHERE product_idx='".$REQ['prd_idx']."' AND invest_state='Y'");
$INV_CNT = ($INVEST['cnt'] > 0) ? $INVEST['cnt'] : 1;		// 투자자수 0으로 등록시 전문오류 발생 (체크요망)


$ARR['REQ_NUM']           = '020';											// 전문번호
$ARR['SUBMIT_GBN']        = $SUBMIT_GBN;								// 거래구분: 투자등록
$ARR['LOAN_SEQ']          = $REQ['prd_idx'];						// 대출식별번호
$ARR['LOAN_AMT']          = $PRDT['recruit_amount'];		// 총대출금
$ARR['LOAN_FEE']	        = 0;													// 취급수수료 (강제 0으로 처리 : 이상규대리 요청)   //$ARR['LOAN_FEE'] = floor( $PRDT['recruit_amount']*($PRDT['loan_usefee']/100) );
$ARR['LOAN_EXEC_DATE']    = $LOAN_EXEC_DATE;						// 대출실행일
$ARR['LOAN_EXP_DATE']     = $LOAN_EXP_DATE;							// 대출만기일
$ARR['LOAN_CUST_ID']      = $LOAN_CUST_ID;							// 대출자고객ID
$ARR['LOAN_CUST_NM']      = $LOAN_CUST_NM;							// 대출자고객명
$ARR['CMS_NB']            = $CMS_NB;										// 가상계좌번호 (모계좌 : 헬로크라우드대부 업체코드로 배당된 가상계좌)
$ARR['LOAN_DEP_CNT']      = $LOAN_DEP_CNT;							// 대출입금계좌건수
$ARR['INV_CNT']           = $INV_CNT;										// 투자자수
for($i=0,$j=1; $i<5; $i++,$j++) {
	$ARR['LOAN_DEP_BANK_CD'.$j] = $REQ['loan_dep_bank_cd'.$j];		// 대출금입금은행코드$j
	$ARR['LOAN_DEP_ACCT_NB'.$j] = $REQ['loan_dep_acct_nb'.$j];		// 대출금입금계좌번호$j
	$ARR['LOAN_DEP_AMT'.$j]     = ($REQ['loan_dep_amt'.$j] > 0) ? $REQ['loan_dep_amt'.$j] : '';		// 대출금입금금액$j
}


// 동일 가상계좌를 사용하는 최초 상품(대출 등록 또는 실행이 된 상품) 가져오기
$PREV_PRDT = sql_fetch("SELECT idx FROM cf_product WHERE idx!='".$REQ['prd_idx']."' AND repay_acct_no='".$PRDT['repay_acct_no']."' AND ib_product_regist='Y' ORDER BY idx ASC LIMIT 1");
if($PREV_PRDT['idx']) {
	$INV_CUST_ID = $PREV_PRDT['idx'];
}
else {
	$INV_CUST_ID = ($PRDT['category']=='3') ?	$PRDT['gr_idx'] : "";		// 다중상품 동일가상계좌 이용은 매출채권상품에 대해서만 적용한다.
}
$ARR['INV_CUST_ID'] = $INV_CUST_ID;

//print_r($ARR); exit;

$RETURN_ARR = insidebank_request('256', $ARR);

if($RETURN_ARR['RCODE']=='00000000') {

	$VA_BANK_CODE = '088';

	// 가상계좌 최초 할당시
	if($vact_first_set) {
		// 유휴가상계좌(헬로크라우드대부)정보 할당
		$sql = "
			UPDATE
				IB_vact_hellocrowd
			SET
				FB_SEQ  = '".$RETURN_ARR['FB_SEQ']."',
				CUST_ID = '".$LOAN_CUST_ID."',
				cmf_nm  = '".$LOAN_CUST_NM."',
				acct_st = '1',
				open_il = '".date('Ymd')."'
			WHERE
				acct_no = '".$CMS_NB."'";
		$res = sql_query($sql);

		// KSNET 가상계좌원장 할당정보 기록
		$sql = "
			INSERT INTO
				KSNET_VR_ACCOUNT
			SET
				BANK_CODE  = '".$VA_BANK_CODE."',
				VR_ACCT_NO = '".$CMS_NB."',
				CORP_NAME  = '".$LOAN_CUST_NM."',
				USE_FLAG   = 'Y'";
		//$sql.= ($PRDT['idx']==$PRDT['gr_idx']) ? "" : ", REF_NO = '".$PRDT['idx'];		// KSNET_VR_ACCOUNT 가상계좌 참조번호 등록
		$res = sql_query($sql);
	}

	// 상품정보 수정
	$sql = "
		UPDATE
			cf_product
		SET
			ib_product_regist = 'Y',
			repay_acct_no     = '".$CMS_NB."', ";
	for($i=0,$j=1; $i<5; $i++,$j++) {
		$sql.= "loan_dep_bank_cd{$j} = '".$REQ['loan_dep_bank_cd'.$j]."', ";
		$sql.= "loan_dep_acct_nb{$j} = '".$REQ['loan_dep_acct_nb'.$j]."', ";
		$sql.= "loan_dep_amt{$j} = '".$REQ['loan_dep_amt'.$j]."', ";
		$sql.= "loan_dep_acct_memo{$j} = '".$REQ['loan_dep_acct_memo'.$j]."'";
		$sql.= ($j < 5) ? ',' : '';
	}
	$sql.=" WHERE idx = '".$REQ['prd_idx']."'";
	$res = sql_query($sql);

	echo "SUCCESS:" . $BANK[$VA_BANK_CODE] . " " . $CMS_NB;

	getBillTable($REQ['prd_idx']);		// 투자수익명세서 테이블 생성

}
else {
	echo $RETURN_ARR['ERRMSG'];
	echo ($RETURN_ARR['RCODE']) ? '('.$RETURN_ARR['RCODE'].')' : '';
}

exit;

?>