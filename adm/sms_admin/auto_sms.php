<?

exit;

###############################################################################
## 예약문자데이터 실제 발송 테이블로 이동
###############################################################################

echo "START ".date("Y-m-d H:i:s")." --------------------------\n";

include_once('/home/crowdfund/public_html/data/sms_dbconfig.php');

$con = mysqli_connect(G5_MYSQL_HOST3, G5_MYSQL_USER3 ,G5_MYSQL_PASSWORD3 ,G5_MYSQL_DB3, "3306");
mysqli_query($con,"SET NAMES UTF8");

$target_table = "agent_msgqueue_test";

if (isset($argv['1'])) {
	if ($argv['1']=="-live") {
		echo "live\n";
		$target_table = "agent_msgqueue";

	}
}

$chk_delete_sql = "select count(*) del_cnt from cf_agent_msgqueue where moved='Y' and reservedTime < now() - interval 1 day";
$chk_delete_res = mysqli_query($con, $chk_delete_sql);
$chk_delete_row = mysqli_fetch_array($chk_delete_res);
$chk_delete = $chk_delete_row['del_cnt'];

if ($chk_delete>0) {
	echo $chk_delete. " 개의 삭제대상 레코드 발견\n";
	$delete_sql = "delete from cf_agent_msgqueue where moved='Y' and reservedTime < now() - interval 1 day ";
	echo $delete_sql . "\n";
	$delete_res = mysqli_query($con, $delete_sql);
	echo mysqli_affected_rows($con) ." 개 삭제\n\n";
}

//$cond = " moved='N' and deleted='N' and reservedTime>=now() and reservedTime <= now() + INTERVAL 20 MINUTE ";
//$cond = " moved='N' and deleted='N' and reservedTime>=now() and reservedTime <= now() + INTERVAL 5 MINUTE ";
$cond = " moved='N' and deleted='N' and reservedTime>=now() and reservedTime <= now() + INTERVAL 10 MINUTE ";

$sql = "select count(idx) from cf_agent_msgqueue where $cond ";
$res = mysqli_query($con, $sql);
$row = mysqli_fetch_array($res);
$cnt = $row[0];

echo "총 $cnt 개 데이타 이동\n";

if ($cnt) {
	echo "데이타 이동 시작 *****\n";

	$sql = "select * from cf_agent_msgqueue where $cond order by idx";
	$res = mysqli_query($con , $sql);
	$cnt = mysqli_num_rows($res);

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = mysqli_fetch_array($res);

		$ins_sql = "insert into $target_table set
			kind = '".$row['kind']."',
			callbackNo = '".$row['callbackNo']."',
			receiveNo = '".$row['receiveNo']."',
			subject = '".addslashes($row['subject'])."',
			message = '".addslashes($row['message'])."',
			mmsfile_path = '".$row['mmsfile_path']."',
			isReserved = '".$row['isReserved']."',
			reservedTime = '".$row['reservedTime']."',
			registTime = '".$row['registTime']."',
			sendTime = '".$row['sendTime']."',
			responseTime = '".$row['responseTime']."',
			resultTime = '".$row['resultTime']."',
			state = '".$row['state']."',
			result = '".$row['result']."',
			telecom = '".$row['telecom']."',
			etc1 = '".$row['etc1']."',
			etc2 = '".$row['etc2']."',
			etc3 = '".$row['etc3']."',
			etc4 = '".$row['etc4']."',
			etc5 = '".$row['etc5']."'
		";
		//echo "$ins_sql";
		mysqli_query($con, $ins_sql);
		$id = mysqli_insert_id($con);

		$up_sql = "update cf_agent_msgqueue set id = '$id',moved='Y' where idx='$row[idx]'";

		mysqli_query($con, $up_sql);

		echo $row['idx']." ".$row['receiveNo']." -- ";
	}
	echo "\n";
}

mysqli_close($con);

echo "END   ".date("Y-m-d H:i:s")." --------------------------\n\n\n";
?>