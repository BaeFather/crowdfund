<?

include_once('./_common.php');

$sub_menu = "900500";
auth_check($auth[$sub_menu], 'w');

while( list($k, $v)=each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


$g5['title'] = $menu["menu900"][7][1];
$g5['title'].= " > " . $menu9005_sub_title;

include_once(G5_ADMIN_PATH . '/admin.head.php');
?>

<div class="row" style="width:100%;">
	<div class="col-lg-12">
		<div class="panel-body">

<?
if($idx) {

	$sql = "
		SELECT
			A.nm_co_name, A.nm_name, A.schedule_req_date, A.nm_phone, A.ip, A.device, A.view_flag, A.admin_memo, A.rdate,
			B.title, B.sdate, B.edate, B.cancel,
			C.mb_id, C.mb_name, C.mb_co_name, C.mb_hp
		FROM
			invitation_event_request A
		LEFT JOIN
			invitation_event B
		ON
			A.event_idx = B.idx
		LEFT JOIN
			g5_member C
		ON
			A.mb_no = C.mb_no
		WHERE
			A.idx='$idx'";
	$sql.= ($member['mb_id']=='seintax') ? " AND A.event_idx='3'" : "";		// 세인법무법인 관리자는 법인설립안내센터 상담신청 관련된 데이터만 보기

	if($DATA = sql_fetch($sql)) {
		include_once("invitaion_event_detail.php");
		//sql_query("UPDATE invitation_event_request SET view_flag='Y' WHERE idx='$idx'");
	}
}

include_once("invitaion_event_list.php");

?>
		</div><!-- /.panel-body -->
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<?
include_once (G5_ADMIN_PATH . '/admin.tail.php');
?>