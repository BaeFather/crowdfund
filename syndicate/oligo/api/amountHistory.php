<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/amountHistory.do
## 12. 투자금거래내역 (작업완료)
##
##	trans_kn(거래구분) 예시
##		입금(01)       -> 상품 투자금
##		수익금지급(02) -> 이자 지급 (부동산)
##		원금지급(03)   -> 원금 지급 (부동산)
##		상환금지급(04) -> 원금+이자 (어음)
##		투자취소(05)   -> 상품취소 (여러가지 사유로)
##		투자(06)       -> 상품 투자금 투자시작
##		출금(07)       -> 사용자 출금
##		플랫폼 이용료(08)
##		기타 수수료(09)
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST['ci']           = 'INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==';		//*** 복호화 필요
$REQUEST['search_st_dt'] = '20191101';		// 검색시작일
$REQUEST['search_ed_dt'] = '20191230';		// 검색종료일
$REQUEST['start_num']    = '10';					// 시작번호
$REQUEST['end_num']      = '20';					// 종료번호
*/

$REQUEST['ci'] = urldecode($REQUEST['ci']);

$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }

$MB = get_member($mb_id);

// 각 항목별 복호화 시간이 너무 오래 걸리므로 한번 복호화 후 변수처리함.
//$enc_acc  = $MB['account_num'];
//$enc_vacc = $MB['virtual_account2'];
$enc_acc  = $crypto->enCrypt($MB['account_num']);
$enc_vacc = $crypto->enCrypt($MB['virtual_account2']);


$where = "";

if($REQUEST['search_st_dt'] && $REQUEST['search_ed_dt']) {

	if($REQUEST['search_st_dt'] > $REQUEST['search_ed_dt']) { $ARR = array("code"=>'9999', "msg"=>"검색기간 설정이 정상적이지 않습니다."); echo printJson($ARR); exit; }

	$search_st_dt = substr($REQUEST['search_st_dt'],0,4) . "-" . substr($REQUEST['search_st_dt'],4,2) . "-" . substr($REQUEST['search_st_dt'],6);
	$search_ed_dt = substr($REQUEST['search_ed_dt'],0,4) . "-" . substr($REQUEST['search_ed_dt'],4,2) . "-" . substr($REQUEST['search_ed_dt'],6);

	$where.= " AND LEFT(po_datetime,10) BETWEEN '".$search_st_dt."' AND '".$search_ed_dt."'";

}
else {

	if($REQUEST['search_st_dt']) {
		$search_st_dt = substr($REQUEST['search_st_dt'],0,4) . "-" . substr($REQUEST['search_st_dt'],4,2) . "-" . substr($REQUEST['search_st_dt'],6);
		$where.= " AND LEFT(po_datetime,10) >= '".$search_st_dt."'";
	}
	if($REQUEST['search_ed_dt']) {
		$search_ed_dt = substr($REQUEST['search_ed_dt'],0,4) . "-" . substr($REQUEST['search_ed_dt'],4,2) . "-" . substr($REQUEST['search_ed_dt'],6);
		$where.= " AND LEFT(po_datetime,10) <= '".$search_ed_dt."'";
	}

}




$sql = "
	SELECT
		po_id, po_datetime, po_content, po_point, po_mb_point, po_rel_table
	FROM
		g5_point
	WHERE 1
		AND mb_no='".$MB['mb_no']."'
		$where
	ORDER BY
		po_datetime DESC";
if($REQUEST['start_num'] && $REQUEST['end_num']) {
	$start_num = $REQUEST['start_num'] - 1;
	$get_row = $REQUEST['end_num'] - $REQUEST['start_num'];
	$sql.= " LIMIT ".$start_num.", " . $get_row;
}
//if($_SERVER['REMOTE_ADDR']=='211.248.149.48') { echo $sql; exit; }

$res  = sql_query($sql);
$rows = sql_num_rows($res);

