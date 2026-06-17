<?

include_once("../../syndication_config.php");
include_once("inc_request_check.php");
include_once(G5_PATH.'/lib/sms.lib.php');

if ($_SERVER["REQUEST_METHOD"]!="POST") {
	header("HTTP/1.0 404 Not Found");
	exit;
}


while(list($k, $v) = each($_POST)) { ${$k} = trim($v); }

//////////////////////////////////////////////////////////////////////////////
// 신규 회원 등록
//////////////////////////////////////////////////////////////////////////////
if($mode=="new") {

	$leadtime = time() - $ordertime;
	if($leadtime > 600) {
		echo "TIME_OVER"; exit;
	}

	$mb_id          = $mb_id1;
	$mb_id          = mysqli_real_escape_string($g5['connect_db'], $mb_id);

	$mb_password    = $mb_password1;
	$articles_agree = $articles_agree1;
	$mb_mailling    = ($mb_mailling1) ? 1 : 0;
	$mb_sms         = ($mb_sms1) ? 1 : 0;
	$mb_name        = $mb_name1;
	$mb_email       = $mb_email1;

	$mb_hp          = $mb_hp1_1 . $mb_hp1_2 . $mb_hp1_3;
	$mb_hp          = mysqli_real_escape_string($g5['connect_db'], $mb_hp);
	$mb_hp_key      = substr($mb_hp, -4);

	$articles_agree = 1 * mysqli_real_escape_string($g5['connect_db'], $articles_agree);
	$mb_mailling    = 1 * mysqli_real_escape_string($g5['connect_db'], $mb_mailling);
	$mb_sms         = 1 * mysqli_real_escape_string($g5['connect_db'], $mb_sms);
	$member_type    = 1 * mysqli_real_escape_string($g5['connect_db'], $member_type);
	$mb_dupinfo     = mysqli_real_escape_string($g5['connect_db'], $mb_dupinfo);
	$mb_name        = mysqli_real_escape_string($g5['connect_db'], $mb_name);
	$mb_email       = mysqli_real_escape_string($g5['connect_db'], $mb_email);
	$member_investor_type = 1;
	$syndi_userid   = mysqli_real_escape_string($g5['connect_db'], $syndi_userid);

	$device = (G5_IS_MOBILE) ? "mobile" : "pc";

	// 아이디 중복체크
	$check_id_query  ="SELECT COUNT(mb_no) AS cnt FROM g5_member WHERE mb_id='".$mb_id."' AND mb_leave_date=''";
	$check_id_row    = sql_fetch($check_id_query);
	if( $check_id_row["cnt"] > 0 ) {
		echo 'DUP_ID'; exit;
	}

	// 핸드폰번호 중복체크
	$check_hp_query = "SELECT COUNT(mb_no) AS cnt FROM g5_member WHERE mb_hp='".$mb_hp."' AND mb_leave_date=''";
	$check_hp_row   = sql_fetch($check_hp_query);
	if( $check_hp_row["cnt"] > 0 ) {
		echo 'DUP_HP'; exit;
	}

	// 이메일 중복체크
	$check_hp_query = "SELECT COUNT(mb_no) AS cnt FROM g5_member WHERE mb_email='".$mb_email."' AND mb_leave_date=''";
	$check_hp_row   = sql_fetch($check_hp_query);
	if( $check_hp_row["cnt"] > 0 ) {
		echo 'DUP_EMAIL'; exit;
	}

	/*
	// 투자유형별 신청 첨부파일 저장
	if( in_array($member_investor_type, array('2','3') ) ) {
		$fdir = G5_DATA_PATH . "/member/investor";
		for($i=0; $i<count($_FILES['attach_file']['name']); $i++) {
			if( $_FILES['attach_file']['error'][$i]==0 && $_FILES['attach_file']['size'][$i] ) {
				if( preg_match('/(pdf|jpg|jpeg|png|gif|bmp)/i', $_FILES['attach_file']['type'][$i]) ) {
					$new_file_name = "";
					list($usec, $sec) = explode(" ", microtime());
					$reg_time = $usec * 1000000;
					$ext = substr(strrchr($_FILES["attach_file"]['name'][$i],"."), 1);
					$ext = strtolower($ext);
					$new_file_name = time().$reg_time.".".$ext;
					if(move_uploaded_file($_FILES['attach_file']['tmp_name'][$i], $fdir.'/'.$new_file_name)) {
						$FNAME[] = $new_file_name;
					}
					else {
						alert('ERROR : 파일 전송 문제가 발생하였습니다.');
					}
				}
				else {
					alert('이미지 및 PDF 문서 파일만 허용됩니다.');
				}
			}
		}
	}
	*/


	$receive_method = '2';

	$query = "
		INSERT INTO
			g5_member
		SET
			mb_id          = '".$mb_id."',
			mb_level       = '1',
			member_type    = '".$member_type."',
			member_investor_type = '".$member_investor_type."',
			mb_name        = '".$mb_name."',
			mb_email       = '".$mb_email."',
			mb_hp          = '".masterEncrypt($mb_hp, false)."',
			mb_hp_key      = '".$mb_hp_key."',
			mb_dupinfo     = '".$mb_dupinfo."',
			mb_mailling    = '".$mb_mailling."',
			mb_sms         = '".$mb_sms."',
			receive_method = '".$receive_method."',
			articles_agree = '".$articles_agree."',
			device         = '".$device."',
			syndi_id       = '".$_CONF['SYNDI_ID']."',
			syndi_userid   = '".$syndi_userid."',
			syndi_date     = NOW(),
			wowstar_userid = '".$syndi_userid."',
			wowstar_rdate  = NOW(),
			mb_ip          = '".$_SERVER['REMOTE_ADDR']."',
			mb_datetime    = NOW(),
			mb_password    = PASSWORD('".$mb_password."')";
	$result = sql_query($query);
	if($result) {
		$mb_no = sql_insert_id();

		// 비회원 SMS수신 번호에 등록된 사용자 삭제
		$res = sql_query("DELETE FROM sms_request_phone WHERE phone_no='$mb_hp'");

		$sms_query  = "SELECT * FROM `g5_sms_userinfo` where use_yn='1' and idx='19'";
		$sms_result = sql_query($sms_query);
		$sms_cnt    = sql_num_rows($sms_result);
		if( $sms_cnt > 0 ) {
			$sms_row = sql_fetch_array($sms_result);
			if($sms_row["msg"]){
				$sms_msg = str_replace("{USER_NAME}", $mb_name, $sms_row["msg"]);
				$rst = unit_sms_send($_admin_sms_number, $mb_hp, $sms_msg);
			}
		}

		echo "OK";

	}

}

exit;

?>