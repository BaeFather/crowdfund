<?
// 피추천인(추천받는 사람) 리스트

include_once("_common.php");


$recmder_count_sql = "SELECT COUNT(mb_no) FROM g5_member WHERE rec_mb_no=A.mb_no AND LEFT(mb_datetime,10) BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'";		// 추천받은 횟수

$where = "";
$where.= " AND A.member_group='F' AND A.mb_level NOT IN('9','10')";
$where.= " AND ( $recmder_count_sql ) > 0";		// 추천받은 횟수가 있는 데이터만..

// 2021-11-12 추가 ----------------------------------------------------------------
if($EVENT_CONF['only_rec_id']) {
	$where.= " AND A.mb_id='".$EVENT_CONF['only_rec_id']."'";		// 피추천인이 고정된 이벤트의 경우 (피추천인ID: hello)
}
else {
	$where.= " AND A.mb_id!='hello'";			// 일반 추천이벤트의 경우
}
// 2021-11-12 추가 ----------------------------------------------------------------

if($field && $keyword) {
	$where.= ($field=='A.mb_hp') ? " AND $field = '".masterEncrypt($keyword, false)."'" : " AND $field = '".$keyword."'";
}

$sql = "SELECT COUNT(A.mb_no) AS cnt FROM g5_member A WHERE (1) $where";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 20;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;

$sql_order = "";
if($sort_field) {
	$sql_order.= $sort_field." ".$sort.", ";
}
else {
	$sql_order.= "approved_count DESC, ";
	$sql_order.= "recmder_count DESC, ";
	$sql_order.= "recmder_invest_amount DESC, ";
}
$sql_order.= "mb_no DESC";


//**** 이벤트에 따른 대상상품군 선별 ****//
if($EVENT_CONF['prdt_ca']=='2') {
	$sqlto = " AND CC.category='2' ";																		// 부동산 전체를 대상으로..
}
else if($EVENT_CONF['prdt_ca']=='2-1') {
	$sqlto = " AND CC.category='2' AND CC.mortgage_guarantees='' ";			// PF만
}
else if($EVENT_CONF['prdt_ca']=='2-2') {
	$sqlto = " AND CC.category='2' AND CC.mortgage_guarantees='1' ";		// 주담대만
}
else if($EVENT_CONF['prdt_ca']=='3') {
	$sqlto = " AND CC.category='3' ";																		// 매출채권 대상
}
else if($EVENT_CONF['prdt_ca']=='1') {
	$sqlto = " AND CC.category='1' ";																		// 동산
}
else {
	//
}
//**** 이벤트에 따른 대상상품군 선별 ****//

$sql = "
	SELECT
		A.mb_no, A.mb_id, A.mb_name, A.mb_co_name, A.mb_hp, A.mb_datetime,
		(
			$recmder_count_sql
		) AS recmder_count,
		(
			SELECT
				COUNT(AA.idx)
			FROM
				cf_product_invest AA
			LEFT JOIN
				g5_member BB  ON AA.member_idx=BB.mb_no
			LEFT JOIN
				cf_product CC  ON AA.product_idx=CC.idx
			WHERE (1)
				AND AA.invest_state IN('Y','R')
				AND BB.rec_mb_no=A.mb_no
				AND LEFT(BB.mb_datetime,10) BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
				AND AA.insert_date BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
				AND CC.state IN('1','2','5','6')
				".$sqlto."
		) AS recmder_invest_count,
		(
			SELECT
				IFNULL(SUM(AA.amount),0)
			FROM
				cf_product_invest AA
			LEFT JOIN
				g5_member BB  ON AA.member_idx=BB.mb_no
			LEFT JOIN
				cf_product CC  ON AA.product_idx=CC.idx
			WHERE (1)
				AND AA.invest_state IN('Y','R')
				AND BB.rec_mb_no=A.mb_no
				AND LEFT(BB.mb_datetime,10) BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
				AND AA.insert_date BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
				AND CC.state IN('1','2','5','6')
				".$sqlto."
		) AS recmder_invest_amount,
		(
			SELECT
				COUNT(idx)
			FROM
				recommend_reward_log
			WHERE (1)
				AND member_idx=A.mb_no
				AND event_no='".$EVENT_CONF['event_no']."'
				AND position='recmdee'
				AND approved='1' AND invalid=''
		) AS approved_count,
		(
			SELECT
				IFNULL(SUM(reward_amount),0)
			FROM
				recommend_reward_log
			WHERE (1)
				AND member_idx=A.mb_no
				AND event_no='".$EVENT_CONF['event_no']."'
				AND position='recmdee'
				AND approved='1' AND invalid=''
		) AS approved_amount,
		(
			SELECT
				COUNT(idx)
			FROM
				recommend_reward_log
			WHERE (1)
				AND member_idx=A.mb_no
				AND event_no='".$EVENT_CONF['event_no']."'
				AND position='recmdee'
				AND paid='1' AND invalid=''
		) AS paid_count,
		(
			SELECT
				IFNULL(SUM(reward_amount),0)
			FROM
				recommend_reward_log
			WHERE (1)
				AND member_idx=A.mb_no
				AND event_no='".$EVENT_CONF['event_no']."'
				AND position='recmdee'
				AND paid='1' AND invalid=''
		) AS paid_amount
	FROM
		g5_member A
	WHERE (1)
		$where
	ORDER BY
		$sql_order
	LIMIT
		$from_record, $rows";

