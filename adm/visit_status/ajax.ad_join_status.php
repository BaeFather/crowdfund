<?
###############################
## 포털사이트 유료광고기준
###############################

include_once('_common.php');

//$stype = "Y";  // 자료요청기준 : Y -> 연간 월별 | Ym => 월간 일별
$stype = ($_REQUEST['stype']) ? $_REQUEST['stype'] : 'Y';

if($stype=='Y') {
	if(!$target_date) $target_date = date('Y');
	$loop_count = 12;
}
else if($stype=='Ym') {
	if(!$target_date) $target_date = date('Y-m');
	$loop_count = date('t', strtotime($target_date));
}

$targetDateLen = strlen($target_date);
$getDateLen = $targetDateLen + 3;

$year = substr($target_date, 0, 4);

$TOTAL = array(
	'view' => 0,
	'join' => 0,
	'joinPerc' => 0,
	'investor' => 0
);

$sql = "
	SELECT
		LEFT(A.rdate, $getDateLen) AS date,
		COUNT(A.idx) AS view,
		IFNULL(SUM(A.join_count), 0) AS join_count,
		A.site_id,
		A.site_ca
	FROM
		cf_visit_status A
	WHERE 1
		AND LEFT(A.rdate, $targetDateLen)='".$target_date."'
		AND ( (site_id='naver' AND site_ca='powerlink') OR (site_id='daum' AND site_ca='keyword') OR (site_id='google' AND site_ca='adwords') )
	GROUP BY
		date,
		site_id,
		site_ca
	ORDER BY
		date DESC, join_count DESC";
//print_rr($sql);
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$LIST = array();

for($i=0; $i<$rows; $i++) {
	$R = sql_fetch_array($res);

	$LIST[$R['date']]['view'] += $R['view'];
	$LIST[$R['date']]['join'] += $R['join_count'];
	$LIST[$R['date']]['joinPerc'] = sprintf('%.2f', ($LIST[$R['date']]['join'] / $LIST[$R['date']]['view']) * 100 );

	$TOTAL['view'] += $R['view'];
	$TOTAL['join'] += $R['join_count'];

	$LIST[$R['date']]['detail'][] = array(
		'site_id' => $R['site_id'],
		'site_ca' => $R['site_ca'],
		'view' => $R['view'],
		'join' => $R['join_count'],
		'joinPerc' => sprintf('%.2f', @(($R['join_count'] / $R['view']) * 100) )
	);

	// 해당 월가입자(사이트별) 번호 가져오기 -------------------------------------------------
	if( $R['join_count'] > 0 ) {
		$sql2 = "
			SELECT
				B.mb_no
			FROM
				cf_visit_status A
			INNER JOIN
				g5_member B ON A.idx=B.vi_idx
			WHERE 1
				AND A.site_id='".$R['site_id']."'
				AND site_ca='".$R['site_ca']."'
				AND LEFT(rdate, ".strlen($R['date']).")='".$R['date']."' ORDER BY mb_no";
		$res2 = sql_query($sql2);
		$mb_no_arr = "";
		while($R2 = sql_fetch_array($res2)) {
			$mb_no_arr.= "'".$R2['mb_no']."',";
		}
		sql_free_result($res2);
		$mb_no_arr = @substr($mb_no_arr, 0, strlen($mb_no_arr)-1);
		//echo $mb_no_arr."\n<br>";

		// 가입자번호로 투자건수,금액 추출
		$investor = 0;
		if($mb_no_arr) {
			$sql3 = "SELECT member_idx FROM cf_product_invest WHERE member_idx IN(".$mb_no_arr.") AND invest_state='Y' GROUP BY member_idx";
			$res3 = sql_query($sql3);
			$investor = sql_num_rows($res3);
		}

		$LIST[$R['date']]['investor'] += $investor;

		$TOTAL['investor'] += $investor;

		$investor = NULL;
	}
	// 해당 월가입자(사이트별) 번호 가져오기 -------------------------------------------------

	$LIST[$R['date']]['ivstTransPerc'] = sprintf('%.2f', @(($LIST[$R['date']]['investor'] / $LIST[$R['date']]['join']) * 100));

}
sql_free_result($res);

