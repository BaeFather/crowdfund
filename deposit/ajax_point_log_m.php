<div style="width:100%;">
<?
if($list_count){

	$No = $affect_num - $size * ($page - 1);

	for($i=0; $i<$list_count; $i++) {

		$fcolor = ($LIST[$i]['gubun']=='plus') ? '#153FA1' : '#FF3333';

?>
	<table class="tblX" style="border-top:1px solid #4863A3; margin-bottom:10px;">
		<colgroup>
			<col width="14%">
			<col width="36%">
			<col width="14%">
			<col width="36%">
		</colgroup>
		<tr>
			<td style="text-align:center;background:#F7F7F7;">No</td>
			<td style="text-align:center;"><?=$No?></td>
			<td style="text-align:center;background:#F7F7F7;">Date</td>
			<td style="text-align:center;"><?=preg_replace("/-/", ".", substr($LIST[$i]['po_datetime'], 0, 16))?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:#F7F7F7;">구분</td>
			<td style="text-align:center;color:<?=$fcolor?>"><?=($LIST[$i]['gubun']=='plus')?'입금':'차감'?></td>
			<td style="text-align:center;background:#F7F7F7;">금액</td>
			<td style="text-align:right;color:<?=$fcolor?>"><?=number_format($LIST[$i]['po_point'])?>원</td>
		</tr>
		<tr>
			<td style="text-align:center;background:#F7F7F7;">상세</td>
			<td colspan="3" style="text-align:left;color:<?=$fcolor?>"><?=$LIST[$i]['po_content']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:#F7F7F7;">잔액</td>
			<td colspan="3" style="text-align:right;color:#000"><?=number_format($LIST[$i]['po_mb_point'])?>원</td>
		</tr>
	</table>
<?
		$No--;

	}
}
else {
?>
	<table class="tblX" style="border-top:1px solid #4863A3; margin-bottom:10px;">
		<tbody>
			<tr>
				<td style="text-align:center;">검색된 데이터가 없습니다.</td>
			</tr>
		</tbody>
	</table>
<?
}
?>
</div>

<div id="paging_span" class="mb20 point_log_paging">
	<? paging($affect_num, $page, $size); ?>
</div>

<?

@sql_close();
exit;

?>