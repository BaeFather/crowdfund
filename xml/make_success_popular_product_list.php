<?php
####################################################################################################
## 0 10,14,18,23 * * * /usr/local/php/bin/php -q /홈디렉토리/xml/make_success_popular_product_list.php
####################################################################################################

header("Cache-Control: no-cache, must-revalidate");
header('Pragma: no-cache');

$base_path = "/home/crowdfund/public_html";

include_once($base_path . '/common.php');

$LIST = array();

///////////////////////////////////////////////////////////////////////////////////////////////////
// 활성 일반 투자상품 가져오기
///////////////////////////////////////////////////////////////////////////////////////////////////
$sql = "
	SELECT
		A.*,
		(SELECT IFNULL(sum(amount),0) FROM cf_product_invest WHERE A.idx = product_idx AND invest_state='Y') AS total_invest_amount,
		(SELECT COUNT(product_idx) AS total_invest_count FROM cf_product_invest WHERE A.idx = product_idx AND invest_state='Y') AS total_invest_count,
		(SELECT MAX(turn) as max_turn FROM cf_product_success WHERE product_idx=A.idx AND invest_give_state='Y') AS repay_count
	FROM
		cf_product A
	WHERE 1=1
		AND A.display='Y'
		AND A.idx NOT IN('157','171','225')
		AND A.popular_goods='Y'
	ORDER BY
		A.open_datetime DESC, A.idx DESC";
$res = sql_query($sql);
$rcount = sql_num_rows($res);
for($i=0; $i<$rcount; $i++) {
	$row2 = sql_fetch_array($res);
	$row2['gubun'] = "normal";
	$row2['detail_url'] = "/investment/investment.php?prd_idx=".$row2['idx'];

	if($row2['main_image']) {
		if(file_exists(G5_DATA_PATH."/product/".$row2['main_image'])){
			if(filesize(G5_DATA_PATH."/product/".$row2['main_image'])) {
				$row2['title_image_url'] = "/data/product/".$row2['main_image'];
			}
		}
	}

	if($row2['main_image_m']) {
		if(file_exists(G5_DATA_PATH . "/product/" . $row2['main_image_m'])) {
			if (filesize(G5_DATA_PATH . "/product/" . $row2['main_image_m'])) {
				$row2['title_image_url_m'] = "/data/product/" . $row2['main_image_m'];
			}
		}
	}

	if($row2["recruit_amount"]>0) {
		$row2['invest_percent'] = ($row2["total_invest_amount"]>0) ? round((($row2["total_invest_amount"]/$row2["recruit_amount"])*100),2) : 0;
	}
	else {
		$row2['invest_percent'] = 0;
	}

	if(!$row2['repay_count']) $row2['repay_count'] = 0;

	$loan_start_date_day = (int)substr($row2['loan_start_date'], -2);
	$row2['total_repay_count'] = ($loan_start_date_day < 5) ? $row2['invest_period'] : $row2['invest_period'] + 1;  //총 지급횟수

	$row2['invest_period']    = ($row2['invest_period']==1 && $row2['invest_days']>0) ? $row2['invest_days']."일" : $row2['invest_period']."개월";		// 투자개월이 1개월 미만인 경우 일수로 표시
	$row2["auto_invest_flag"] = ($row2['ai_grp_idx']) ? 'Y' : 'N';
	$row2["new_flag"] = (G5_TIME_YMD <= date('Y-m-d', strtotime('+5day', strtotime($row2['open_datetime']))) && ($row2['recruit_amount'] > $row2['total_invest_amount'])) ? 'Y' : 'N';

	array_push($LIST, $row2);

}

$sql = $res = $row = NULL;


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
		default	:
			$button_caption = '상품상세보기';
			$cover_caption  = "";
		break;
	}


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
	$str.= "		<total_invest_amount>".$LIST[$i]['total_invest_amount']."</total_invest_amount>\n";
	$str.= "		<invest_percent>".$LIST[$i]['invest_percent']."</invest_percent>\n";
	$str.= "		<invest_period><![CDATA[".$LIST[$i]['invest_period']."]]></invest_period>\n"; // 투자기간
	$str.= "		<total_repay_count>".$LIST[$i]['total_repay_count']."</total_repay_count>\n";
	$str.= "		<repay_count>".$LIST[$i]['repay_count']."</repay_count>\n";

	$str.= "		<detail_state><![CDATA[".$PRDT_STATE['code_str']."]]></detail_state>\n";
	$str.= "		<button_caption><![CDATA[".$button_caption."]]></button_caption>\n";
	$str.= "		<cover_caption><![CDATA[".$cover_caption."]]></cover_caption>\n";

	$str.= "		<stream_url1><![CDATA[".$LIST[$i]['stream_url1']."]]></stream_url1>\n";
	$str.= "		<stream_url2><![CDATA[".$LIST[$i]['stream_url2']."]]></stream_url2>\n";

	$str.= "		<auto_invest_flag>".$LIST[$i]['auto_invest_flag']."</auto_invest_flag>\n";
	$str.= "		<new_flag>".$LIST[$i]['new_flag']."</new_flag>\n";

	$str.= "	</product>\n";

}
$str.= "	</product_list>\n";
$str.= "</result>\n";

echo $str;

$xml_path = $base_path.'/xml/success_popular_product_list.xml';
if(!@file_exists($xml_path)) {
	exec("touch " . $xml_path);
}

if(file_put_contents($xml_path, $str)) {
	echo "XML SUCCESS: " . $version . "\n";
}

?>