$TOTAL['joinPerc']      = sprintf('%.2f', @(($TOTAL['join'] / $TOTAL['view']) * 100));
$TOTAL['ivstTransPerc'] = sprintf('%.2f', @(($TOTAL['investor'] / $TOTAL['join']) * 100));

$loop_count = count($LIST);

//print_rr($LIST, 'font-size:12px');

?>

<div class="tbl_head02 tbl_wrap">

	<!-- 검색영역 START -->
	<form id="searchForm" class="form-horizontal">
	<ul class="col-sm-10 list-inline" style="width:100%; padding:0;margin-bottom:5px;">
		<li>대상기간</li>
		<li>
			<select name="target_date" id="target_date" value="<?=$target_date?>" onChange="fSubmit();" class="form-control input-sm" style="width:120px;text-align:center;">
				<option value="">:: 선택하세요 ::</option>
<?
if($stype=='Y') {
	for($i=2016; $i<=date('Y'); $i++) {
		$selected = ($i==$target_date) ? 'selected' : '';
		echo "<option value='".$i."' $selected>".$i."년</option>\n";
	}
}
else {
	for($i=0,$j=1; $i<12; $i++,$j++) {
		$yearMonth = $year."-".sprintf("%02d", $j);
		$selected = ($yearMonth==$target_date) ? 'selected' : '';
		echo "<option value='".$yearMonth."' $selected>".$yearMonth."</option>\n";
	}
}
?>
			</select>
		</li>
		<li style="float:right;padding-right:0;"><?if($stype=='Y'){?><a href="javascript:;" onClick="List('Ym', '<?=$year."-01";?>');"><?}?><span class="btn btn-sm <?=($stype=='Ym')?'btn-primary':'btn-default'?>">일별/월</span></a></li>
		<li style="float:right;padding-right:0;"><?if($stype=='Ym'){?><a href="javascript:;" onClick="List('Y', '<?=$year?>');"><?}?><span class="btn btn-sm <?=($stype=='Y')?'btn-primary':'btn-default'?>">월별/연</span></a></li>
	</ul>

	<table border='1' class='table-hover' style='font-size:11pt'>
		<tr align='center'>
			<th rowspan="2" style="width:8%;background:#F8F8EF">DATE</th>
			<th rowspan="2" style="width:8%;background:#F8F8EF">접속</th>
			<th rowspan="2" style="width:8%;background:#F8F8EF">가입</th>
			<th rowspan="2" style="width:8%;background:#F8F8EF">가입전환율</th>
			<th colspan="4" style="background:#F8F8EF">사이트별 내역</th>
			<th rowspan="2" style="width:8%;background:#F8F8EF">투자자수</th>
			<th rowspan="2" style="width:8%;background:#F8F8EF">투자전환율</th>
		</tr>
		<tr align='center'>
			<th style="width:14%;background:#F8F8EF">가입점유율</th>
			<th style="width:2%;background:#F8F8EF">접속</th>
			<th style="width:2%;background:#F8F8EF">가입</th>
			<th style="width:2%;background:#F8F8EF">가입률</th>
		</tr>
		<tr align='center' bgcolor='FFDDDD'>
			<td>합계</td>
			<td align='right'><?=number_format($TOTAL['view'])?></td>
			<td align='right'><?=number_format($TOTAL['join'])?></td>
			<td align='right'><?=$TOTAL['joinPerc']?>%</td>
			<td colspan='4'></td>
			<td align='right'><?=number_format($TOTAL['investor'])?></td>
			<td align='right'><?=$TOTAL['ivstTransPerc']?>%</td>
		</tr>
