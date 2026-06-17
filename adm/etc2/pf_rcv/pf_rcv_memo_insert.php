<?
include_once('./_common.php');

if($midx) {
	$sql = "
		DELETE
		FROM
			cf_pf_accounts_rcv_memo
		WHERE
			idx = '$midx';
	";

	sql_query($sql);
	alert("삭제되었습니다.");

} else {
	$sql = "
	INSERT INTO 
		cf_pf_accounts_rcv_memo 
	SET 
		pf_ar_idx = '$idx',
		contents = '$memo',
		reg_date = NOW(),
		mb_id='".$member['mb_id']."',
		writer='".$member['mb_name']."'
	";

	sql_query($sql);
	alert("등록되었습니다.");
}

?>

