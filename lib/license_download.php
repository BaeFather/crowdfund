<?
include_once('./_common.php');

exit;
//_common/download_single.php?FILE_INFO=테이블명|파일컬럼명|시퀀스컬럼명|시퀀스|경로
//업로드 경로는 무조건 /_upload
$ARR_FILE_INFO = explode("|",$FILE_INFO);

if (count($ARR_FILE_INFO) != "5") {
	echo "<script>alert('입력이 올바르지 않습니다.');history.go(-1);</script>";
	exit;
}

$query = "select ".$ARR_FILE_INFO[1]." from edu_".$ARR_FILE_INFO[0]." where ".$ARR_FILE_INFO[2]." = ".$ARR_FILE_INFO[3];


//echo$query;exit;
$result = mysql_query($query);
$affect_num = mysql_num_rows($result);

if(!$affect_num) {
	echo "<script>alert('파일이 존재하지 않습니다.');history.go(-1);</script>";
	exit;
}
$row = mysql_fetch_row($result);

$file_name = $row[0];

$file_path = $upload_folder.$ARR_FILE_INFO[4]."/".$file_name;

if($file_name && $file_path) {
	if(file_exists($file_path)) {
		
("Content-Type: doesn/matter");
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
		echo "<script>alert(' 존재하지 않습니다.');history.go(-1);</script>";
	}
}
else {
	echo "<script>alert('파일이 존재하지 않습니다.');history.go(-1);</script>";
}
?>