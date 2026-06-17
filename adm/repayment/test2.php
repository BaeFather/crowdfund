<?

set_time_limit(0);

include_once ('/home/crowdfund/public_html/common.cli.php');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once(G5_LIB_PATH.'/repay_calculation_new.php');

$begin_time = get_microtime();


$prd_idx = '5545';
$mb_id = 'softjs';


$INV_ARR = repayCalculationNew($prd_idx, $mb_id);
//$REPAY = $INV_ARR['REPAY'];

print_rr($INV_ARR['REPAY'], 'font-size:11px;line-height:12px;');


sql_close();


$left_time = get_microtime() - $begin_time;
echo sprintf("%.2f", $left_time);

?>