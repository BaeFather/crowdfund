<?
include_once('./_common.php');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$sub_menu = "920400";
$g5['title'] = $menu['menu920'][4][1];

include_once ('../../admin.head.php');

$where = '';

if($sdate) $where.= " AND $reg_date >= '$sdate' ";
if($edate) $where.= " AND $reg_date <= '$edate' ";

if($p_category) {
	if($p_category=='1') {
		$where.= " AND p_category='PF' ";
	} else if($p_category=='2') {
		$where.= " AND p_category='주담대'";
	}
}

if($kind_type) {
	if($kind_type=='1') {
		$where.= " AND k_idx='1' ";
	} else if($kind_type=='2') {
		$where.= " AND k_idx='2' ";
	} else if($kind_type=='3') {
		$where.= " AND k_idx='3' ";
	} else if($kind_type=='4') {
		$where.= " AND k_idx='4' ";
	}
}

if($loan_name) {
	$where.= " AND loan_name LIKE '%".$loan_name."%'";
}

$sql = "
	SELECT
		COUNT(k_idx) AS tot_cnt
	FROM
		cf_paper
	WHERE 1
		$where
	";
$row = sql_fetch($sql);
$total_count = $row['tot_cnt'];						  // 조회한 쿼리 결과 count
$rows = 15;																	// row 개수
$total_page = ceil($total_count / $rows);   // 전체 페이지 계산
if($page < 1) $page = 1;										// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows;					// 시작 열을 구함
$num = $total_count - $from_record;


$sql = "
	SELECT 
		p_no, k_idx, loan_name, p_category, reg_date, writer
	FROM
		cf_paper
	WHERE 1
		$where
	ORDER BY 
		p_no DESC
	LIMIT
		$from_record, $rows
	";
$res = sql_query($sql);
$cnt = $res->num_rows;

for($i=0; $i<$cnt; $i++) {
	$LIST[] = sql_fetch_array($res);
}


?>

<style type="text/css">
#frmConfirmSrch {width: 40%; min-width: 800px;}
#frmConfirmSrch .srch-table {margin-bottom: 15px;}
#frmConfirmSrch .srch-table th {width: 14%;}
#frmConfirmSrch .srch-table td {width: 50%;}
#frmConfirmSrch .srch-btn {display: flex; flex-direction: row-reverse;}
#frmConfirmSrch .srch-btn button {margin-left: 10px;}

.tbl_wrap .confirm-list {width: 100%; min-width: 1200px; font-size: 13px; text-align: center; margin-bottom: 50px;}

.list-top-btn {margin: 20px 0 10px; display: flex; align-content: space-between;}
.list-top-btn button.btn-success {width: 150px; margin-right: auto;}
</style>


<div class="tbl_head02 tbl_wrap">
	<form id="frmConfirmSrch" name="frmConfirmSrch" method="get" class="form-horizontal">
		<!-- 검색 영역 --> 
		<table class="table srch-table">
			<tr>
				<th>작성일</th>
				<td>
					<ul class="col col-md-* list-inline">
						<li class="date"><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" placeholder="대상일자(시작)"></li>
						<li>~</li>
						<li class="date"><input type="text" id="edate" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" placeholder="대상일자(종료)"></li>
					</ul>
				</td>
				<th>카테고리</th>
				<td>
					<select name="p_category" class="form-control input-sm">
						<option value="">:: 카테고리 선택 ::</option>
						<option value="1" <?if($p_category=='1') {echo 'selected';}?>>PF</option>
						<option value="2" <?if($p_category=='2') {echo 'selected';}?>>주택담보대출</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>차입자</th>
				<td><input type="text" name="loan_name" value="<?=$loan_name?>" class="form-control input-sm" /></td>
				<th>확인서</th>
				<td>
					<select name="kind_type" class="form-control input-sm">
						<option value="">:: 종류 선택 ::</option>
						<option value="1" <?if($kind_type=='1') {echo 'selected';}?>>이자내역서</option>
						<option value="2" <?if($kind_type=='2') {echo 'selected';}?>>금융거래확인서</option>
						<option value="3" <?if($kind_type=='3') {echo 'selected';}?>>완납확인서</option>
						<option value="4" <?if($kind_type=='4') {echo 'selected';}?>>이자납입내역서</option>
					</select>
				</td>
			</tr>
		</table>
		<div class="srch-btn">
			<button id="listSearch" class="btn btn-sm btn-warning">검색</button>
			<button type="button" class="btn btn-sm btn-default" onClick="location.href='<?=$_SERVER['PHP_SELF']?>';">초기화</button>
		</div>
	</form>

	<!-- 리스트 상단 위치 버튼 -->
	<div class="list-top-btn">
		<button class="btn btn-sm btn-success" onclick="location.href='./file/확인서양식.zip';">양식 통합 다운로드</button>
	</div>

	<!-- 리스트 출력 -->
	<table class="confirm-list">
		<colgroup>
			<col width="7%"/>
			<col width="15%"/>
			<col width="17%"/>
			<col width="12%"/>
			<col width="12%"/>
			<col width="15%"/>
			<col width="22%"/>
		</colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>차입자</th>
				<th>확인서</th>
				<th>카테고리</th>
				<th>작성일</th>
				<th>작성자</th>
				<th>비고</th>
			</tr>
		</thead>
		<tbody>
		<?
			for($i=0; $i<count($LIST); $i++) {
				
				$type_name = '';
				if($LIST[$i]['k_idx']=='1') {
					$type_name = '완납확인서';
				} else if($LIST[$i]['k_idx']=='2') {
					$type_name = '금융거래확인서';
				} else if($LIST[$i]['k_idx']=='3') {
					$type_name = '이자납입내역서';
				} else if($LIST[$i]['k_idx']=='4') {
					$type_name = '이자내역서';
				}

		?>
			<tr>
				<td><?=$num?></td>
				<td><?=$LIST[$i]['loan_name']?></td>
				<td><?=$type_name?></td>
				<td><?=$LIST[$i]['p_category']?></td>
				<td><?=$LIST[$i]['reg_date']?></td>
				<td><?=$LIST[$i]['writer']?></td>
				<td>
					<button class="btn btn-sm btn-primary" onclick="list_btn(<?=$LIST[$i]['p_no']?>, <?=$LIST[$i]['k_idx']?>, 'print');">출력</button>
					<button class="btn btn-sm btn-danger" onclick="list_btn(<?=$LIST[$i]['p_no']?>, <?=$LIST[$i]['k_idx']?>, 'delete');">삭제</button>
				</td>
			</tr>
		<?
				$num--;
			}	
		?>
		</tbody>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>
</div>


<script type="text/javascript">
$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'+ 
							'?page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

function press(f) {
	console.log(event.keyCode);
	if(event.keyCode == 13){	// 13이 enter키를 의미
		$("#listSearch").click();
	}
}

function list_btn(idx, type, mode) {
	if(mode=='delete') {
		if(confirm("해당 내역을 삭제하시겠습니까?")) {
			location.href="./confirm_list_action.php?mode="+mode+"&idx="+idx+"&type="+type;
		}
	} else if(mode=='print') {
		if(confirm("해당 내역을 다운로드 하시겠습니까?")) {
			location.href="./confirm_list_action.php?mode="+mode+"&idx="+idx+"&type="+type;
		}
	}
}
</script>


<? include_once ('../../admin.tail.php'); ?>