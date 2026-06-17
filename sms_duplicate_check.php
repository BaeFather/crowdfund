<?
include_once("_common.php");

$sql = "
	SELECT
		idx, phone_no,
		(SELECT COUNT(mb_no) FROM g5_member WHERE mb_hp=phone_no) AS use_count
	FROM
		sms_request_phone
	ORDER BY
		use_count DESC";
$res = sql_query($sql);
$rows = sql_num_rows($res);

echo $rows."<br>\n";

for($i=0; $i<$rows; $i++) {
	$row = sql_fetch_array($res);
	print_rr($row);
	if($row['use_count']>0)	{
		//$sql2 = "DELETE FROM sms_request_phone WHERE idx='".$row['idx']."'";
		//$res2 = sql_query($sql2);
	}
}

?>