<?php
####################################################################################################
## 활성 일반 투자상품 가져오기
## * * * * * /usr/local/php/bin/php -q /홈디렉토리/xml/make_active_product_list.php
##  2018-01-12 수정. 투자모집완료된 상품은 즉시 빠지도록..
####################################################################################################

header("Cache-Control: no-cache, must-revalidate");
header('Pragma: no-cache');

$base_path = "/home/crowdfund/public_html";

include_once($base_path . '/common.php');


$special_product = "157,171,225,238";		// 출력차단 특수물건

$LIST = array();

$sql = "
	SELECT
		A.idx, A.ai_grp_idx, A.category, A.state, A.title, A.main_image, A.main_image_m,
		A.invest_return, A.invest_period, A.invest_days,
		A.recruit_amount, A.recruit_period_start, A.recruit_period_end, A.start_datetime,
		A.purchase_guarantees, A.advanced_payment,
		A.advance_invest, A.advance_invest_ratio, A.stream_url1, A.stream_url2,
		B.evaluate_score1, B.evaluate_score2, B.evaluate_score3, B.evaluate_score4,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx = A.idx AND invest_state='Y') AS total_invest_amount,
		(SELECT COUNT(product_idx) FROM cf_product_invest WHERE product_idx = A.idx AND invest_state='Y') AS total_invest_count
	FROM
		cf_product A
	LEFT JOIN
		cf_product_container B  ON A.idx=B.product_idx
	WHERE 1=1
		AND A.state=''
		AND A.display='Y'
		AND A.idx NOT IN(".$special_product.")
		AND A.invest_end_date=''
		-- AND A.end_date>=NOW()
	ORDER BY
		IFNULL(((total_invest_amount/A.recruit_amount)*100), 0) ASC,
		A.open_datetime DESC";
$res = sql_query($sql);
$rcount = sql_num_rows($res);
for($i=0; $i<$rcount; $i++) {
	$row = sql_fetch_array($res);
	$row['gubun'] = "normal";
	$row['detail_url'] = "/investment/investment.php?prd_idx=".$row['idx'];

	if($row['main_image']) {
		if(filesize(G5_DATA_PATH."/product/".$row['main_image'])) {
			$row['title_image_url'] = "/data/product/".$row['main_image'];
		}
	}

	if($row['main_image_m']) {
		if(filesize(G5_DATA_PATH."/product/".$row['main_image_m'])) {
			$row['title_image_url_m'] = "/data/product/".$row['main_image_m'];
		}
	}

	if($row["recruit_amount"]>0) {
		$row['invest_percent'] = ($row["total_invest_amount"]>0) ? round((($row["total_invest_amount"]/$row["recruit_amount"])*100),2) : 0;
	}
	else {
		$row['invest_percent'] = 0;
	}

	if(!$row['repay_count']) $row['repay_count'] = 0;

	$loan_start_date_day = (int)substr($row['loan_start_date'], -2);
	$row['total_repay_count'] = ($loan_start_date_day < 5) ? $row['invest_period'] : $row['invest_period'] + 1;  //총 지급횟수

	if($row['invest_period']==1 && $row['invest_days'] > 0) {
		$row['print_invest_period'] = $row['invest_days'] . "일";
	}
	else {
		$row['print_invest_period'] = $row['invest_period'] . "개월";
	}

	if($_REQUEST['mode']=='debug') {
		echo $row['invest_percent']."<br>\n";
	}

	if($row['invest_percent'] < 100) {
		array_push($LIST, $row);
	}
	/*
	else {
		// 종료된건도 오픈후 6시간동안은 출력
		$LAST_INVEST = sql_fetch("SELECT insert_date, insert_time FROM cf_product_invest WHERE product_idx='".$row['idx']."' AND invest_state='Y' ORDER BY idx DESC LIMIT 1");
		$last_invest_datetime = $LAST_INVEST['insert_date'] . " " . $LAST_INVEST['insert_time'];
		$target_datetime = date("Y-m-d H:i:s", strtotime($last_invest_datetime) + 3600*6);
		//echo $target_datetime . " >= " . G5_TIME_YMDHIS . " " . ($target_datetime >= G5_TIME_YMDHIS) . "\n";
		if( $target_datetime >= G5_TIME_YMDHIS ) {
			array_push($LIST, $row);
		}
	}
	*/
}

$sql = $res = $row = NULL;

