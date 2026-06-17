<?
error_reporting(0);
header("Content-Type:application/json");

include_once("_common.php");



$result = get_product_info($_POST['idx']);
$result['idx']=$_POST['idx'];


echo json_encode($result);



function get_product_info($idx) {

	$sql = "
		SELECT
			A.idx, A.title, A.recruit_amount, A.invest_return, A.invest_period, A.state,
			A.loan_start_date, A.loan_end_date_orig, A.loan_end_date, A.right_display,
			A.stream_url1, A.stream_url2
		FROM
			cf_product A
		WHERE
			idx='$idx'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);


	if ($row['state']=="1") $row['state_txt']="이자상환중";
	else if ($row['state']=="2") $row['state_txt']="상환완료";
	else if ($row['state']=="3") $row['state_txt']="투자금모집실패";
	else if ($row['state']=="4") $row['state_txt']="부실(매각처리중)";
	else if ($row['state']=="5") $row['state_txt']="중도상환";
	else if ($row['state']=="6") $row['state_txt']="대출최소(기표전)";
	else if ($row['state']=="7") $row['state_txt']="대출최소(기표후)";
	else if ($row['state']=="8") $row['state_txt']="연체";
	else if ($row['state']=="9") $row['state_txt']="부도(상환불가)";
	else $row['state_txt'] = $row['state'];



	if ($row['loan_end_date']=='0000-00-00') $row['loan_end_date_txt'] = $row['loan_end_date_orig'];
	else $row['loan_end_date_txt'] = $row['loan_end_date'];



	$tmp = getNumberArr($row['recruit_amount']);
	$row['recruit_amount_txt'] = $tmp[0].$tmp[1]."원";

	$row['invest_return_txt'] = number_format($row['invest_return'])."%";

	$row['invest_period_txt'] = $row['invest_period']."개월";

	$row['info3'] = $row['recruit_amount_txt']." / ".$row['invest_return_txt']." / ".$row['invest_period_txt'];



	if (number_format($row['loan_start_date'])<=5) $row['total_inter'] = $row['invest_period'];
	else $row['total_inter'] = $row['invest_period']+1 ;

	$inter_sql = "select max(turn) real_turn from cf_product_success where product_idx='$idx'";
	$inter_res = sql_query($inter_sql);
	$inter_row = sql_fetch_array($inter_res);
	$row['real_inter'] = $inter_row['real_turn']?$inter_row['real_turn']:0;

	$row['inter_txt'] = $row['real_inter']." / ".$row['total_inter'];



	return $row;
}
?>