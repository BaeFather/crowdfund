<?
include_once('_common.php');

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('../_head.php');
}


$_REQUEST['bo_table'] = "recruit";
$notice_array = explode(',', trim($board['bo_notice']));
if($board['bo_notice']) $wh_nt = "and wr_id not in (".implode(', ', $notice_array).")";
else $wh_nt="";

if ($is_admin) {
	$wh1 = "";
	$wh2 = "";
} else {
	if ($board['bo_notice']) $wh1 = " AND wr_id NOT IN($board[bo_notice]) ";
	else $wh1 = "";
	//$wh2 = " AND wr_1<>'마감' ";
	$wh2 = " AND wr_4<>'N' ";
	$size= 50;
}

$sql = "SELECT ca_name, COUNT(*) cnt FROM g5_write_recruit WHERE 1>0 $wh1 $wh2  GROUP BY ca_name";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	$CLIST["total"] += $row["cnt"];
	$CLIST["list"][$i] = $row;
}
//echo "<pre>";print_rr($CLIST);echo "</pre>";

$sqlt = "SELECT count(*) tcnt FROM g5_write_recruit WHERE 1>0 $wh1 $wh2";
$rest = sql_query($sqlt);
$rowt = sql_fetch_array($rest);
$tott = $rowt["tcnt"];


$total = $tott;
if(!$page) $page = 1;
if(!$size) $size = 10;
$total_page = ceil($total / $size);
$start_num  = ($page - 1) * $size;
$sql_limit = "LIMIT $start_num, $size";

$idx = 0;

// 공지 처리
if(!$sca && !$stx) {
	$arr_notice = explode(',', trim($board['bo_notice']));
	$from_notice_idx = ($page - 1) * $page_rows;
	if($from_notice_idx < 0) $from_notice_idx = 0;
	$board_notice_count = count($arr_notice);

	for ($k=0; $k<$board_notice_count; $k++) {
		if (trim($arr_notice[$k]) == '') continue;

		$Query = " select * from {$write_table} where wr_id = '{$arr_notice[$k]}' ";
		$row = sql_fetch($Query);

		if (!$row['wr_id']) continue;

		$notice_array[] = $row['wr_id'];
		if($k < $from_notice_idx) continue;
		$LIST[$idx] = $row;

		$ca_name = (in_array((int)$row["wr_id"], $notice_array)) ? "공지" : $row["ca_name"];
		$LIST[$idx]["ca"] = $ca_name;

		if ($row["wr_1"]=="기간내") {
			$gap = strtotime($row["wr_3"]." 00:00:00") - strtotime(date("Y-m-d")." 00:00:00");
			$gap = floor($gap / (60 * 60 * 24)) ;
			$gigan = "D $gap";
		} else $gigan = $row["wr_1"];
		$LIST[$idx]["gigan"] = $gigan;

		$idx++;
    $notice_count++;

    if($notice_count >= $list_page_rows) break;

	}
}


$sql = "SELECT * FROM g5_write_recruit WHERE 1>0 $wh1 $wh2 $wh_nt  ORDER BY wr_4 desc,wr_num, wr_reply ASC $sql_limit";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

for ($i=0 ; $i<$cnt ; $i++) {

	$row = sql_fetch_array($res);

	$LIST[$idx] = $row;

	if (in_array((int)$row["wr_id"], $notice_array)) $ca_name = "공지";
	else $ca_name = $row["ca_name"];
	$LIST[$idx]["ca"] = $ca_name;

	if ($row["wr_1"]=="기간내") {
		$gap = strtotime($row["wr_3"]." 00:00:00") - strtotime(date("Y-m-d")." 00:00:00");
		$gap = floor($gap / (60 * 60 * 24)) ;
		$gigan = "D $gap";
	} else $gigan = $row["wr_1"];
	$LIST[$idx]["gigan"] = $gigan;

	$idx++;
}
?>

<script>
<?
$js_array = json_encode($LIST);
echo "var list = ".$js_array.";\n";
?>
console.log(list);

