<?

/**
 * 투자후기 관리 가능
 * User: 김국현
 * Date: 2018-01-12
 */

include_once('./_common.php');
include_once('./admin.loan.function.php');

$sub_menu = '300740';
auth_check($sub_menu, "r"); // 메뉴 권한체크

$g5["title"] = "추천평";

include_once (G5_ADMIN_PATH.'/admin.head.php');

// 값에 따른 변수 생성
while(list($key, $value) = each($_REQUEST)) {
	if($_FILES) continue;
	if(!is_array($_REQUEST)) ${$key} = clean_xss_tage($value);
}


$page = ($page) ? $page : 1;
$S1	=	($S1) ? $S1 : 3;

$where = "";
$where.= " AND section='$S1'";

// 테이블의 전체 레코드수만 얻음
$sql = "SELECT COUNT(id) AS cnt FROM epilogue_list WHERE 1 " . $where;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)

$from_record = ($page - 1) * $rows; // 시작 열을 구함

//$sql = "
//	SELECT
//		*
//	FROM
//		epilogue_list
//	WHERE 1
//		$where
//	ORDER BY
//		(
//			CASE
//				WHEN display_yn='R' THEN 1
//				WHEN display_yn='Y' THEN 2 ELSE 3
//			END
//		),
//		-- display_yn DESC,
//		best_review DESC,
//		sort ASC,
//		id DESC
//	LIMIT
//		$from_record, {$rows}";

$sql = "
	SELECT
		*
	FROM
		epilogue_list
	WHERE 1
		$where
	ORDER BY
		(
			CASE 
				WHEN display_yn='R' THEN 1 ELSE 2
			END
		),
		best_review DESC,
		regdate DESC,
		sort ASC,
		id DESC
	LIMIT
		$from_record, {$rows}";
//print_rr($sql);
$result = sql_query($sql);


$img_url = G5_IMG_URL . "/review/";
$img_dir = G5_IMG_PATH . "/review/";

?>

<div class="local_ov01 local_ov">
	<? if($page > 1) { ?><a href="<?=$_SERVER['SCRIPT_NAME']?>">처음으로</a><? } ?>
	<span>전체 추천평 <?=($total_count) ? $total_count : 0; ?>건</span>
</div>

<div class="btn_add01 btn_add" style="padding-bottom:7px;overflow:hidden;">
	<div style="float:left;width:50%;text-align:left;">&nbsp;</div>
	<div style="float:left;width:50%;">
		<a href="javascript:del_this();">선택삭제</a>
		<a href="javascript:sort_this();">선택수정</a>
		<a href="./recommend_reg.php">새로추가</a>
	</div>
</div>

<form name="epilogue_list" method="post">
<input type="hidden" name="from_record" value="<?=$from_record;?>"/>

<div class="tbl_head01 tbl_wrap">
	<table>
		<colgroup>
			<col width="30px">
			<col width="50px">
			<col width="80px">
			<col width="80px">
			<col width="60px">
			<col width="120px">
			<col width="*">
			<col width="70px">
			<col width="170px">
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="chk" id="chk"/></th>
				<th scope="col">순서</th>
				<th scope="col">진행구분</th>
				<th scope="col">구분</th>
				<th scope="col">ID</th>
				<th scope="col">이미지</th>
				<th scope="col">제목/내용</th>
				<th scope="col">순번</th>
				<th scope="col">관리</th>
			</tr>
		</thead>
		<tbody>
