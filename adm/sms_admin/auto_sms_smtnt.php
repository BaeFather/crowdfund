<?
###############################################################################
## (SMTNT) 예약문자데이터 실제 발송 테이블로 이동
###############################################################################

echo "START ".date("Y-m-d H:i:s")." --------------------------\n";

include_once('/home/crowdfund/public_html/data/sms_dbconfig.php');

$con = mysqli_connect(G5_MYSQL_HOST3, G5_MYSQL_USER3, G5_MYSQL_PASSWORD3, G5_MYSQL_DB3, "3306");
mysqli_query($con,"SET NAMES UTF8");

$target_table = "Msg_Tran_test";

if (isset($argv['1'])) {
	if ($argv['1']=="-live") {
		echo "live\n";
		$target_table = "Msg_Tran";
	}
}


// 오래된것 삭제
$chk_delete_sql = "select count(*) del_cnt from cf_Msg_Tran where moved='Y' and Send_Time < now() - interval 30 DAY";
$chk_delete_res = mysqli_query($con, $chk_delete_sql);
$chk_delete_row = mysqli_fetch_array($chk_delete_res);
$chk_delete = $chk_delete_row['del_cnt'];

if ($chk_delete>0) {
	echo $chk_delete. " 개의 삭제대상 레코드 발견\n";
	$delete_sql = "delete from cf_Msg_Tran where moved='Y' and Send_Time < now() - interval 30 DAY";
	echo $delete_sql . "\n";
	$delete_res = mysqli_query($con, $delete_sql);
	echo mysqli_affected_rows($con) ." 개 삭제\n\n";
}

$chk_delete_sql = "select count(*) del_cnt from cf_Msg_Tran where moved='N' and Send_Time < now() - interval 60 DAY";
$chk_delete_res = mysqli_query($con, $chk_delete_sql);
$chk_delete_row = mysqli_fetch_array($chk_delete_res);
$chk_delete = $chk_delete_row['del_cnt'];

if ($chk_delete>0) {
	echo $chk_delete. " 개의 미처리 삭제대상 레코드 발견\n";
	$delete_sql = "delete from cf_Msg_Tran where moved='N' and Send_Time < now() - interval 60 DAY";
	echo $delete_sql . "\n";
	$delete_res = mysqli_query($con, $delete_sql);
	echo mysqli_affected_rows($con) ." 개 삭제\n\n";
}

$cond = " moved='N' AND deleted='N' AND Send_Time>=NOW() AND Send_Time <= NOW() + INTERVAL 10 MINUTE ";
//$cond = " moved='N' and deleted='N' and Send_Time>=now() "; // 테스트용

$sql = "SELECT COUNT(idx) FROM cf_Msg_Tran WHERE $cond ";
$res = mysqli_query($con, $sql);
$row = mysqli_fetch_array($res);
$cnt = $row[0];

echo "총 $cnt 개 데이타 이동\n";

if ($cnt) {
	echo "데이타 이동 시작 *****\n";

	$sql = "SELECT * FROM cf_Msg_Tran WHERE $cond ORDER BY idx";
	$res = mysqli_query($con , $sql);
	$cnt = mysqli_num_rows($res);

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = mysqli_fetch_array($res);

		$ins_sql = "
			INSERT INTO
				$target_table
			SET
				Phone_No    = '".$row['Phone_No']."',
				Callback_No = '".$row['Callback_No']."',
				Msg_Type    = '".$row['Msg_Type']."',
				Send_Time   = '".$row['Send_Time']."',
				Save_Time   = NOW(),
				Subject     = '".$row['Subject']."',
				Message     = '".$row['Message']."'";

		mysqli_query($con, $ins_sql);
		$id = mysqli_insert_id($con);

		$up_sql = "
			UPDATE
				cf_Msg_Tran
			SET
				id    = '$id',
				moved = 'Y'
			WHERE
				idx = '".$row['idx']."'";
		mysqli_query($con, $up_sql);

		echo $row['idx']." ".$row['Phone_No']." -- ";
	}
	echo "\n";
}

mysqli_close($con);

echo "END   ".date("Y-m-d H:i:s")." --------------------------\n\n\n";
?>