<?
###############################
## 포털사이트 유입키워드 기준
###############################

include_once('_common.php');

$stype = ($_REQUEST['stype']) ? $_REQUEST['stype'] : 'Y';

if($stype=='Y') {
	if(!$target_date) $target_date = date('Y');
}
else if($stype=='Ym') {
	if(!$target_date) $target_date = date('Y-m');
}

$targetDateLen = strlen($target_date);
$year  = substr($target_date, 0, 4);
$month = substr($target_date, 5, 2);
$day   = substr($target_date, 8);

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
		LEFT(A.rdate, $targetDateLen) AS date,
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
//print_rr($sql);
//print_rr($_REQUEST);
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$LIST = array();

for($i=0,$j=1; $i<$rows; $i++,$j++) {
	$LIST[$i] = sql_fetch_array($res);

	$LIST[$i]['joinPerc'] = floatRtrim(sprintf('%.2f', @(($LIST[$i]['join_count'] / $LIST[$i]['view'])*100)));


	$sqlx = "
		SELECT
			A.site_id, A.site_ca, A.referer,
			COUNT(A.idx) AS view,
			IFNULL(SUM(A.join_count), 0) AS join_count
		FROM
			cf_visit_status A
		WHERE
			$where
			AND keyword='".$LIST[$i]['keyword']."'
		GROUP BY
			A.site_id, A.site_ca
		ORDER BY
			view DESC, join_count DESC, A.keyword ASC";
	//echo $sqlx."<br>\n";
	$resx  = sql_query($sqlx);
	$rowsx = sql_num_rows($resx);
	for($x=0; $x<$rowsx; $x++) {
		$RX = sql_fetch_array($resx);
		$LIST[$i]['detail'][$x] = $RX;
		$LIST[$i]['detail'][$x]['joinPerc'] = floatRtrim(sprintf('%.2f', @(($RX['join_count'] / $RX['view'])*100)));

		// 해당 월가입자(사이트별) 번호 가져오기 -------------------------------------------------
		if( $LIST[$i]['detail'][$x]['join_count'] > 0 ) {
			$sql2 = "
				SELECT
					B.mb_no
				FROM
					cf_visit_status A
				INNER JOIN
					g5_member B ON A.idx=B.vi_idx
				WHERE 1=1
					AND LEFT(rdate, ".strlen($LIST[$i]['date']).")='".$LIST[$i]['date']."'
					AND A.keyword='".$LIST[$i]['keyword']."' AND site_id='".$LIST[$i]['detail'][$x]['site_id']."' AND site_ca='".$LIST[$i]['detail'][$x]['site_ca']."'
				ORDER BY
					mb_no";
			$res2 = sql_query($sql2);
			$mb_no_arr = "";
			while($R2 = sql_fetch_array($res2)) {
				$mb_no_arr.= "'".$R2['mb_no']."',";
			}

			$mb_no_arr = @substr($mb_no_arr, 0, strlen($mb_no_arr)-1);
			//echo $mb_no_arr."\n<br>";

			// 가입자번호로 투자건수,금액 추출
			if($mb_no_arr) {
				$sql3 = "SELECT member_idx FROM cf_product_invest WHERE member_idx IN(".$mb_no_arr.") AND invest_state='Y' GROUP BY member_idx";
				$res3 = sql_query($sql3);
				$investor_count = sql_num_rows($res3);

				$sql4 = "SELECT IFNULL(SUM(amount),0) AS amount FROM cf_product_invest WHERE member_idx IN(".$mb_no_arr.") AND invest_state='Y'";
				$INVEST = sql_fetch($sql4);
				$invest_amount = $INVEST['amount'];
			}

			$LIST[$i]['detail'][$x]['investor']      = $investor_count;
			$LIST[$i]['detail'][$x]['ivstTransPerc'] = floatRtrim(sprintf('%.2f', @(($LIST[$i]['detail'][$x]['investor'] / $LIST[$i]['detail'][$x]['join_count'])*100)));
			$LIST[$i]['detail'][$x]['invest_amount'] = $invest_amount;

		}
		else {
			$LIST[$i]['detail'][$x]['investor']      = 0;
			$LIST[$i]['detail'][$x]['ivstTransPerc'] = 0;
			$LIST[$i]['detail'][$x]['invest_amount'] = 0;
		}
		// 해당 월가입자(사이트별) 번호 가져오기 -------------------------------------------------

		$LIST[$i]['investor'] += $LIST[$i]['detail'][$x]['investor'];
		$LIST[$i]['invest_amount'] += $LIST[$i]['detail'][$x]['invest_amount'];

		$TOTAL['investor'] += $LIST[$i]['detail'][$x]['investor'];
		$TOTAL['invest_amount'] += $LIST[$i]['detail'][$x]['invest_amount'];

	}

	$LIST[$i]['ivstTransPerc'] = floatRtrim(sprintf('%.2f', @(($LIST[$i]['investor'] / $LIST[$i]['join_count'])*100)));

	$TOTAL['view']       += $LIST[$i]['view'];
	$TOTAL['join_count'] += $LIST[$i]['join_count'];

}
sql_free_result($res);

