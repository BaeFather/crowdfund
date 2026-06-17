<?php
define('G5_IS_ADMIN', true);
include_once ('../../common.php');

if (!defined('G5_USE_SHOP') || !G5_USE_SHOP)
    die('<p>쇼핑몰 설치 후 이용해 주십시오.</p>');

include_once(G5_ADMIN_PATH.'/admin.lib.php');
include_once(G5_ADMIN_PATH.'/inc_sub_admin_check.php');  //부관리자 체크

?>