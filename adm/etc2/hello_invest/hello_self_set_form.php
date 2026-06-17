<?
include_once('./_common.php');

$sql = "SELECT * FROM hello_self_invest";
$row = sql_fetch($sql);

if(isset($idx)) {
    $mode_txt = '수정';
    $mode = 'mod';

	$msql = "SELECT * FROM hello_self_invest WHERE idx = '{$_GET['idx']}' ";
    $mrow = sql_fetch($msql);

} else {
    $mode_txt = '등록';
    $mode = 'save';
}

?>

<link rel="stylesheet" href="css/hello_invest.css" />
<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>


<div id="helloSetInvest">
	<p class="h3">자기자본 관리</p>
	<form id="frmSetInvest" name="frmSetInvest" method="post" class="form-horizontal" >
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>" />
		<input type="hidden" name="idx" id="idx" value="<?=$mrow['idx']?>" />
		<table class="tbl-list">
			<thead>
				<tr>
					<th>기간</th>
					<th>자기자본</th>
					<th>등록일</th>
					<th>비고</th>
				</tr>
			</thead>
			<tbody>
				<? if($mode == 'mod') { ?>
					<tr>
						<td><input type="text" name="start_date" id="start_date" class="datepicker" placeholder="시작일자" value="<?=$mrow['start_date']?>" readonly /> ~ <input type="text" name="end_date" id="end_date" class="datepicker" placeholder="종료일자" value="<?=$mrow['end_date']?>" readonly /></td>
						<td><input type="text" name="price" id="price" class="price" value="<?=$mrow['price']?>" /> 원</td>
						<td><?=$mrow['reg_date']?></td>
						<td></td>
					</tr>
				<? } else { ?>
					<tr>
						<td><input type="text" name="start_date" id="start_date" class="datepicker" placeholder="시작일자" value="" readonly /> ~ <input type="text" name="end_date" id="end_date" class="datepicker" placeholder="종료일자" value="" readonly /></td>
						<td><input type="text" name="price" id="price" class="price" value="" /> 원</td>
						<td></td>
						<td></td>
					</tr>
				<? } ?>
			</tbody>
		</table>
	</form>
	<div class="btn-wrap">
		<button id="saveList" class="btn btn-save" onclick="submitForm();" value="<?=$mode_txt?>"><?=($mode == "mod") ? '수정' : '등록';?></button>
		<button id="" class="btn btn-del" onclick="location.href='./hello_self_set_list.php'">취소</button>
	</div>
</div>


<script type="text/javascript">
// 등록
function submitForm() {
	var f = document.frmSetInvest;
	var start_date = f.start_date;
	var end_date = f.end_date;
	var price = f.price;
	var btn_value = $('#saveList').val();

	if(start_date.value == '') {
		alert("시작 날짜를 입력해주세요");
		$(start_date).focus();
		return false;

	} else if(end_date.value == '') {
		alert("종료 날짜를 입력해주세요");
		$(end_date).focus();
		return false;

	} else if(price.value == '') {
		alert("자기자본 금액을 입력해주세요");
		$(price).focus();
		return false;

	} else {
		if( confirm(btn_value+'하시겠습니까?') ) {
			f.action = 'hello_self_set_update.php';
			f.target = "_self";
			f.submit();
		}
	}
	
}


// datepicker option
$(function() {
	$('.datepicker').datepicker({
		dateFormat : 'yy-mm-dd',
		changeMonth: true,
		changeYear : true,
		showMonthAfterYear: true,
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin: ['월', '화', '수', '목', '금', '토', '일']

	});
});

</script>