$TOTAL['joinPerc']      = floatRtrim(sprintf('%.2f', @(($TOTAL['join_count'] / $TOTAL['view']) * 100)));
$TOTAL['ivstTransPerc'] = floatRtrim(sprintf('%.2f', @(($TOTAL['investor'] / $TOTAL['join_count']) * 100)));

$loop_count = count($LIST);

//print_rr($LIST, 'font-size:12px');
//exit;

?>

<div class="tbl_head02 tbl_wrap">

	<!-- 검색영역 START -->
	<form id="searchFormX" class="form-horizontal">
	<ul class="col-sm-10 list-inline" style="width:100%; padding:0;margin-bottom:5px;">
		<li>대상기간</li>
		<li>
			<select name="target_year" id="target_year" class="form-control input-sm" style="width:120px;text-align:center;">
				<option value="">:: 년 선택 ::</option>
<?
for($i=0,$j=2016; $i<=date('Y'); $i++,$j++) {
	$selected = ($i==$year) ? 'selected' : '';
	echo "<option value='".$i."' $selected>".$i."년</option>\n";
}
?>
			</select>
		</li>
		<li>
			<select name="target_month" id="target_month" onChange="fSubmitX();" class="form-control input-sm" style="width:120px;text-align:center;">
				<option value="">:: 월 선택 ::</option>
<?
for($i=0,$j=1; $i<12; $i++,$j++) {
	$jj = sprintf("%02d", $j);
	$selected = ($jj==$month) ? 'selected' : '';
	echo "<option value='".$jj."' $selected>".$jj."월</option>\n";
}
?>
			</select>
		</li>
		<li>
			<select name="target_day" id="target_day" onChange="fSubmitX();" class="form-control input-sm" style="width:120px;text-align:center;">
				<option value="">:: 일 선택 ::</option>
<?
for($i=0,$j=1; $i<31; $i++,$j++) {
	$jj = sprintf("%02d", $j);
	$selected = ($jj==$day) ? 'selected' : '';
	echo "<option value='".$jj."' $selected>".$jj."일</option>\n";
}
?>
			</select>
		</li>
		<li><button type="button" onClick="fSubmitX()" class="btn btn-sm btn-primary">조회</button></li>
	</ul>

	<table border='1' class='table-hover' style='font-size:10pt'>
		<tr align='center'>
			<th rowspan="2" style="width:9%;background:#F8F8EF">키워드</th>
			<th colspan="10" style="width:91%;background:#F8F8EF">레퍼러 내역</th>
		</tr>
		<tr align='center'>
			<th style="width:9.1%;background:#F8F8EF">사이트</th>
			<th style="width:9.1%;background:#F8F8EF">카테고리</th>
			<th style="width:9.1%;background:#F8F8EF">접속</th>
			<th style="width:9.1%;background:#F8F8EF">접속점유율</th>
			<th style="width:9.1%;background:#F8F8EF">가입</th>
			<th style="width:9.1%;background:#F8F8EF">가입전환률</th>
			<th style="width:9.1%;background:#F8F8EF">가입점유율</th>
			<th style="width:9.1%;background:#F8F8EF">투자자수</th>
			<th style="width:9.1%;background:#F8F8EF">투자전환율</th>
			<th style="width:9.1%;background:#F8F8EF">누적투자액</th>
		</tr>
		<tr align='center' style="background:#FFDDDD;color:red">
			<td style='border-right:1px solid #ccc'>전체합계</td>
			<td colspan='2'></td>
			<td align='right'><?=number_format($TOTAL['view'])?></td>
			<td></td>
			<td align='right'><?=number_format($TOTAL['join_count'])?></td>
			<td align='right'><?=$TOTAL['joinPerc']?>%</td>
			<td></td>
			<td align='right'><?=number_format($TOTAL['investor'])?></td>
			<td align='right'><?=$TOTAL['ivstTransPerc']?>%</td>
			<td align='right'><?=number_format($TOTAL['invest_amount'])?></td>
		</tr>
<?

