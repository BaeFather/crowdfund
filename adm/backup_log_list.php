<?php
include_once('./_common.php');

$g5['title'] = '백업로그';

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
if ($srch_bu_code) $srch1 = " AND bu_code='$srch_bu_code' ";
if ($srch_ymd) $srch2 = " AND backup_file_datetime LIKE '$srch_ymd%' ";
$sql = "SELECT * FROM cf_backup_file_check WHERE 1 $srch1 $srch2 ORDER BY backup_file_datetime DESC LIMIT 50";
$res = sql_query($sql);
$cnt = $res->num_rows;
$lcnt = $cnt;

$ymd = date("Y-m-d");
if (!$rpt_date) $rpt_date = date("Y-m-d");
?>
<div class="tbl_head02 tbl_wrap">

	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">
		<form method="post" name="f_srch">
		<select name="srch_bu_code" onchange="go_sumbmit();" class="form-control input-sm" style="display:inline;width:200px;">
			<option value="">전체</option>
			<option value="EH00DB" <?=$srch_bu_code=="EH00DB"?"selected":""?> >중요테이블 시간별 백업</option>
			<option value="ED06PG" <?=$srch_bu_code=="ED06PG"?"selected":""?> >WEB 일자별 백업 06시</option>
			<option value="ED06DB" <?=$srch_bu_code=="ED06DB"?"selected":""?> >DB 일자별 백업 06시</option>
			<option value="ED00DB" <?=$srch_bu_code=="ED00DB"?"selected":""?> >DB 일자별 백업 00시</option>
		</select>
		<input type="text" name="srch_ymd" placeholder="2020-11-19" class="form-control input-sm" style="display:inline;width:100px;" value="<?=$srch_ymd?>">

		<button type="button" class="btn btn-sm btn-success" onClick="go_report1();" style="margin-left:50px;">보고서(시간별)</button>
		<input type="text" name="rpt_date" placeholder="2020-11-19" class="form-control input-sm" style="display:inline;width:85px;" value="<?=$rpt_date?>">
		<button type="button" class="btn btn-sm btn-success" onClick="go_report2();" >보고서(일자별)</button>
		</form>
	</div>

	<table class="table table-striped table-bordered table-hover" style="min-width:1000px; padding-top:0; font-size:12px;">
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;">No</th>
			<th scope="col" style="text-align:center;border:1px solid green;">구분</th>
			<th scope="col" style="text-align:center;border:1px solid green;">대상</th>
			<th scope="col" style="text-align:center;border:1px solid green;">파일명</th>
			<th scope="col" style="text-align:center;border:1px solid green;">백업시각</th>
			<th scope="col" style="text-align:center;border:1px solid green;">백업파일용량</th>
			<th scope="col" style="text-align:center;border:1px solid green;">백업파일위치</th>
		</tr>
<?
for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	?>
		<tr>
			<td style="text-align:center;border:1px solid green;"><?=$lcnt--?></td>
			<td style="text-align:center;border:1px solid green;font-weight:bold;"><?=$row["backup_gubun"]?></td>
			<td style="text-align:center;border:1px solid green;font-weight:bold;"><?=$row["src_gubun"]?></td>
			<td style="text-align:center;border:1px solid green;font-weight:bold;"><?=$row["backup_file_name"]?></td>
			<td style="text-align:center;border:1px solid green;font-weight:bold;"><?=$row["backup_file_datetime"]?></td>
			<td style="text-align:center;border:1px solid green;font-weight:bold;"><?=$row["backup_file_size2"]?></td>
			<td style="text-align:center;border:1px solid green;font-weight:bold;"><?=$row["backup_file_location"]?> <?=$row["backup_file_path"]?></td>
		</tr>
	<?
}
?>
	</table>
</div>

<script>
function go_sumbmit() {
	var f = document.f_srch;
	f.submit();
}
function go_report1() {
	var f = document.f_srch;
	var w_soo2 = window.open('backup_log_report1.html?ymd='+f.rpt_date.value,'BOGO1','width=1200px,height=800px,scrollbars=yes,resizable=yes');
}

function go_report2() {
	var f = document.f_srch;
	var w_soo3 = window.open('backup_log_report2.html?ymd='+f.rpt_date.value,'BOGO2','width=1200px,height=800px,scrollbars=yes,resizable=yes');
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>