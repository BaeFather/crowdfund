<?
################################################################################
## g5_member-g5_point 간 예치금 싱크
## 기준테이블 g5_point. 싱크변경테이블 g5_member
## 0 8,23 * * * php -q /home/crowdfund/public_html/adm/auto_point_sync.php
################################################################################

set_time_limit(0);

$base_path = "/home/crowdfund/public_html";
include_once($base_path."/common.cli.php");


$res  = sql_query("SELECT mb_no, mb_id, mb_point FROM g5_member WHERE mb_level='1' ORDER BY mb_datetime, mb_no");
$rows = $res->num_rows;

for($i=0,$j=1; $i<$rows; $i++,$j++) {

	$list = sql_fetch_array($res);

	// 포인트 체크 (실시간 추출된 포인트와 셋팅된 포인트가 다르면 포인트정보 업데이트 후 출력)

	$sum_point = get_point_sum($list['mb_id']);
	$sum_point = ($sum_point) ? $sum_point : 0;

	if($list['mb_point'] <> $sum_point) {
		//echo $list['mb_no']." : ".$list['mb_id']." : ".$list['mb_point']." : ".$sum_point."\n";
		$sql = "UPDATE g5_member SET mb_point = '$sum_point' WHERE mb_id='{$list['mb_id']}'";
		sql_query($sql);
	}

}

sql_free_result($res);
sql_close();
exit;

?>