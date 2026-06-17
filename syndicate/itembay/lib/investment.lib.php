<?php
/**
 * Created by PhpStorm.
 * User: 김국현
 * Date: 2018-04-13
 * Time: 오후 3:33
 *
 *
 * 2018 개편 시 추가된 함수 모음
 */

function lazy_session_start()
{
	// 세션이 이미 열렸거나 $_SESSION 세션 쿠키가 비어있다. 무시함
	if (session_id() != '' || count($_SESSION) == 0) {
		return;
	}
	// $_SESSION 복사
	$temp = $_SESSION;
	// 세션 시작
	session_start();
	// $_SESSION restore
	$_SESSION = $temp;
}

/**
 * html 태그 제거
 */
function html_escape($var, $double_encode = TRUE, $charset="UTF-8"){
	if (empty($var)) {
		return $var;
	}
	if (is_array($var)) {
		foreach (array_keys($var) as $key) {
			$var[$key] = html_escape($var[$key], $double_encode);
		}
		return $var;
	}
	return htmlspecialchars($var, ENT_QUOTES, $charset, $double_encode);
}

/**
 * 괄호에 포함된 값과 문자열 분리
 */
function extractText($string)
{
	$text_outside=array();
	$text_inside=array();
	$t="";
	for($i=0;$i<strlen($string);$i++)
	{
		if($string[$i]=='[')
		{
			$text_outside[]=$t;
			$t="";
			$t1="";
			$i++;
			while($string[$i]!=']')
			{
				$t1.=$string[$i];
				$i++;
			}
			$text_inside[] = $t1;

		}
		else {
			if($string[$i]!=']')
				$t.=$string[$i];
			else {
				continue;
			}

		}
	}
	if($t!="") $text_outside[]=$t;
	return array_values(array_filter(array_merge($text_inside, $text_outside)));
}

/**
 * 금액 100만단위로 출력
 * 850000000 -> 8.5억
 */
function wonFormat($k)
{
//	$k = 31500000;
	$len = strlen ($k); // 9
	$len_ahr = ceil($len / 4); // 3
	$len_skanwj = ($len % 4); // 1

//	echo "len : ".$len."<br/>";
//	echo "len_ahr : ".$len_ahr."<br/>";
//	echo "len_skanwj : ".$len_skanwj."<br/>";
//	echo str_replace('0', '', $k);

	$k = substr($k, 0, 4);
	$loop = strlen($k);
	$tmpStr = "";


	for($i=0; $i<$loop; $i++){
		if($len_skanwj > 0){
			if($i == 0){
				$tmpStr .= substr($k, $i, $len_skanwj);
			}else{
				if(substr($k, $i + $len_skanwj , $len_skanwj) > 0){
					if($i <= $len_skanwj){
						$tmpStr .= '.';
					}
					$tmpStr .= substr($k, $i + $len_skanwj - 1, $len_skanwj);
				}
			}
		}else{
			$tmpStr .= substr($k, $i, 1);
		}
	}

	$sub = array("원", "만", "억", "조". "경"); // 0, 1, 2, 3
	$unit .= $sub[$len_ahr - 1];
		$ch = strpos($unit,"원");
	if($ch == 0) {
		$unit = $unit."원";
	}

	if(strlen($tmpStr)==4) $tmpStr = number_format($tmpStr);

	return array($tmpStr, $unit);
}

/*
억대금액 금액 -> 소수표기
천만원대 -> number_format표기
*/
function getNumberArr($value) {
	if($value > 0) {
		if($value >= 100000000) {
			$unit_amt	= 100000000;
			$nums = $value/$unit_amt;
			$nums = floatCutting($nums, 4);
			$nums = floatRtrim($nums);
			$unit = '억';
		}
		else {
			$unit_amt	= 10000;
			$nums = number_format($value/$unit_amt);
			$unit = '만';
		}
		return array($nums, $unit);
	}
}

/**
 * 상품 상태값에 따른 버튼형태
 */
function productStatusCheck($idx = null){
	if(!$idx){
		return false;
	}

	$productStatusData = getProductStat($idx);

	###################################
	## 리턴 상태코드(code) 예시 :
	## A01 : 이자상환중
	## A02 : 투자상환완료 (상품마감)
	## A03 : 투자모집실패
	## A04 : 부실(매각협의중)
	## A05 : 중도일시상환
	## A06 : 대출취소(기표전)
	## A07 : 대출취소(기표후)
	## A08 : 상환지연(연체)
	## A09 : 상환불가(부도)
	## B00 : 상품준비중
	## B01 : 투자대기중
	## B02 : 투자모집중
	## B03 : 투자모집완료
	## B04 : 투자모집실패
	###################################

	switch($productStatusData['code']) {
		case "A01" :
			$coverCaption  = "펀딩성공";
			$buttonCaption = "이자상환중";
			break;
		case "A02" :
			$coverCaption  = "펀딩성공";
			$buttonCaption = '원금상환완료';
			break;
		case "A03" :
			$coverCaption  = "펀딩종료";
			$buttonCaption = "모집종료";
			break;
		case "A04" :
			$coverCaption  = "펀딩종료";
			$buttonCaption = "매각협의중";
			break;
		case "A05" :
			$coverCaption  = "펀딩성공";
			$buttonCaption = "원금상환완료";
			break;
		case "A06" :
			$coverCaption  = "펀딩종료";
			$buttonCaption = "투자금반환완료";
			break;
		case "A07" :
			$coverCaption  = "펀딩종료";
			$buttonCaption = "대출취소";
			break;
		case "A08" :
			$coverCaption  = "펀딩성공";
			$buttonCaption = "상환지연중";
			break;
		case "A09" :
			$coverCaption  = "펀딩성공";
			$buttonCaption = "상환불가";
			break;
		case "B00" :
			$coverCaption  = "상품준비중";
			$buttonCaption = '상품상세보기';
			break;
		case "B01" :
			$coverCaption  = "투자대기중";
			$buttonCaption = '상품상세보기';
			break;
		case "B02" :
			$coverCaption  = "모집중";
			$buttonCaption = '상품상세보기';
			break;
		case "B03" :
			$coverCaption  = "펀딩성공";
			$buttonCaption = "모집완료(대출실행준비중)";
			break;
		case "B04" :
			$coverCaption  = "펀딩종료";
			$buttonCaption = "투자 미달성";
			break;
		default	:
			$coverCaption  = "상품준비중";
			$buttonCaption = '상품상세보기';
			break;
	}

	// 사전투자 모집중일때의 처리
	if($productStatusData['advence_invest_ing']=='Y') {
		$productStatusData['code'] = 'B02';
		$coverCaption  = "모집중";
		$buttonCaption = '상품상세보기';
	}

	return (['buttonCaption' => $buttonCaption, 'coverCaption' => $coverCaption, 'code' => $productStatusData['code']]);
}

/**
 * 순서지정
 */
function sortArrayByArray(array $array, array $orderArray) {
	$ordered = array();
	foreach ($orderArray as $key) {
		if (array_key_exists($key, $array)) {
			$ordered[$key] = $array[$key];
			unset($array[$key]);
		}
	}
	return $ordered + $array;
}

/**
 * 날짜 오전/오후 구분
 */
function getTimeFormat($date_str) {
	$result = "";
	$hour = date("H", strtotime($date_str));
	$minute = date("i", strtotime($date_str));

	if($hour > 12) {
		$hour = $hour - 12;
		$result = "PM ".$hour.":".$minute;
	} else {
		$result = "AM ".$hour.":".$minute;
	}
	return $result;
}
?>