<?
/**
 * 투자상품 목록
 */
$sub_menu = "920100";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);
auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while(list($key, $value) = each($_GET)) {
	if(!is_array(${$key})) ${$key} = trim($value);
}

$g5['title'] = $menu['menu920'][1][1];
include_once('../admin.head.php');
?>
<style>
#paging_span { margin:0; padding:0; text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#000; border:1px solid #000; cursor:default }
</style>
<?
/*
$sql = "select count(A.idx) AS cnt
			from cf_product A , (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B
			where (A.state='1' OR A.state='2' OR A.state='5')
			and (A.right_display ='Y' OR A.right_set_date <> '0000-00-00')
			and A.idx=B.product_idx
			order by A.idx desc";
*/
$sql = "
	SELECT
		COUNT(A.idx) AS cnt
	FROM
		cf_product A
	LEFT JOIN
		cf_product_container B  ON A.idx=B.product_idx
	LEFT JOIN
		(SELECT product_idx, max(turn) max_turn FROM cf_product_success GROUP BY product_idx) C  ON A.idx=C.product_idx
	WHERE 1
		AND A.state IN('1','2','5')
		AND (B.right_display='Y' OR B.right_set_date <> '0000-00-00')";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$page_rows = 10;
$total_page  = ceil($total_count / $page_rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows; // 시작 열을 구함
$num = $total_count - $from_record;

/*
$sql = "select A.idx, A.recruit_amount,A.invest_days, A.invest_period, A.title, A.invest_return ,A.state, A.invest_days, A.end_date,
			   A.loan_end_date_orig, A.loan_start_date,
			   A.right_set_date, A.right_pic, A.field_pic, A.deposit_pic,
			   A.stream_url1, A.stream_url2, A.state,
			   B.max_turn
			from cf_product A , (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B
			where (A.state='1' OR A.state='2' OR A.state='5')
			and (A.right_display ='Y' OR A.right_set_date <> '0000-00-00')
			and A.idx=B.product_idx
			order by A.idx desc
			LIMIT $from_record, $page_rows";
*/
$sql = "
	SELECT
		A.idx, A.recruit_amount,A.invest_days, A.invest_period, A.title, A.invest_return ,A.state, A.invest_days, A.end_date,
		A.loan_end_date_orig, A.loan_start_date,
		A.stream_url1, A.stream_url2, A.state,
		B.right_set_date, B.right_pic, B.field_pic, B.deposit_pic,
		C.max_turn
	FROM
		cf_product A
	LEFT JOIN
		cf_product_container B  ON A.idx=B.product_idx
	LEFT JOIN
		(SELECT product_idx, max(turn) max_turn FROM cf_product_success GROUP BY product_idx) C on A.idx=C.product_idx
	WHERE 1
		AND A.state IN('1','2','5')
		AND (B.right_display ='Y' OR B.right_set_date <> '0000-00-00')
	ORDER BY
		A.idx DESC
	LIMIT
		$from_record, $page_rows";
//echo "<pre>".$sql."</pre>";
$result = sql_query($sql);
$list_count = $result->num_rows;
for($i=0; $i<$list_count; $i++) {
	$LIST[] = sql_fetch_array($result);
}
?>
<div class="row">
	<div class="col-lg-12">
		<div class="panel-body" style="padding:0 1% 0 1%;">

			<!-- 검색영역 START -->
			<div style="line-height:28px;">
				<form id="frmSearch" name= "frmSearch" method="get" class="form-horizontal">
				<ul class="col col-md-* list-inline" style="width:100%;padding-left:0;margin-bottom:5px;">
					<li style="vertical-align: middle;">
						<select name="search_cat" id="search_cat" class="form-control input-sm">
							<option value="">::상품형태::</option>
						</select>
					</li>
					<li style="vertical-align: middle;">
						<input type="text" class="form-control input-sm" name="search_keyword" size="30" value="<?=$search_keyword?>" placeholder="상품명">
					</li>
					<li style="vertical-align: middle;">
						<button type="submit" class="btn btn-sm btn-warning" >검색</button>
					</li>
					<li style="float:right;">
						<button type="button" class="btn btn-sm btn-primary" style="margin-right:20px;" onclick="go_modi();" style="display:inline;" >추가</button>
					</li>
				</ul>
				</form>
			</div>

			<div class="dataTable_wrapper" style="margin-top:10px;">
				<table class="table table-striped table-bordered table-hover" style="font-size:13px;">
					<colgroup>
						<col width="4%">
						<col width="*">
						<col width="10%">
						<col width="6%"><!-- 상품형태 -->
						<col width="5%">
						<col width="8%">
						<col width="5%">
						<col width="7%">
						<col width="7%">
						<col width="6%">
						<col width="4%">
						<col width="7%">
						<col width="8%">
					<colgroup>
					<thead>
						<tr>
							<th class="text-center" style="background-color:#F8F8EF">NO.</th>
							<th class="text-center" style="background-color:#F8F8EF">상품명</th>
							<th class="text-center" style="background-color:#F8F8EF">상품정보</th>
							<th class="text-center" style="background-color:#F8F8EF">상품형태</th>
							<th class="text-center" style="background-color:#F8F8EF">대출실행일</th>
							<th class="text-center" style="background-color:#F8F8EF">원금상환일<br/>예정일</th>
							<th class="text-center" style="background-color:#F8F8EF">이자지급<br/>회차</th>
							<th class="text-center" style="background-color:#F8F8EF">권리설정일</th>
							<th class="text-center" style="background-color:#F8F8EF">권리설정 증빙차료</th>
							<th class="text-center" style="background-color:#F8F8EF">헬로 live</th>
							<th class="text-center" style="background-color:#F8F8EF">현장사진</th>
							<th class="text-center" style="background-color:#F8F8EF">입금<br/>확인증</th>
							<th class="text-center" style="background-color:#F8F8EF">비고</th>
						</tr>
					</thead>
					<tbody>
<?
for ($i=0; $i<$list_count; $i++) {
	$tmp = getNumberArr($LIST[$i]['recruit_amount']);
	if($LIST[$i]['invest_days'] > 0 && $LIST[$i]['invest_days'] < 30) {
		$invest_period = $LIST[$i]['invest_days'] . '일';
	}
	else {
		$invest_period = $LIST[$i]['invest_period'] . '개월';
	}

	if ($LIST[$i]['state']=="1") $state = "이자상환중";
	else if ($LIST[$i]['state']=="2") $state = "상환완료";
	else if ($LIST[$i]['state']=="5") $state = "중도상환";

	$total_eja_time = $LIST[$i]['invest_period'];
	if (substr($LIST[$i]['loan_start_date'],-2)>5) $total_eja_time = $total_eja_time + 1;

	// 실시간 카메라 스트림
	$live_link = "";
	if($LIST[$i]['stream_url1']) {
		if($LIST[$i]['stream_url1']=='ready') {
			$live_link = "openStreamReady();";  // /popup/inc_stream_ready.php 에 함수 정의
		}
		else {
			$play_url = "http://hellolivetv.co.kr/onair/".$LIST[$i]['idx'];
			$play_url.= (preg_match("/dev.hellofunding/", $_SERVER['HTTP_HOST'])) ? "&mode=test" : "";
			if(G5_IS_MOBILE) {
				$live_link = "window.open('".$play_url."','stream_win','toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
			}
			else {
				$live_link = "window.open('".$play_url."','stream_win','width=730,height=500,toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
			}
		}
	}
?>
						<tr class="odd" align="center" style="background:<?=$bgcolor?>">
							<td><?=$num?></td>
							<td align="left"><?=$LIST[$i]['title']?></td>
							<td><?=$tmp[0]?><?=$tmp[1]?>원 / <?=number_format($LIST[$i]['invest_return'])?>% / <?=$invest_period?></td>
							<td><?=$state?></td>
							<td nowrap><?=$LIST[$i]['loan_start_date']?></td>
							<td><?=preg_replace("/-/", ".", $LIST[$i]['loan_end_date_orig'])?></td>
							<td><?=number_format($LIST[$i]['max_turn'])?> / <?=$total_eja_time?></td>
							<td><?=$LIST[$i]['right_set_date']?></td>
							<td><? if($LIST[$i]['field_pic']) { ?><a href="/data/product/<?=$LIST[$i]['right_pic']?>" target=_blank>증빙자료 보기</a><? } ?></td>
							<td><? if($LIST[$i]['state']=='1' && $live_link) { ?><a href="javascript:;" onClick="<?=$live_link?>">Live TV</a><? }?></td>
							<td><? if($LIST[$i]['field_pic']) { ?><a href="/data/product/<?=$LIST[$i]['field_pic']?>" target=_blank>보기</a><? } ?></td>
							<td><? if($LIST[$i]['deposit_pic']) { ?><a href="/data/product/<?=$LIST[$i]['deposit_pic']?>" target=_blank>보기</a><? } ?></td>
							<td><a href="./rcv_add.php?product_idx=<?=$LIST[$i]['idx']?>">수정</a></td>
						</tr>
<?
	$num--;
}
?>
					</tbody>
				</table>
			</div>


			<!--div style="line-height:28px;text-align:right;">
				<!--상품번호 <input type="text" class="form-control input-sm" name="search_idx" value="" placeholder="상품번호" style="width:100px;display:inline;">-->
				<!--button type="button" class="btn btn-sm btn-primary" style="margin-right:20px;" onclick="go_modi();" style="display:inline;" >추가</button>
			</div-->
			<script>
			$("input[name=search_idx]").keydown(function (key) {

				if(key.keyCode == 13){//키가 13이면 실행 (엔터는 13)
					go_modi();
				}
			});
			function go_modi() {
				//document.location.href='./rcv_add.php?product_idx='+document.getElementsByName('search_idx')[0].value;
				document.location.href='./rcv_add.php';
			}
			</script>

			<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $page_rows, 10); ?></div>

		</div>
	</div>
</div>

<script type="text/javascript">
$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});
</script>

<? include_once ('../admin.tail.php'); ?>