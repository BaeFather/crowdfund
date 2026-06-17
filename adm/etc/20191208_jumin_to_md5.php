<?
exit;

//https://www.hellofunding.co.kr/adm/etc/20191208_jumin_to_md5.php

set_time_limit(0);

include_once("_common.php");


$encJumin = masterEncrypt($private_jumin, true);
$md5Jumin = strtoupper(md5($private_jumin));

echo $encJumin ."<br/><br/>\n";
echo $md5Jumin ."<br/>\n";


$link2 = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2);

$res = sql_query("SELECT idx, regist_number FROM member_private WHERE regist_number!='' ORDER BY idx", "", $link2);

while($row = sql_fetch_array($res)) {

	$jumin = masterDecrypt($row['regist_number'], true);

	if(strlen($jumin)==13) {

		$md5Jumin = strtoupper(md5(masterEncrypt($jumin,false)));

		$sql2 = "
			UPDATE
				member_private
			SET
				5dm = '".$md5Jumin."'
			WHERE
				idx = '".$row['idx']."'";
		if( sql_query($sql2, "", $link2) ) {
			debug_flush($jumin . " : ". $md5Jumin ."<br>\n");
		}
	}

}

sql_close($link2);

?>