<?

// 공개 API를 통한 IP 조회

if(!preg_match("/183\.98\.101/", $_SERVER["REMOTE_ADDR"])) { header("HTTP/1.0 404 Not Found"); exit; }

set_time_limit(0);


include_once("_common.php");
include_once(G5_LIB_PATH.'/repay_calculation_new.php');

//상품번호 6815 (투자자 15명 기간 4일) / 회원번호 개인 : 6258  법인 : 16218

//$REPAYSET = repayCalculationNew('6815', 'yeonwj');		// 개인
$REPAYSET = repayCalculationNew('6796', 'oneway');		// 법인
print_rr($REPAYSET, 'font-size:12px');






sql_close();
exit;


?>