//if($member['mb_id']=='admin_sori9th') { print_rr($sql,'font-size:12px;line-height:14px;'); }

$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);
}
$list_count = count($LIST);
sql_free_result($result);

$num = $total_count - $from_record;

?>

<div>

	<div style="padding:4px 10px 4px 10px; font-weight:bold">
		이벤트명 : <?=$EVENT_CONF['event_title'] . " :: " . $event_sub_title?><br/>
		시행기간 : <?=preg_replace("/-/", ".", $EVENT_CONF['sdate'])?> ~ <?=preg_replace("/-/", ".", $EVENT_CONF['edate'])?>
	</div>

	<div style="border:1px solid #DDD;background:#FAFAFA;width:100%;display:inline-block">
		<form id="f_srch" name="f_srch" method="get" action="<?=$_SERVER['PHP_SELF']?>">
		<ul class="col-sm-10 list-inline" style="margin-top:10px">
			<li><select name="event_no" id="event_no" class="form-control input-sm">
					<option value="">::이벤트 조회::</option>
					<?
					$resx = sql_query("SELECT event_no, event_title FROM recommend_event_config WHERE is_real='1' ORDER BY event_no DESC");
					while( $row = sql_fetch_array($resx) ) {
						$selected = ($row['event_no']==$event_no) ? 'selected' : '';
						echo "<option value='".$row['event_no']."' $selected>".$row['event_title']."</option>";
					}
					?>
				</select>
			</li>
			<li>
				<select id="field" name="field" class="form-control input-sm">
					<option value="">::검색항목선택::</option>
					<option value="A.mb_no" <?=($field=='A.mb_no')?'selected':''?>>회원번호</option>
					<option value="A.mb_id" <?=($field=='A.mb_id')?'selected':''?>>아이디</option>
					<option value="A.mb_name" <?=($field=='A.mb_name')?'selected':''?>>성명</option>
					<option value="A.mb_co_name" <?=($field=='A.mb_co_name')?'selected':''?>>법인명</option>
					<option value="A.mb_hp" <?=($field=='A.mb_hp')?'selected':''?>>연락처</option>
				</select>
			</li>
			<li><input type="text" id="keyword" name="keyword" value="<?=$keyword?>" class="form-control input-sm"></li>
			<li><button type="submit" class="btn btn-sm btn-warning">검색</button></li>
		</ul>
		</form>
		<ul class="col-sm-10 list-inline">
			<li>
				<select id="sort_field" class="form-control input-sm">
					<option value="">::정렬필드선택::</option>
					<option value="recmder_count" <?=($sort_field=='recmder_count')?'selected':''?>>추천인수</option>
					<option value="recmder_invest_count" <?=($sort_field=='recmder_invest_count')?'selected':''?>>추천인누적투자수</option>
					<option value="recmder_invest_amount" <?=($sort_field=='recmder_invest_amount')?'selected':''?>>추천인누적투자금</option>
					<option value="approved_amount" <?=($sort_field=='approved_amount')?'selected':''?>>피추천인보상예치금</option>
				</select>
			</li>
			<li>
				<button type="button" onClick="sortList('DESC');" class="btn btn-sm btn-<?=($sort=='DESC')?'info':'default';?>">내림차순</button>
				<button type="button" onClick="sortList('ASC');" class="btn btn-sm btn-<?=($sort=='ASC')?'info':'default';?>">오름차순</button>
			</li>
			<li><button type="button" class="btn btn-sm btn-success" onClick="excel_down();">검색결과 시트저장</button></li>
		</ul>
	</div>

	<div style="margin-top:10px; text-align:right;font-size:12px;color:brown;">"추천인수. 추천인 누적투자수, 추천인 누적투자금"은 이벤트 기간중의 누적데이터 내역임,</div>
	<table class="table table-striped table-bordered table-hover" style="font-size:12px">
		<colgroup>
			<col style="width:60px">
		</colgroup>
		<thead>
			<tr>
				<th style="background:#CC3366;color:#FFF" class="text-center">NO</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">회원번호</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">아이디</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">성명</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">연락처</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">가입일시</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">추천인수(명)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">추천인<br/>누적투자수(건)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">추천인<br/>누적투자금(원)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">보상확정건수</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">확정보상금(원)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">지급건수</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">지급보상금(원)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">추천인내역</th>
			</tr>
		</thead>
		<tbody>
