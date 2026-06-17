<?php

include_once('./_common.php');

$sub_menu = "900600";
auth_check($auth[$sub_menu], 'w');

while( list($k, $v)=each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


$g5['title'] = $menu["menu900"][8][1];
$g5['title'].= " > " . $menu9006_sub_title;

include_once(G5_ADMIN_PATH . '/admin.head.php');
?>
<div class="row" style="width:100%;">
	<div class="col-lg-12">

		<div class="panel-body">

				<div style="text-align:right;margin-bottom:10px;">
					<a onclick="go_new();"  class="btn btn-info">신규</a>
				</div>

<?

$event_idx = trim($_REQUEST['event_idx']);
$field     = trim($_REQUEST['field']);
$keyword   = trim($_REQUEST['keyword']);
$device    = trim($_REQUEST['device']);
$view_flag = trim($_REQUEST['view_flag']);

$where = " 1=1 ";
$where.= ($member['mb_id']=='seintax') ? " AND event_idx='3'" : "";		// 세인법무법인 관리자는 법인설립안내센터 상담신청 관련된 데이터만 보기
$where.= ($event_idx) ? " AND event_idx='$event_idx'" : "";
$where.= ($device) ? " AND device='$device' " : "";
$where.= ($view_flag) ? " AND view_flag='$view_flag' " : "";
$where.= ($field && $keyword) ? " AND $field LIKE '%$keyword%' " : "";

$sql = "
	SELECT
		COUNT(idx) AS cnt_idx
	FROM
		cf_event_10bM";
$row = sql_fetch($sql);
$total_count = $row['cnt_idx'];

$page_rows  = $config['cf_page_rows'];
$total_page = ceil($total_count / $page_rows);							// 전체 페이지 계산
if ($page < 1) $page = 1;																		// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;										// 시작 열을 구함

$sql = "
	SELECT
		A.* , (select count(*) from cf_event_10bS where ymd=A.ymd) app_num
	FROM
		cf_event_10bM A  order by A.ymd desc";

$res = sql_query($sql);
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++) {
	$LIST[] = sql_fetch_array($res);
}
//print_rr($LIST, "font-size:11px");

?>


			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center">NO</th>
							<th class="text-center">일자</th>
							<th class="text-center">상태</th>
							<th class="text-center">펀딩금액</th>
							<th class="text-center">상금</th>
							<th class="text-center">응모</th>
							<th class="text-center">당첨자수</th>
							<th class="text-center">신규가입</th>
							<th class="text-center">수정</th>
							<th class="text-center">상품명</th>
						</tr>
					</thead>
					<form id="fList">
					<tbody>
<?

if(count($LIST)) {
	$list_num = $total_count - ($page - 1) * $page_rows;	for($i=0,$j=1; $i<count($LIST);$i++) {

	$tmp = explode(",",$LIST[$i]["input_date"]);
	$l_ymd="";
	if (count($tmp)) {
		for ($m=0 ; $m<count($tmp) ; $m++) {
			if  ($m<>0) $l_ymd .= ",";
			$l_ymd .= "'".$tmp[$m]."'";
		}
	} else {
		$l_ymd = "'".$lIST["input_date"]."'";
	}
	//$l_ymd = $LIST[$i][ymd]-1;
	$sqlN = "select count(*) ccn from g5_member where event_id='100B' and substr(mb_datetime ,1,10) in ($l_ymd)";
	//echo "$sqlN<br/>";
	$resN = sql_query($sqlN);
	$rowN = sql_fetch_array($resN);
	$new_cnt = $rowN['ccn'];
	if ($LIST[$i]['stat']=="R") $stm = "준비";
	else if ($LIST[$i]['stat']=="I") $stm = "진행중";
	else if ($LIST[$i]['stat']=="E") $stm = "종료";
	else $stm = "ERROR";
?>
						<tr class="odd">
							<td align="center"><?=$list_num?></td>
							<td align="center"><a onclick="go_edit('<?=$LIST[$i]['ymd']?>');" style="cursor:pointer;"><?=$LIST[$i]['ymd']?></a></td>
							<td align="center"><?=$stm?> </td>
							<td align="center"><?=number_format($LIST[$i]['funding_money'])?></td>
							<td align="center"><?=number_format($LIST[$i]['prize']/10000)?> 만원</td>
							<td align="center"><?=$LIST[$i]['app_num']?> </td>
							<td align="center"><?=$LIST[$i]['prize_cnt']?$LIST[$i]['prize_cnt']:"&nbsp;"?></td>
							<td align="center"><?=$new_cnt?number_format($new_cnt):"&nbsp;"?></td>
							<td align="center"><a onclick="go_edit('<?=$LIST[$i][ymd]?>');"  class="btn btn-info">수정</a></td>
							<td align="center"><?=$LIST[$i]['product_name']?></td>
						</tr>
<?
		$list_num--;
	}
}
else {
	echo '<tr class="odd"><td colspan="11" align="center">데이터가 없습니다.</td></tr>' . PHP_EOL;
}
?>
					</tbody>
					</form>
				</table>
			</div>

			<div style="width:100%; text-align: center;">
				<ul class="pagination">
<?
$qstr = @preg_replace("/?page=([0-9]){1,10}|&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
$qstr = @preg_replace("/?idx=([0-9]){1,10}|&idx=([0-9]){1,10}/", "", $qstr);
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page=');
?>
				</ul>
			</div>




		</div><!-- /.panel-body -->
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<script>
function go_new() {
	var pu = window.open("100b_edit.php" , "_blank" , "width=700,height=600,resizable=yes,scrollbars=yes");
}
function go_edit(ymd) {
	var pu = window.open("100b_edit.php?ymd="+ymd, "_blank" , "width=700,height=600,resizable=yes,scrollbars=yes");
}
</script>