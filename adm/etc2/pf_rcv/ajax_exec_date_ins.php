<?
include_once('./_common.php');

while(list($key, $value) = each($_REQUEST)) { ${$key} = trim($value); }

if($gidx==$pidx) {
	$esql = "
		UPDATE
			cf_pf_accounts_rcv
		SET 
			exec_date = '".$exdate."'
		WHERE
			product_idx = '$pidx'
	";
	sql_query($esql);	
} else {
	$sql = "SELECT product_idx FROM cf_pf_accounts_rcv WHERE product_idx = '$pidx'";
	$row = sql_fetch($sql);
	
	if($row['product_idx']) {
		$esql = "
			UPDATE
				cf_pf_accounts_rcv
			SET 
				exec_date = '".$exdate."'
			WHERE
				product_idx = '$pidx'
		";
		sql_query($esql);
	} else {
		$esql = "
			INSERT INTO
				cf_pf_accounts_rcv
			SET 
				group_idx = '".$gidx."', 
				product_idx = '".$pidx."', 
				exec_date = '".$exdate."'
		";
		sql_query($esql);
	}
}



?>