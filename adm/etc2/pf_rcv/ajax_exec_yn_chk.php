<?
include_once('./_common.php');

while(list($key, $value) = each($_REQUEST)) { ${$key} = trim($value); }

if($yn=='N') {
	$yn='Y';
	
	$exsql = "SELECT product_idx FROM cf_pf_accounts_rcv WHERE product_idx = '$pidx'";
	$exrow = sql_fetch($exsql);

	if($exrow['product_idx']) {
		$csql = "
			UPDATE
				cf_pf_accounts_rcv
			SET 
				exec_yn = '$yn'
			WHERE
				product_idx = '$pidx' AND group_idx = '$gidx'
		";
		sql_query($csql);
	} else {

		$csql = "
			INSERT INTO
				cf_pf_accounts_rcv
			SET 
				group_idx = '".$gidx."', 
				product_idx = '".$pidx."', 
				exec_yn = '$yn'
		";
		sql_query($csql);
	} 
} else if($yn=='Y') {
	$yn='N';

	$exsql = "SELECT product_idx FROM cf_pf_accounts_rcv WHERE product_idx = '$pidx'";
	$exrow = sql_fetch($exsql);

	if($exrow['product_idx']) {
		$csql = "
			UPDATE
				cf_pf_accounts_rcv
			SET 
				exec_yn = '$yn'
			WHERE
				product_idx = '$pidx' AND group_idx = '$gidx'
		";
		sql_query($csql);
	} else {

		$csql = "
			INSERT INTO
				cf_pf_accounts_rcv
			SET 
				group_idx = '".$gidx."', 
				product_idx = '".$pidx."', 
				exec_yn = '$yn'
		";
		sql_query($csql);
	} 
}


?>