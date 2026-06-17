<?php
$sub_menu = "940100";
include_once('./_common.php');
auth_check($auth[$sub_menu], "w");

$g5['title'] = '중앙기록관리 - 상품리스트';

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

?>
<?
if ($only_hello_end=="Y") {
	$wh1 = " AND (A.state IN ('2','3','4','5','6','7','8','9')) ";
}

if ($add_p2p_end=="Y") {
} else {
	$wh2 = " AND (B.p2pctr_end='N') ";
}

?>
<style>
.pagi33 {
  display: inline-block;
}

.pagi33 a {
  color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
}

.pagi33 a.active {
  background-color: #4CAF50;
  color: white;
}

.pagi33 a:hover:not(.active) {background-color: #ddd;}
</style>
<div style="width:95%; margin:5px auto 10px; text-align:right;">
	<form method="POST" name="srch_form" action="<?=$_SERVER['PHP_SELF']?>">

	<input type="checkbox" name="only_hello_end" id="only_hello_end" value="Y" <?=$only_hello_end=="Y"?"checked":""?> >
	<label class="checkbox-inline" for="only_hello_end" style="padding-left:5px;">내부종료된상품만</label>

	<input type="checkbox" name="add_p2p_end" id="add_p2p_end" value="Y" <?=$add_p2p_end=="Y"?"checked":""?> style="margin-left:20px;">
	<label class="checkbox-inline" for="add_p2p_end" style="padding-left:5px;">중앙기록관리 종료포함</label>

	<button type="submit" class="btn btn-sm btn-warning" onclick="go_srch();" style="margin-left:15px;">검색</button>
	</form>
</div>
<div style="height:560px;">
<table id="dataList" class="table table-striped table-bordered table-hover" style="font-size:12px;width:95%;margin:auto;">
	<tr>
		<th class="text-center" style="background:#F8F8EF">NO.</th>
		<th class="text-center" style="background:#F8F8EF">품번</th>
		<th class="text-center" style="background:#F8F8EF">상품명</th>
		<th class="text-center" style="background:#F8F8EF">모집금액</th>
		<th class="text-center" style="background:#F8F8EF">투자자수</th>
		<th class="text-center" style="background:#F8F8EF">대출기간</th>
		<th class="text-center" style="background:#F8F8EF">상태</th>
		<th class="text-center" style="background:#F8F8EF">중앙기록관리</th>
		<th class="text-center" style="background:#F8F8EF">대출신청</th>
		<th class="text-center" style="background:#F8F8EF">상품모집</th>
		<th class="text-center" style="background:#F8F8EF">투자신청</th>
		<th class="text-center" style="background:#F8F8EF">투자계약</th>
		<th class="text-center" style="background:#F8F8EF">상환기록</th>
		<th class="text-center" style="background:#F8F8EF">원리금지급</th>
		<th class="text-center" style="background:#F8F8EF">대출종료</th>
		<th class="text-center" style="background:#F8F8EF">투자종료</th>
	</tr>
<?
$tot_sql = "SELECT COUNT(A.idx) ccnt
			  FROM cf_product A
			  LEFT JOIN p2pctr_product B ON(A.idx=B.product_idx)
			 WHERE A.loan_register_id<>'' $wh1 $wh2";
$tot_row = sql_fetch($tot_sql);
$total_count = $tot_row['ccnt'];
$no = $total_count ;

$rows = 15;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT A.idx, A.title, A.recruit_amount, A.state, A.loan_start_date, A.loan_end_date,
			   B.loan_register_id, B.loan_register_status, B.loan_register_datetime,
			   B.goods_id, B.goods_status, B.goods_update_datetime,
			   B.loan_contract_id, B.loan_status, B.loan_contract_datetime,
			   B.p2pctr_end
          FROM cf_product A
	 LEFT JOIN p2pctr_product B ON(A.idx=B.product_idx)
		 WHERE A.loan_register_id<>''
			   $wh1
			   $wh2
		   AND A.recruit_amount>=10000
	  ORDER BY A.idx desc LIMIT {$from_record}, {$rows}";
$res = sql_query($sql);
//echo "<pre>"; echo $sql; echo "</pre>";
$cnt = $res->num_rows;
?>
<?
for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	$inv_reg_sql = "SELECT count(idx) ccnt, sum(amount) camt FROM cf_product_invest WHERE product_idx='".$row['idx']."' AND invest_state='Y' AND investment_register_id<>''";
	$inv_reg_row = sql_fetch($inv_reg_sql);
	$inv_reg_cnt = $inv_reg_row["ccnt"]; $inv_reg_amt = $inv_reg_row["camt"];

	$inv_cont_sql = "SELECT count(idx) ccnt, sum(amount) camt FROM cf_product_invest WHERE product_idx='".$row['idx']."' AND contract_id<>''";
	$inv_cont_row = sql_fetch($inv_cont_sql);
	$inv_cont_cnt = $inv_cont_row["ccnt"]; $inv_cont_amt = $inv_cont_row["camt"];

	if ($row["state"] == "1") $prd_stat="상환중";
	else if ($row["state"] == "2") $prd_stat="상환완료";
	else if ($row["state"] == "5") $prd_stat="중도상환";
	else $prd_stat=$row["state"];

	?>
	<tr>
		<td style="text-align:center;"><?=$no--?></td>
		<td style="text-align:center;"><?=$row["idx"]?></td>
		<td style="text-align:left;"><?=$row["title"]?></td>
		<td style="text-align:right;"><?=number_format($row["recruit_amount"])?></td>
		<td style="text-align:right;"><?=number_format($inv_reg_cnt)?></td>
		<td style="text-align:center;"><?=$row["loan_start_date"]?>~<?=$row["loan_end_date"]?></td>
		<td style="text-align:center;"><?=$prd_stat?></td>
		<td style="text-align:center;">
			<a onclick="go_p2pctr(<?=$row['idx']?>)" class="btn btn-sm btn-<?=$row['p2pctr_end']=='Y'?'default':'warning';?>" style="margin-top:0px;display:inline;">중앙기록관리</a>
		</td>
		<td style="text-align:center;"><?=substr($row["loan_register_id"],11,6)?>-<?=substr($row["loan_register_id"],-10)*1?></td>
		<td style="text-align:center;"><?=substr($row["loan_contract_id"],11,6)?>-<?=substr($row["loan_contract_id"],-10)*1?></td>
		<td style="text-align:right;"><?=number_format($inv_reg_amt)?></td>
		<td style="text-align:right;"><?=number_format($inv_cont_amt)?></td>
		<td style="text-align:center;"></td>
		<td style="text-align:center;"></td>
		<td style="text-align:center;"></td>
		<td style="text-align:center;"><?=$row["p2pctr_end"]=="Y"?"종료":""?></td>
	</tr>
	<?
}
?>
</table>
</div>
		<div style="width: 100%; text-align: center;">
			<ul class="pagi33">
