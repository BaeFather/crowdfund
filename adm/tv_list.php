<?

/**
 * 헬로라이브TV 관리
 * User: 김국현
 * Date: 2018-04-20
 * Time: 오후 12:22
 */

include_once('./_common.php');

$sub_menu = '300730';
auth_check($sub_menu, "r"); // 메뉴 권한체크

$g5["title"] = "헬로라이브TV 관리";
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 값에 따른 변수 생성
while(list($key, $value) = each($_REQUEST)) {
	if($_FILES) continue;
	if(!is_array($_REQUEST)) ${$key} = clean_xss_tage(trim($value));
}

if(!isset($page) OR empty($page)){
	$page = 1;
}

$sql_common = " FROM `media_tv_list`";

// 테이블의 전체 레코드수만 얻음
$sql = " SELECT COUNT(*) AS cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)

$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT * $sql_common WHERE 1 = 1 ORDER BY `sort` ASC, `regdate` DESC LIMIT $from_record, {$rows}";
$result = sql_query($sql);

if( ! function_exists("getIframeVideoId")){
	function getIframeVideoId($src){
		preg_match('#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=‌​(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#', $src, $matches);
		return array_pop($matches);
	}
}

$img_url = G5_IMG_URL . "/live_tv/";
$img_dir = G5_IMG_PATH . "/live_tv/";
?>

<div class="local_ov01 local_ov">
	<? if ($page > 1) {?><a href="<? echo $_SERVER['SCRIPT_NAME']; ?>">처음으로</a><? } ?>
	<span>전체 투자후기 <? echo ($total_count) ? $total_count : 0; ?>건</span>
</div>

<div class="local_desc01 local_desc">
	<ol>
		<li>홈 > 이용안내 > 메인 헬로라이브TV 항목을 관리할 수 있습니다.</li>
		<li><strong>새로추가</strong>를 눌러 헬로라이브TV를 생성합니다.</li>
		<li>마우스로 드래그하여 순서를 변경할 수 있습니다.</li>
		<li>새로추가 후 선택수정을 하면 순서가 정렬됩니다.</li>
	</ol>
	<a href="/index.php">메인으로 이동</a>
	<br/>
	<br/>
</div>

<div class="btn_add01 btn_add">
	<a href="javascript:del_this();">선택삭제</a>
	<a href="javascript:sort_this();">선택수정</a>
	<a href="./tv_reg.php">새로추가</a>
</div>

<form name="epilogue_list" method="post">
	<input type="hidden" name="from_record" value="<? echo $from_record;?>"/>
	<div class="tbl_head01 tbl_wrap">
		<table>
			<colgroup>
				<col width="30px">
				<col width="50px">
				<col width="60px">
				<col width="60px">
				<col width="*">
				<col width="70px">
				<col width="130px">
			</colgroup>
			<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="chk" id="chk"/></th>
				<th scope="col">순서</th>
				<th scope="col">ID</th>
				<th scope="col">라이브 커버</th>
				<th scope="col">제목</th>
				<th scope="col">순번</th>
				<th scope="col">관리</th>
			</tr>
			</thead>
			<tbody>
			<?

			// $nLoop = $total_count - ($page - 1) * $rows + 1;
			$nLoop = abs($from_record);
			for ($i=1; $row = sql_fetch_array($result); $i++) {

				if(isset($row["sort"]) && $row["sort"] !== "9999"){
					$nLoop++;
				}


				// 이미지 확인
				if (!empty($row["thumbnail"]) && file_exists($img_dir.$row["thumbnail"])) {
					$row["thumb_url"] = $img_url.$row["thumbnail"];
				}else{
					$row["thumb_url"] = '/shop/img/no_image.gif';
				}

				?>
				<tr data-sort-order="<? echo $nLoop;?>">
					<td class="text-center">
						<input type="checkbox" name="chk[]" value="<? echo $row["id"];?>" data-bar="best"/>
					</td>
					<td class="text-center">
						<? echo $nLoop;?>
					</td>
					<td class="text-center">
						<? echo $row["id"];?>
					</td>
					<td class="text-center">
						<img src="<? echo $row["thumb_url"];?>" alt="<? echo $row["subject"];?>" width="150" height="100"/>
					</td>
					<td>
						<a href="#" target="_blank" onclick="go_link(<? echo $row["id"];?>, 'edit'); return false;"> <? echo $row["subject"];?> </a>
					</td>
					<td align="center"><? echo $row['sort'];?></td>
					<td class="text-center">
						<button type="button" onclick="go_link(<? echo $row["id"];?>, 'edit');">수정</button>
						<button type="button" onclick="del_this(<? echo $row["id"];?>);">삭제</button>
					</td>
				</tr>
			<? } ?>
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
		document.location.href = '/adm/tv_reg.php?id='+id+'&page=<? echo $page;?>&mode='+mode;
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
				url: g5_admin_url + "/tv_update.php?mode=delete",
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
				url: g5_admin_url + "/tv_update.php?mode=sort",
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

<? echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>
<? include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>
