<?

include_once("syndication_config.php");
include_once('head.php');

if(!$is_admin && !$_SESSION['syndi_admin_login']) {
	msg_go('로그인 하십시요.', './');
}

$pid = $_CONF['SYNDI_ID'];


$g5['title'] = '관리자 > 가입.투자 통계';

foreach($_REQUEST as $k=>$v) {
	$$_REQUEST[$k] = $v;
}


$start_date = (trim($start_date)) ? $start_date : date('Y-m')."-01";
$end_date   = (trim($end_date))   ? $end_date : date("Y-m-d");

$sql_search = " 1=1 ";
$sql_search.= " AND A.pid='$pid' ";

if($start_date || $end_date) {
	if($start_date) $sql_search.= " AND LEFT(A.mb_datetime, 10) >= '$start_date'";
	if($end_date)   $sql_search.= " AND LEFT(A.mb_datetime, 10) <= '$end_date'";
}

$sql = "
	SELECT
		LEFT(A.mb_datetime,10) AS rdate,
		COUNT(mb_no) AS join_cnt
	FROM
		g5_member A
	WHERE
		$sql_search
	GROUP BY
		rdate
	ORDER BY
		rdate DESC";
//echo "<pre>".$sql."</pre>";//die();

$result = sql_query($sql);
$rcount = sql_num_rows($result);
$LIST = array();

for($i=0; $i<$rcount; $i++) {

	$ROW = sql_fetch_array($result);

	$LIST[$i]['rdate'] = $ROW['rdate'];
	$LIST[$i]['join_cnt'] = $ROW['join_cnt'];

	$TOTAL['join_cnt'] += $ROW['join_cnt'];

}

if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
	//echo "<pre>".$sql."</pre>";
}

$list_count = count($LIST);

?>

		<link href="/adm/css/bootstrap.min.css" rel="stylesheet">
		<style>
		.tblX { width:100%; border:1px solid #ccc }
		.tblX th,
		.tblX td { padding:8px; border-left:1px solid #ccc; border-bottom:1px solid #ccc }
		.btn_blue_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#284893; border:0; vertical-align:middle; cursor:pointer; }
		.btn_black_s { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#000000; border:0; vertical-align:middle; cursor:pointer; }
		.btn_gray_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#777; border-radius:3px; background-color:#CCCCCC; border:0; vertical-align:middle; cursor:pointer; }
		.btn_red     { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#FF6633; border:0; vertical-align:middle; cursor:pointer; }
		.btn_red:hover, .btn_green:active { color:#fff; background-color:#FF2222; }
		.btn_gray_s2  { display:inline-block; padding:0 10px; line-height:18px; text-align:center; font-family:'NG'; font-size:11px; color:#fff; border-radius:3px; background-color:#888; border:0; vertical-align:middle; cursor:pointer; }
		span.left  { float:left; }
		span.right { float:right; }
		.new {padding:0 6px 2px 6px; font-size:8pt; color:#fff; border:0px; background-color:red; border-radius:10px; margin:0 4px;}
		</style>

		<div id="content" style="position:absolute;">
			<div class="content investment" style="width:98%;margin:-50px auto;">

				<ul class="tab_type03" style="margin:0">
					<li data-gubun="tab2" onClick="location.href='member_status.php'">가입자 현황</li>
					<li data-gubun="tab3" class="on">가입 통계</li>
					<li data-gubun="tab4" style="float:right;text-align:right;border:0;background:#FFF;"><button type="button" class="btn_gray" onClick="location.href='./'">로그아웃</button></li>
				</ul>

				<div class="tabArea" style="display:block;padding:30px;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">

					<div style="margin-bottom:10px;">
						<form id="member_list_frm" method="get">
							가입일 <input type="text" class="frm_input datepicker"  name="start_date" value="<?=$start_date;?>" readonly style="margin-left:10px;width:100px"> ~
							<input type="text" class="frm_input datepicker" name="end_date" value="<?=$end_date;?>" readonly style="width:100px;">
							<button type="submit" class="btn_blue">검 색</button>
						</span>
						</form>
					</div>

					<table class="tblX">
						<thead>
							<tr style="background-color:#EFEFEF">
								<th scope="col" style="width:20%;text-align:center;">Date</th>
								<th scope="col" style="width:20%;text-align:center;">가입자수</th>
							</tr>
						</thead>
						<tbody>
<?
if($list_count > 0) {
?>
							<tr bgcolor="#FFDDDD">
								<td style="text-align:center;color:brown;">합계</td>
								<td style="text-align:right;color:brown;"><?=number_format($TOTAL['join_cnt'])?> 건</td>
							</tr>
<?
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {
?>
							<tr bgcolor="<?=$tr_bgcolor?>">
								<td style="text-align:center;"><?=$LIST[$i]['rdate']?></td>
								<td style="text-align:right;"><?=number_format($LIST[$i]['join_cnt'])?> 건</td>
							</tr>
<?
	}
}
else {
?>
							<tr>
								<td colspan="2" style="text-align:center">데이터가 없습니다.</td>
							</tr>
<?
}
?>
						</tbody>
					</table>

				</div>

			</div>
		</div>

<?
include_once('./tail.php');
?>

<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: "yy-mm-dd",
		monthNames: [ "1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월" ],
		dayNamesShort: [ "일", "월", "화", "수", "목", "금", "토" ]
	});
});
</script>