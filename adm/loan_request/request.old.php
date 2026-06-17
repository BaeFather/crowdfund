<?

include_once('./_common.php');

$sub_menu = '910200';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu910'][2][1];

$g5['title'] = ($idx!='') ? $html_title.' 상세보기' : $html_title.' 목록';


// 받은 데이터를 변수화
foreach($_REQUEST as $k=>$v) { ${$_REQUEST[$k]} = $v; }


$where = "";

$sql = "
	SELECT
		COUNT(seq) AS cnt
	FROM
		loan_info
	WHERE (1)
		$where";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 10;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;


$sql = "
	SELECT
		*,
		(SELECT mb_name FROM g5_member WHERE mb_id=A.admin_id) AS admin_name
	FROM
		loan_info A
	WHERE (1)
		$where
	ORDER BY
		seq DESC
	LIMIT
		$from_record, $rows";
//print_rr($sql,'font-size:12px');
$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);

	if($LIST[$i]['loan_type']=='1')  $LIST[$i]['print_loan_type'] = '부동산';
	else if($LIST[$i]['loan_type']=='1A') $LIST[$i]['print_loan_type'] = '부동산 - 준공자금PF';
	else if($LIST[$i]['loan_type']=='1B') $LIST[$i]['print_loan_type'] = '부동산 - 아파트/주택담보대출';
	else if($LIST[$i]['loan_type']=='1C') $LIST[$i]['print_loan_type'] = '부동산 - 부동산기타대출';
	else if($LIST[$i]['loan_type']=='2')  $LIST[$i]['print_loan_type'] = '동산담보';
	else if($LIST[$i]['loan_type']=='3')  $LIST[$i]['print_loan_type'] = '확정매출채권';
	else $LIST[$i]['print_loan_type'] = '미지정';
}
$list_count = count($LIST);
sql_free_result($result);

$num = $total_count - $from_record;

include_once (G5_ADMIN_PATH.'/admin.head.php');

?>

<style>
td {font-size:13px}
#paging_span { margin-top:10px;  text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:30px; padding:0 5px; color:#585657; line-height:30px; border:1px solid #d0d0d0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#284893; border-color:#284893; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

	<div style="margin:0 10px 8px">
		<label><input type="checkbox" id="chkall" value="1"> 전체선택</label> &nbsp; <button type="button" id="btnSubmit" class="btn btn-sm btn-danger">관리자확인</button>
	</div>

	<form id="form1" name="form1">
<?
if($list_count) {
	for($i=0; $i<$list_count; $i++) {
?>
	<div style="padding:10px; margin-bottom:10px; border:1px solid #aaa; border-radius:3px; background:#ffffcc">
		<table class="table-bordered" style="background:#ffffff">
			<colgroup>
				<col style="width:16.66%">
				<col style="width:16.66%">
				<col style="width:16.66%">
				<col style="width:16.66%">
				<col style="width:16.66%">
				<col style="width:16.67%">
			</colgroup>
			<tr>
				<td colspan="6" style="background:#F8F8EF">
					<ul class="list-inline" style="margin:0">
						<li style="margin:0"><?if($LIST[$i]['admin_id']==''){?><label><input type="checkbox" name="chk[]" value="<?=$LIST[$i]['seq']?>"><? } ?> <?=$num?></label>
						<li style="margin:0;float:right;color:<?=(substr($LIST[$i]['rdate'],0,10)==date('Y-m-d'))?'red':''?>"><?=substr($LIST[$i]['rdate'],0,16)?></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td style="text-align:center;background:#EFEFEF">성명</td>
				<td style="text-align:center;"><?=$LIST[$i]['name']?></td>
				<td style="text-align:center;background:#EFEFEF">Email</td>
				<td style="text-align:center;"><?=$LIST[$i]['email']?></td>
				<td style="text-align:center;background:#EFEFEF">연락처</td>
				<td style="text-align:center;"><?=$LIST[$i]['phone']?></td>
			</tr>
			<tr>
				<td style="text-align:center;background:#EFEFEF">대출상품</td>
				<td style="text-align:center;"><?=$LIST[$i]['print_loan_type']?></td>
				<td style="text-align:center;background:#EFEFEF">주소</td>
				<td colspan="3">
					<?="도로명주소: ".$LIST[$i]['address_road']." ".$LIST[$i]['address_detail'];?>
					<?="<br/>\n지번주소: ".$LIST[$i]['address_dong']; ?>
				</td>
			</tr>
			<tr>
				<td style="text-align:center;background:#EFEFEF">대출신청금액</td>
				<td style="text-align:center;"><?=number_format($LIST[$i]['request_money'])?>원</td>
				<td style="text-align:center;background:#EFEFEF">희망대출기간</td>
				<td style="text-align:center;"><?=number_format($LIST[$i]['request_period'])?>개월</td>
				<td style="background:#EFEFEF" colspan="2"></td>
			</tr>
<? if($LIST[$i]['loan_type']=='2') { ?>
			<tr>
				<td style="text-align:center;background:#EFEFEF">동산내용</td>
				<td colspan="5">
					<?=nl2br(htmlSpecialChars($LIST[$i]['dongsan_info']))?>
				</td>
			</td>
<? } ?>
			<tr>
				<td style="text-align:center;background:#EFEFEF">문의내용</td>
				<td colspan="5">
					<?=nl2br(htmlSpecialChars($LIST[$i]['content']))?>
				</td>
			</td>
			<tr>
				<td style="text-align:center;background:#EFEFEF">확인관리자</td>
				<td style="text-align:center;"><?=$LIST[$i]['admin_name']?></td>
				<td style="text-align:center;background:#EFEFEF">확인시간</td>
				<td style="text-align:center;"><?=substr($LIST[$i]['admin_check_datetime'],0,16)?></td>
				<td style="background:#EFEFEF" colspan="2"></td>
			</tr>
		</table>
	</div>


<?
		$num--;
	}
}
?>
	</form>
	<div id="paging_span" style="width:100%; margin:10px 0 20px 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

</div>

<? $qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']); ?>

<script type="text/javascript">
$(function() {
	$("input[id=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});

function formSubmit() {
	if(confirm("관리자확인 처리 하시겠습니까?")) {
		form_data = $('#form1').serialize();
		$.ajax({
			url : 'request.old.proc.ajax.php',
			type: 'POST',
			data:form_data,
			dataType: 'json',
			success:function(data, textStatus, jqXHR) {
				if(data.result=='SUCCESS') { window.location.reload(); }
				else if(data.result=='CHK_EMPTY') { alert('대상 의뢰내역을 선택하십시요.'); $('input[id=chkall]').focus(); }
			},
			error: function (jqXHR, textStatus, errorThrown)	{
				console.log(jqXHR);
			}
		});
	}
}

$('#btnSubmit').click(function() {
	formSubmit();
});



$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

</script>

<?

include_once (G5_ADMIN_PATH.'/admin.tail.php');

?>