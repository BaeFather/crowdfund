<?php
###############################################################################
## 일반회원 세션체커
## common.php 로드 금지
## 웹방식  : https://도메인/sscheck.php
## CLI방식 : php -q /home/crowdfund/public_html/sscheck.php $_COOKIE['PHPSESSID']
###############################################################################

/*
// 마이크로 타임을 얻어 계산 형식으로 만듦
if( !function_exists('get_microtime') ) {
	function get_microtime() {
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
}

$ssec = get_microtime();
*/

//문자열 추출 함수
if( !function_exists('str_f6') ) {
	function str_f6($val, $ss, $ee){
		$temp_arr = explode($ss, $val);
		$temp_arr2 = explode($ee, $temp_arr[1]);
		$value = trim($temp_arr2[0]);
		$temp_arr = $temp_arr2 = NULL;
		return $value;
	}
}

//echo "<pre>"; print_r($_COOKIE); echo "</pre>";

$conn_type = (@$_SERVER['HTTP_USER_AGENT']) ? 'http' : 'exec';
if($conn_type=='http') {
	$referer = @$_SERVER['HTTP_REFERER'];
	//if( $referer=='' || preg_match("/hellofunding\.co\.kr/", $referer)==false ) {
	//	header('HTTP/1.0 404 Not Found'); exit;
	//}
}

$ARR = array('ss_id' => '', 'mb_no' => '', 'mb_id' => '', 'mb_level' => '');

$sessid = (@$conn_type=='http') ? @$_COOKIE['PHPSESSID'] : trim($_SERVER['argv']['1']);
if(!$sessid) { echo json_encode($ARR); exit; }

$ARR['ss_id'] = $sessid;

$path          = "/home/crowdfund/public_html";
$sessdir       = $path . "/data/session";
$sessfile      = 'sess_' . $sessid;
$sessfile_path = $sessdir . '/' . $sessfile;

if( is_file($sessfile_path) ) {

	$sesstext = file_get_contents($sessfile_path);
	$sesstext = substr($sesstext, 0, -1);
	//echo "<pre>"; print_r($sesstext); echo "</pre>";

	$LINE = explode(";", $sesstext);
	//echo "<pre>"; print_r($LINE); echo "</pre>";

	if( count($LINE) ) {
		for($i=0; $i<count($LINE); $i++) {
			if(preg_match("/ss_mb_no/i", $LINE[$i])) $ARR['mb_no'] = str_f6($LINE[$i], "\"", "\"");
			else if(preg_match("/ss_mb_id/i", $LINE[$i])) $ARR['mb_id'] = str_f6($LINE[$i], "\"", "\"");
			else if(preg_match("/ss_mb_level/i", $LINE[$i])) $ARR['mb_level'] = str_f6($LINE[$i], "\"", "\"");
		}
	}

}
//echo "<pre>"; print_r($ARR); echo "</pre>";


echo json_encode($ARR);

$sesstext = $LINE = NULL;


//echo 'loading : ' . sprintf("%.6f", (get_microtime() - $ssec)) . 's';

exit;

?>