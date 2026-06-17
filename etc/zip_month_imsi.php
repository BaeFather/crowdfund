<?
include_once('./_common.php');
include_once('./zip.lib.php');

while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }
if (!$ym) die("년월을 입력해주세요.");


$zip_data = array();

// 월 대출건수, 대출금액
$loan_data = get_loan_amt($ym);
$zip_data["loan_amt"] = $loan_data["loan_amt"];
$zip_data["loan_cnt"] = $loan_data["loan_cnt"];

// 월 상환건수, 상환금액
$repay_data = get_repay_amt($ym);
$zip_data["repay_cnt"] = $repay_data["repay_cnt"];
$zip_data["repay_amt"] = $repay_data["repay_amt"];

// 대출잔액
$remain_data = get_remain_amt($ym);
$zip_data["remain_cnt"] = $remain_data["remain_cnt"];
$zip_data["remain_amt"] = $remain_data["remain_amt"];

// 연체건수 , 연체율
$late_data = get_late_per($ym);
$zip_data["late_cnt"] = $late_data["cnt"];
$zip_data["late_per"] = $late_data["per"];


echo "<pre>";print_r($zip_data);echo "</pre>";
//die("safe die hehe");

$del_sql = "DELETE FROM cf_zip_month WHERE ym='$ym' AND g_type='A'";
sql_query($del_sql);


$ins_sql = "INSERT INTO cf_zip_month SET 
				ym              = '$ym',
				g_type          = 'A',
				loan_cnt        = '$zip_data[loan_cnt]',
				loan_amt        = '$zip_data[loan_amt]',
				repay_cnt       = '$zip_data[repay_cnt]',
				repay_amt       = '$zip_data[repay_amt]',
				remain_cnt      = '$zip_data[remain_cnt]',
				remain_amt      = '$zip_data[remain_amt]',
				overdue_cnt     = '$zip_data[late_cnt]',
				overdue_rate    = '$zip_data[late_per]',
				insert_datetime = NOW()";
sql_query($ins_sql);
echo "<br/><br/>".$ins_sql;


die();








$del_sql = "DELETE FROM cf_zip_month WHERE SUBSTRING(ym,1,4)='$yy'";
sql_query($del_sql);

for ($i=1 ; $i<=12 ; $i++) {
	
	$mm = str_pad($i , 2 , "0" , STR_PAD_LEFT);
	$ym = $yy."-".$mm;
	echo "$ym<br/>";
	
	// 대출
	$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt FROM cf_product WHERE loan_start_date LIKE '$ym-%'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	
	$loan_cnt = $row["cnt"];
	$loan_amt = $row["amt"];
	echo "대출건수 $loan_cnt , 대출금액 $loan_amt <br/>";



	// 상환
	$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt FROM cf_product WHERE loan_end_date LIKE '$ym-%'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);	

	$repay_cnt = $row["cnt"];
	$repay_amt = $row["amt"];
	echo "상환건수 $repay_cnt , 상환금액 $repay_amt <br/>";
	
	

	// 잔여 대출
	$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt FROM cf_product 
				WHERE (loan_start_date<='$ym-31' AND loan_start_date>'0000-00-00')
				AND (loan_end_date>'$ym-31' OR loan_end_date='' OR loan_end_date='0000-00-00')";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);	

	$remain_cnt = $row["cnt"];
	$remain_amt = $row["amt"];
	echo "잔여건수 $remain_cnt , 잔여금액 $remain_amt <br/>";	
	

	
	$ins_sql = "INSERT INTO cf_zip_month SET 
					ym = '$ym',
					loan_cnt = '$loan_cnt',
					loan_amt = '$loan_amt',
					repay_cnt = '$repay_cnt',
					repay_amt = '$repay_amt',
					remain_cnt = '$remain_cnt',
					remain_amt = '$remain_amt',
					insert_datetime = NOW()";
	sql_query($ins_sql);
	
	echo "<br/>";
}
?>