$(document).on('click', '#paging_span span.btn_paging', function() {
	var url = '<?=$_SERVER['SCRIPT_NAME']?>?<?=$qstr?>&page=' + $(this).attr('data-page');
	$(location).attr('href', url);
});
</script>

<?

if(G5_IS_MOBILE) {
	include_once("recruit_m.php");
	return;
}

add_stylesheet('<link href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css" rel="stylesheet" type="text/css">', 0);
add_stylesheet('<link href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css" rel="stylesheet" type="text/css">', 0);
add_stylesheet('<link href="recruit.css?ver=2" rel="stylesheet">', 0);

?>

<div id="content">
	<div id="recruit">
		<div class="img1"><img src="img/recruit_01.jpg" alt=""></div>
		<div class="img1_1">
			<ul>
				<li class="title">함께 Hello 하는<br>행복금융 디자이너를<br>기다립니다.</li>
				<li class="btn"><a href="#list">채용중 공고 확인하기<span class="num"><?=number_format($CLIST["total"])?></span></a></li>
			</ul>
		</div>
		<div class="text1">
			<ul>
				<li class="title01 pd01"><img src="img/hello.jpg"><br>원합니다</li>
				<li class="word t01"><p>열정</p><br>헬로펀딩은 하고자 하는<br>열정을 갖고, 고객만족을 위해<br>끊임없이 노력합니다.</li>
				<li class="word t02"><p>집념</p><br>헬로펀딩은 목표를 이루고자<br>하는 강한 집념으로 모든 구성원이<br>하나되어 반드시 해냅니다.</li>
				<li class="word t03"><p>도전</p><br>헬로펀딩은 도전을 즐깁니다.<br>진취적이고 긍적적인 마인드로<br>혁신과 변화를 추구합니다.</li>
			</ul>
		</div>
		<div class="img2"><img src="img/recruit_02.jpg" alt=""></div>
		<div class="text1">
			<ul>
				<li class="title01 pd02"><img src="img/hello.jpg"><br>행동합니다</li>
				<li class="word t04"><p>방향과소통</p><br>회사가 지금 무슨 일을<br>하는지 알고 있고,<br>내가 무슨 일을 하는지<br>회사가 알고 있습니다.</li>
				<li class="word t05"><p>자율과실행</p><br>어떤 일이든<br>마감시간을 정하고<br>업무하고 있습니다.</li>
				<li class="word t06"><p>직원의존중</p><br>정시 퇴근을 존중하고<br>퇴근한 직원들에게 업무로<br>연락하지 않습니다.</li>
			</ul>
		</div>
		<div class="img3"><img src="img/recruit_03.jpg" alt=""></div>
		<div class="text2">
			<ul>
				<li  class="title02 pd03"><img src="img/hello.jpg"><br>약속합니다</li>
				<li class="benefit t07"><p><span>불금을 즐기세요</span></p><br>금요일 5시 퇴근으로<br>2시간 조기 퇴근합니다.</li>
				<li class="benefit t08"><p><span>생일엔 빨리가요</span></p><br>생일에는 반차와 함께<br>상품권을 드립니다.</li>
				<li class="benefit t09"><p><span>마음의 양식도 함께</span></p><br>자기계발을 위하여 매월 2권의<br>도서구입비를 지원합니다.</li>
				<li class="benefit t10"><p><span>눈치보지 마세요</span></p><br>구성원의 리프레시를 위하여<br>자유로운 연차제도를 운영합니다.</li>
				<li class="benefit t11"><p><span>노고에 감사합니다</span></p><br>근속연수 3년 이상의 직원에게<br>축하금과 특별휴가를 제공합니다.</li>
				<li class="benefit t12"><p><span>전국 8도 어디든</span></p><br>헬로펀딩과 제휴된 리조트 시설을<br>할인혜택으로 이용가능합니다. </li>
				<li class="benefit t13"><p><span>살쪄도 좋아요</span></p><br>편의점 수준의 카페테리아에서<br>맘껏 드실 수 있습니다.</li>
			</ul>
		</div>
		<div class="img4"><img src="img/recruit_04.jpg" alt=""></div>
		<div id="list" class="text1 title03" style="text-align:center;">진행중 공고</div>
		<div>
			<div class="tab">
				<ul>
					<li class="on" id="cat_a"><a onclick="new_list('전체','a');" style="cursor:pointer;">전체보기 (<?=$CLIST["total"]?>)</a></li>
