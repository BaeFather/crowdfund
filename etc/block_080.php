<?
include_once("_common.php");
?>
<?
$cid = $_GET["cid"];
$fr = $_GET["fr"];

if (!$cid or !$fr) die("0");
?>
<?
$hp_ec = masterEncrypt($cid, false);
$hp_dc = masterDecrypt($hp_ec, false);


$chk_mem_sql = "SELECT mb_no,mb_id, mb_hp, mb_sms FROM g5_member WHERE mb_hp='".$hp_ec."'";
$chk_mem_res = sql_query($chk_mem_sql);
$chk_mem = sql_num_rows($chk_mem_res);;

$out = 0;

if ($chk_mem) {
	for ($i=0 ; $i<$chk_mem ; $i++) {
		$mem_row = sql_fetch_array($chk_mem_res);


		if ($mem_row["mb_sms"]=="1") {
			
			$up_sql = "UPDATE g5_member SET mb_sms='0', edit_datetime=NOW() WHERE mb_no='$mem_row[mb_no]'";
			sql_query($up_sql);
			member_edit_log($mem_row["mb_no"]);

			$log_sql = "INSERT INTO cf_sms_block SET
							mb_no = '$mem_row[mb_no]',
							mb_hp = '".$hp_ec."',
							rcv_sms = 'N',
							change_by = '$fr',
							input_datetime = NOW()";
			sql_query($log_sql);
			$out = 1;
		} 

	}
}



$chk_nom_sql = "SELECT * FROM sms_request_phone WHERE phone_no='$cid'";
$chk_nom_res = sql_query($chk_nom_sql);
$chk_nom = sql_num_rows($chk_nom_res);

if ($chk_nom) {
	$del_sql = "DELETE FROM sms_request_phone WHERE phone_no='$cid'";
	sql_query($del_sql);

	$log_sql = "INSERT INTO cf_sms_block SET
					mb_no = '-1',
					mb_hp = '".$hp_ec."',
					rcv_sms = 'N',
					change_by = '$fr',
					input_datetime = NOW()";
	sql_query($log_sql);
	$out = 1;
}

if ($out==0) {
	$log_sql = "INSERT INTO cf_sms_block SET
					mb_hp = '".$hp_ec."',
					rcv_sms = 'Y',
					change_by = '$fr',
					input_datetime = NOW()";
	sql_query($log_sql);
}

echo $out;
?>