#!/usr/local/php/bin/php -c /etc/php.ini -q
<?
###############################################################################
## 존재하지 않는 회원의 주민번호 정보 삭제
## php -q /home/crowdfund/schedule_work/private_check.php yes
###############################################################################
set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', true);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/common.cli.php');

$action = (@$_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : 'debug';

$link2 = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2);

$res = sql_query("SELECT idx, mb_no, regist_number, regist_number_ineb FROM member_private WHERE regist_number!='' ORDER BY idx", "", $link2);
while( $row = sql_fetch_array($res) ) {

	$sql = "SELECT mb_no, mb_level FROM g5_member WHERE mb_no='".$row['mb_no']."'";
	$MB = sql_fetch($sql);

	if($MB['mb_no']=='' || in_array($MB['mb_level'], array('0','200'))) {

		$sql2 = "INSERT INTO member_private_drop (idx, mb_no, regist_number, rdate) VALUES ('".$row['idx']."','".$row['mb_no']."','".$row['regist_number']."','".$row['regist_number_ineb']."' CURDATE())";
		$sql3 = "DELETE FROM member_private WHERE idx='".$row['idx']."'";

		if($action=='yes') {
			sql_query($sql2, "", $link2);
			sql_query($sql3, "", $link2);
		}
		else {
			debug_flush($sql2.";  " . $sql3.";\n");
		}

	}

}

sql_close($link2);

?>