<?
// $nLoop = $total_count - ($page - 1) * $rows + 1;
$nLoop = abs($from_record);
for ($i=1; $row = sql_fetch_array($best_result); $i++) {
	if(isset($row["sort"]) && $row["sort"] !== "9999") {
		$nLoop++;
	}

	// 이미지 확인
	if(!empty($row["thumbnail"]) && file_exists($img_dir.$row["thumbnail"])) {
		$row["thumb_url"] = $img_url.$row["thumbnail"];
	}
	else{
		$row["thumb_url"] = G5_IMAGES_URL.'/review/sumnail_img01.jpg';
	}

?>
			<tr data-sort-order="<?=$nLoop;?>">
				<td class="text-center"><input type="checkbox" name="chk[]" value="<?=$row["id"];?>" data-bar="best"/></td>
				<td class="text-center">BEST</td>
				<td class="text-center"><?=fn_general_select($row["section"],"txt",fn_epilogue_section(),"","section","","");?></td>
				<td class="text-center"><?=fn_general_select($row["display_yn"],"txt",fn_recommend_display_yn(),"","display_yn","","");?></td>
				<td class="text-center"><?=$row["id"];?></td>
				<td><img src="<?=$row["thumb_url"];?>" alt="<?=$row["subject"];?>" width="150" height="100"/></td>
				<td>
					<p>BEST!!!</p>
					<span><?=$row["mem_name"].' ('.$row["mem_id"].')';?></span>
					<br/>
					<a href="<?=$row["target_link"];?>" target="_blank"> <?=$row["subject"];?> </a>
					<div style='height:50px;border:1px dotted #ccc; overflow-y:hidden'><?=$row['contents']?></div>
				</td>
				<td class="text-center">BEST</td>
				<td class="text-center">
					<button type="button" onclick="go_link(<?=$row["id"];?>, 'edit');">수정</button>
					<button type="button" onclick="del_this(<?=$row["id"];?>);">삭제</button>
				</td>
			</tr>
<?
}

// $nLoop = $total_count - ($page - 1) * $rows + 1;
$nLoop = abs($from_record);
for($i=1; $row = sql_fetch_array($result); $i++) {
	$nLoop++;

	if(!empty($row["thumbnail"]) && file_exists(G5_IMG_PATH."/review/".$row["thumbnail"])) {
		$row["thumb_url"] = $img_url.$row["thumbnail"];
	}
	else {
		$row["thumb_url"] = G5_IMAGES_URL.'/review/sumnail_img01.jpg';
	}

?>
			<tr data-sort-order="<?=$nLoop;?>">
				<td class="text-center"><input type="checkbox" name="chk[]" value="<?=$row["id"];?>"/></td>
				<td class="text-center"><?=$nLoop;?></td>
				<td class="text-center"><?=fn_general_select($row["section"],"txt",fn_epilogue_section(),"","section","","");?></td>
				<td class="text-center"><?=fn_general_select($row["display_yn"],"txt",fn_recommend_display_yn(),"","display_yn","","");?></td>
				<td class="text-center"><?=$row["id"];?></td>
				<td><img src="<?=$row["thumb_url"];?>" alt="<?=$row["subject"];?>" width="150" height="100"/></td>
				<td>
					<span><?=$row["mem_name"].' ('.$row["mem_id"].')';?></span>
					<br/>
					<a href="<?=$row["target_link"];?>" target="_blank"> <?=$row["subject"]?> </a>
					<div style='height:50px;border:1px dotted #ccc;color:#AAA; overflow-y:hidden'><?=$row['contents']?></div>
				</td>
				<td class="text-center">
					<?=($row["sort"] == 9999) ? '미지정' : $row["sort"];?>
				</td>
				<td class="text-center">
					<button type="button" onclick="go_link(<?=$row["id"]?>, 'edit');">수정</button>
					<button type="button" onclick="del_this(<?=$row["id"]?>);">삭제</button>
				</td>
			</tr>
<?
}

if($i <= 1) {
	echo '<tr><td colspan="8" class="empty_table"><span>자료가 한건도 없습니다.</span></td></tr>';
}
?>
		</tbody>
	</table>
</div>

</form>

