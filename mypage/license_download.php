<?

include_once('./_common.php');

$mb_id = trim($_REQUEST['mb_id']);
$filNm = 'business_license';

if($mb_id=="") {

	if(!$is_member) {
		alert("올바른 경로가 아닙니다."); exit;
	}
	$file_name  = $member[$filNm];

}
else{

	$MB = sql_fetch("SELECT mb_no, $filNm FROM g5_member WHERE mb_id = '".$mb_id."'");
	if($MB['mb_no']) {
		$file_name  = $MB[$filNm];
	}
	else {
		alert("올바른 경로가 아닙니다."); exit;
	}

}


$upload_folder = G5_DATA_PATH."/member/" . $filNm;
$file_path = $upload_folder."/".$file_name;

if($file_name && $file_path) {

	if(file_exists($file_path)) {

		header("Content-Type: doesn/matter");
		header("content-length: ". filesize($file_path));
		header("Content-Disposition: attachment; filename=$file_name");
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0");

		if(is_file($file_path)) {
			$fp = fopen($file_path, "r");
			if(!fpassthru($fp)) {
				fclose($fp);
			}
		}

	}
	else {
		alert("존재하지 않습니다."); exit;
	}

}
else {
	alert("존재하지 않습니다."); exit;
}

sql_close();
exit;

?>