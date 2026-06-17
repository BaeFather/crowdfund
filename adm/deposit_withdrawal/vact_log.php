<?
###############################################################################
## 가상계좌입금내역 (신한은행)
###############################################################################

include_once('./_common.php');

$sub_menu = '500400';
auth_check($auth[$sub_menu], 'w');

$g5['title'] = $menu["menu500"][4][1] ." (신한은행)";

while( list($k, $v)=each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }


if(!$view) $view = 'investor';
if(!$sdate) $sdate = date('Y-m-d', strtotime('-1 day'));
if(!$edate) $edate = date('Y-m-d');


if($mode == 'download') {
	$file_name = $html_title . " " . date('Ymd_Hi') . ".xls";
	$file_name = iconv("utf-8", "euc-kr", $file_name);

	header( "Content-type: application/vnd.ms-excel;" );
	header( "Content-Disposition: attachment; filename=$file_name" );
	header( "Content-description: PHP5 Generated Data" );
}


if($mode != 'download') {

	include_once(G5_ADMIN_PATH . '/admin.head.php');

?>

<style>
table {border-collapse:collapse; font-size:13px}
.table th {font-size:14px}
.content { min-width:1280px }
.content .tabX { height:42px; background:url('/images/tab_bg.gif') repeat-x left bottom; }
.content .tabX li { float:left; width:200px; margin-right:3px; line-height:40px; text-align:center; font-size:16px; color:#202020; background-color:#f7f7f7; border:1px solid #e5e5e5; border-bottom:0; cursor:pointer; }
.content .tabX li.on { border:1px solid #ccc; background-color:#fff; border-bottom-color:#fff; }
.content .tabX li:last-child { margin:0; display:inline-block; }
.content .tabXarea { display:block;margin:0; padding:20px; min-height:400px;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
#cont > div     { line-height:16px; padding:0; font-size:12px; }
#cont > div.off { height:17px; overflow:hidden; color:'' }
#cont > div.on  { color:#3366FF }
.btn_area { text-align:left; }
</style>
<style>
#paging_span { margin:0; padding:0; text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#000; border:1px solid #000; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

	<div class="content" style="margin:30px auto">
		<ul class="tabX" style="width:100%;list-style:none;padding-left:20px;margin:0;">
			<li onClick="location.href='?view=investor'" <?=($view=='investor')?'class="on"':''?>>투자자 예치금</li>
			<li onClick="location.href='?view=loaner';" <?=($view=='loaner')?'class="on"':''?>>차주 상환금</li>
		</ul>
		<div class="tabXarea">

<?
}

include_once("vact_log_list.{$view}.php");

if($mode != 'download') {
?>

		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>

<?

include_once (G5_ADMIN_PATH . '/admin.tail.php');

}

?>