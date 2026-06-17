<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//처리
?>
<?php
include_once('./_common.php');
include_once('../lib/etc.lib.php');
?>
<?
//$chk_sql = "SELECT COUNT(idx) chk_count FROM cf_apat_loan_request WHERE ip='154.16.51.115'";
$chk_sql = "SELECT COUNT(idx) chk_count FROM cf_apat_loan_request WHERE ip='".$_SERVER['REMOTE_ADDR']."'";
$chk_res = sql_query($chk_sql);
$chk_row = sql_fetch_array($chk_res);
$chk_count = $chk_row["chk_count"];

echo $chk_count;
?>
