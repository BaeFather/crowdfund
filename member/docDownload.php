<?
include_once('./_common.php');

while(list($key, $value) = each($_REQUEST)) { ${$key} = trim($value); }

if( !in_array($orderFile, array('junior_agreement')) ) { alert("올바른 요청이 아닙니다."); exit; }




$upload_folder = G5_DATA_PATH . '/file';

if($orderFile=='junior_agreement') {
	$file_name = $orderFile.".docx";
	$doc_title = "헬로펀딩_법정대리인동의서";
}


$file_path = $upload_folder . "/" . $file_name;
$FILE_NAME = explode(".", $file_path);

$extension = $FILE_NAME[count($FILE_NAME)-1];

$download_name = $doc_title . ".". $extension;

if($file_name && $file_path) {

	if(file_exists($file_path)) {

		header("Content-Type: doesn/matter");
		header("content-length: ". filesize($file_path));
		header("Content-Disposition: attachment; filename=$download_name");
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
	else { alert("존재하지 않습니다."); exit; }
}
else { alert("존재하지 않습니다."); exit; }

exit;

?>