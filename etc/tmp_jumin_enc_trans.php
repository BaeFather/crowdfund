#!/usr/local/php/bin/php -c /etc/php.ini -q
<?

exit;

set_time_limit(0);

include_once("_common.php");
include_once(G5_PATH . "/mypage/crypt.php");
include_once(G5_LIB_PATH . "/crypt.lib.php");

$connect_db2 = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2) or die('MySQL Connect Error!!!');


/*
기존암호화
encrypt($value, $key)
decrypt($value, $key)
*/

/*
 절차
 1. 개인정보저장 DB member_private 테이블에 regist_number_bak 필드 추가하고 regist_number 데이터를 regist_number_bak 필드에 복사한다.
 2. member_private regist_number 필드형식을 VARCHAR(400) 으로 변경
 3. 개인정보저장 DB에서 다음 쿼리 실행 : UPDATE member_private SET regist_number_bak=regist_number WHERE 1
 4. g5_member 테이블에 mb_birth(VARCHAR(10)) 로 변경
 5. g5_member 테이블에 mb_hp_key(VARCHAR(4)), mb_hp_bak(VARCHAR(20)), account_num_key(VARCHAR(4)), account_num_key_bak(VARCHAR(20)) 필드 추가
*/


/*
$member_table  = "g5_member";
$private_table = "member_private";

$res = sql_query("SELECT mb_no, mb_hp, account_num FROM $member_table ORDER BY mb_no");
while($LIST = sql_fetch_array($res)) {

	// 주민번호 암호화 ---------------------------------------------------------------
	$res2 = sql_query("SELECT idx, regist_number, regist_number_bak FROM $private_table WHERE mb_no='".$LIST['mb_no']."' ORDER BY idx DESC", G5_DISPLAY_SQL_ERROR, $connect_db2);
	while($LIST2 = sql_fetch_array($res2)) {

		$LIST2['decrypt_regist_number'] = decrypt($LIST2['regist_number'], "jumin");

		if($LIST2['decrypt_regist_number'] && strlen($LIST2['decrypt_regist_number']) == 13) {

			$regist_number = masterDecrypt($LIST2['regist_number'], true);
			debug_flush($regist_number."\n");

			$sql3 = "UPDATE $private_table SET regist_number = '".masterEncrypt($LIST2['decrypt_regist_number'], true)."' WHERE idx = '".$LIST2['idx']."'";
			debug_flush($sql3 . "\n");
			sql_query($sql3, G5_DISPLAY_SQL_ERROR, $connect_db2);

			// 주민번호에서 생년월일 및 성별 추출
			$ARR = getBirthGender($regist_number);
			$birthdate = $ARR[0];
			$gender    = $ARR[1];

			$sql4 = "UPDATE $member_table SET mb_birth='".$birthdate."', mb_sex='".$gender."' WHERE mb_no = '".$LIST['mb_no']."'";
			debug_flush($sql4 . "\n");
			sql_query($sql4);

			unset($regist_number); unset($birthdate); unset($gender);
		}

	}


	// 휴대폰번호 암호화 --------------------------------------------------------------
	if( trim($LIST['mb_hp']) ) {
		$LIST['mb_hp'] = preg_replace("/(-| )/", "", $LIST['mb_hp']);

		$hp_key = substr($LIST['mb_hp'], -4);
		$enc_hp = masterEncrypt($LIST['mb_hp'], false);
		$dec_hp = masterDecrypt($enc_hp, false);
	//debug_flush($LIST['mb_hp'] . " -> " . $enc_hp . " -> " . $dec_hp . " ::: " . $hp_key . "\n");

		$sql5 = "UPDATE $member_table SET mb_hp='".$enc_hp."', mb_hp_key='".$hp_key."', mb_hp_bak='".$LIST['mb_hp']."' WHERE mb_no='".$LIST['mb_no']."'";
		debug_flush($sql5."\n");
		sql_query($sql5);
	}


	// 계좌번호 암호화 --------------------------------------------------------------
	if( trim($LIST['account_num']) ) {
		$LIST['account_num'] = preg_replace("/(-| )/", "", $LIST['account_num']);

		$acct_key = substr($LIST['account_num'], -4);
		$enc_acct = masterEncrypt($LIST['account_num'], false);
		$dec_acct = masterDecrypt($enc_acct, false);
	//debug_flush($LIST['account_num'] . " -> " . $enc_acct . " -> " . $dec_acct . " ::: " . $acct_key . "\n");

		$sql6 = "UPDATE $member_table SET account_num='".$enc_acct."', account_num_key='".$acct_key."', account_num_bak='".$LIST['account_num']."' WHERE mb_no='".$LIST['mb_no']."'";
		debug_flush($sql6."\n");
		sql_query($sql6);
	}

	debug_flush("\n");

	//usleep(10000);

}

*/

?>
