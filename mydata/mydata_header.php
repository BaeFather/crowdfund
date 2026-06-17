<?
$json = file_get_contents('php://input');
$rdata = json_decode($json, true);

$ins_sql = "INSERT INTO mydata_log
					SET rcv_tran_id = '".addslashes(trim($rdata["Header"]["x-api-tran_id"]))."',
						org_code = '".$rdata["Parameter"]["org_code"]."',
						uri = '".$_SERVER["REQUEST_URI"]."',
						rcv_json = '".addslashes(trim($json))."',
						ins_datetime = NOW()
			";
$res = mysqli_query($con, $ins_sql);
$log_id = mysqli_insert_id($con);
//echo "$log_id $ins_sql";
?>