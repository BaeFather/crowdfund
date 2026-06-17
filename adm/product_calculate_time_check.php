<?
/////////////////////////////
// 정산 프로세스 시간체크
/////////////////////////////

set_time_limit(0);
include_once('./_common.php');

$begin_time = get_microtime();

include_once(G5_LIB_PATH.'/repay_calculation.php');		// 월별 정산내역 추출함수 호출

$prd_idx = trim($_REQUEST['idx']);											// 상품번호기준

$INV_ARR   = repayCalculation($prd_idx, $mb_id);


$last_second = sprintf("%.4f", get_microtime() - $begin_time);
debug_flush("RUN TIME : " . $last_second);

sql_close();

?>