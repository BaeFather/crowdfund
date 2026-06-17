<?php
INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/common.php";
INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/function.php";
//error_reporting (E_ALL);
include('kcaptcha.php');

//session_start();


$captcha = new KCAPTCHA();
$captcha->setKeyString(get_session2("captcha_keystring"));
$captcha->getKeyString();
$captcha->image();
?>