<?
/////////////////////////////////////////////////////////////////////////
//  월별 대출 집계
//  23 0 1 * * /usr/local/php/bin/php /home/crowdfund/public_html/etc/zip_month.php `date +\%Y-\%m`
/////////////////////////////////////////////////////////////////////////

$path = '/home/crowdfund/public_html';
include_once($path . '/common.cli.php');
include_once($path . '/etc/zip.lib.php');

$this_ym = $_SERVER['argv'][1];
$first_date = $this_ym . '-01';

$ym = date('Y-m', strtotime($first_date . ' -1 month') );

if(!$ym) die("년월을 입력해주세요.");

$zip_data = array();

// 월 대출건수, 대출금액
$loan_data = get_loan_amt($ym);
$zip_data['loan_cnt'] = $loan_data['loan_cnt'];
$zip_data['loan_amt'] = $loan_data['loan_amt'];


// 월 상환건수, 상환금액
$repay_data = get_repay_amt($ym);
$zip_data['repay_cnt'] = $repay_data['repay_cnt'];
$zip_data['repay_amt'] = $repay_data['repay_amt'];
$zip_data['partial_repay_amt'] = $repay_data['partial_repay_amt'];
$zip_data['last_repay_amt'] = $zip_data['last_repay_amt'];


// 대출잔액
$remain_data = get_remain_amt($ym);
$zip_data['remain_cnt'] = $remain_data['remain_cnt'];
$zip_data['remain_amt'] = $remain_data['remain_amt'];
$zip_data['last_remain_amt'] = $remain_data['last_remain_amt'];

//print_r($zip_data);

$del_sql = "DELETE FROM cf_zip_month WHERE ym = '".$ym."' AND g_type = 'A'";
sql_query($del_sql);

$ins_sql = "
	INSERT INTO
		cf_zip_month
	SET
		ym              = '".$ym."',
		g_type          = 'A',
		loan_cnt        = '".$zip_data['loan_cnt']."',
		loan_amt        = '".$zip_data['loan_amt']."',
		repay_cnt       = '".$zip_data['repay_cnt']."',
		repay_amt       = '".$zip_data['repay_amt']."',
		partial_repay_amt = '".$zip_data['partial_repay_amt']."',
		last_repay_amt  = '".$zip_data['last_repay_amt']."',
		remain_cnt      = '".$zip_data['remain_cnt']."',
		remain_amt      = '".$zip_data['remain_amt']."',
		last_remain_amt = '".$zip_data['last_remain_amt']."',
		overdue_cnt     = '".$zip_data['late_cnt']."',
		overdue_rate    = '".$zip_data['late_per']."',
		insert_datetime = NOW()";
sql_query($ins_sql);

$zip_data = array();

$gb_arr = array("1","2","3","4");  // 1 부동산 pf , 2 주택담보 , 3 매출채권 , 4 동산

for ($i=0 ; $i<count($gb_arr) ; $i++) {

	$gb = $gb_arr[$i];

	// 월 대출건수, 대출금액
	$loan_data_t = get_loan_amt_t($ym, $gb);
	$zip_data['loan_amt'] = $loan_data_t['loan_amt'];
	$zip_data['loan_cnt'] = $loan_data_t['loan_cnt'];


	// 월 상환건수, 상환금액
	$repay_data_t = get_repay_amt_t($ym, $gb);
	$zip_data['repay_cnt'] = $repay_data_t['repay_cnt'];
	$zip_data['repay_amt'] = $repay_data_t['repay_amt'];
	$zip_data['partial_repay_amt'] = $repay_data_t['partial_repay_amt'];
	$zip_data['last_repay_amt'] = $repay_data_t['last_repay_amt'];

	// 대출잔액
	$remain_data_t = get_remain_amt_t($ym, $gb);
	$zip_data['remain_cnt'] = $remain_data_t['remain_cnt'];
	$zip_data['remain_amt'] = $remain_data_t['remain_amt'];
	$zip_data['last_remain_amt'] = $remain_data_t['last_remain_amt'];

	// 연체건수 , 연체율
	$late_data = get_late_per_t($ym,$gb);
	$zip_data['late_cnt'] = $late_data['cnt'];
	$zip_data['late_per'] = $late_data['per'];


	//echo $gb."\n";
	//print_r($zip_data); echo "\n\n";


	$del_sql = "DELETE FROM cf_zip_month WHERE ym = '".$ym."' AND g_type = '".$gb."'";
	sql_query($del_sql);

	$ins_sql = "
		INSERT INTO
			cf_zip_month
		SET
			ym              = '".$ym."',
			g_type          = '".$gb."',
			loan_cnt        = '".$zip_data['loan_cnt']."',
			loan_amt        = '".$zip_data['loan_amt']."',
			repay_cnt       = '".$zip_data['repay_cnt']."',
			repay_amt       = '".$zip_data['repay_amt']."',
			partial_repay_amt = '".$zip_data['partial_repay_amt']."',
			last_repay_amt  = '".$zip_data['last_repay_amt']."',
			remain_cnt      = '".$zip_data['remain_cnt']."',
			remain_amt      = '".$zip_data['remain_amt']."',
			last_remain_amt = '".$zip_data['last_remain_amt']."',
			overdue_cnt     = '".$zip_data['late_cnt']."',
			overdue_rate    = '".$zip_data['late_per']."',
			insert_datetime = NOW()";
	sql_query($ins_sql);
	//echo "<br/><br/>".$ins_sql."<br/>";


}

sql_close();
exit;

?>