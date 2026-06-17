<?

set_time_limit(0);

include_once('../../common.php');


$sql = "
	SELECT
		idx, recruit_amount, loan_start_date, loan_end_date
	FROM
		cf_product
	WHERE 1
		AND isTest='' AND recruit_amount > 10000
		AND state IN(1, 8)
	ORDER BY
		loan_start_date ASC,
		idx ASC";
$res = sql_query($sql);
$rows = sql_num_rows($res);

for($i=0; $i<$rows; $i++) {

	$LIST[$i] = sql_fetch_array($res);


	$isHoliday = 0;
	$fcolor = 'silver';
	$final_loan_edate = '';
	$diff_day_count = '';

	if( in_array(date('w', strtotime($LIST[$i]['loan_end_date'])), array('0','6') ) || in_array($LIST[$i]['loan_end_date'], $CONF['STATIC_HOLYDAY']) || in_array($LIST[$i]['loan_end_date'], $CONF['DYNAMIC_HOLYDAY']) ) {
		$isHoliday = 1;
		$fcolor = 'red';
		$final_loan_edate = getUsableDate($LIST[$i]['loan_end_date']);
		$diff_day_count = "+" . (strtotime($final_loan_edate) - strtotime($LIST[$i]['loan_end_date'])) / 86400;
	}

	echo $LIST[$i]['idx'] . " " . $LIST[$i]['loan_end_date'];
	if($isHoliday) echo " → <span style='color:{$fcolor}'>" . $final_loan_edate . " " . $diff_day_count . "</span>";
	echo "<br/>\n";

}


sql_close();
exit;

?>