<?php
######################################
## 주민번호 강제 입력
## php -q /home/crowdfund/adm/encrypt_jumin.php
######################################

include_once('_common.php');
include_once('../mypage/crypt.php');

$key = 'jumin';

$DATA[] = array('mb_no'=>'85', 'want_jumin'=>'7509031114220');

echo $DATA['want_jumin']."<br>\n";

$link2 = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2);

for($i=0; $i<count($DATA); $i++) {

	$jumin_enc = encrypt($DATA[$i]['want_jumin'], $key);

	$sql = "UPDATE member_private SET regist_number='$jumin_enc' WHERE mb_no='".$DATA[$i]['mb_no']."'";
	if($result = mysqli_query($link2, $sql)){
		echo $sql."\n";
	}

}

mysqli_close($link2);
exit;

?>