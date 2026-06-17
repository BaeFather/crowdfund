<?

include_once('./_common.php');

$sub_menu = '900901';
auth_check($auth[$sub_menu], "w");

while( list($k, $v) = each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }



$qstr = $_SERVER['QUERY_STRING'];
if($idx) {
	$qstr = preg_replace("/&idx=([0-9]){1,10}/", "", $qstr);
}
if($page) {
	$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $qstr);
}


$html_title = $menu['menu900'][12][1];
$g5['title'] = ($idx!='') ? $html_title.' 상세보기' : $html_title.' 목록';

include_once (G5_ADMIN_PATH.'/admin.head.php');

?>

<style>
#paging_span { margin-top:10px;  text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:30px; padding:0 5px; color:#585657; line-height:30px; border:1px solid #d0d0d0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#284893; border-color:#284893; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

<?
if($idx!='') {
	include_once("corp_invest_request.detail.php");
	echo "<br /><br />\n";
}

include_once("corp_invest_request.list.php");
?>

</div>

<?

include_once (G5_ADMIN_PATH.'/admin.tail.php');

?>