$LIST = array();
$x = 0;
for($i=0; $i<$rows; $i++) {

	$row = sql_fetch_array($res);
	if($row['po_rel_table']) {

		$po_datetime = explode(" ", preg_replace("/(-|:)/", "", $row['po_datetime']));

		$LIST[$x]['trans_dt'] = $po_datetime[0];
		$LIST[$x]['trans_tm'] = $po_datetime[1];

		$acc_no = "";

		if( in_array($row['po_rel_table'], array('@deposit','@charge')) ) {
			$trans_kn  = '01';		// 입금
			$txt       = "입금 : " . $row['po_content'];
			$bank_code = $MB['va_bank_code2'];
			$acc_no    = $enc_vacc;
		}
		else if($row['po_rel_table']=='@repay') {
			$trans_kn  = '02';		// 수익금지급
			$txt       = "수익금지급 : " . $row['po_content'];
			$bank_code = $MB['va_bank_code2'];
			$acc_no    = $enc_vacc;
		}
		else if( in_array($row['po_rel_table'], array('@cancel','@loaner_cancel','@return')) ) {
			$trans_kn  = '05';		// 투자취소
			$txt       = "투자취소 : " . $row['po_content'];
			$bank_code = $MB['va_bank_code2'];
			$acc_no    = $enc_vacc;
		}
		else if($row['po_rel_table']=='@invest') {
			$trans_kn  = '06';		// 투자
			$txt       = "투자 : " . $row['po_content'];
			$bank_code = $MB['va_bank_code2'];
			$acc_no    = $enc_vacc;
		}
		else if( in_array($row['po_rel_table'], array('@withdrawal','@discharge')) ) {
			$trans_kn  = '07';		// 출금
			$txt       = "출금 : " . $row['po_content'];
			$bank_code = $MB['bank_code'];
			$acc_no    = $enc_acc;
		}
		/*
		else if($row['po_rel_table']=='') {
			$trans_kn  = '03';		// 원금지급
			$txt       = '원금지급';
			$bank_code = $MB['va_bank_code2'];
			$acc_no    = $enc_vacc;
		}
		else if($row['po_rel_table']=='') {
			$trans_kn  = '04';		// 상환금지급
			$txt       = '상환금지급';
			$bank_code = $MB['va_bank_code2'];
			$acc_no    = $enc_vacc;
		}
		else if($row['po_rel_table']=='') {
			$trans_kn  = '08';		// 플랫폼 이용료
			$txt       = '플랫폼 이용료';
			$bank_code = $MB['va_bank_code2'];
			$acc_no    = $enc_vacc;
		}
		else if($row['po_rel_table']=='') {
			$trans_kn  = '09';		// 기타 수수료
			$txt       = '기타 수수료';
			$bank_code = $MB['va_bank_code2'];
			$acc_no    = $enc_vacc;
		}
		*/
		$LIST[$x]['trans_kn']    = $trans_kn;
		$LIST[$x]['txt']         = $txt;
		$LIST[$x]['trans_amt']   = abs($row['po_point']);
		$LIST[$x]['current_amt'] = (string)max(array(0, $row['po_mb_point']));
		$LIST[$x]['bank_code']   = $bank_code;
		$LIST[$x]['acc_no']      = $acc_no;

		$x += 1;

	}

}



$ARR['code'] = '0000';									// 결과코드
$ARR['msg']  = '정상처리되었습니다.';		// 메세지

//$ARR['data']
$ARR['data']['comp_cd']     = $_CONF['comp_cd'];						// 제휴코드
$ARR['data']['tot_cnt']     = (string)$rows;								// 조회건수
$ARR['data']['current_amt'] = (string)$MB['mb_point'];			// 현재잔액

$ARR['data']['acc_trans_list'] = array();									// [거래내역 배열 시작]

$list_count = count($LIST);
for($i=0; $i<$list_count; $i++) {
	$ARR['data']['acc_trans_list'][$i]['trans_dt']    = (string)$LIST[$i]['trans_dt'];			// 거래일시(YYYYMMDD)
	$ARR['data']['acc_trans_list'][$i]['trans_tm']    = (string)$LIST[$i]['trans_tm'];			// 거래시간(HHMMSS)
	$ARR['data']['acc_trans_list'][$i]['trans_kn']    = $LIST[$i]['trans_kn'];							// 거래구분
	$ARR['data']['acc_trans_list'][$i]['txt']         = $LIST[$i]['txt'];										// 거래구분 KOR
	$ARR['data']['acc_trans_list'][$i]['trans_amt']   = (string)$LIST[$i]['trans_amt'];			// 거래금액
	$ARR['data']['acc_trans_list'][$i]['current_amt'] = (string)$LIST[$i]['current_amt'];		// 현재잔액
	$ARR['data']['acc_trans_list'][$i]['bank_cd']     = (string)$LIST[$i]['bank_code'];			// 은행코드
	$ARR['data']['acc_trans_list'][$i]['acc_no']      = $LIST[$i]['acc_no'];				// 계좌번호 : 암호화(AES256)
}


//print_r($ARR); exit;

##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>