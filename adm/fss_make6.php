<?
/*
CREATE TABLE `cf_fss_loan_detail` (
	`idx` INT(11) NOT NULL AUTO_INCREMENT,
	`quarter` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '분기' COLLATE 'utf8_general_ci',
	`product_idx` INT(11) NULL DEFAULT '0',
	`title` VARCHAR(255) NULL DEFAULT '' COLLATE 'utf8_general_ci',
	`loan_dep_bank_cd1` VARCHAR(3) NULL DEFAULT '' COLLATE 'utf8_general_ci',
	`loan_dep_acct_nb1` VARCHAR(20) NULL DEFAULT '' COLLATE 'utf8_general_ci',
	`loan_dep_owner` VARCHAR(100) NULL DEFAULT '' COLLATE 'utf8_general_ci',
	`input_datetime` DATETIME NOT NULL COMMENT '입력일',
	PRIMARY KEY (`idx`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DYNAMIC;
*/

set_time_limit(0);

include_once('./_common.php');

$base_path = "/home/crowdfund/public_html";
include_once($base_path."/lib/common.lib.php");
include_once($base_path."/lib/insidebank.lib.php");
?>
<?
$SHISDBK['target_host']       = "222.231.31.120";			// 실서버
//$SHISDBK['target_host']       = "222.231.31.34";		// 테스트서버
$SHISDBK['000']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5000";  //TESTCALL
$SHISDBK['128']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5001";
$SHISDBK['128']['enc_key']    = "ECgYB1tH7pFPbDvT";
$SHISDBK['256']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5002";
$SHISDBK['256']['enc_key']    = "esYax1AADKlC7KmTjhdcd6itjLQ+2cyU";
?>
<?
/*
$sql = "SELECT * FROM cf_product WHERE loan_start_date>='2020-01-01' AND loan_start_date<='2020-03-31' ";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);

	$ins_sql = "INSERT INTO cf_fss_loan_detail SET
					quarter = '2020-1',
					product_idx = '$row[idx]',
					title = '".$row['title']."',
					loan_dep_bank_cd1 = '$row[loan_dep_bank_cd1]',
					loan_dep_acct_nb1 = '$row[loan_dep_acct_nb1]',
					input_datetime = NOW()
				";
	//echo "$ins_sql<br/><br/>";
	//sql_query($ins_sql);

}
*/
?>


<!--table border=1 -->
<?
$sql = "SELECT loan_dep_acct_nb1,loan_dep_bank_cd1  FROM cf_fss_loan_detail WHERE quarter = '2020-1' and loan_dep_owner='' GROUP BY loan_dep_acct_nb1 limit 1";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);

	//$sch_res = get_acc_owner($row['loan_dep_bank_cd1'],$row['loan_dep_acct_nb1']);

	//$up_sql2 = "UPDATE cf_fss_loan_detail SET loan_dep_owner='$sch_res[ACCT_OWNER_NM]'  WHERE loan_dep_bank_cd1='$row[loan_dep_bank_cd1]' AND loan_dep_acct_nb1='$row[loan_dep_acct_nb1]'";
	//$up_res = sql_query($up_sql2);
	?>
	<!--tr>
		<td><?=$i+1?></td>
		<td><?=$row['product_idx']?></td>
		<td><?=$row['title']?></td>
		<td><?=$row['loan_dep_bank_cd1']?></td>
		<td><?=$row['loan_dep_acct_nb1']?></td>
		<td><?=$sch_res['ACCT_OWNER_NM']?></td>
		<td><?=$up_res?></td>
	</tr-->
	<?
}
?>
<!-- /table -->

<?
function get_acc_owner($bank_code, $acc_num) {
	$enc_bit = '256';

	$ARR['REQ_NUM'] = "040";
	$ARR['BANK_CD'] = $bank_code;
	$ARR['ACCT_NB'] = $acc_num;

	//$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
	//echo json_encode($insidebank_result);
	//echo "<pre>"; print_r($insidebank_result) ; echo "</pre>";

	return $insidebank_result;
}
?>


<table border=1>
<?
$sql = "SELECT *  FROM cf_fss_loan_detail WHERE quarter = '2020-1'";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	?>
	<tr>
		<td><?=$i+1?></td>
		<td><?=$row['product_idx']?></td>
		<td><?=$row['title']?></td>
		<td><?=$row['loan_dep_bank_cd1']?></td>
		<td><?=$row['loan_dep_acct_nb1']?></td>
		<td><?=$row['loan_dep_owner']?></td>
	</tr>
	<?
}
?>
</table>