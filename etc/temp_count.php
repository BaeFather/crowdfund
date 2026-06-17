<?
//월별 카운트

include_once("_common.php");

$target_month = '2016-08';

$x = true;
$i=0;
$j=1;
while($x > 0) {

	$DATE[$i] = date('Y-m', strtotime('+'.$i.' month', strtotime($target_month)));

	// 방문자 수
	$temp = sql_fetch("SELECT SUM(vs_count) AS vs_count FROM ".$g5['visit_sum_table']." WHERE LEFT(vs_date,7)='".$DATE[$i]."'");
	$LIST[$i]['view_count'] = $temp['vs_count'];

	// 직접 방문자 수 (referer가 없는 경우)
	$temp = sql_fetch("SELECT COUNT(*) AS direct_view_count FROM ".$g5['visit_table']." WHERE LEFT(vi_date,7)='".$DATE[$i]."' AND vi_referer=''");
	$LIST[$i]['direct_view_count'] = $temp['direct_view_count'];

	// 가입자 수 (mb_datetime으로 확인)
	$temp = sql_fetch("SELECT COUNT(*) AS join_count FROM `".$g5['member_table']."` WHERE LEFT(mb_datetime,7)='".$DATE[$i]."'");
	$LIST[$i]['join_count'] = $temp['join_count'];

	if($DATE[$i] < date('Y-m')) {
		$i++;
		$j++;
	}
	else {
		break;
	}

}

?>
<table border="1">
	<tr>
		<th>Date</th>
		<th>전체방문</th>
		<th>직접방문</th>
		<th>가입</th>
	</tr>
<?
for($i=0; $i<count($DATE); $i++) {
?>
	<tr>
		<td align="center"><?=$DATE[$i]?></td>
		<td align="right"><?=number_format($LIST[$i]['view_count'])?></td>
		<td align="right"><?=number_format($LIST[$i]['direct_view_count'])?></td>
		<td align="right"><?=number_format($LIST[$i]['join_count'])?></td>
	</tr>
<?
}
?>
</table>