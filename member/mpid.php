<?php

// 2022-04-11 무효화 처리
exit;

include_once('./_common.php');
include_once('../lib/function_prc.php');
include_once('../lib/etc.lib.php');

// etc.lib class
$strClass    = new strMemberRefererCheck();
$strLinkUrl  = $_POST["q"];
$strPid      = $_POST["pid"];

$strClass->strLinkUrl = $strLinkUrl;
ECHO $strClass->fn_pid_check($strPid);

?>