<script type="text/javascript">

	// 정렬변경
	$("table tbody").sortable({
		axis: 'Y',
		cursor: 'MOVE',
		containment: "document",
		stop: function(event, ui){
			var order = $(this).sortable("toArray", {
				attribute: "data-sort-order"
			});

			for(var i in order){
				$(this).find("tr").eq(i).find("td").eq(1).text(order[i]);
			}
		}
	});
	$("table tbody").disableSelection();

	// 수정
	function go_link(id, mode){
		document.location.href = '/adm/recommend_reg.php?id='+id+'&page=<?=$page;?>&mode='+mode;
	}

	function check_epilogue_section(id){
		if(confirm("정말 구분을 변경 하시겠습니까?")){

			var send_array = Array();
			var send_cnt = 0;

			if(!id){
				var chkbox = $("input:checkbox[name='chk[]']");

				for (var i = 0; i < chkbox.length; i++) {
					if (chkbox[i].checked == true) {
						send_array[send_cnt] = chkbox[i].value;
						send_cnt++;
					}
				}
			}else{
				send_array.push(id);
			}

			if(send_array.length <= 0){
				alert("수정하실 후기글을 선택해주세요.");
				return false;
			}

		var token = get_ajax_token();
		var section = $("input[name='section']:checked").val();
		var snskind = $("select[name='snskind']").val();
			$.ajax({
				url: g5_admin_url + "/recommend_update.php?mode=update",
				type: "POST",
				data: {id: send_array, token: token, section: section, snskind: snskind},
				dataType: "JSON",
				async: false,
				cache: false,
				success: function(data, textStatus) {
					if (data.error) {
						alert(data.message);
					} else {
						alert(data.message);
						document.location.reload();
					}
				}
			});
		}
	}

	// 삭제
	function del_this(id){
		if(confirm("정말 삭제하시겠습니까?")){

			var send_array = Array();
			var send_cnt = 0;

			if(!id){
				var chkbox = $("input:checkbox[name='chk[]']");

				for (var i = 0; i < chkbox.length; i++) {
					if (chkbox[i].checked == true) {
						send_array[send_cnt] = chkbox[i].value;
						send_cnt++;
					}
				}
			}else{
				send_array.push(id);
			}

			if(send_array.length <= 0){
				alert("삭제하실 후기글을 선택해주세요.");
				return false;
			}

			var token = get_ajax_token();
			$.ajax({
				url: g5_admin_url + "/epilogue_update.php?mode=delete",
				type: "POST",
				data: {id: send_array, token: token},
				dataType: "JSON",
				async: false,
				cache: false,
				success: function(data, textStatus) {
					if (data.error) {
						alert(data.message);
					} else {
						alert(data.message);
						document.location.reload();
					}
				}
			});
		}
	}

	// 정렬변경 적용
	function sort_this(){
		if(confirm("선택하신 순서대로 정렬하시겠습니까?")) {
			var send_array = Array();
			var send_cnt = 0;
			var from_record = $("input:hidden[name='from_record']").val();

			var chkbox = $("input:checkbox[name='chk[]']").not();

			for (var i = 0; i < chkbox.length; i++) {
				if (chkbox[i].checked == true && chkbox[i].dataset.bar != "best") {
					send_array[send_cnt] = chkbox[i].value;
					send_cnt++;
				}
			}

			if (send_array.length <= 0) {
				alert("정렬하실 후기글을 선택해주세요.");
				return false;
			}

			var token = get_ajax_token();
			$.ajax({
				url: g5_admin_url + "/epilogue_update.php?mode=sort",
				type: "POST",
				data: {id: send_array, token: token, from_record: from_record},
				dataType: "JSON",
				async: false,
				cache: false,
				success: function (data, textStatus) {
					if (data.error) {
						alert(data.message);
					} else {
						alert(data.message);
						top.document.location.reload();
					}
				}
			});
		}
	}

	// 선택
	$('input:checkbox[name="chk"]').on("click", function(){
		if($(this).prop("checked"))
		{
			$('input:checkbox[name="chk[]"]').prop("checked",true);
		} else {
			$('input:checkbox[name="chk[]"]').prop("checked",false);
		}
	});
</script>

<?

$qstr .= "&S1=".$S1;

echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page=");

include_once (G5_ADMIN_PATH.'/admin.tail.php');

?>