<?
$qstr = "only_hello_end=$only_hello_end&amp;add_p2p_end=$add_p2p_end";
echo get_paging321(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page=');
?>
			</ul>
		</div>

<script>
function go_srch() {
	var f = document.srch_form;
	f.submit();
}
</script>

<? include_once ('../admin.tail.php'); ?>

<?
// 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL
function get_paging321($write_pages, $cur_page, $total_page, $url, $add="")
{
	//$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
	$url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

	$str = '';
	if($cur_page > 1) {
		$str .= '<a href="'.$url.'1'.$add.'" ><<</a>'.PHP_EOL;
	}

	$start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
	$end_page = $start_page + $write_pages - 1;

	if($end_page >= $total_page) $end_page = $total_page;

	if($start_page > 1) $str .= '<a href="'.$url.($start_page-1).$add.'" ><</a>'.PHP_EOL;

	if($total_page > 1) {
		for($k=$start_page;$k<=$end_page;$k++) {
			if($cur_page != $k)
				$str .= '<a href="'.$url.$k.$add.'">'.$k.'</a>'.PHP_EOL;
			else
				$str .= '<a class="active">'.$k.'</a>'.PHP_EOL;
		}
	}

	if($total_page > $end_page) $str .= '<a href="'.$url.($end_page+1).$add.'" >></a>'.PHP_EOL;

	if($cur_page < $total_page) {
		$str .= '<a href="'.$url.$total_page.$add.'">>></a>'.PHP_EOL;
	}

	if($str)
		return "<div class=\"pg_wrap\"><span class=\"pg\">{$str}</span></div>";
	else
		return "";
}
?>