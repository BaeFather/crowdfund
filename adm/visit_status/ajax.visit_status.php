<?
###############################
## 아이디별 총입금액
###############################

include_once('_common.php');

while(list($k,$v)=each($_REQUEST)) { ${$k} = trim($v); }

/*
SELECT ip, device, COUNT(idx) AS cnt FROM cf_visit_stats WHERE 1 AND is_bot='' GROUP BY ip ORDER BY cnt DESC;
SELECT rdate, device, COUNT(idx) AS cnt FROM cf_visit_stats WHERE 1 AND is_bot='' GROUP BY rdate, device ORDER BY rdate DESC;
SELECT referer, device, COUNT(idx) AS cnt FROM cf_visit_stats WHERE 1 AND is_bot='' GROUP BY referer ORDER BY cnt DESC LIMIT 100;
SELECT site_id, site_ca, COUNT(idx) AS cnt FROM cf_visit_stats WHERE 1 AND is_bot='' GROUP BY site_id ORDER BY cnt DESC LIMIT 100;
SELECT site_id, site_ca, keyword, pkeyword, COUNT(idx) AS cnt FROM cf_visit_stats WHERE 1 AND is_bot='' AND keyword!='' GROUP BY site_id, keyword ORDER BY cnt DESC LIMIT 100;
SELECT site_id, site_ca, keyword, pkeyword, COUNT(idx) AS cnt FROM cf_visit_stats WHERE 1 AND is_bot='' AND keyword!='' GROUP BY site_id, pkeyword ORDER BY cnt DESC LIMIT 100;

SELECT country, device, COUNT(idx) AS cnt FROM cf_visit_stats WHERE 1 AND is_bot='' GROUP BY referer ORDER BY cnt DESC LIMIT 100;
SELECT referer, device, COUNT(idx) AS cnt FROM cf_visit_stats WHERE 1 AND is_bot='' GROUP BY referer ORDER BY cnt DESC LIMIT 100;
SELECT referer, device, COUNT(idx) AS cnt FROM cf_visit_sta
*/


$sdate = ($sdate) ? $sdate : date('Y-m-d', strtotime('-1 month'));
$edate = ($edate) ? $edate : date('Y-m-d');

if($sdate > $edate) { echo 'ERROR-SEARCHDATE'; exit; }

$dCount = (strtotime($edate)-strtotime($sdate)) / 86400;
for($i=0; $i<=$dCount; $i++) {
	$date = date('Y-m-d', strtotime('+'.$i.' day', strtotime($sdate)));
	$ARR[$date] = array();

	$R = sql_fetch("SELECT COUNT(mb_no) AS joinCnt FROM g5_member WHERE LEFT(mb_datetime,10)='".$date."' AND mb_level IN('1','200')");
	$ARR[$date]['joinCnt'] = $R['joinCnt'];
	$TOTAL['joinCnt'] += $R['joinCnt'];
}
//print_rr($ARR);


//$ARR = $_ARR;
//$ARR = array_reverse($_ARR);


$sql = "
	SELECT
		rdate,
		site_id,
		COUNT(idx) AS visitCnt
	FROM
		cf_visit_status
	WHERE 1
		AND rdate BETWEEN '".$sdate."' AND '".$edate."'
	GROUP BY
		rdate, site_id
	ORDER BY
		rdate DESC, visitCnt DESC, site_id ASC";
//echo $sql;
$res = sql_query($sql);
while($ROW = sql_fetch_array($res)) {
	$ARR[$ROW['rdate']]['visitCnt'] += $ROW['visitCnt'];
	$ARR[$ROW['rdate']]['RANK'][] = array('site_id'=>$ROW['site_id'], 'visitCnt'=>$ROW['visitCnt']);

	$TOTAL['visitCnt'] += $ROW['visitCnt'];
}
sql_free_result($res);

$TOTAL['joinPerc'] = @($TOTAL['joinCnt'] / $TOTAL['visitCnt']) * 100;

$arrCount = count($ARR);

