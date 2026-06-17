#!/usr/local/php/bin/php -c /etc/php.ini -q
<?
###############################################################################
## 2. 투자상품상태업데이트
##		/usr/local/php/bin/php -q /home/crowdfund/public_html/syndicate/oligo/report/productStateReport.php {상품번호}
##		- 관리자페이지에서 상품상태값 수정시 본 파일을 실행하여, 상태값을 전달할 것.
##		전송URL : https://m.mycereal.co.kr:8443/matcs/external/api/updateProductStat.do
###############################################################################

$prd_idx = (@$_SERVER['HTTP_USER_AGENT']) ? @$_REQUEST['prd_idx'] : $_SERVER['argv'][1];

if( !trim($prd_idx) ) { exit; }

$base_path = "/home/crowdfund/public_html/syndicate/oligo";
include_once($base_path . "/syndication_config.php");

$now_datetime = date("Y-m-d H:i:s");

//$where = "1=1";
$where = " 1=false";
//$where.= " AND A.state IN('','1','2','5','8','9') ";														// 투자실패건을 제외한
$where.= " AND A.display='Y' AND A.isTest='' AND A.only_vip=''";									// 기본출력이 허용된, 테스트상품이 아닌, 특별투자상품이 아닌
$where.= " AND A.idx='".$prd_idx."'";

$sql = "
	SELECT
		A.idx, A.recruit_amount, A.platform,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS total_invest_cnt,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS total_invest_amt
	FROM
		cf_product A
	WHERE
		$where";

if( $PRDT = sql_fetch($sql) ) {

	$inve_rate = @floor(($PRDT['total_invest_amt'] / $PRDT['recruit_amount']) *100);
	$status    = getProductStatOligo($PRDT['idx']);

	$ARR['comp_cd']   = (string)$_CONF['comp_cd'];						// 제휴업체 코드값
	$ARR['prod_cd']   = (string)$PRDT['idx'];									// 상품코드
	$ARR['status']	  = (string)$status;											// 상태값 ( 01:모집예정/02:모집중/03:모집취소/04:모집완료/05:상환중/06:상환완료/07:상환지연/08:연체중(단기)/09:연체중(장기)/10:부도/11:상환완료(연체) )
	$ARR['inve_num']  = (string)$PRDT['total_invest_cnt'];		// 투자인원
	$ARR['inve_amt']  = (string)$PRDT['total_invest_amt'];		// 투자모집금액
	$ARR['inve_rate']	= (string)$inve_rate;										// 투자모집률(숫자만)

	//print_r($ARR);

	///////////////////////////////////////////////////////////////////////////////
	// 레포팅 및 로그 기록
	///////////////////////////////////////////////////////////////////////////////
	$url = $_CONF['syndi_url'] . "/matcs/external/api/updateProductStat.do";

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
			title = '상품정보업데이트',
			path  = '/syndicate/oligo/report/productStateReport.php',
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