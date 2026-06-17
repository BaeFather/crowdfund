<?
###############################################################################
## 친구초대 이벤트
###############################################################################

include_once('./_common.php');

$sub_menu = "900101";
auth_check($auth[$sub_menu], 'w');

while( list($k, $v)=each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }


$sql = "SELECT * FROM recommend_event_config WHERE 1 ";
$sql.= ($event_no) ? " AND event_no='".$event_no."'" : " ORDER BY event_no DESC LIMIT 1";
$EVENT_CONF = sql_fetch($sql);

if(!$event_no) $event_no = $EVENT_CONF['event_no'];

//if($member['mb_id']=='admin_sori9th') { print_rr($EVENT_CONF, 'font-size:12px;line-height:14px;color:red'); }

$target = (!$target) ? "recmdee" : $target;
switch($target) {
	case 'recmdee' : $print_target = "피추천인";     $print_target_summary = "(추천을 받는 회원)"; break;
	case 'recmder' : $print_target = "추천인";       $print_target_summary = "(추천을 하는 회원)"; break;
	case 'pid'     : $print_target = "파트너가입";   $print_target_summary = "(파트너가입 회원)"; break;
	case 'recadm'  : $print_target = "파트너가입";   $print_target_summary = "(파트너가입 회원)"; break;
}


$event_sub_title = $print_target . $print_target_summary . " 목록";


$g5['title'] = $menu["menu900"][1][1];
//if($event_sub_title) $g5['title'].= " > " . $event_sub_title;

include_once(G5_ADMIN_PATH . '/admin.head.php');
?>

<style>
table {border-collapse:collapse; font-size:13px}
.content .tabX { height:42px; background:url('/images/tab_bg.gif') repeat-x left bottom; }
.content .tabX li { float:left; width:200px; margin-right:3px; line-height:40px; text-align:center; font-size:16px; color:#202020; background-color:#f7f7f7; border:1px solid #e5e5e5; border-bottom:0; cursor:pointer; }
.content .tabX li.on { border:1px solid #ccc; background-color:#fff; border-bottom-color:#fff; }
.content .tabX li:last-child { margin:0; display:inline-block; }
.content .tabXarea { display:block;margin:0; padding:20px; min-height:400px;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
#cont > div     { line-height:16px; padding:0; font-size:12px; }
#cont > div.off { height:17px; overflow:hidden; color:'' }
#cont > div.on  { color:#3366FF }
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
			<li onClick="location.href='?event_no=<?=$event_no?>&target=recmdee'" <?=($target=='recmdee')?'class="on"':''?>>피추천인</li>
			<li onClick="location.href='?event_no=<?=$event_no?>&target=recmder'" <?=($target=='recmder')?'class="on"':''?>>추천인</li>
			<li onClick="location.href='?event_no=<?=$event_no?>&target=pid'" <?=($target=='pid')?'class="on"':''?>>파트너가입(네이버)</li>
			<li onClick="location.href='?event_no=<?=$event_no?>&target=recadm'" <?=($target=='recadm')?'class="on"':''?>>이벤트목록관리</li>
		</ul>
		<div class="tabXarea">

<?

if($target=='recmdee') {
	include_once("recmdee_list.php");
}
else if($target=='recmder') {
	include_once("recmder_list.php");
}
else if($target=="pid") {
	include_once("pid_list.php");
}
elseif($target=="recadm") {
	include_once("recadmin_list.php");
}


?>

		</div>
	</div>
</div>

<?

include_once (G5_ADMIN_PATH . '/admin.tail.php');

?>