for($i=0, $j=1; $i<$loop_count; $i++,$j++) {

	$loop_count2 = count($LIST[$i]['detail']);
	$sClass = ($loop_count2 > 1) ? "table-striped" : "";

?>
		<tr align='center'>
			<td style="font-size:9pt;border-right:1px solid #ccc;border-bottom:1px solid #ccc"><?=$LIST[$i]['keyword']?></td>
			<td colspan="10" style="padding:0;border-bottom:1px solid #ccc" onClick="<?=$detailRankLoadTag?>"><table class="<?=$sClass?>" style="width:100%;margin:0;border:0;">
					<colgroup>
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
					</colgroup>
<?
	if( $loop_count2 ) {
		for($x=0,$y=1; $x<$loop_count2; $x++,$y++) {

			$LIST[$i]['detail'][$x]['viewSgPerc'] = floatRtrim(sprintf('%.2f', @(($LIST[$i]['detail'][$x]['view'] / $LIST[$i]['view']) * 100) ));
			$LIST[$i]['detail'][$x]['joinSgPerc'] = floatRtrim(sprintf('%.2f', @(($LIST[$i]['detail'][$x]['join_count'] / $LIST[$i]['join_count']) * 100) ));

			$divId = "rank".$i."_".$x;

			$fcolor1 = ($LIST[$i]['detail'][$x]['join_count'] > 0) ? "" : "#ccc";
			$fcolor2 = ($LIST[$i]['detail'][$x]['joinPerc'] > 0) ? "" : "#ccc";
			$fcolor3 = ($LIST[$i]['detail'][$x]['joinSgPerc'] > 0) ? "" : "#ccc";
			$fcolor4 = ($LIST[$i]['detail'][$x]['investor'] > 0) ? "" : "#ccc";
			$fcolor5 = ($LIST[$i]['detail'][$x]['ivstTransPerc'] > 0) ? "" : "#ccc";

			echo "					<tr id='rank'".$i."' align='right'>
						<td style='border:0;padding:1px 6px;text-align:center;line-height:18px;overflow:hidden;'><a href='".$LIST[$i]['detail'][$x]['referer']."' style='color:#000' target='_blank'>".$LIST[$i]['detail'][$x]['site_id']."</a></td>
						<td style='border:0;padding:1px 6px;text-align:center;'>".$LIST[$i]['detail'][$x]['site_ca']."</td>
						<td style='border:0;padding:1px 6px;'>".number_format($LIST[$i]['detail'][$x]['view'])."</td>
						<td style='border:0;padding:1px 6px;'>".$LIST[$i]['detail'][$x]['viewSgPerc']."%</td>
						<td style='border:0;padding:1px 6px;color:$fcolor1'>".number_format($LIST[$i]['detail'][$x]['join_count'])."</td>
						<td style='border:0;padding:1px 6px;color:$fcolor2'>".$LIST[$i]['detail'][$x]['joinPerc']."%</td>
						<td style='border:0;padding:1px 6px;color:$fcolor3'>".$LIST[$i]['detail'][$x]['joinSgPerc']."%</td>
						<td style='border:0;padding:1px 6px;color:$fcolor4'>".number_format($LIST[$i]['detail'][$x]['investor'])."</td>
						<td style='border:0;padding:1px 6px;color:$fcolor5'>".$LIST[$i]['detail'][$x]['ivstTransPerc']."%</td>
						<td style='border:0;padding:1px 6px;color:$fcolor4'>".number_format($LIST[$i]['detail'][$x]['invest_amount'])."</td>
					</tr>\n";

			$print_keyword = $LIST[$i]['keyword'];

			$print_siteJoinPerc = round( @((int)$LIST[$i]['detail'][$x]['join_count'] / (int)$LIST[$i]['join_count'] * 100) );

			$divId = $print_siteId = $print_siteJoinPerc = NULL;

		}

		if($loop_count2 > 1) {

			echo "					<tr align='right' style='background:#EDEDED;'>
						<td style='border:0;padding:1px 6px;text-align:center;line-height:18px;overflow:hidden;'>합계</td>
						<td style='border:0;padding:1px 6px;text-align:center;line-height:18px;overflow:hidden;'></td>
						<td style='border:0;padding:1px 6px;'>".number_format($LIST[$i]['view'])."</td>
						<td style='border:0;padding:1px 6px;'></td>
						<td style='border:0;padding:1px 6px;'>".number_format($LIST[$i]['join_count'])."</td>
						<td style='border:0;padding:1px 6px;'>".$LIST[$i]['joinPerc']."%</td>
						<td style='border:0;padding:1px 6px;'></td>
						<td style='border:0;padding:1px 6px;'>".number_format($LIST[$i]['investor'])."</td>
						<td style='border:0;padding:1px 6px;'>".$LIST[$i]['ivstTransPerc']."%</td>
						<td style='border:0;padding:1px 6px;'>".number_format($LIST[$i]['invest_amount'])."</td>
					</tr>\n";

		}

	}
?>
				</table>
			</td>
		</tr>
<?
}
?>
	</table>
</div>

<script>
function fSubmitX() {
	var target_date = $('#target_year').val();
	if($('#target_month').val()!='') target_date += "-" + $('#target_month').val();
	if($('#target_day').val()!='')   target_date += "-" + $('#target_day').val();

	$.ajax({
		url : "./ajax.keyword_rank.php",
		type: 'POST',
		data: {target_date:target_date},
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

/*
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
*/

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