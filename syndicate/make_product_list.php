<?
################################################################################
## 투모다 정보제공용 json 파일 생성
## php -q /home/crowdfund/public_html/syndicate/make_product_list.php
################################################################################

$base_path = "/home/crowdfund/public_html";

include_once($base_path.'/common.php');

define(BASE_URL, "https://www.hellofunding.co.kr:443");

/*
{
"product": [
	{
	"open_date": "2016-01-07", //투자 시작일
	"close_date" : "2016-02-07" , // 투자 마감일
	"already_amount": 200000000, //현재 투자 금액 (원)
	"image": http://www.toomoda.com/web/images/main/logo.png, // 16:9 비율의 투자 상품 이미지 , 앱에서 자동 리사이징
	"amount": 200000000, //총 투자 금액 (원)
	"period_code": 24, //투자 기간 (개월)
	"product_uid": 139, // 상품 고유 코드 (PK)
	"type": "portfolio", //상품 타입 , 이 부분은 알려주시면 반영하겠습니다. 일반 단일 상품은 single 입니다. [ “portfolio” | “single” ]
	"state_code": "success_and_done", // 상태값 , [ "ready" | "progress" | "success_and_done" | "cancel" ] -> [ “준비중” | “투자진행중” | “투자성공” | “취소” ]
	"product_name": "투자 상품명", //상품명
	"return_of_rate": 8.3, //연이율 (%)
	"url": "http://www.toomoda.com/?product_uid=139", //링크될 상품 상세 코드(고유하여야 합니다)
	"cm_level": 5, //KCB 또는 나이스 신용 등급 (있을 경우)
	"cm_comp" : "NICE신용정보", //신용 등급 평가 기관 (있을 경우)
	"custom_level" : "B+” // 자체 신용 등급 (있을 경우)
	},
	{
	"open_date": "2016-01-07", //투자 시작일
	"close_date" : "2016-02-07" , // 투자 마감일
	"already_amount": 200000000, //현재 투자 금액
	"image": "http://www.toomoda.com/web/images/main/logo.png", // 16:9 비율의 투자 상품 이미지 , 앱에서 자동 리사이징
	"amount": 200000000, //총 투자 금액
	"period_code": 24, //투자 기간
	"product_uid": 139, // 상품 고유 코드 (PK)
	"type": "portfolio", //상품 타입 , 이 부분은 알려주시면 반영하겠습니다
	"state_code": "success_and_done", // 상태값 , [ "ready" | "progress" | "success_and_done" | "cancel" ]
	"product_name": "투자 상품명", //상품명
	"return_of_rate": 8.3, //연이율
	"url": "http://www.toomoda.com/?product_uid=139", //링크될 상품 상세 코드
	"cm_level": 5, //KCB 또는 나이스 신용 등급 (있을 경우)
	"cm_comp" : "NICE신용정보", //신용 등급 평가 기관 (있을 경우)
	"custom_level" : "B+” // 자체 신용 등급 (있을 경우)
	}
	],
	"version": "0.0.1" // 최근 100개의 상품 정도가 적당합니다. 지나간 상품의 경우 투모다에서 완료처리가 필요하므로
}
*/


$version = date(YmdH);

$sql = "
	SELECT
		A.*,
		(SELECT SUM(amount) FROM cf_product_invest WHERE A.idx = product_idx AND invest_state='Y') AS total_invest_amount
	FROM
		cf_product A
	WHERE 1
		AND A.category < '3'
		AND A.display = 'Y'
		AND A.idx NOT IN('157','171')
	ORDER BY
		A.idx DESC
	LIMIT 100";

$res = sql_query($sql);
$rows = sql_num_rows($res);

$LIST = array();