<?
$ARRKEYS = array_keys($LIST);
for($i=0, $j=1; $i<$loop_count; $i++,$j++) {

	if($stype=='Y') {
		$print_date = "<a href='javascript:;' onClick=\"List('Ym', '".$ARRKEYS[$i]."');\">" . $ARRKEYS[$i] . "</a>\n";
	}
	else {
		$print_date = "<a href='javascript:;' onClick=\"List('Y', '".substr($ARRKEYS[$i],0,4)."');\">" . $ARRKEYS[$i] . "</a>\n";
	}

?>
		<tr align='center'>
			<td><?=$print_date?></td>
			<td align='right'><?=number_format($LIST[$ARRKEYS[$i]]['view'])?></td>
			<td align='right'><?=number_format($LIST[$ARRKEYS[$i]]['join'])?></td>
			<td align='right'><?=$LIST[$ARRKEYS[$i]]['joinPerc']?>%</td>
			<td colspan="4">
				<ul id='rank<?=$i?>' class="list-inline" style="width:100%;min-width:600px;margin-bottom:-5px;">
<?
	if( count($LIST[$ARRKEYS[$i]]['detail']) ) {
		$loop_count2 = count($LIST[$ARRKEYS[$i]]['detail']);
		for($x=0,$y=1; $x<$loop_count2; $x++,$y++) {

			$divId = "rank".$i."_".$x;

			echo "					<li id='".$divId."' style='width:71%;height:22px;text-align:left;margin-left:0;'></li>\n";
			echo "					<li style='width:9%;height:22px;text-align:right;font-size:10pt;'>".number_format($LIST[$ARRKEYS[$i]]['detail'][$x]['view'])."</li>\n";
			echo "					<li style='width:9%;height:22px;text-align:right;font-size:10pt;'>".number_format($LIST[$ARRKEYS[$i]]['detail'][$x]['join'])."</li>\n";
			echo "					<li style='width:9%;height:22px;text-align:right;font-size:10pt;'>".$LIST[$ARRKEYS[$i]]['detail'][$x]['joinPerc']."%</li><br>\n";

			if($y==1) $barColor = "#02528A";
			else if($y==2) $barColor = "#0076BE";
			else if($y==3) $barColor = "#1F9BDE";
			else $barColor = "#8FC7E9";

			$print_siteId = $LIST[$ARRKEYS[$i]]['detail'][$x]['site_id'];
			if($LIST[$ARRKEYS[$i]]['detail'][$x]['site_ca']) $print_siteId.= " " . $LIST[$ARRKEYS[$i]]['detail'][$x]['site_ca'];

			$print_siteJoinPerc = round( @((int)$LIST[$ARRKEYS[$i]]['detail'][$x]['join'] / (int)$LIST[$ARRKEYS[$i]]['join'] * 100) );

			$SCRIPT[] = "\$('#".$divId."').jqbar({ label:'".$print_siteId."', value:'".$print_siteJoinPerc."', barColor:'".$barColor."' });";

			$divId = $print_siteId = $print_siteJoinPerc = NULL;

		}

	}
?>
				</ul>
			</td>
			<td align='right'><?=number_format($LIST[$ARRKEYS[$i]]['investor'])?></td>
			<td align='right'><?=$LIST[$ARRKEYS[$i]]['ivstTransPerc']?>%</td>
		</tr>
<?
}
?>
	</table>
</div>

<script>
function fSubmit() {
	$.ajax({
		url : "./ajax.ad_join_status.php",
		type: 'POST',
		data: {stype:'<?=$stype?>', target_date:$('#target_date').val()},
		success:function(data, textStatus, jqXHR){
			if(data=="ERROR-LOGIN") {
				window.location.replace('/bbs/login.php');
			}
			else {
				$('.tabXarea').html(data);
			}
		},
		beforeSend: function() { loading('on'); },
		complete: function() { loading('off'); },
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	})
}

function List(stype, date) {
	$.ajax({
		url : "./ajax.ad_join_status.php",
		type: 'POST',
		data: {stype:stype, target_date:date},
		success:function(data, textStatus, jqXHR){
			if(data=="ERROR-LOGIN") {
				window.location.replace('/bbs/login.php');
			}
			else {
				$('.tabXarea').html(data);
			}
		},
		beforeSend: function() { loading('on'); },
		complete: function() { loading('off'); },
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	})
}
</script>

<script>
$(document).ready(function() {
<?
for($i=0; $i<count($SCRIPT); $i++) {
	echo "\t" . $SCRIPT[$i] . "\n";
}
?>
});
</script>