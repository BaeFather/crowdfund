<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
?>
<?
$aaa="^^^경기도 안산시 단원구 고잔동 720 호수공원아파트 제130동 제2층 제202호_신동기.pdf^^신한은행 CMS.pdf^^^";
$bbb = preg_replace("/[\^]+/", "^", $aaa);
echo $aaa."<br/>".$bbb;
die();
?>
<?
$idx="783";
$file_name_ori = "경기도 안산시 단원구 고잔동 720 호수공원아파트 제130동 제2층 제202호_신동기.pdf";
$file_name_ori = "신동기 근저당,전세권계약서.pdf";
$file_name_ori = "법률의견서_신동기_헬로펀딩_20220509_제출본.pdf";
$file_name_ori = "신한은행 CMS.pdf";

$sql = "SELECT * FROM hloan_content WHERE hcseq='$idx'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

$ifile = $row["ifile"];
$ifile_ori = $row["ifile_ori"];

echo $ifile;
echo "<br/>";
echo $ifile_ori;

$ifile = str_replace("^$file_name","", $ifile);
$ifile = str_replace("$file_name^","", $ifile);
$ifile = str_replace("$file_name","", $ifile);

$ifile_ori = str_replace("^$file_name_ori","", $ifile_ori);
$ifile_ori = str_replace("$file_name_ori^","", $ifile_ori);
$ifile_ori = str_replace("$file_name_ori","", $ifile_ori);

echo "<br/><br/>";
echo $ifile;
echo "<br/>";
echo $ifile_ori;

echo "<br/><br/>";
$up_sql = "UPDATE hloan_content 
			  SET ifile = '$ifile',
				  ifile_ori = '$ifile_ori'
			WHERE hcseq = '$idx'";
echo $up_sql;
?>