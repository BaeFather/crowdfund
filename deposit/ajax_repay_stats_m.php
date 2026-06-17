<div class="mb30">

	<div style="width:100%; display:inline-block;">
		<span class="left" style="margin:0 0 8px">
			<span class="<?=($type==1)?'btn_blue_s':'btn_gray_s';?>" onClick="loadPage('1', '<?=$sy?>', '');" style="width:90px">연간.월별</span>
			<span class="<?=($type==2)?'btn_blue_s':'btn_gray_s';?>" onClick="loadPage2('2', '', '');" style="width:90px; margin-right:10px">일별.기간별</span>
		</span>
		<span class="left">
<? if($type=='1') { ?>
			<select id="sy" style="height:22px;color:navy;border:1px solid #bdbdbd;border-radius:3px;">
				<option value="">:: 대상년도 ::</option>
<?
	for($i=2016; $i<=$maxYear; $i++) {
		$selected = ($i==$sy) ? 'selected' : '';
		echo "<option value='".$i."' $selected>".$i."년</option>\n";
	}
?>
			</select>
<? } else { ?>
			<input type="text" class="inp datepicker" id="sdate" value="<?=$sdate?>" placeholder="검색시작일" style="width:100px;height:22px" readonly> ~
			<input type="text" class="inp datepicker" id="edate" value="<?=$edate?>" placeholder="검색종료일" style="width:100px;height:22px" readonly>
<? } ?>
			<span id="load_btn" class="btn_red">확인</span>
		</span>
	</div>

	<table class="tblX" style="border-top:2px solid #284893;">
		<colgroup>
			<col style="width:25%">
			<col style="width:20%">
			<col style="width:20%">
			<col style="width:35%">
		</colgroup>
		<tr style="background-color:#F7F7F7;border-top:2px solid #284893;">
			<th style="color:brown" rowspan="6"><b>합계</b></th>
			<th style="color:brown" rowspan="2"><b>투자발생</b></th>
			<th style="color:brown"><b>건수</b></th>
			<th style="text-align:right;color:#0000ff;"><?=number_format($TOTAL['invest_count'])?>건</th>
		</tr>
		<tr style="background-color:#F7F7F7;">
			<th style="color:brown"><b>금액</b></th>
			<th style="text-align:right;color:#0000ff;"><?=number_format($TOTAL['invest_amount'])?>원</th>
		</tr>
		<tr style="background-color:#F7F7F7;">
			<th style="color:brown" rowspan="2"><b>원금상환</b></th>
			<th style="color:brown"><b>예정</b></th>
			<th style="text-align:right;color:#0000ff;"><?=number_format($TOTAL['target_principal'])?>원</th>
		</tr>
		<tr style="background-color:#F7F7F7;">
			<th style="color:brown"><b>지급</b></th>
			<th style="text-align:right;color:#0000ff;"><?=number_format($TOTAL['payed_principal'])?>원</th>
		</tr>
		<tr style="background-color:#F7F7F7;">
			<th style="color:brown" rowspan="2"><b>이자상환</b></th>
			<th style="color:brown"><b>예정</b></th>
			<th style="text-align:right;color:#0000ff;"><?=number_format($TOTAL['target_interest'])?>원</th>
		</tr>
		<tr style="background-color:#F7F7F7;">
			<th style="color:brown"><b>지급</b></th>
			<th style="text-align:right;color:#0000ff;"><?=number_format($TOTAL['payed_interest'])?>원</th>
		</tr>
