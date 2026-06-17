<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
?>
<?
$sData = array(
	'fileName' => $file_name
);

$sql = "SELECT * FROM hloan_content WHERE hcseq='$idx'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

$ifile = $row["ifile"];
$ifile_ori = $row["ifile_ori"];


$ifile = str_replace("^$file_name","", $ifile);
$ifile = str_replace("$file_name^","", $ifile);
$ifile = str_replace("$file_name","", $ifile);

$ifile_ori = str_replace("^$file_name_ori","", $ifile_ori);
$ifile_ori = str_replace("$file_name_ori^","", $ifile_ori);
$ifile_ori = str_replace("$file_name_ori","", $ifile_ori);

$ifile     = preg_replace("/[\^]+/", "^", $ifile);
$ifile_ori = preg_replace("/[\^]+/", "^", $ifile_ori);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://admin.hellofunding.kr/etc/remote_delete.php');
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $sData);
//$response = curl_exec($curl);
curl_close($curl);

//$fullname = "./afile/".$file_name;
$fullname = "../../data/afile/".$file_name;;
unlink($fullname);

$up_sql = "UPDATE hloan_content 
			  SET ifile = '$ifile',
				  ifile_ori = '$ifile_ori'
			WHERE hcseq = '$idx'";
sql_query($up_sql);
?>