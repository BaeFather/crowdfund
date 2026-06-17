<?
exit;
include_once("_common.php");

foreach($_POST as $k=>$v) {
	if( !is_array($_POST[$k]) ) ${$k} = trim($v);
}


if(!$name || !$hp) { echo "ERROR-AUTH"; exit; }

if($name)    $name    = sql_real_escape_string($name);
if($co_name) $co_name = sql_real_escape_string($co_name);
if($email)   $email   = sql_real_escape_string($email);
if($loc)     $loc     = sql_real_escape_string($loc);
if($memo)    $memo    = sql_real_escape_string($memo);
if($pid)	   $pid	    = sql_real_escape_string($pid);

if($already_dept)  $already_dept = preg_replace("/,/", "", $already_dept) * 10000;
if($tadwo)         $tadwo        = preg_replace("/,/", "", $tadwo) * 10000;
if($wamt)          $wamt         = preg_replace("/,/", "", $wamt) * 10000;
if($income)        $income       = preg_replace("/,/", "", $income) * 10000;

$ip     = $_SERVER['REMOTE_ADDR'];
$device = getDevice();
$area   = $_SERVER['GEOIP_CITY'];

$sql = "
	INSERT INTO
		cf_apat_loan_request
	SET
		type          = '".$type."',
		name          = '".$name."',
		co_name       = '".$co_name."',
		hp            = '".masterEncrypt($hp, false)."',
		email         = '".$email."',
		loc           = '".$loc."',
		already_dept  = '".$already_dept."',
		tadwo         = '".$tadwo."',
		relation      = '".$relation."',
		wamt          = '".$wamt."',
		purpose       = '".$purpose."',
		period        = '".$period."',
		income        = '".$income."',
		wtime         = '".$wtime."',
		tenant        = '".$tenant."',
		content       = '".$memo."',
		ip            = '".$ip."',
		device        = '".$device."',
		area          = '".$area."',
		judge_state   = '1',
		pid			  = '".$pid."',
		skin		  = '1',
		regdate       = NOW()";

if(sql_query($sql)) {
	echo 1;
}


exit;


?>