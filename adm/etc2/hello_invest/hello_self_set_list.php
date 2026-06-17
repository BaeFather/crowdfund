<?
include_once('./_common.php');

$sql = "SELECT * FROM hello_self_invest";
$res = sql_query($sql);
$rows = sql_num_rows($res);

?>

<link rel="stylesheet" href="css/hello_invest.css" />

<div id="helloSetInvest">
	<p class="h3">자기자본 관리</p>
	<button id="addList" class="btn btn-add" onclick="location.href='./hello_self_set_form.php';">추가</button>

	<form id="frmListInvest" name="frmListInvest" method="post" class="form-horizontal" >
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>" />
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
				<? for($i=0; $i<$rows; $i++) { 
					$SET[$i] = sql_fetch_array($res);
				?>
				<tr>
					<td><?=$SET[$i]['start_date']?> ~ <?=$SET[$i]['end_date']?></td>
					<td class="price"><?=number_format($SET[$i]['price'])?> 원</td>
					<td><?=$SET[$i]['reg_date']?></td>
					<td><button id="modList" class="btn btn-mod" onclick="actionList(<?=$SET[$i]['idx']?>, 'mod');">수정</button><button id="delList" class="btn btn-del" onclick="actionList(<?=$SET[$i]['idx']?>, 'del');">삭제</button></td>
				</tr>
				<? } if (!$rows) { ?>
					<td colspan='4'><? echo "리스트가 없습니다."; ?></td>
				<? } ?>

			</tbody>
		</table>
	</form>

</div>


<script type="text/javascript">
// 수정, 삭제
function actionList(idx, mode) {
	var f = document.frmListInvest;
	
	if(mode == 'mod') {
		f.mode.value = "mod";
		f.action = 'hello_self_set_form.php?idx='+idx;
		f.submit();
	}
	if(mode == 'del') {
		if( confirm("정말로 삭제하시겠습니까?") ) {
			f.mode.value = "del";
			f.action = 'hello_self_set_update.php?idx='+idx;
			f.submit();
		}
	}

	
}

</script>