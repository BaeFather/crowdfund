<?php
include_once('./_common.php');

$g5['title'] = '헬로 시세';

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
if (!$sido) $sido="11";

if (date('D')=="Fri") $last_friday = date('Y.m.d');
else $last_friday = date('Y.m.d',strtotime('last friday'));

?>

<div class="tbl_head02 tbl_wrap">

	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">

	<form method="post" name="f_srch">
		<select name="sido" onchange="go_submit();">
			<option value="11" <?=$sido=="11"?"selected":""?> >서울특별시</option>
			<option value="41" <?=$sido=="41"?"selected":""?> >경기도</option>
			<option value="28" <?=$sido=="28"?"selected":""?> >인천광역시</option>
			<option value="36" <?=$sido=="36"?"selected":""?> >세종특별자치시</option>
			<option value="30" <?=$sido=="30"?"selected":""?> >대전광역시</option>
			<option value="27" <?=$sido=="27"?"selected":""?> >대구광역시</option>
			<option value="29" <?=$sido=="29"?"selected":""?> >광주광역시</option>
			<option value="26" <?=$sido=="26"?"selected":""?> >부산광역시</option>
			<option value="31" <?=$sido=="31"?"selected":""?> >울산광역시</option>
		</select>
	</form>
	</div>

	<script>
	function go_submit() {
		var f = document.f_srch;
		f.submit();
	}
	</script>

	<table class="table table-striped table-bordered table-hover" style="min-width:1000px; padding-top:0; font-size:12px;">
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;">No</th>
			<th scope="col" style="text-align:center;border:1px solid green;">지역</th>
			<th scope="col" style="text-align:center;border:1px solid green;">지역코드</th>
			<th scope="col" style="text-align:center;border:1px solid green;">지역명</th>
			<th scope="col" style="text-align:center;border:1px solid green;">기준물건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">물건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">기준일</th>
		</tr>
<?
$sql = "SELECT A.* FROM scrap_kbss_code A WHERE A.d1_code='$sido' AND A.d_name<>'' ORDER BY A.d2_code,A.d_name";
//$sql = "SELECT A.*, (SELECT kijun FROM scrap_kbss B WHERE A.d_code=B.d_code8 order by input_datetime desc limit 1) nkijun FROM scrap_kbss_code A WHERE A.d_name<>'' ORDER BY A.d2_code,A.d_name";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

$bgcolor="";

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);


	//$sql2 = "SELECT B.* FROM scrap_kbss B WHERE substring($row[d_code],1,8)=substring(B.d_code,1,8) order by input_datetime desc limit 1";
	$sql2 = "SELECT uid,COUNT(idx) cout, kijun FROM scrap_kbss B WHERE '$row[d_code]'=B.d_code8 GROUP BY uid ORDER BY uid DESC LIMIT 1";
	//if ($row['d_code']=="41150113") echo $sql2;
	$res2 = sql_query($sql2);
	$row2 = sql_fetch_array($res2);

	if ($row["d2_code"]<>$tmp_d2_code) {
		if ($bgcolor=="white") $bgcolor="#FDEBD0";
		else $bgcolor = "white";
		$tmp_d2_code=$row["d2_code"];
		$sector_cnt=1;
	} else {
		$sector_cnt++;
	}

	?>
		<tr style="background-color:<?=$bgcolor?>;" onmouseover="this.style.background='#F1F1F1';" onmouseleave="this.style.background='<?=$bgcolor?>';">
			<td scope="col" style="text-align:center;border:1px solid green; " ><?=$sector_cnt?></td>
			<td scope="col" style="text-align:center;border:1px solid green; " ><?=$row['d1_name']?> <?=$row['d2_name']?></td>
			<td scope="col" style="text-align:center;border:1px solid green; "><?=$row['d_code']?></td>
			<td scope="col" style="text-align:center;border:1px solid green; " ><?=$row['d_name']?></td>
			<td scope="col" style="text-align:center;border:1px solid green; " >
				<?=($row['apt_cnt']<$row2['cout'])?"<font color=red>":"<font>"?><?=number_format($row['apt_cnt'])?></font></td>
			<td scope="col" style="text-align:center;border:1px solid green; " >
				<?=($row['apt_cnt']>$row2['cout'])?"<font color=red>":"<font>"?><?=$row2['cout']?number_format($row2['cout']):''?></font></td>
			<td scope="col" style="text-align:center;border:1px solid green; " >
				<?=$last_friday<>$row2['kijun']?"<font color=red>":""?><?=$row2['kijun']?></font></td>
		</tr>
	<?
}
?>

	</table>
</div>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>