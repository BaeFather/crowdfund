<?
include_once('./_common.php');

//include_once('../lib/common.lib.php');
include_once(HF_PATH.'/lib/sms.lib.php');


if ($_SERVER["REQUEST_METHOD"]!="POST") {
	header("HTTP/1.0 404 Not Found");
	exit;
}

while(list($k, $v) = each($_POST)) { ${$k} = @trim($v); }

$leadtime = time() - $ordertime;
if($leadtime > 600) {
	echo "TIME_OVER"; exit;
}


$mb_hp          = $mb_hp1 . $mb_hp2 . $mb_hp3;
$articles_agree = ($agree_provision && $agree_privacy) ? '1' : '';

$options_agree  = ($agree_marketing) ? '1' : '';
$mb_mailling    = ($options_agree) ? 1 : 0;
$mb_sms         = ($options_agree) ? 1 : 0;


$mb_id          = mysqli_real_escape_string($hf['connect_db'], $mb_id);
$mb_hp          = mysqli_real_escape_string($hf['connect_db'], $mb_hp);
$mb_hp_key      = substr($mb_hp, -4);

$mb_dupinfo     = mysqli_real_escape_string($hf['connect_db'], $mb_dupinfo);
if($mb_dupinfo=='') { echo 'DUP_ID_CHECK_ERROR'; exit; }

$mb_ci          = sql_real_escape_string($mb_ci);

$mb_name        = mysqli_real_escape_string($hf['connect_db'], $mb_name);
$mb_email       = mysqli_real_escape_string($hf['connect_db'], $mb_email);
$mb_mailling    = 1 * mysqli_real_escape_string($hf['connect_db'], $mb_mailling);
$mb_sms         = 1 * mysqli_real_escape_string($hf['connect_db'], $mb_sms);
$rec_mb_id      = mysqli_real_escape_string($hf['connect_db'], $rec_mb_id);



$basic_member_investor_type = 1;  // 기본 개인 투자자 유형 : 1.일반, 2.소득적격투자자, 3.전문투자자

if( $rec_mb_id ) {
	$ROW = sql_fetch("SELECT mb_no FROM g5_member WHERE mb_id='".$rec_mb_id."' AND  mb_level>'0'");
	if($ROW['mb_no']) {
		$rec_mb_no = $ROW['mb_no'];
	}
}

$device = (G5_IS_MOBILE) ? "mobile" : "pc";

// ID 중복체크
$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE mb_id='".$mb_id."'");
if($row["cnt"]>0) { echo 'DUP_ID'; exit; }

// 주민번호 중복체크 (CI값 활용 : 2019-12-03 추가)
$row = sql_fetch("SELECT COUNT(mb_no) AS cnt FROM g5_member WHERE mb_level='1' AND member_type='1' AND mb_ci!='' AND mb_ci='".$mb_ci."'");
if($row['cnt']>0) { echo 'DUP_CI'; exit; }

// 핸드폰 중복체크
//$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE member_type='1' AND mb_leave_date='' AND mb_hp='".$mb_hp."'");
$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE member_type='1' AND mb_leave_date='' AND mb_hp='".masterEncrypt($mb_hp, false)."'");
if($row["cnt"]>0) { echo 'DUP_HP'; exit; }

// 이메일 중복체크
$row = sql_fetch("SELECT COUNT(mb_no) AS cnt FROM g5_member WHERE mb_leave_date='' AND member_type='1' AND mb_email='".$mb_email."'");
if($row["cnt"]>0) { echo 'DUP_EMAIL'; exit; }

// 첨부파일 저장
if( in_array($member_investor_type, array('2','3') ) ) {
	$fdir = G5_DATA_PATH . "/member/investor";

	$fcount = (count($_FILES['attach_file']['size'])) ? count($_FILES['attach_file']['size'])-1 : 0;  // 파일필드수 + 1이 전송되어옴.. 원인 모름
	for($i=0; $i<$fcount; $i++) {
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
					echo "FILE_UPLOAD_ERROR";		//alert('ERROR : 파일 전송 문제가 발생하였습니다.');
				}
			}
			else {
				echo "DISALLOW_FILE";		//alert('이미지 및 PDF 문서 파일만 허용됩니다.');
			}
		}
	}
}


$receive_method = '2';		// 최초가입시 원리금 수취방식을 예치금으로 받도록 처리 : 2018-03-21

$query = "
	INSERT INTO
		g5_member
	SET
		mb_id          = '".$mb_id."',
		mb_level       = '1',
		member_type    = '".$member_type."',
		member_investor_type = '".$basic_member_investor_type."',
		mb_name        = '".$mb_name."',
		mb_hp          = '".masterEncrypt($mb_hp, false)."',
		mb_hp_key      = '".$mb_hp_key."',
		mb_email       = '".$mb_email."',
		mb_dupinfo     = '".$mb_dupinfo."',
		mb_ci          = '".$mb_ci."',
		mb_mailling    = '".$mb_mailling."',
		receive_method = '".$receive_method."',
		mb_sms         = '".$mb_sms."',
		invested_mailling = '1',
		mb_ip          = '".$_SERVER['REMOTE_ADDR']."',
		mb_datetime    = NOW(),
		articles_agree = '".$articles_agree."',
		options_agree  = '".$options_agree."',
		device         = '".$device."',
		mb_password    = '".get_encrypt_string2($mb_password)."',
		syndi_id       = '".$_CONF['SYNDI_ID']."',
		syndi_userid   = '',
		syndi_date     = NOW(),
		r114_userid    = '".$_CONF['SYNDI_ID']."',
		r114_rdate     = NOW()";
$result = sql_query($query);

if($result) {

	$mb_no = sql_insert_id();
	member_edit_log($mb_no);	// 회원정보변경기록

	// 2:소득적격개인투자자, 3:전문투자자 일때 첨부파일 내역이 존재하면 승인요청 로그 남김
	if( in_array($member_investor_type, array('2','3')) && count($FNAME)) {
		$osql = "INSERT INTO investor_type_change_request (mb_no, order_type, order_date, allow) VALUES ('$mb_no', '$member_investor_type', NOW(), 'wait')";
		$ores = sql_query($osql);
		$req_idx = sql_insert_id();
		for($i=0; $i<count($FNAME); $i++) {
			$description = ($_POST['memo'][$i]) ? sql_real_escape_string($_POST['memo'][$i]) : '';
			$fsql = "INSERT INTO investor_type_change_request_file (req_idx, mb_no, fname, description) VALUES ('$req_idx', '$mb_no', '".$FNAME[$i]."', '".$description."')";
			$fres = sql_query($fsql);
		}
	}

	// 비회원 SMS수신 번호에 등록된 사용자 삭제
	$res = sql_query("DELETE FROM sms_request_phone WHERE phone_no='$mb_hp'");

	$sms_row = sql_fetch("SELECT * FROM `g5_sms_userinfo` WHERE use_yn='1' AND idx='19'");
	if($sms_row["msg"]){
		$sms_msg = str_replace("{USER_NAME}", $mb_name, $sms_row["msg"]);
		$rst = unit_sms_send($_admin_sms_number, $mb_hp, $sms_msg);
	}

	echo "OK";

}
else {

	echo "X";

}

exit;

?>