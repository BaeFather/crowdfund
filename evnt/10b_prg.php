<?
$today_add_prize = 0; // 오늘의 누적 상금액
$total_add_price = 0; // 총 지금 누적 상금액

$i_sql = "select * from cf_event_10bM where stat='I'";
$i_res = sql_query($i_sql);
$i_check = sql_num_rows($i_res);
if ($i_check) {
	$i_row = sql_fetch_array($i_res);
	$today_add_prize = $i_row["prize"];
}

$a_sql = "select * from cf_event_10bM order by ymd";
$a_res = sql_query($a_sql);
$a_cnt = sql_num_rows($a_res);
$this_turn = 1000;
for ($i=0 ; $i<$a_cnt ; $i++) {
	$a_row = sql_fetch_array($a_res);
	$marray[$i] = $a_row;
	$turn[$i] = $a_row['ymd'];
	$turn2[$i] = $a_row['product_name'];
	if ($a_row['stat']=="I") $this_turn = $i;
}
if ($this_turn==1000) $this_turn = $i;

$all_nujuk_sql = "select sum(A.prize) tot_price from cf_event_10bM A where A.prize_cnt>0";
$all_nujuk_res = sql_query($all_nujuk_sql);
$all_nujuk_row = sql_fetch_array($all_nujuk_res);
$total_add_price = $all_nujuk_row["tot_price"];

$my_sql = "select * from cf_event_10bS where ymd='$i_row[ymd]' and mb_no='$member[mb_no]'";
$my_res = sql_query($my_sql);
$my_cnt = sql_num_rows($my_res);
if ($my_cnt) $my_row = sql_fetch_array($my_res);

if ($_GET['sort']=="money") $ob = "order by answer asc";
else if ($_GET['sort']=="id") $ob = "order by mb_id asc";
else $ob = "order by idx desc";
$status_apply = "select * from cf_event_10bS where ymd='$i_row[ymd]' $ob";
$status_res = sql_query($status_apply);
$status_cnt = sql_num_rows($status_res);
if ($status_cnt) {
	for ($i=0 ; $i<$status_cnt ; $i++) {
		$status_row = sql_fetch_array($status_res);
		$LIST_STATUS[$i] = $status_row;
	}
}
?>
<script type="text/javascript">
var mArray = <?php echo json_encode($marray); ?>;
var this_turn = <?=$this_turn?$this_turn:0?>;
var max_turn  = <?=$this_turn?$this_turn:0?>;
</script>