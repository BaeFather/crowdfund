#!/usr/local/php/bin/php -c /etc/php.ini -q
<?
###############################################################################
## 1. 투자상품리스트 (상품오픈시 보냄)
##		crontab으로 1분마다 실행 (주말제외)
##		* * * * * /usr/local/php/bin/php -q /home/crowdfund/public_html/syndicate/oligo/report/productListReport.php yes
##		전송URL : https://m.mycereal.co.kr:8443/matcs/external/api/setProductListNew.do
###############################################################################

$base_path = "/home/crowdfund/public_html/syndicate/oligo";
include_once($base_path . "/syndication_config.php");

$action = (@$_SERVER['argv'][1]) ? @$_SERVER['argv'][1] : 'debug';


$ARR['comp_cd'] = $_CONF['comp_cd'];								// 제휴업체 코드값
$ARR['call_cd'] = ((date('i')%5)==0)  ? 'I' : 'U';	// 구분: I:Insert|U:update
$ARR['prod_list'] = array();


//$where = "1=1";
$where = "1=false";
//$where.= " AND A.state IN('','1','2','5','8','9') ";														// 투자실패건을 제외한
$where.= " AND A.display='Y' AND A.isTest='' AND A.only_vip='' ";									// 기본출력이 허용된, 테스트상품이 아닌, 특별투자상품이 아닌
$where.= " AND A.scrap_out='' AND A.platform LIKE '%".$_CONF['SYNDI_ID']."%' ";		// 외부스크래핑이 허용된 상품중 당 신디케이션사에게 노출이 허락된
$where.= " AND A.open_datetime <= NOW() ";																				// 노출시작시간이 경과되지 않은
$where.= " AND A.end_datetime >= NOW() ";																					// 모집종료시간이 경과되지 않은
$where.= " AND A.invest_end_date=''";																							// 투자 완료가 되지 않은 상품 리스트

$sql = "
	SELECT
		A.idx, A.state, A.category, A.mortgage_guarantees, A.display, A.title, A.recruit_amount, A.invest_period, A.invest_days, A.invest_return,
		A.open_datetime, A.start_datetime, A.end_date, A.invest_end_date, A.loan_start_date, A.loan_end_date,
		A.main_image, A.main_image_m,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_count,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_amount
	FROM
		cf_product A
	WHERE
		$where
	ORDER BY
		A.start_num DESC,
		A.idx DESC";
if($action=='debug') { echo $sql . "\n\n"; }
$res = sql_query($sql);
$rows = sql_num_rows($res);

