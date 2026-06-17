<?
//20210329_investor_type_change_request.proc.php

include_once('_common.php');

$sql = "
	SELECT
		A.idx
	FROM
		investor_type_change_request A
	WHERE 1
		AND A.allow = 'Y'
		AND A.mkind = '1'
		AND (SELECT COUNT(idx) FROM investor_type_change_request WHERE mb_no=A.mb_no AND allow = 'Y' AND idx < A.idx) > 0";
echo $sql."<br><br>\n";
$res = sql_query($sql);
$rows = $res->num_rows;

for($i=0; $i<$rows; $i++) {

	$R = sql_fetch_array($res);

	$sqlx = "UPDATE investor_type_change_request SET mkind = '2' WHERE idx='".$R['idx']."'";
	$resx = sql_query($sqlx);
	echo $sqlx . "; (" . sql_affected_rows() . ")<br>\n";


}


/*
UPDATE
	investor_type_change_request
SET
	mkind = '1'
WHERE
	allow = 'Y' AND mkind=''
	AND (SELECT COUNT(idx) FROM investor_type_change_request WHERE mb_no=A.mb_no AND idx < A.idx) = 0;




SELECT
	A.*
FROM
	investor_type_change_request A
WHERE
	A.allow = 'Y' AND A.mkind=''
	AND (SELECT COUNT(idx) FROM investor_type_change_request WHERE mb_no=A.mb_no AND idx < A.idx);

*/




sql_close();
exit;

?>