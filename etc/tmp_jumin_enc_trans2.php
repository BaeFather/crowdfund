#!/usr/local/php/bin/php -c /etc/php.ini -q
<?

exit;

set_time_limit(0);

include_once("_common.php");

$sql = "
	SELECT
		mb_no
	FROM
		g5_member
	WHERE 1
		AND mb_level='1' AND member_type='1'
		AND mb_birth=''
	ORDER BY
		mb_no DESC";
$res = sql_query($sql);
while($LIST = sql_fetch_array($res)) {

	$jumin = getJumin($LIST['mb_no']);
	$ARR = getBirthGender($jumin);

	if($jumin) {
		debug_flush($LIST['mb_no'] . " : " . $jumin . " :: " . $ARR[0] . " :: " . $ARR[1] . "\n");
	}

}

?>