///////////////////////////////////////////////////////////////////////////////////////////////////
// 활성 이벤트 상품 가져오기
///////////////////////////////////////////////////////////////////////////////////////////////////
$sql = "
	SELECT
		EV.idx, EV.category, EV.state, EV.title, EV.main_image, EV.main_image_m,
		EV.invest_amount, EV.invest_profit, EV.invest_return, EV.total_return_amount, EV.start_datetime,
		EV.recruit_amount, EV.recruit_period_start, EV.recruit_period_end,
		EV.evaluate_score1, EV.evaluate_score2, EV.evaluate_score3, EV.evaluate_score4,
		(SELECT IFNULL(SUM(amount),0) FROM cf_event_product_invest WHERE product_idx = EV.idx AND invest_state='Y') AS total_invest_amount,
		(SELECT COUNT(product_idx) FROM cf_event_product_invest WHERE product_idx = EV.idx AND invest_state='Y') AS total_invest_count
	FROM
		cf_event_product EV
	WHERE 1=1
		AND EV.display='Y'
		AND EV.end_date >= NOW()
	ORDER BY
		EV.open_datetime DESC, EV.idx DESC";
$res = query($sql);
$rcount = sql_num_rows($res);
for($i=0; $i<$rcount; $i++) {
	$row = sql_fetch_array($res);
	$row['gubun'] = "event";
	$row['detail_url'] = "/event_invest/event_invest.php?prd_idx=".$row['idx'];

	if($row['main_image']) {
		if(filesize(G5_DATA_PATH."/product_special/".$row['main_image'])) {
			$row['title_image_url'] = "/data/product_special/".$row['main_image'];
		}
	}

	if($row['main_image_m']) {
		if(filesize(G5_DATA_PATH."/product_special/".$row['main_image_m'])) {
			$row['title_image_url_m'] = "/data/product_special/".$row['main_image_m'];
		}
	}

	if($row["recruit_amount"]>0) {
		$row['invest_percent'] = ($row["total_invest_amount"]>0) ? round((($row["total_invest_amount"]/$row["recruit_amount"])*100),2) : 0;
	}
	else {
		$row['invest_percent'] = 0;
	}

	$row['print_invest_period'] = ceil(((strtotime($row["recruit_period_end"]) - strtotime($row["recruit_period_start"]))+86400) / 86400) . "일";

	if($row['invest_percent'] < 100) {
		array_push($LIST, $row);
	}
	else {
		// 종료된건도 오픈후 6시간동안은 출력
		$LAST_INVEST = sql_fetch("SELECT insert_date, insert_time FROM cf_event_product_invest WHERE product_idx='".$row['idx']."' AND invest_state='Y' ORDER BY idx DESC LIMIT 1");
		$last_invest_datetime = $LAST_INVEST['insert_date'] . " " . $LAST_INVEST['insert_time'];
		$target_datetime = date("Y-m-d H:i:s", strtotime($last_invest_datetime) + 3600*6);
		//echo $target_datetime . " >= " . G5_TIME_YMDHIS . " " . ($target_datetime >= G5_TIME_YMDHIS) . "\n";
		if( $last_invest_datetime >= G5_TIME_YMDHIS ) {
			array_push($LIST, $row2);
		}
	}
}

$sql = $res = $row = NULL;

//echo "<pre style='fon-size:8pt'><xmp>";
//print_r($LIST);
//echo "</xmp></pre>";



///////////////////////////////////////////////////////////////////////////////////////////////////
// XML 생성
///////////////////////////////////////////////////////////////////////////////////////////////////

$version = date(YmdHi);

