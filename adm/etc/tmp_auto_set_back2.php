<?
include_once('./_common.php');
?>

<?
$main_table = "cf_auto_invest_config";
$back_table = "cf_auto_invest_config_back";

$pre_chk_sql = "select count(*) pre_cnt from $back_table";
$pre_chk_res = sql_query($pre_chk_sql);
$pre_chk_row = sql_fetch_array($pre_chk_res);
if ($pre_chk_row['pre_cnt']) {
	die("이미 변경된 내역이 있습니다.");
}

$del_sql = "delete from $back_table";
//$del_res = sql_query($del_sql);


$ins_sql = "insert into $back_table select * from $main_table";
echo "$ins_sql";
?>