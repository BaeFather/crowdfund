<?

include_once('../common.php');
include_once('../lib/common.lib.php');
include_once('../lib/sms.lib.php');
include_once(G5_PATH . '/mypage/crypt.php');

if($_SERVER["REQUEST_METHOD"]!="POST") {
	//header("HTTP/1.0 404 Not Found");
	//exit;
}

while(list($k, $v) = each($_POST)) { ${$k} = @trim($v); }

$mb_hp          = $c_mb_hp1 . $c_mb_hp2 . $c_mb_hp3;
$articles_agree = ($c_agree_provision && $c_agree_privacy) ? '1' : '';
$options_agree  = ($c_agree_marketing) ? '1' : '';
$mb_mailling    = ($c_mb_mailling) ? 1 : 0;
$mb_sms         = ($c_mb_sms) ? 1 : 0;

$syndi_id       = mysqli_real_escape_string($g5['connect_db'], $syndi_id);
if ($syndi_id=="tvtalk") $tvtalk_id = mysqli_real_escape_string($g5['connect_db'], $tvtalk_id);

$mb_id          = mysqli_real_escape_string($g5['connect_db'], $c_mb_id);
$mb_hp          = mysqli_real_escape_string($g5['connect_db'], $mb_hp);
$mb_hp_key      = substr($mb_hp, -4);

$mb_dupinfo     = mysqli_real_escape_string($g5['connect_db'], $c_mb_dupinfo);
if($mb_dupinfo=='') { echo 'DUP_ID_CHECK_ERROR'; exit; }

$mb_name        = mysqli_real_escape_string($g5['connect_db'], $c_mb_name);
$mb_email       = mysqli_real_escape_string($g5['connect_db'], $c_mb_email);
$mb_mailling    = 1 * mysqli_real_escape_string($g5['connect_db'], $mb_mailling);
$mb_sms         = 1 * mysqli_real_escape_string($g5['connect_db'], $mb_sms);

$mb_gender      = mysqli_real_escape_string($g5['connect_db'], $c_mb_sex);
$mb_birthday    = mysqli_real_escape_string($g5['connect_db'], $c_mb_birthday);
$key = 'jumin';

$birthday = encrypt($mb_birthday, $key);

$basic_member_investor_type = 1;  // 기본 개인 투자자 유형 : 1.일반, 2.소득적격투자자, 3.전문투자자

$device = (G5_IS_MOBILE) ? "mobile" : "pc";

if (substr($mb_email,0,4)=="test") $mode="test";

// ID 중복체크
$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE mb_id='".$mb_id."'");
if($row["cnt"]>0) { echo 'DUP_ID'; exit; }

// 핸드폰 중복체크
//$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE member_type='1' AND mb_leave_date='' AND mb_hp='".$mb_hp."'");
//$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE member_type='1' AND mb_hp='".$mb_hp."'");
$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE member_type='1' AND mb_hp='".masterEncrypt($mb_hp, false)."'");
if($row["cnt"]>0) { 
	if ($mode=="test") {
	} else {
		echo 'DUP_HP'; 
		exit; 
	}
}

$receive_method = '2';		// 최초가입시 원리금 수취방식을 예치금으로 받도록 처리 : 2018-03-21

$query = "
	INSERT INTO
		g5_member
	SET
		mb_level       = '1',
		mb_id          = '".$mb_id."',
		member_type    = '".$member_type."',
		member_investor_type = '".$basic_member_investor_type."',
		mb_password    = '".get_encrypt_string2($mb_password)."',
		mb_name        = '".$mb_name."',
		mb_hp          = '".masterEncrypt($mb_hp, false)."',
		mb_hp_key      = '".$mb_hp_key."',
		mb_email       = '".$mb_email."',
		mb_dupinfo     = '".$mb_dupinfo."',
		mb_mailling    = '".$mb_mailling."',
		receive_method = '".$receive_method."',
		mb_sms         = '".$mb_sms."',
		mb_ip          = '".$_SERVER['REMOTE_ADDR']."',
		mb_datetime    = NOW(),
		articles_agree = '".$articles_agree."',
		options_agree  = '".$options_agree."',
		device         = '".$device."',
		mb_1           = '".$birthday."',
		mb_2           = '".$mb_gender."',
		syndi_id       = '".$syndi_id."',
		syndi_date     = NOW()";

if ($syndi_id == "TvTalk") $query .= ", tvtalk_userid='".$tvtalk_id."', tvtalk_rdate=now()";
		
//echo "$query";

if (substr($mb_email,0,4)=="test")
	$result = 1; // test
else 
	$result = sql_query($query);
/*
$s = curl_init();
curl_setopt($s,CURLOPT_URL, "http://m.tvtalk.tv/partner_proc.php?tvtalkid=$tvtalk_id&helloid=$mb_id");
curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
$curl_res = curl_exec($s); 
curl_close($s); 
*/
if($result) {
	echo "OK"; exit;
}

//$json_res = array();
//$json_res["sql_res"] = $result;
//echo json_encode($json_res);
?>