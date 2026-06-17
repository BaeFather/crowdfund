<?php
$sub_menu = "940200";
include_once('./_common.php');
auth_check($auth[$sub_menu], "w");

$g5['title'] = '중앙기록관리 - 이관';

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

?>
<table id="dataList" class="table table-striped table-bordered table-hover" style="font-size:12px;width:95%;margin:auto;">
	<tr>
		<th class="text-center" style="background:#F8F8EF">NO.</th>
		<th class="text-center" style="background:#F8F8EF">품번</th>
		<th class="text-center" style="background:#F8F8EF">진행상태</th>
		<th class="text-center" style="background:#F8F8EF">상품명</th>
		<th class="text-center" style="background:#F8F8EF">금액</th>
		<th class="text-center" style="background:#F8F8EF">대출기간</th>
		<th class="text-center" style="background:#F8F8EF">차주타입</th>
		<th class="text-center" style="background:#F8F8EF">투자자</th>
		<th class="text-center" style="background:#F8F8EF">중기관</th>
	</tr>
<?
$sql = "SELECT A.*,B.member_type
          FROM cf_product A
	 LEFT JOIN g5_member B ON(B.mb_no=A.loan_mb_no)
		 WHERE (A.loan_start_date>='2021-08-27' OR A.loan_end_date>='2021-08-27')
		   AND A.loan_start_date<='2021-09-12'
		   AND A.recruit_amount>=10000
	  ORDER BY A.loan_start_date desc";
$res = sql_query($sql);
$cnt = sql_num_rows($res);
$no = $cnt;

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);

	$inv_sql = "SELECT COUNT(*) inv_cnt FROM cf_product_invest WHERE product_idx='$row[idx]' AND invest_state='Y'";
	$inv_res = sql_query($inv_sql);
	$inv_row = sql_fetch_array($inv_res);
	$inv_cnt = $inv_row["inv_cnt"];

	$LIST[$i] = $row;
	$pstate = "";

		if($LIST[$i]['state']) {
				if ($LIST[$i]['state'] == '1') {
						$pstate = '이자상환중';
						$pstate_code = '2';
				}
				else if($LIST[$i]['state'] == '2') {
						$pstate = '상품마감<br>(정상상환)';
				}
				else if($LIST[$i]['state'] == '3') {
						$pstate = '투자금<br>모집실패';
						$bgcolor = "#FFDDDD";
				}
				else if($LIST[$i]['state'] == '4') {
						$pstate = '부실';
						$bgcolor = "#FFDDDD";
				}
				else if($LIST[$i]['state'] == '5') {
						$pstate = '상품마감<br><span style="color:blue">(중도상환)</span>';
						$pstate_code = '2';
				}
				else 	if ($LIST[$i]['state'] == '6') {
						$pstate = '대출계약취소<br>(기표전)';
						$pstate_code = '8';
						$bgcolor = "#FFDDDD";
				}
				else 	if ($LIST[$i]['state'] == '7') {
						$pstate = '대출계약취소<br>(기표후)';
						$pstate_code = '9';
						$bgcolor = "#FFDDDD";
				}
		}
		else {
				if($LIST[$i]['open_datetime'] > $date) {
						$pstate = '상품준비중';
				}
				else {
						if($LIST[$i]['invest_end_date'] == '') {
								if($LIST[$i]['end_datetime'] < $date){
										$pstate = '투자금<br>모집실패';
										$bgcolor = "#FFDDDD";
								}
								else {
										$pstate = '대기중';
										$pstate_code = '1';
								}
						}

						if($LIST[$i]['start_datetime'] < $date && $LIST[$i]['end_datetime'] > $date) {
								if($LIST[$i]['recruit_amount'] == $INVEST['amount']) {
										$pstate = '투자금<br>모집완료';
								}
								else {
										$pstate = '투자금<br>모집중';
								}
						}
				}

		}
	?>
	<tr>
		<td><?=$no--?></td>
		<td><?=$row["idx"]?></td>
		<td><?=$row["title"]?></td>
		<td><?=$pstate?></td>
		<td align="right"><?=number_format($row["recruit_amount"])?></td>
		<td align="center"><?=$row["loan_start_date"]?> ~ <?=$row["loan_end_date"]?></td>
		<td align="center"><?=$row["member_type"]=="2"?"사업자":"개인"?></td>
		<td align="right"><?=number_format($inv_cnt)?> 명</td>
		<td align="center">
		<? if ($row["loan_register_id"]) { ?>
			<a onclick="go_p2pctr2(<?=$row['idx']?>)" class="btn btn-sm btn-<?=$row['loan_register_id']?'warning':'default';?>" style="margin-top:4px;"><?=$row["loan_register_id"]?></a>
		<?} else { ?>
			<a onclick="go_p2pctr2(<?=$row['idx']?>)" class="btn btn-sm btn-<?=$row['loan_register_id']?'warning':'default';?>" style="margin-top:4px;">중앙기록관리</a>
		<? } ?>
		</td>
	</tr>
	<?
}
?>
</table>
<script>
function go_p2pctr2(idx) {
	var w_p2pctr = window.open(g5_admin_url+"/p2pctr/main_pop.php?product_idx="+idx , "p2pctr", "left=500px, top=100px, width=500,height=700");
	//var w_p2pctr = window.open("/adm/p2pctr/main_pop_svc.php?product_idx="+idx , "p2pctr", "left=500px, top=100px, width=800,height=700");
}
</script>

<? include_once ('../admin.tail.php'); ?>