$str = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$str.= "<result>\n";
$str.= "	<version>".$version."</version>\n";
$str.= "	<product_count>".count($LIST)."</product_count>\n";
$str.= "	<product_list>\n";
for($i=0; $i<count($LIST); $i++) {

	$PRDT_STATE = getProductStat($LIST[$i]['idx']);
	echo "(".$PRDT_STATE['code'].")";
	switch($PRDT_STATE['code']) {
		case "A01" :
			$button_caption = $PRDT_STATE['code_str'];
			$cover_caption  = "펀딩성공";
		break;
		case "A02" :
			$button_caption = '원금상환완료';
			$cover_caption  = "펀딩성공";
		break;
		case "A03" :
			$button_caption = $PRDT_STATE['code_str'];
			$cover_caption  = "펀딩종료";
		break;
		case "A04" :
			$button_caption = $PRDT_STATE['code_str'];
			$cover_caption  = "펀딩종료";
		break;
		case "A05" :
			$button_caption = "원금상환완료";
			$cover_caption  = "펀딩성공";
		break;
		case "B00" :
			$button_caption = '상품상세보기';
			$cover_caption  = "";
		break;
		case "B01" :
			$button_caption = '상품상세보기';
			$cover_caption  = "";
		break;
		case "B02" :
			$button_caption = '상품상세보기';
			$cover_caption  = "";
		break;
		case "B03" :
			$button_caption = $PRDT_STATE['code_str'];
			$cover_caption  = "펀딩성공";
		break;
		case "B04" :
			$button_caption = $PRDT_STATE['code_str'];
			$cover_caption  = "펀딩종료";
		break;
		default    :
			$button_caption = '상품상세보기';
			$cover_caption  = "";
		break;
	}

	$auto_invest = ($LIST[$i]['ai_grp_idx']>0) ? '1' : '';

	$str.= "	<product>\n";
	$str.= "		<gubun>".$LIST[$i]['gubun']."</gubun>\n";
	$str.= "		<idx>".$LIST[$i]['idx']."</idx>\n";
	$str.= "		<category>".$LIST[$i]['category']."</category>\n";
	$str.= "		<state>".$LIST[$i]['state']."</state>\n";
	$str.= "		<title><![CDATA[".$LIST[$i]['title']."]]></title>\n";
	$str.= "		<title_image_url><![CDATA[".$LIST[$i]['title_image_url']."]]></title_image_url>\n";
	$str.= "		<title_image_url_m><![CDATA[".$LIST[$i]['title_image_url_m']."]]></title_image_url_m>\n";
	$str.= "		<detail_url><![CDATA[".$LIST[$i]['detail_url']."]]></detail_url>\n";

	$str.= "		<invest_amount>".$LIST[$i]['invest_amount']."</invest_amount>\n";
	$str.= "		<invest_profit>".$LIST[$i]['invest_profit']."</invest_profit>\n";
	$str.= "		<invest_return>".$LIST[$i]['invest_return']."</invest_return>\n";
	$str.= "		<total_return_amount>".$LIST[$i]['total_return_amount']."</total_return_amount>\n";

	$str.= "		<recruit_amount>".$LIST[$i]['recruit_amount']."</recruit_amount>\n";
	$str.= "		<recruit_period_start>".$LIST[$i]['recruit_period_start']."</recruit_period_start>\n";
	$str.= "		<recruit_period_end>".$LIST[$i]['recruit_period_end']."</recruit_period_end>\n";
	$str.= "		<start_datetime>".$LIST[$i]['start_datetime']."</start_datetime>\n";
	$str.= "		<evaluate_score1>".$LIST[$i]['evaluate_score1']."</evaluate_score1>\n";
	$str.= "		<evaluate_score2>".$LIST[$i]['evaluate_score2']."</evaluate_score2>\n";
	$str.= "		<evaluate_score3>".$LIST[$i]['evaluate_score3']."</evaluate_score3>\n";
	$str.= "		<evaluate_score4>".$LIST[$i]['evaluate_score4']."</evaluate_score4>\n";

	$str.= "		<purchase_guarantees>".$LIST[$i]['purchase_guarantees']."</purchase_guarantees>\n";
	$str.= "		<advanced_payment>".$LIST[$i]['advanced_payment']."</advanced_payment>\n";
	$str.= "		<auto_invest>".$auto_invest."</auto_invest>\n";
	$str.= "		<total_invest_amount>".$LIST[$i]['total_invest_amount']."</total_invest_amount>\n";
	$str.= "		<invest_percent>".$LIST[$i]['invest_percent']."</invest_percent>\n";
	$str.= "		<invest_period><![CDATA[".$LIST[$i]['print_invest_period']."]]></invest_period>\n";

	$str.= "		<state_code>".$PRDT_STATE['code']."</state_code>\n";
	$str.= "		<detail_state><![CDATA[".$PRDT_STATE['code_str']."]]></detail_state>\n";
	$str.= "		<button_caption><![CDATA[".$button_caption."]]></button_caption>\n";
	$str.= "		<cover_caption><![CDATA[".$cover_caption."]]></cover_caption>\n";

	$str.= "		<advance_invest>".$LIST[$i]['advance_invest']."</advance_invest>\n";
	$str.= "		<advance_invest_ratio>".$LIST[$i]['advance_invest_ratio']."</advance_invest_ratio>\n";

	$str.= "		<stream_url1><![CDATA[".$LIST[$i]['stream_url1']."]]></stream_url1>\n";
	$str.= "		<stream_url2><![CDATA[".$LIST[$i]['stream_url2']."]]></stream_url2>\n";

	$str.= "	</product>\n";

}
//$str.= "  <fl>".$_SERVER['PHP_SELF']."</fl>\n";
$str.= "	</product_list>\n";
$str.= "</result>\n";

echo $str;

$xml_path = $base_path.'/xml/active_product_list.xml';
if(!@file_exists($xml_path)) {
	exec("touch " . $xml_path);
}

if(file_put_contents($xml_path, $str)) {
	echo "XML SUCCESS: " . $version . "\n";
}

?>