<?
include_once('./_common.php');
include_once('../lib/sms.lib.php');


if($_SERVER['REQUEST_METHOD']!='POST') {
	header("HTTP/1.0 404 Not Found");
	exit;
}


while(list($k, $v) = each($_POST)) { ${$k} = @trim($v); }


$mb_id          = sql_real_escape_string($mb_id1);
$mb_hp1         = sql_real_escape_string($mb_hp1);
$mb_hp2         = sql_real_escape_string($mb_hp2);
$mb_hp3         = sql_real_escape_string($mb_hp3);
$mb_hp          = $mb_hp1 . $mb_hp2 . $mb_hp3;
$mb_hp_key      = substr($mb_hp, -4);
$vi_idx         = get_cookie('ck_vistatus_idx');
$articles_agree = ($agree_provision && $agree_privacy) ? '1' : '';

$options_agree  = ($agree_marketing) ? '1' : '';
$mb_mailling    = ($options_agree) ? 1 : 0;
$mb_sms         = ($options_agree) ? 1 : 0;
//$mb_mailling    = ($mb_mailling) ? 1 : 0;
//$mb_sms         = ($mb_sms) ? 1 : 0;

$member_type    = sql_real_escape_string($member_type);
$member_investor_type = sql_real_escape_string($member_investor_type);
$mb_dupinfo     = sql_real_escape_string($mb_dupinfo);
$mb_name        = sql_real_escape_string($mb_name);
$mb_email       = sql_real_escape_string($mb_email);
$device         = (G5_IS_MOBILE) ? 'mobile' : 'pc';

$check_id_row = sql_fetch("SELECT mb_no FROM g5_member WHERE mb_id = '".$mb_id."'");
if($check_id_row['mb_no']) { echo "<script>alert('이미 등록 된 아이디 입니다.'); location.replace('about:blank');</script>"; exit; }


/* 핸드폰 번호 중복체크 무효화 (2017-01-20)
$check_hp_query ="SELECT COUNT(*) cnt FROM g5_member WHERE mb_hp = '".masterEncrypt($mb_hp, false)."' and mb_leave_date = ''  ";
$check_hp_result = query($check_hp_query);
$check_hp_row = mysqli_fetch_array($check_hp_result);
if($check_hp_row["cnt"]>0){
	echo "<script>alert('이미 등록 된 핸드폰 번호 입니다.'); location.replace('about:blank');</script>"; exit;
}*/



