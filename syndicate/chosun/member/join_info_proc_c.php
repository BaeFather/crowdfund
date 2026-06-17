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
$mb_mailling    = ($mb_mailling) ? 1 : 0;
$mb_sms         = ($mb_sms) ? 1 : 0;
$mb_co_reg_num  = $mb_co_reg_num1.'-'.$mb_co_reg_num2.'-'.$mb_co_reg_num3;
$is_creditor    = ($is_creditor) ? $is_creditor : 'N';

$mb_id          = mysqli_real_escape_string($hf['connect_db'], $mb_id);
$mb_hp          = mysqli_real_escape_string($hf['connect_db'], $mb_hp);
$mb_hp_key      = substr($mb_hp, -4);

//$mb_dupinfo     = mysqli_real_escape_string($g5['connect_db'], $mb_dupinfo);
$mb_name        = mysqli_real_escape_string($hf['connect_db'], $mb_name);
$mb_email       = mysqli_real_escape_string($hf['connect_db'], $mb_email);
$mb_mailling    = 1 * mysqli_real_escape_string($hf['connect_db'], $mb_mailling);
$mb_sms         = 1 * mysqli_real_escape_string($hf['connect_db'], $mb_sms);
$rec_mb_id      = mysqli_real_escape_string($hf['connect_db'], $rec_mb_id);
$mb_co_name     = mysqli_real_escape_string($hf['connect_db'], $mb_co_name);
$mb_co_reg_num  = mysqli_real_escape_string($hf['connect_db'], $mb_co_reg_num);



if( $rec_mb_id ) {
	$ROW = sql_fetch("SELECT mb_no FROM g5_member WHERE mb_id='".$rec_mb_id."' AND  mb_level>'0'");
	if($ROW['mb_no']) {
		$rec_mb_no = $ROW['mb_no'];
	}
}

$device = (G5_IS_MOBILE) ? "mobile" : "pc";

// ID 중복체크
$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE mb_id='".$mb_id."'");
if($row["cnt"]>0) {
	echo 'DUP_ID'; exit;
}


// 핸드폰 중복체크
//$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE mb_hp='".$mb_hp."' AND mb_leave_date='' AND member_type='1'");
$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE mb_hp='".masterEncrypt($mb_hp, false)."' AND mb_leave_date='' AND member_type='1'");
if($row["cnt"]>0){
	echo 'DUP_HP'; exit;
}


/*
// 이메일 중복체크
$row   = sql_fetch("SELECT COUNT(mb_no) AS cnt FROM g5_member WHERE mb_email='".$mb_email."' AND mb_leave_date='' AND member_type='1'");
if( $row["cnt"] > 0 ) {
	echo 'DUP_EMAIL'; exit;
}
*/

$fdir = HF_DATA_PATH."/member";


//사업자등록증
if ( $_FILES['business_license']['error']==0 && $_FILES["business_license"]['size'] ) {
	if( preg_match('/(pdf|jpg|jpeg|png|gif|bmp)/i', $_FILES['business_license']['type']) ) {
		$new_file_name = "";
		list($usec, $sec) = explode(" ", microtime());
		$reg_time = $usec * 1000000;
		$ext = substr(strrchr($_FILES["business_license"]['name'],"."), 1);
		$ext = strtolower($ext);
		$new_file_name = time().$reg_time.".".$ext;
		if(move_uploaded_file($_FILES['business_license']['tmp_name'], $fdir.'/'.$new_file_name)) {
			$fname1 = $new_file_name;
		}
		else { echo "FILE_SAVE_ERROR(1)"; exit; }
	}
	else {
		echo "DISALLOW_FILE(1)";		//alert('이미지 및 PDF 문서 파일만 허용됩니다.');
	}
}

//통장사본
if ( $_FILES['bankbook']['error']==0 && $_FILES["bankbook"]['size'] ) {
	if( preg_match('/(pdf|jpg|jpeg|png|gif|bmp)/i', $_FILES['bankbook']['type']) ) {
		$new_file_name = "";
		list($usec, $sec) = explode(" ", microtime());
		$reg_time = $usec * 1000000;
		$ext = substr(strrchr($_FILES["bankbook"]['name'],"."), 1);
		$ext = strtolower($ext);
		$new_file_name = time().$reg_time.".".$ext;
		if(move_uploaded_file($_FILES['bankbook']['tmp_name'], $fdir.'/'.$new_file_name)) {
			$fname2 = $new_file_name;
		}
		else { echo "FILE_SAVE_ERROR(2)"; exit; }
	}
	else {
		echo "DISALLOW_FILE(2)";		//alert('이미지 및 PDF 문서 파일만 허용됩니다.');
	}
}

//대부업 등록증
if ( $_FILES['loan_co_license']['error']==0 && $_FILES["loan_co_license"]['size'] ) {
	if( preg_match('/(pdf|jpg|jpeg|png|gif|bmp)/i', $_FILES['loan_co_license']['type']) ) {
		$new_file_name = "";
		list($usec, $sec) = explode(" ", microtime());
		$reg_time = $usec * 1000000;
		$ext = substr(strrchr($_FILES["loan_co_license"]['name'],"."), 1);
		$ext = strtolower($ext);
		$new_file_name = time().$reg_time.".".$ext;
		if(move_uploaded_file($_FILES['loan_co_license']['tmp_name'], $fdir.'/'.$new_file_name)) {
			$fname3 = $new_file_name;
		}
		else { echo "FILE_SAVE_ERROR(3)"; exit; }
	}
	else {
		echo "DISALLOW_FILE(3)";		//alert('이미지 및 PDF 문서 파일만 허용됩니다.');
	}
}


$receive_method = '2';		// 최초가입시 원리금 수취방식을 예치금으로 받도록 처리 : 2018-03-21

$query = "
	INSERT INTO
		g5_member
	SET
		mb_id            = '".$mb_id."',
		mb_level         = '0',
		member_type      = '".$member_type."',
		mb_name          = '".$mb_name."',
		mb_co_name       = '".$mb_co_name."',
		mb_co_reg_num    = '".$mb_co_reg_num."',
		mb_hp            = '".masterEncrypt($mb_hp, false)."',
		mb_hp_key        = '".$mb_hp_key."',
		mb_email         = '".$mb_email."',
		mb_dupinfo       = '".$mb_dupinfo."',
		mb_mailling      = '".$mb_mailling."',
		receive_method   = '".$receive_method."',
		mb_sms           = '".$mb_sms."',
		is_creditor      = '".$is_creditor."',
		business_license = '".$fname1."',
		bankbook         = '".$fname2."',
		loan_co_license  = '".$fname3."',
		mb_ip            = '".$_SERVER['REMOTE_ADDR']."',
		mb_datetime      = NOW(),
		articles_agree   = '".$articles_agree."',
		options_agree    = '".$options_agree."',
		device           = '".$device."',
		mb_password      = '".get_encrypt_string2($mb_password)."',
		syndi_id         = 'chosun',
		syndi_userid     = '',
		syndi_date       = NOW(),
		chosun_userid    = 'chosun',
		chosun_rdate     = NOW()";

if($rec_mb_no && $rec_mb_id) {
	$query.= ", ";
	$query.= "rec_mb_no='$rec_mb_no', ";
	$query.= "rec_mb_id='$rec_mb_id'";
	//$query.= "rec_date=NOW()";  //가상계좌설정시 등록해준다.
}

$result = sql_query($query);
if($result) {

	$mb_no = sql_insert_id();
	member_edit_log($mb_no);	// 회원정보변경기록

	echo "OK";

} else {

	echo "X";

}
?>