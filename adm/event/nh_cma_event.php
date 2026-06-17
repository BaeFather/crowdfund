<?

include_once('./_common.php');

$sub_menu = "900800";
auth_check($auth[$sub_menu], 'w');

while( list($k, $v)=each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


$where = " 1=1";

$sql = "
	SELECT
		COUNT(A.idx) AS cnt
	FROM
		cf_event_nhCMA A
	LEFT JOIN
		g5_member B  ON A.mb_no=B.mb_no
	WHERE
		$where";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 5000;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;

$num = $total_count - $from_record;

$sql = "
	SELECT
		A.*,
		B.member_type, B.mb_id, B.mb_name, B.mb_co_name, B.mb_hp, B.mb_email, B.bank_name, B.account_num, B.virtual_account2, B.va_private_name2
	FROM
		cf_event_nhCMA A
	LEFT JOIN
		g5_member B  ON A.mb_no=B.mb_no
	WHERE
		$where
	ORDER BY
		A.idx DESC
	LIMIT
		$from_record, $rows";
//echo "<pre>".$sql."</pre>";
$res = sql_query($sql);
$rcount = $res->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($res);
	$LIST[$i]['mb_hp'] = ($LIST[$i]['mb_hp']) ? masterDecrypt($LIST[$i]['mb_hp'],false) : '';
}


if ($mode=="excel") {
	header( "Content-type: application/vnd.ms-excel;" );
	header( "Content-Disposition: attachment; filename=event_nh_cma.xls" );
	header( "Content-description: PHP5 Generated Data" );
}
else {

	$g5['title'] = $menu["menu900"][10][1];

	include_once (G5_ADMIN_PATH . '/admin.head.php');

}

?>
<?
if ($mode<>"excel") {
?>
<style>
.btn-mini { padding:0;width:25px;height:25px;line-height:24px; border-radius:20px; }
.new_mark { display:inline-block; font-size:8pt; padding:0 2px; line-height:12px;color:#fff; background:red; border-radius:3px; }

div.td { margin:0; width:100%;height:100%;line-height:100%;text-align:center; }
</style>
<?
}
?>
<div class="row" style="width:100%;">
	<div class="col-lg-12">
		<div class="panel-body">
			<form method="get" name="ff">
			<input type="hidden" name="mode" value="">
			<div style="text-align:right;margin-bottom:10px;">
				<button class="btn btn-danger" type="button" onclick="jigup();" style="width:100px;">지급</button>
				<button class="btn btn-sm btn-default" type="button" onclick="excl();" style="width:100px;margin-left:10px;">엑셀</button>
			</div>
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover" style="font-size:12px">
					<thead>
						<tr bgcolor="#F8F8EF">
							<th class="text-center">NO</th>
							<th class="text-center">선택</th>
							<th class="text-center">회원번호</th>
							<th class="text-center">이름</th>
							<th class="text-center">아이디</th>
							<th class="text-center">연락처</th>
							<th class="text-center">응모일</th>
							<!--th class="text-center">환급은행</th-->
							<!--th class="text-center">환급계좌</th-->
							<th class="text-center">NH CMA 계좌</th>
							<th class="text-center">가상계좌</th>
							<th class="text-center">예금주</th>
							<th class="text-center">개인번호</th>
						</tr>
<?
$jcnt = count($LIST);
for($i=0,$j=1; $i<count($LIST);$i++) {
	//$mb = get_member($LIST[$i]['mb_id']);
	if ($LIST[$i]['account_num']) $account_num =  masterDecrypt($LIST[$i]['account_num'], false);
	else $account_num="";

	$jumin = "";
	$jumin = ($_SESSION['ss_accounting_admin']) ? getJumin($LIST[$i]["mb_no"]) : "";


	//if ($i==0) $imsi_m = substr($LIST[$i]["insert_datetime"],0,7);
	if ($imsi_m<>substr($LIST[$i]["insert_datetime"],0,7)) {
		if ($bgc=="#CCE8FF") $bgc="#FFFFFF";
		else $bgc="#CCE8FF";
		$imsi_m = substr($LIST[$i]["insert_datetime"],0,7);

		$chk_sql = "select count(*) cnt from cf_event_nhCMA where jigup='Y' and substring(insert_datetime,1,7) = substring('".$LIST[$i]["insert_datetime"]."',1,7)";
		$chk_res = sql_query($chk_sql);
		$chk_row = sql_fetch_array($chk_res);
		if ($chk_row['cnt']>0) $ji_end = "Y";
		else $ji_end = "N";
	}
	//else $bgc="#FFFFFF";
	?>
						<tr align="center" style="background-color:<?=$bgc?>" onmouseover="this.style.background='#fcecae';" onmouseleave="this.style.background='<?=$bgc?>';">
							<td><?=$jcnt--?></td>
							<td>
							<?
							if ($LIST[$i]["jigup"]=="Y") {
								echo "지급완료";
							} else {
								if ($mode<>"excel") {
								?>
								<input type="checkbox" name="evnt_idx[<?=$i?>]" value="<?=$LIST[$i]['idx']?>" <?=$ji_end=="Y"?"disabled":""?> >
								<?
								}
							}
							?>
							</td>
							<td><?=$LIST[$i]["mb_no"]?></td>
							<td><?=$LIST[$i]["mb_name"]?></td>
							<td><?=$LIST[$i]["mb_id"]?></td>
							<td style="mso-number-format:'\@';"><?=$LIST[$i]["mb_hp"]?></td>
							<td><?=$LIST[$i]["insert_datetime"]?></td>
							<!--td><?=$LIST[$i]["bank_name"]?></td-->
							<!--td><?=$account_num?></td-->
							<td style="mso-number-format:'\@';"><?=$LIST[$i]["cma_num"]?></td>
							<td style="mso-number-format:'\@';"><?=$LIST[$i]["virtual_account2"]?></td>
							<td><?=$LIST[$i]["va_private_name2"]?></td>
							<td style="mso-number-format:'\@';">
							<?
							if ($mode=="excel") echo $jumin;
							else {
								?>
								<?=$jumin?substr($jumin,0,6)."*******":""?>
								<?
							}
							?>
							</td>
						</tr>
	<?
}
?>
					</thead>
				</table>
			</div>
			</form>
		</div><!-- /.panel-body -->
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<script>
function jigup() {
	var f = document.ff;
	f.action = "nh_cma_event_apply.php";
	var opn = window.open("","nwnw","width=400px,height=400px,scrollbars=yes,resizable=yes")
	f.target = "nwnw";
	f.submit();

	f.action = "";
	f.target = "";
}
function excl() {
	var f = document.ff;
	ff.mode.value="excel";
	f.target = "_blank";
	f.submit();
	f.action = "";
	f.target = "";
}
</script>