<?
for($i=0 ; $i<count($CLIST['list']) ; $i++) {
	if($CLIST['list']['$i']['ca_name']) {
?>
					<li id='cat_<?=$i?>'><a onclick='new_list('<?=$CLIST['list']['$i']['ca_name']?>',<?=$i?>);' style='cursor:pointer;'><?=$CLIST['list']['$i']['ca_name']?> (<?=$CLIST['list']['$i']['cnt']?>)</a></li>
<?
	}
}
?>
				</ul>
			</div>
			
			<div class="board" >
				
				<table id="rlist_tbl">

				
				<? for($i=0 ; $i<count($LIST) ; $i++) { ?>
					<tr <?=$LIST[$i]["wr_4"]<>'Y'?"style='background-color:#f0f0f0;'":""?> >
						<td class="type"><?=$LIST[$i]["ca"]?></td>
						<td class="subject">
							<a href="/company/recruit/recruit_view.php?wr_id=<?=$LIST[$i]['wr_id']?>">
							<?=$LIST[$i]["wr_subject"]?></a>
						</td>
						<td class="date"><?=$LIST[$i]["gigan"]?></td>
						<td class="r_bt">
						<? if ($is_admin) { ?>
							<?=number_format($LIST[$i]['wr_hit'])?>
						<? } else { ?>
							<a href="/company/recruit/recruit_view.php?wr_id=<?=$LIST[$i]['wr_id']?>">지원하기</a>
						<? } ?>
						</td>
					</tr>
				<? } ?>
				</table>
				
				

				

<? if($is_admin) { ?>
				<div id="paging_start" style="display:inline-block; width:100%;height:55px;">
					<div id="paging_span" style="background-color:#fff">
						<? paging($tott, $page, $size); ?>
					</div>
				</div>
				<div style="text-align: right; margin-top: 10px;">
					<a class="btn_blue" href="/bbs/write.php?bo_table=recruit">글쓰기</a>
				</div>
<? } ?>

			</div>
		</div>
	</div>
</div>

<br/><br/><br/>

<script>
function new_list(gbn, idx) {
	proc_on(idx);
	table_reset();
	for (var i=0 ; i<<?=$cnt?> ; i++) {
		if (list[i]['ca_name']==gbn || gbn=='전체') {
			d = "<tr>"+
					"<td class='type'>"+list[i]['ca']+"</td>"+
					"<td class='subject'><a href='/company/recruit/recruit_view.php?wr_id="+ list[i]["wr_id"] +"'>"+list[i]['wr_subject']+"</a></td>"+
					"<td class='date'>"+list[i]['gigan']+"</td>"+
					"<td class='r_bt'><a href='/company/recruit/recruit_view.php?wr_id="+ list[i]["wr_id"] +"'>지원하기</a></td>"+
				"</tr>";
			$('#rlist_tbl').append(d);
		}
	}
}

function table_reset() {
	var trCnt = $('#rlist_tbl tr').length;
	if (trCnt) $('#rlist_tbl').empty();
}

function proc_on(idx) {
	$("#cat_a").removeClass('on');
<?
for($i=0 ; $i<count($CLIST["list"]) ; $i++) {
	if($CLIST["list"]["$i"]["ca_name"]) {
?>
			if (idx==<?=$i?>) $("#cat_<?=$i?>").addClass('on');
			else $("#cat_<?=$i?>").removeClass('on');
<?
	}
}
?>
	if(idx=="a") $("#cat_a").addClass('on');
}
</script>

<?

if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('../_tail.php');
}

?>