?>
<div class="tbl_head02 tbl_wrap">

	<!-- 검색영역 START -->
	<form id="searchForm" class="form-horizontal">
	<ul class="col-sm-10 list-inline" style="width:100%; padding-left:0;margin-bottom:5px;">
		<li>검색대상일</li>
		<li><input type="text" name="sdate" id="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" style="width:120px;text-align:center;"></li>
		<li>~</li>
		<li><input type="text" name="edate" id="edate" value="<?=$edate?>" class="form-control input-sm datepicker" style="width:120px;text-align:center;"></li>
		<li></li>
		<li><button type="button" id="submit_button" class="btn btn-sm btn-warning">검색</button></li>
	</ul>

	<table border='1' class='table-hover' style='font-size:10pt'>
		<tr align='center' bgcolor='#F8F8EF'>
			<th style="width:15%;background:#F8F8EF">DATE</th>
			<th style="width:15%;background:#F8F8EF">접속</th>
			<th style="width:%;background:#F8F8EF">접속상세</th>
			<th style="width:15%;background:#F8F8EF">가입</th>
			<th style="width:15%;background:#F8F8EF">가입률</th>
		</tr>
		<tr align='center' bgcolor='FFDDDD'>
			<td>합계</td>
			<td align='right'><?=number_format($TOTAL['visitCnt'])?></td>
			<td></td>
			<td align='right'><?=number_format($TOTAL['joinCnt'])?></td>
			<td align='right'><?=sprintf("%.2f", $TOTAL['joinPerc'])?>%</td>
		</tr>

<?
//print_rr($ARR);
$ARRKEYS = array_keys($ARR);

for($i=0,$j=1; $i<$arrCount; $i++,$j++) {

	$joinPerc  = @(($ARR[$ARRKEYS[$i]]['joinCnt'] / $ARR[$ARRKEYS[$i]]['visitCnt']) * 100);

	echo "		<tr align='center'>
			<td>".$ARRKEYS[$i]."</td>
			<td align='right'>".number_format($ARR[$ARRKEYS[$i]]['visitCnt'])."</td>
			<td align='left'>
				<div id='rank".$i."' style='width:100%;'>\n";

	if(count($ARR[$ARRKEYS[$i]]['RANK'])) {

		for($x=0,$y=1; $x<count($ARR[$ARRKEYS[$i]]['RANK']); $x++,$y++) {

			$visitPerc = @(( $ARR[$ARRKEYS[$i]]['RANK'][$x]['visitCnt'] / $ARR[$ARRKEYS[$i]]['visitCnt'] ) * 100);

			if($visitPerc < 1) break;

			$divId = "rank".$i."_".$x;
			echo "					<div id='".$divId."'></div>\n";

			if($y==1) $barColor = "#02528A";
			else if($y==2) $barColor = "#0076BE";
			else if($y==3) $barColor = "#1F9BDE";
			else $barColor = "#8FC7E9";

			$siteId = ($ARR[$ARRKEYS[$i]]['RANK'][$x]['site_id']) ? $ARR[$ARRKEYS[$i]]['RANK'][$x]['site_id'] : "직접방문";

			$SCRIPT[] = "\$('#".$divId."').jqbar({ label:'".addSlashes($siteId)."', value:".$visitPerc.", barColor:'".$barColor."' });";

			$visitPerc = $divId = $siteId = NULL;

		}

	}

	echo "				</div>
			</td>
			<td align='right'>".number_format($ARR[$ARRKEYS[$i]]['joinCnt'])."</td>
			<td align='right'>".sprintf("%.2f", $joinPerc)."%</td>
		</tr>\n";

	$joinPerc = NULL;

}
?>
	</table>


</div>

<script>
$('#submit_button').on('click', function() {
	$.ajax({
		url : "./ajax.visit_status.php",
		type: 'POST',
		data: {sdate:$('#sdate').val(), edate:$('#edate').val()},
		success:function(data, textStatus, jqXHR){
			$('.tabXarea').html(data);
		},
		beforeSend: function() { loading('on'); },
		complete: function() { loading('off'); },
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	})
});

$(function(){
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesShort: ['일' ,'월', '화', '수', '목', '금', '토']
	});
});
</script>

<script>
$(function(){
<?
for($i=0; $i<count($SCRIPT); $i++) {
	echo "\t" . $SCRIPT[$i] . "\n";
}
?>
});
</script>