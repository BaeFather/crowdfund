<?
include_once('./_common.php');
?>

<?
$main_table = "cf_auto_invest_config_user";

$pre_chk_sql = "select count(*) pre_cnt from cf_auto_invest_config_user_change";
$pre_chk_res = sql_query($pre_chk_sql);
$pre_chk_row = sql_fetch_array($pre_chk_res);
if ($pre_chk_row['pre_cnt']) {
	die("이미 변경된 내역이 있습니다.");
}

$del_sql = "delete from cf_auto_invest_config_user_change";
$del_res = sql_query($del_sql);
//echo "$del_res<br/><br/>";

$chk_sql1 = "select * from $main_table";
$chk_res1 = sql_query($chk_sql1);
$ori_cnt = $chk_res1->num_rows;
for ($i=0 ; $i<$ori_cnt ; $i++ )  {
	$chk_row = sql_fetch_array($chk_res1);
	echo "$i ";

	$ins_sql = "insert into cf_auto_invest_config_user_change set
		idx = '$chk_row[idx]',
		ai_grp_idx = '$chk_row[ai_grp_idx]',
		member_idx = '$chk_row[member_idx]',
		setup_amount = '$chk_row[setup_amount]',
		invest_warning_agree = '$chk_row[invest_warning_agree]',
		rdate = '$chk_row[rdate]',
		edate = '$chk_row[edate]'
	";
	$ins_res = sql_query($ins_sql);
	$change_idx = sql_insert_id();
	//echo "$ins_res<br/>";
	//$new_val = "";
	/*
	$new_val = $chk_row['ai_grp_idx'];
	//$new_val2 = $chk_row['setup_amount'];
	$new_val2 = get_new_amt($chk_row['member_idx']);

	if ($chk_row['ai_grp_idx']=="6") { // 확정매출채권
		echo "확정매출채권";
	} else if ($chk_row['ai_grp_idx']=="7") { // 동산담보
		echo "동산담보";
	} else if ($chk_row['ai_grp_idx']=="8") { // 확정매출채권A  -> 6 확정매출채권
		$new_val = "6";
		echo $chk_row['ai_grp_idx']." 확정매출채권A -> $new_val";
	} else if ($chk_row['ai_grp_idx']=="9") { // 면세점매출채권
		echo "면세점매출채권";
	} else if ($chk_row['ai_grp_idx']=="10") { // 확정매출채권B  -> 6 확정매출채권
		$new_val = "6";
		echo $chk_row['ai_grp_idx']." 확정매출채권B  -> $new_val";
	} else if ($chk_row['ai_grp_idx']=="11") { // 부동산담보
		echo "부동산담보";
	} else if ($chk_row['ai_grp_idx']=="12") { // 주택담보
		echo "주택담보";
	} else {
		echo "error";
	}

	$up_sql = "";
	if ($new_val) {
		$up_sql = "update $main_table set ai_grp_idx='$new_val' ,  setup_amount='$new_val2' where idx='$chk_row[idx]'";
		sql_query($up_sql);
	}
	echo " $up_sql<br/>";

	$rat_sql = "select * from $main_table where idx='$chk_row[idx]'";
	$rat_res = sql_query($rat_sql);
	$rat_row = sql_fetch_array($rat_res);
	//if ($chk_row['ai_grp_idx']<>$rat_row['ai_grp_idx']) {
		$upu_sql = "update cf_auto_invest_config_user_change set
			ai_grp_idx_new = '$rat_row[ai_grp_idx]',
			setup_amount_new = '$rat_row[setup_amount]'
			where oidx = '$change_idx'
		";
		sql_query($upu_sql);
	//}
	*/
}

function get_new_amt($member_idx) {
	global $main_table;
	$sql = "select max(setup_amount) new_val2 from $main_table where member_idx='$member_idx'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	return $row['new_val2'];
}

die("end");

$chk_sql2 = "select count(*) cnt from cf_auto_invest_config_user_back201901";
$chk_res2 = sql_query($chk_sql2);
$chk_row2 = sql_fetch_array($chk_res2);
$back_cnt = $chk_row2["cnt"];

echo "$ori_cnt $back_cnt<br/>";
if ($ori_cnt == $back_cnt) echo "백업 완료<br/>";
else echo "불일치<br/>";
?>