<?
if($list_count) {
	for($i=0; $i<$list_count; $i++) {

		$blind_mb_hp = (strlen($LIST[$i]['mb_hp']) > 4) ? substr($LIST[$i]['mb_hp'], 0, strlen($LIST[$i]['mb_hp'])-4) . "●●●●" : $LIST[$i]['mb_hp'];

		$link = "../member/member_view.php?member_group=F&mb_id=" . $LIST[$i]['mb_id'];

		$recmder_detail_list_link = "recommend_event.php?event_no=$event_no&target=recmder&approved=&date_field=&sdate=&edate=&field=B.target_member_idx&keyword=" . $LIST[$i]['mb_no'];

?>
			<tr align="center">
				<td><?=$num?></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['mb_no']?></a></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['mb_id']?></a></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['mb_name']?><?if($LIST[$i]['mb_co_name']){ echo "<br/>\n(".$LIST[$i]['mb_co_name'].")"; }?></a></td>
				<td><?=$blind_mb_hp?></td>
				<td><?=substr($LIST[$i]['mb_datetime'],0,16)?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_count'])?></a></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_amount'])?></td>
				<td align="right"><?=number_format($LIST[$i]['approved_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['approved_amount'])?></td>
				<td align="right"><?=number_format($LIST[$i]['paid_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['paid_amount'])?></td>
				<td align="center"><button onClick="location.href='<?=$recmder_detail_list_link?>';" class="btn btn-sm btn-primary" style="height:24px;padding:0 10px;line-height:24px;">내역보기</button></td>
			</tr>
<?
		$num--;
	}
}
else {
	echo "<tr><td colspan='20' align='center'>데이터가 없습니다.</td></tr>\n";
}
?>
		</tbody>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 20px 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

</div>

<? $qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']); ?>

<script type="text/javascript">
$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

// 상품정렬
function sortList(param)
{
	if($('#sort_field').val()!='') {
		url = '<?=$_SERVER['PHP_SELF']?>'
		    + '?<?=$qstr?>'
		    + '&sort_field=' + $('#sort_field').val()
		    + '&sort=' + param
		$(location).attr('href', url);
	}
	else {
		alert('정렬필드를 선택하십시요.'); $('#sort_field').focus();
	}
}

function excel_down() {
	if( confirm('다운로드 하시겠습니까?') ) {
		var f = document.f_srch;
		f.target = "axFrame";
		f.action = "recmdee_list_download.php";
		f.submit();

		f.target = f.action = '';
	}
}
</script>