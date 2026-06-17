#!/usr/local/php/bin/php -q
<?
set_time_limit(0);

$path = '/home/crowdfund/public_html';
include_once($path . '/config.cli.php');
include_once($path . '/data/dbconfig.php');
include_once($path . '/lib/common.lib.php');
include_once($path . '/lib/crypt.lib.php');

$cfdb = preg_replace("/(-| )/", "", file_get_contents($path.'/adm/etc/20210714.db.txt'));
$CFDB = explode("\r\n", $cfdb);
$cfCount = count($CFDB);
unset($cfdb);


$member_type = '1';			// 1:개인 2:법인


//---------------------------------------------------------------------------
$link = sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD, G5_MYSQL_DB);
sql_set_charset("UTF8", $link);

$link2 = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2);
sql_set_charset("UTF8", $link2);
//---------------------------------------------------------------------------

$sql = "
	SELECT
		mb_no, member_type, mb_co_reg_num
	FROM
		g5_member
	WHERE 1
		AND mb_level='1' AND member_group='F'
		AND member_type ='".$member_type."'
		AND (va_bank_code2!='' AND virtual_account2!='')
	ORDER BY
		mb_no ASC";
$res  = sql_query($sql, true, $link);
$rows = $res->num_rows;

for($i=0,$j=1; $i<$rows; $i++,$j++) {

	$R = sql_fetch_array($res);

	$R2 = sql_fetch("SELECT regist_number FROM member_private WHERE mb_no='".$R['mb_no']."' AND regist_number!='' ORDER BY idx DESC LIMIT 1", true, $link2);

	$jm = $bizno = NULL;

	if($member_type=='2') {
		$bizno = preg_replace("/(-| )/", "", $R['mb_co_reg_num']);
	}
	else {
		$jm = preg_replace("/(-| )/", "", masterDecrypt($R2['regist_number'], true));
	}

	$isFuck = '';

	for($k=0; $k<$cfCount; $k++) {
		if($member_type=='2') {
			if($bizno == $CFDB[$k]) {
				$isFuck = '1';
				break;
			}
		}
		else {
			if($jm == $CFDB[$k]) {
				$isFuck = '1';
				break;
			}
		}
	}


	echo $j .': ';

	$sqlX = '';

	if($jm || $bizno) {

		$R3 = sql_fetch("SELECT idx, isFuck FROM 20210714_tmp WHERE midx='".$R['mb_no']."'", true, $link);

		if($R3['idx']) {
			if($R3['isFuck']) $sqlX = "UPDATE 20210714_tmp SET isFuck = '".$isFuck."' WHERE idx='".$R3['idx']."'";
		}
		else {
			$sqlX = "INSERT INTO 20210714_tmp (midx, mtype, jm, bizno, isFuck, rdate) VALUE ('".$R['mb_no']."','".$R['member_type']."','".$jm."','".$bizno."','".$isFuck."', CURDATE())";
		}

		if($sqlX) {
			echo $sqlX;
			$resX = sql_query($sqlX, true, $link);
			echo " (".$resX.")";
		}

	}

	echo "\n";

}

unset($CFDB);

sql_close($link);
sql_close($link2);

exit;

?>