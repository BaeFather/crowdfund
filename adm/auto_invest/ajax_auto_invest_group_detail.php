<?
include_once("_common.php");

$idx = trim($_GET['idx']);

$DATA = sql_fetch("SELECT * FROM cf_auto_invest_config WHERE idx='".$idx."'");

if(!$DATA) { echo 'NULL'; exit; }

?>
<style>
#T20180206 { border-collapse:collapse; border:1px solid #000; width:350px;font-size:12px; }
</style>
<table id="T20180206">
	<colgroup>
		<col width="13%">
		<col width="37%">
	</colgroup>
	<tbody>
	<tr align="center">
		<td style="background:#F8F8EF">자동투자그룹</td>
		<td><span style="color:red"><?=$DATA['grp_title']?></span></td>
	</tr>
	<tr align="center">
		<td style="background:#F8F8EF">투자기간</td>
		<td><?=$DATA['min_period_days']?> ~ <?=$DATA['max_period_days']?>일</td>
	</tr>
	<tr align="center">
		<td style="background:#F8F8EF">수익률(연)</td>
		<td><?=$DATA['min_profit']?> ~ <?=$DATA['max_profit']?>%</td>
	</tr>
	<tr align="center">
		<td style="background:#F8F8EF">자동투자<br>가능금액</td>
		<td><?=($DATA['auto_inv_unlimited']=='1') ? '제한없음' : '전체 모집금의 '.$DATA['auto_inv_limit_per'].'%';?></td>
	</tr>
	<tr align="center">
		<td style="background:#F8F8EF">등급별<br>제한금액</td>
		<td style="padding:2px"><table style="width:100%;border-collapse:collapse;font-size:12px;border-color:#CCC;">
				<colgroup>
					<col width="30%">
					<col width="30%">
					<col width="40%">
				</colgroup>
				<tr align="center">
					<td style="background:#DDD;">법인회원</td>
					<td style="background:#DDD;">전체</td>
					<td><?=($DATA['mb2_unlimited']=='1') ? '제한없음' : number_format($DATA['mb2_limit_amt']).'원';?>
				</tr>
				<tr align="center">
					<td rowspan="3" style="background:#DDD;">개인회원</td>
					<td style="background:#DDD;">일반투자자</td>
					<td><?=($DATA['mb11_unlimited']=='1') ? '제한없음' : number_format($DATA['mb11_limit_amt']).'원';?>
				</tr>
				<tr align="center">
					<td style="background:#DDD;">소득적격자</td>
					<td><?=($DATA['mb12_unlimited']=='1') ? '제한없음' : number_format($DATA['mb12_limit_amt']).'원';?>
				</tr>
				<tr align="center">
					<td style="background:#DDD;">전문투자자</td>
					<td><?=($DATA['mb13_unlimited']=='1') ? '제한없음' : number_format($DATA['mb13_limit_amt']).'원';?>
				</tr>
			</table>
		</td>
	</tr>
	<tr align="center">
		<td style="background:#F8F8EF">자동투자<br>우선순위</td>
		<td>
<?
		if($DATA['inv_order']=='1') echo '법인우선';
		else if($DATA['inv_order']=='2') echo '개인우선';
		else if($DATA['inv_order']=='3') echo '고액우선';
		else echo '선착순';
?>
		</td>
	</tr>
</table>