if($is_junior) {

	if(!$_FILES["junior_doc1"]['size'] || !$_FILES["junior_doc2"]['size'] || !$_FILES["junior_doc3"]['size']) {
		echo "<script>alert('미성년자 가입에 대한 필요서류들을 등록 하여 주십시요.'); location.replace('about:blank');</script>"; exit;
	}

	$upload_folder = G5_DATA_PATH."/member/junior";

	//법정대리인동의서
	if($_FILES["junior_doc1"]['size'] > 0) {
		$new_file_name = "";
		list($usec, $sec) = explode(" ", microtime());
		$reg_time  =   $usec * 1000000;
		$ext = substr(strrchr($_FILES["junior_doc1"]['name'],"."), 1);
		$ext = strtolower($ext);
		$new_file_name = time() . $reg_time. "." . $ext;

		$junior_doc1 = UploadFile($upload_folder, '10', 'junior_doc1', '', $new_file_name);
		if(!$junior_doc1) {
			echo "<script>alert('[법정대리인동의서] 잘못된 경로 입니다.'); location.replace('about:blank');</script>"; exit;
		}
	}

	//가족관계증명서
	if($_FILES["junior_doc2"]['size'] > 0) {
		$new_file_name = "";
		list($usec, $sec) = explode(" ", microtime());
		$reg_time  =   $usec * 1000000;
		$ext = substr(strrchr($_FILES["junior_doc2"]['name'],"."), 1);
		$ext = strtolower($ext);
		$new_file_name = time() . $reg_time. "." . $ext;

		$junior_doc2 = UploadFile($upload_folder, '10', 'junior_doc2', '', $new_file_name);
		if(!$junior_doc2) {
			echo "<script>alert('[가족관계증명서] 잘못된 경로 입니다.'); location.replace('about:blank');</script>"; exit;
		}
	}

	//법정대리인신분증사본
	if($_FILES["junior_doc3"]['size'] > 0) {
		$new_file_name = "";
		list($usec, $sec) = explode(" ", microtime());
		$reg_time  =   $usec * 1000000;
		$ext = substr(strrchr($_FILES["junior_doc3"]['name'],"."), 1);
		$ext = strtolower($ext);
		$new_file_name = time() . $reg_time. "." . $ext;

		$junior_doc3 = UploadFile($upload_folder, '10', 'junior_doc3', '', $new_file_name);
		if(!$junior_doc3) {
			echo "<script>alert('[법정대리인신분증사본] 잘못된 경로 입니다.'); location.replace('about:blank');</script>"; exit;
		}
	}

}
else {

	//신분증사본
	if($_FILES["id_card"]['size'] > 0) {
		$upload_folder = G5_DATA_PATH."/member/id_card";
		$new_file_name="";
		list($usec, $sec) = explode(" ", microtime());
		$reg_time  =   $usec * 1000000;
		$ext = substr(strrchr($_FILES["id_card"]['name'],"."), 1);
		$ext = strtolower($ext);
		$new_file_name = time() . $reg_time. "." . $ext;

		$id_card = UploadFile($upload_folder, '10', 'id_card', '', $new_file_name);
		if(!$id_card) {
			echo "<script>alert('[신분증사본] 잘못된 경로 입니다.'); location.replace('about:blank');</script>"; exit;
		}
	}

}



$member_investor_type = 1;

$query = "
	INSERT INTO
		g5_member
	SET
		mb_id          = '".$mb_id."',
		mb_password    = PASSWORD('".$mb_password."'),
		mb_name        = '".$mb_name."',
		mb_level       = '0',
		mb_hp          = '".masterEncrypt($mb_hp, false)."',
		mb_hp_key      = '".$mb_hp_key."',
		mb_email       = '".$mb_email."',
		vi_idx         = '".$vi_idx."',
		mb_dupinfo     = '".$mb_dupinfo."',
		mb_mailling    = '".$mb_mailling."',
		mb_sms         = '".$mb_sms."',
		member_type    = '".$member_type."',
		member_investor_type = '".$member_investor_type."',
		mb_ip          = '".$_SERVER['REMOTE_ADDR']."',
		mb_datetime    = NOW(),
		articles_agree = '".$articles_agree."',
		options_agree  = '".$options_agree."',
		id_card        = '".$id_card."',
		junior_doc1    = '".$junior_doc1."',
		junior_doc2    = '".$junior_doc2."',
		junior_doc3    = '".$junior_doc3."',
		device         = '".$device."'";

$result = sql_query($query);

if($result) {

	$resxx = sql_query("UPDATE cf_visit_status SET join_count=join_count+1 WHERE idx='".get_cookie('ck_vistatus_idx')."'");

	/*
	//SMS 발송 생략
	$sms_query = "SELECT * FROM `g5_sms_userinfo` where use_yn ='1' and idx = '19' ";
	$sms_result =  query($sms_query);
	$sms_cnt = mysqli_num_rows($sms_result);
	if($sms_cnt> 0 ){
		$sms_row = mysqli_fetch_array($sms_result);
		if($sms_row["msg"]){
			$sms_msg = str_replace("{USER_NAME}", $mb_name, $sms_row["msg"]);
			$rst = unit_sms_send($_admin_sms_number, $mb_hp, $sms_msg);
		}
	}
	*/

	if($is_junior) {
		echo "<script>top.location.replace('/member/welcome.php?is_junior=1');</script>";
	}
	else {
		echo "<script>top.location.replace('/member/welcome.php?is_none_auth=1');</script>";
	}

}
else {

	echo "
	<script>
		alert('시스템 에러 입니다. 관리자에 문의해 주세요');
		location.replace('about:blank');
	</script>
	";

}

?>