<?
if(count($DATA)) {
	$DATA_ARR = array_keys($DATA);
	for($i=0,$j=1; $i<count($DATA); $i++,$j++) {

		$date = $DATA_ARR[$i];

		if($type==1) {
			$from_date = $date.'-01';
			$to_date   = $date."-".sprintf("%02d", date(t, strtotime($from_date)));
			$link      = "loadPage2('2','$from_date','$to_date');";
		}
		else if($type==2) {
			$from_date = substr($date, 0, 4);
			$to_date   = '';
			$link      = "loadPage('1','$from_date','$to_date');";
		}

		if($DATA[$date]['target_principal'] > 0) {
			$target_principal = $DATA[$date]['target_principal'];
			$fcolor1 = '#000';
		}
		else {
			$target_principal = 0;
			$fcolor1 = '#ccc';
		}

		if($DATA[$date]['target_interest'] > 0) {
			$target_interest = $DATA[$date]['target_interest'];
			$fcolor2 = '#000';
		}
		else {
			$target_interest = 0;
			$fcolor2 = '#ccc';
		}

		$tr_bgcolor = ($j%2==0) ? "#FAFAFA" : "#EFEFEF";

?>
		<tr style="background:<?=$tr_bgcolor?>">
			<td style="text-align:center;" rowspan="6">
			  <a href="#" onClick="<?=$link?>"><?=preg_replace("/-/", ".", $DATA_ARR[$i])?></a>
				<a href="#" onClick="<?=$link?>" style="color:#153FA1"><span class="btn_gray_s2" style="margin-top:4px;"><?=($type==1)?'일별보기':'월별보기'?></span></a>
			</td>
			<td style="text-align:center;" rowspan="2">투자발생</td>
			<td style="text-align:center;">건수</td>
			<td style="text-align:right;color:<?=($DATA[$date]['invest_count'])?'#000':'#bbb'?>"><?=number_format($DATA[$date]['invest_count'])?>건</td>
		</tr>
		<tr style="background:<?=$tr_bgcolor?>">
			<td style="text-align:center;">금액</td>
			<td style="text-align:right;color:<?=($DATA[$date]['invest_amount'])?'#000':'#bbb'?>"><?=number_format($DATA[$date]['invest_amount'])?>원</td>
		</tr>
		<tr style="background:<?=$tr_bgcolor?>">
			<td style="text-align:center;" rowspan="2">원금상환</td>
			<td style="text-align:center;">예정</td>
			<td style="text-align:right;color:<?=($DATA[$date]['target_principal'])?'#000':'#bbb'?>"><?=number_format($target_principal)?>원</td>
		</tr>
		<tr style="background:<?=$tr_bgcolor?>">
			<td style="text-align:center;">지급</td>
			<td style="text-align:right;color:<?=($DATA[$date]['payed_principal'])?'#000':'#bbb'?>"><?=number_format($DATA[$date]['payed_principal'])?>원</td>
		</tr>
		<tr style="background:<?=$tr_bgcolor?>">
			<td style="text-align:center;" rowspan="2">이자상환</td>
			<td style="text-align:center;">예정</td>
			<td style="text-align:right;color:<?=($DATA[$date]['target_interest'])?'#000':'#bbb'?>"><?=number_format($target_interest)?>원</td>
		</tr>
		<tr style="background:<?=$tr_bgcolor?>">
			<td style="text-align:center;">지급</td>
			<td style="text-align:right;color:<?=($DATA[$date]['payed_interest'])?'#000':'#bbb'?>"><?=number_format($DATA[$date]['payed_interest'])?>원</td>
		</tr>
<?
	}
}else {
?>
		<tr align="right" style="height:25px;" onMouseOver="this.bgColor='#F7F7F7'" onMouseOut="this.bgColor=''">
			<td align="center" colspan="10">데이터가 없습니다.</td>
		</tr>
<?
}
?>
	</table>

	<div style="margin-top:10px;color:brown;font-size:13px;">※ 이자상환액은 플랫폼 이용료 및 세금(부가가치세,지방소득세 등)을 제외한 금액입니다.</div>

</div>

<script type="text/javascript">
$(function(){
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesShort: ['일' ,'월', '화', '수', '목', '금', '토']
	});
});

$('#sy').on('change', function() {
	val = $('#sy').val();
	loadPage('<?=$type?>', val, '');
});

$('#load_btn').on('click', function() {
<?if($type==1) { ?>
	val1 = $('#sy').val();
	val2 = '';
	loadPage('<?=$type?>', val1, val2);
<? } else if($type==2) { ?>
	val1 = $('#sdate').val();
	val2 = $('#edate').val();
	loadPage2('<?=$type?>', val1, val2);
<? } ?>
});

loadPage = function(arg1, arg2) {
	val1 = (arg1) ? arg1 : '<?=$type?>';
	val2 = (arg2) ? arg2 : '<?=$sy?>';
	$.ajax({
		url: '/deposit/ajax_repay_stats.php',
		type: 'GET',
		data: {type:val1, sy:val2},
		success: function(data) {
			$('#ajax_return_txt').val(data);
			$('#interest_status_area').empty();
			$('#interest_status_area').html(data);
		},
		beforeSend: function() { $('#loading').css('display','block'); },
		complete: function() { $('#loading').css('display','none'); }
	});
}

loadPage2 = function(arg1, arg2, arg3) {
	val1 = (arg1) ? arg1 : '<?=$type?>';
	val2 = (arg2) ? arg2 : '<?=$sdate?>';
	val3 = (arg3) ? arg3 : '<?=$edate?>';
	$.ajax({
		url: '/deposit/ajax_repay_stats.php',
		type: 'GET',
		data: {type:val1, sdate:val2, edate:val3},
		success: function(data) {
			$('#ajax_return_txt').val(data);
			$('#interest_status_area').empty();
			$('#interest_status_area').html(data);
		},
		beforeSend: function() { $('#loading').css('display','block'); },
		complete: function() { $('#loading').css('display','none'); }
	});
}
</script>
<?

@sql_close();
exit;

?>