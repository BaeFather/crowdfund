<?
// https://www.hellofunding.co.kr/common_variables.js 로 rewrite 처리 되어 호출됨

include_once("_common.php");

header('Content-type: application/javascript; charset=UTF-8');

?>
var g5_url       = "<?=G5_URL?>";
var g5_bbs_url   = "<?=G5_BBS_URL?>";
var g5_is_member = "<?=isset($is_member) ? $is_member : '';?>";
var g5_is_admin  = "<?=isset($is_admin) ? $is_admin : '';?>";
var g5_is_mobile = "<?=G5_IS_MOBILE?>";
var g5_bo_table  = "<?=isset($bo_table) ? $bo_table : '';?>";
var g5_sca       = "<?=isset($sca) ? $sca : '';?>";
var g5_editor    = "<?=($config['cf_editor'] && $board['bo_use_dhtml_editor']) ? $config['cf_editor'] : '';?>";
var g5_cookie_domain = "<?=G5_COOKIE_DOMAIN?>";
<? if($is_admin) { echo "var g5_admin_url = \"".G5_ADMIN_URL."\";\n"; } ?>