for($i=0; $i<$rows; $i++) {

	if($row = sql_fetch_array($res)) {

		//상태정보
		$date = date('Y-m-d H:i:s');
		if ($row['open_datetime'] > $date) $state = 'ready';
		if ($row['start_datetime'] < $date && $row['end_datetime'] > $date && $row['invest_end_date'] == '') $state = 'progress';
		if ($row['end_datetime'] < $date && $row['invest_end_date'] == '') $state = 'cancel';
		if ($row['invest_end_date'] != '' && $row['state'] == '')	$state = 'ready';
		if ($row['state'] == '1') $state = 'success_and_done';
		if ($row['state'] == '2')	$state = 'success_and_done';
		if ($row['state'] == '4')	$state = 'success_and_done';
		if ($row['state'] == '5') $state = 'success_and_done';

		if($row['evaluate_score1'] && $row['evaluate_score2'] && $row['evaluate_score3'] && $row['evaluate_score4']) {
			//-- 개정 등급 산정방식 --------------------------------------------------//
			$total_evaluate_score = round($row["evaluate_score1"]/48*100) + round($row["evaluate_score2"]/5*100) + round($row["evaluate_score3"]/5*100) + round($row["evaluate_score4"]/44*100);
			$lv = round($total_evaluate_score/5);
			$local_level = $_gudge_grade_array[$lv];
			//-- 기존 등급 산정방식 --------------------------------------------------//
		}
		else {
			//-- 기존 등급 산정방식 --------------------------------------------------//
			$total_evaluate_star = $row["evaluate_star1"] + $row["evaluate_star2"] + $row["evaluate_star3"];
			$local_level = $_evaluation_grade_array[$total_evaluate_star];
			//-- 기존 등급 산정방식 --------------------------------------------------//
		}


		$LIST['product'][$i] = array(
			"open_date"      => $row['start_date'],																					// 투자시작일
			"close_date"     => $row['end_date'],																						// 투자마감일
			"already_amount" => $row['total_invest_amount'],																// 현재 모집금액
			"image"          => BASE_URL."/data/product/".$row['main_image'],   						// 16:9 비율의 투자 상품 이미지 , 앱에서 자동 리사이징
			"amount"         => $row['recruit_amount'],																			// 모집금액 (원)
		  "period_code"    => $row['invest_period'],																			// 투자 기간 (개월)
			"product_uid"    => $row['idx'],																								// 상품 고유 코드 (PK)
			"type"           => "single",																										// 상품 타입 (portfolio:포트폴리오상품 | single:일반단일상품)
			"state_code"     => $state,																											// 상태값 (ready:준비중|progress:투자진행중|success_and_done:투자성공|cancel:취소)
			"product_name"   => $row['title'],																							// 상품명
			"return_of_rate" => $row['invest_return'],																		  // 연이율 (%)
			"url"            => BASE_URL."/investment/investment.php?prd_idx=".$row['idx'],	// 링크될 상품 상세 코드(고유하여야 합니다)
			"cm_level"       => "",																													// KCB 또는 나이스 신용 등급 (있을 경우)
			"cm_comp"        => "",																													// NICE신용정보", //신용 등급 평가 기관 (있을 경우)
			"custom_level"   => $local_level																								// 자체 신용 등급 (있을 경우)
		);
		$state = $total_evaluate_star = $total_evaluate_score = $lv = $local_level = NULL;

	}

}

$LIST['version'] = $version;

//print_r($LIST);

###################
## json 파일 생성
###################
$str = json_encode($LIST, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);
$json_path = $base_path.'/syndicate/product.json';
if(!@file_exists($json_path)) {
	exec("touch " . $json_path);
}
if(file_put_contents($json_path, $str)) {
	echo "JSON SUCCESS: " . $version . "\n";
}


###################
## xml 파일 생성
###################
$xml_path = $base_path.'/syndicate/product.xml';
if(!@file_exists($xml_path)) {
	exec("touch " . $xml_path);
}

$str2 = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$str2.= "<result>\n";
$str2.= "	<version><![CDATA[".$LIST['version']."]]></version>\n";
$str2.= "	<product_count><![CDATA[".count($LIST['product'])."]]></product_count>\n";
for($i=0; $i<count($LIST['product']); $i++) {

	$str2.= "	<product>\n";
	$str2.= "		<open_date><![CDATA[".$LIST['product'][$i]['open_date']."]]></open_date>\n";
	$str2.= "		<close_date><![CDATA[".$LIST['product'][$i]['close_date']."]]></close_date>\n";
	$str2.= "		<already_amount><![CDATA[".$LIST['product'][$i]['already_amount']."]]></already_amount>\n";
	$str2.= "		<image><![CDATA[".$LIST['product'][$i]['image']."]]></image>\n";
	$str2.= "		<amount><![CDATA[".$LIST['product'][$i]['amount']."]]></amount>\n";
	$str2.= "		<period_code><![CDATA[".$LIST['product'][$i]['period_code']."]]></period_code>\n";
	$str2.= "		<product_uid><![CDATA[".$LIST['product'][$i]['product_uid']."]]></product_uid>\n";
	$str2.= "		<type><![CDATA[".$LIST['product'][$i]['type']."]]></type>\n";
	$str2.= "		<state_code><![CDATA[".$LIST['product'][$i]['state_code']."]]></state_code>\n";
	$str2.= "		<product_name><![CDATA[".$LIST['product'][$i]['product_name']."]]></product_name>\n";
	$str2.= "		<return_of_rate><![CDATA[".$LIST['product'][$i]['return_of_rate']."]]></return_of_rate>\n";
	$str2.= "		<url><![CDATA[".$LIST['product'][$i]['url']."]]></url>\n";
	$str2.= "		<cm_level><![CDATA[".$LIST['product'][$i]['cm_level']."]]></cm_level>\n";
	$str2.= "		<cm_comp><![CDATA[".$LIST['product'][$i]['cm_comp']."]]></cm_comp>\n";
	$str2.= "		<custom_level><![CDATA[".$LIST['product'][$i]['custom_level']."]]></custom_level>\n";
	$str2.= "	</product>\n";

}

$str2.= "</result>\n";

if(file_put_contents($xml_path, $str2)) {
	echo "XML SUCCESS: " . $version . "\n";
}


exit;

?>