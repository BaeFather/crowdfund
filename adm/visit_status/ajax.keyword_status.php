<?
###############################
## 포털사이트 유입키워드 기준
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
	'view'     => 0,
	'join'     => 0,
	'joinPerc' => 0,
	'investor' => 0
);

$where = " 1=1";
$where.= " AND LEFT(A.rdate, $targetDateLen)='".$target_date."'";
$where.= " AND A.keyword!=''";
$where.= ($_REQUEST['is_paid']) ? " AND A.is_paid='1'" : "";

$sql = "
	SELECT
		LEFT(A.rdate, $getDateLen) AS date,
		A.keyword,
		COUNT(A.idx) AS view,
		IFNULL(SUM(A.join_count), 0) AS join_count
	FROM
		cf_visit_status A
	WHERE
		$where
	GROUP BY
		date,
		A.keyword
	ORDER BY
		date DESC, view DESC, join_count DESC, A.keyword ASC";
//print_rr($sql); exit;
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$LIST = array();

for($i=0,$j=1; $i<$rows; $i++,$j++) {
	$R = sql_fetch_array($res);

	$LIST[$R['date']]['view']    += $R['view'];
	$LIST[$R['date']]['join']    += $R['join_count'];
	$LIST[$R['date']]['joinPerc'] = sprintf('%.2f', @(($LIST[$R['date']]['join'] / $LIST[$R['date']]['view']) * 100) );

	$LIST[$R['date']][$R['keyword']]['view']     = $R['view'];
	$LIST[$R['date']][$R['keyword']]['join']     = $R['join_count'];
	$LIST[$R['date']][$R['keyword']]['joinPerc'] = sprintf('%.2f', @(($R['join_count'] / $R['view']) * 100) );

	$TOTAL['view'] += $R['view'];
	$TOTAL['join'] += $R['join_count'];


	$LIST[$R['date']]['detail'][] = array(
		'keyword' => $R['keyword'],
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
			WHERE 1=1
				AND LEFT(rdate, ".strlen($R['date']).")='".$R['date']."'
				AND A.keyword='".$R['keyword']."'
			ORDER BY
				mb_no";
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
			sql_free_result($res3);
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

//print_rr($LIST, 'font-size:12px'); exit;

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

	<table border='1' class='table-hover' style='font-size:10pt'>
		<tr align='center'>
			<th rowspan="2" style="width:9%;background:#F8F8EF">DATE</th>
			<th colspan="6" style="width:46%;background:#EFEFEF">키워드 내역 (상위 10개)</th>
			<th rowspan="2" style="width:9%;background:#F8F8EF">전체접속</th>
			<th rowspan="2" style="width:9%;background:#F8F8EF">전체가입</th>
			<th rowspan="2" style="width:9%;background:#F8F8EF">가입전환율</th>
			<th rowspan="2" style="width:9%;background:#F8F8EF">투자자수</th>
			<th rowspan="2" style="width:9%;background:#F8F8EF">투자전환율</th>
		</tr>
		<tr align='center'>
			<th style="width:%;background:#EFEFEF">키워드</th>
			<th style="width:7%;background:#EFEFEF">접속</th>
			<th style="width:7%;background:#EFEFEF">접속점유율</th>
			<th style="width:7%;background:#EFEFEF">가입</th>
			<th style="width:7%;background:#EFEFEF">가입전환률</th>
			<th style="width:7%;background:#EFEFEF">가입점유율</th>
		</tr>
		<tr align='center' style="background:#FFDDDD;color:red">
			<td>전체합계</td>
			<td colspan='6'></td>
			<td align='right'><?=number_format($TOTAL['view'])?></td>
			<td align='right'><?=number_format($TOTAL['join'])?></td>
			<td align='right'><?=$TOTAL['joinPerc']?>%</td>
			<td align='right'><?=number_format($TOTAL['investor'])?></td>
			<td align='right'><?=$TOTAL['ivstTransPerc']?>%</td>
		</tr>
<?
$ARRKEYS = array_keys($LIST);
for($i=0, $j=1; $i<$loop_count; $i++,$j++) {

	if($stype=='Y') {
		$_date = $ARRKEYS[$i];
		$print_date = "<a href='javascript:;' onClick=\"List('Ym', '".$_date."');\">" . $ARRKEYS[$i] . "</a>\n";
		$detailRankLoadTag = "keywordRank('Ym', '".$_date."');";
	}
	else {
		$_date = substr($ARRKEYS[$i], 0, 4);
		$print_date = "<a href='javascript:;' onClick=\"List('Y', '".$_date."');\">" . $ARRKEYS[$i] . "</a>\n";
		$detailRankLoadTag = "keywordRank('Y', '".$_date."');";
	}

?>
		<tr align='center'>
			<td><?=$print_date?></td>
			<td colspan="6" style="padding:0;" onClick="<?=$detailRankLoadTag?>"><table class="table-striped" style="width:100%;margin:0;border:0;">
					<colgroup>
						<col style="width:24%;">
						<col style="width:15.2%">
						<col style="width:15.2%">
						<col style="width:15.2%">
						<col style="width:15.2%">
						<col style="width:15.2%">
					</colgroup>
<?
	if( count($LIST[$ARRKEYS[$i]]['detail']) ) {
		$loop_count2 = count($LIST[$ARRKEYS[$i]]['detail']);
		$loop_count2 = ($loop_count2 < 10) ? $loop_count2 : 10;
		for($x=0,$y=1; $x<$loop_count2; $x++,$y++) {

			$LIST[$ARRKEYS[$i]]['detail'][$x]['viewSgPerc'] = sprintf('%.2f', @(($LIST[$ARRKEYS[$i]]['detail'][$x]['view'] / $LIST[$ARRKEYS[$i]]['view']) * 100) );
			$LIST[$ARRKEYS[$i]]['detail'][$x]['joinSgPerc'] = sprintf('%.2f', @(($LIST[$ARRKEYS[$i]]['detail'][$x]['join'] / $LIST[$ARRKEYS[$i]]['join']) * 100) );

			$divId = "rank".$i."_".$x;

			echo "					<tr id='rank'".$i."' align='right'>
						<td style='border:0;padding:1px 6px;line-height:18px;overflow:hidden;' align='center'>".$LIST[$ARRKEYS[$i]]['detail'][$x]['keyword']."</td>
						<td style='border:0;padding:1px 6px;'>".number_format($LIST[$ARRKEYS[$i]]['detail'][$x]['view'])."</td>
						<td style='border:0;padding:1px 6px;'>".$LIST[$ARRKEYS[$i]]['detail'][$x]['viewSgPerc']."%</td>
						<td style='border:0;padding:1px 6px;'>".number_format($LIST[$ARRKEYS[$i]]['detail'][$x]['join'])."</td>
						<td style='border:0;padding:1px 6px;'>".$LIST[$ARRKEYS[$i]]['detail'][$x]['joinPerc']."%</td>
						<td style='border:0;padding:1px 6px;'>".$LIST[$ARRKEYS[$i]]['detail'][$x]['joinSgPerc']."%</td>
					</tr>\n";

			if($y==1) $barColor = "#02528A";
			else if($y==2) $barColor = "#0076BE";
			else if($y==3) $barColor = "#1F9BDE";
			else $barColor = "#8FC7E9";

			$print_keyword = $LIST[$ARRKEYS[$i]]['detail'][$x]['keyword'];

			$print_siteJoinPerc = round( @((int)$LIST[$ARRKEYS[$i]]['detail'][$x]['join'] / (int)$LIST[$ARRKEYS[$i]]['join'] * 100) );

			$SCRIPT[] = "\$('#".$divId."').jqbar({ label:'".$print_keyword."', value:'".$print_siteJoinPerc."', barColor:'".$barColor."' });";

			$divId = $print_siteId = $print_siteJoinPerc = NULL;

		}

	}
?>
				</table>
			</td>
			<td align='right'><?=number_format($LIST[$ARRKEYS[$i]]['view'])?></td>
			<td align='right'><?=number_format($LIST[$ARRKEYS[$i]]['join'])?></td>
			<td align='right'><?=$LIST[$ARRKEYS[$i]]['joinPerc']?>%</td>
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
		url : "./ajax.keyword_status.php",
		type: 'POST',
		data: {stype:'<?=$stype?>', target_date:$('#target_date').val()},
		success:function(data, textStatus, jqXHR) {
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
		url : "./ajax.keyword_status.php",
		type: 'POST',
		data: {stype:stype, target_date:date},
		success:function(data, textStatus, jqXHR) {
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

function keywordRank(stype, date) {
	$.ajax({
		url : "./ajax.keyword_rank.php",
		type: 'POST',
		data: {stype:stype, target_date:date},
		success:function(data, textStatus, jqXHR) {
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