$x = 0;
for($i=0; $i<$rows; $i++) {

	if( $ROW = sql_fetch_array($res) ) {

		//print_rr($ROW, 'font-size:12px;line-height:13px');

		$status = getProductStatOligo($ROW['idx']);

		if($ROW['category']=='1') {
			$prod_cate = "movable";		// 동산 카테고리 추가 : 2020-01-28
		}
		if($ROW['category']=='2') {
			$prod_cate = "real";
		}
		else {
			$prod_cate = "movable";
		//$prod_cate = "etc";
		}

		$LOAN_START_DATE = explode(" ", preg_replace("/(-|:)/", "", $ROW['start_datetime']));
		$close_dt    = preg_replace("/(-|:)/", "", $ROW['end_date']);
		$rate        = floatRtrim($ROW['invest_return']);
		$invest_perc = @floor(($ROW['invest_amount'] / $ROW['recruit_amount']) * 100);
		$img_url     = ($ROW['main_image'] || $ROW['main_image_m']) ? $_CONF['host_domain']."/oligo/image/". $ROW['idx'] : "";
		$prod_url    = $_CONF['host_domain']."/oligo/product/".$ROW['idx'];

		if($ROW['loan_end_date'] > '0000-00-00') {
			$loan_end_date = preg_replace("/-/", "", $ROW['loan_end_date']);
		}

		if($ROW['invest_days'] > 0) {
			// 일단위 상품
			$inve_term_kn = 'D';
			$invest_period = $ROW['invest_days'];
		}
		else {
			// 개월단위 상품
			$inve_term_kn = 'M';
			$invest_period = $ROW['invest_period'];
		}


		$DATA['prod_cd']         = $ROW['idx'];											// 상품코드
		$DATA['prod_nm']         = $ROW['title'];										// 상품명
		$DATA['status']          = $status;													// 상태값: 01:모집예정|02:모집중|03:모집취소|04:모집완료|05:상환중|06:상환완료|07:상환지연|08:연체중(단기)|09:연체중(장기)|10:부도|11:상환완료(연체)
		$DATA['prod_cate']       = $prod_cate;											// 상품카테고리: cred:신용|real:부동산|bond:어음|etc:기타
		$DATA['open_dt']         = (string)$LOAN_START_DATE[0];			// 투자오픈일->헬로기준은 투자모집시작일: YYYYMMDD
		$DATA['open_time']       = (string)$LOAN_START_DATE[1];			// 투자오픈일: HHMISS
		$DATA['close_dt']        = (string)$close_dt;								// 모집마감일: YYYYMMDD
		$DATA['tot_amt']         = (string)$ROW['recruit_amount'];	// 총모집금액
		$DATA['rate']            = (string)$rate;										// 연수익률
		$DATA['inve_term_kn']    = $inve_term_kn;										// 투자기간구분 (월단위 M / 일단위 D)
		$DATA['inve_term']       = (string)$invest_period;					// 투자기간 (월단위 상품->개월수, 일단위 상품->투자일수)
		$DATA['inve_num']        = (string)$ROW['invest_count'];		// 투자자수 (숫자만)
		$DATA['inve_amt']        = (string)$ROW['invest_amount'];		// 투자모집금액 (실시간 반영된 투자모집금액-숫자만)
		$DATA['inve_rate']       = (string)$invest_perc;						// 투자모집률 (숫자만)
		$DATA['inve_payback_dt'] = (string)$loan_end_date;					// 투자상환일 : YYYYMMDD
		$DATA['img_url']         = $img_url;												// 상품이미지url
		$DATA['prod_url']        = $prod_url;												// 상품상세url
		$DATA['view_yn']         = $ROW['display'];									// 상품노출여부: 없으면 Y로 설정


		$ARR['prod_list'][$x]['basic_info'] = $DATA;

		unset($DATA);
		unset($ROW);
		$rate = $close_dt = $rate = $invest_perc = $img_url = $prod_url = $loan_end_date = $inve_term_kn = $invest_period = NULL;

		$x++;

	}

}

if(count($ARR['prod_list'])) {

	///////////////////////////////////////////////////////////////////////////////
	// 레포팅 및 로그 기록
	///////////////////////////////////////////////////////////////////////////////
	$url = $_CONF['syndi_url'] . "/matcs/external/api/setProductListNew.do";

	$exec_string = "curl -X POST";
	$exec_string.= " -k -s";
	$exec_string.= " -H 'Expect:'";
	$exec_string.= " -H 'Content-Type: application/json'";
	$exec_string.= " -A 'Mozilla/5.0'";
	$exec_string.= " -d '".printJson($ARR)."'";
	$exec_string.= " " . $url;

	$log_table = 'oligo_send_report_log_' . date('Ym');

	// 발송로그기록시작
	$log_res = sql_query("
		INSERT INTO
			{$log_table}
		SET
			title = '신규상품목록전송',
			path  = '/syndicate/oligo/report/productListReport.php',
			input = '".sql_real_escape_string($exec_string)."',
			rdate = NOW()");
	$log_idx = sql_insert_id();

	// 실행
	$exec_result = @exec($exec_string);

	// 발송결과 저장
	$log_res = sql_query("UPDATE {$log_table} SET output='".sql_real_escape_string($exec_result)."', edate=NOW() WHERE idx='".$log_idx."'");

}


sql_close();
exit;

?>