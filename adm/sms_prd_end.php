<?
include_once('./_common.php');
include_once(G5_LIB_PATH.'/sms.lib.php');

if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

if (!function_exists('unit_sms_send_smtnt')) die("sms.lib.php not found");

while(list($key, $value) = each($_REQUEST)) { if(!is_array(${$key})) ${$key} = trim($value); }

if (!$prd_idx) die("상품 번오 오류");

include_once('admin.head.nomenu.php');
?>
<?
$from_hp = $CONF['admin_sms_number'];
$send_id = get_sms_send_id_smtnt();

$sqlp = "SELECT * FROM cf_product WHERE idx='$prd_idx'";
$resp = sql_query($sqlp);
$rowp = sql_fetch_array($resp);

if ($rowp["state"]=="5") $sgubun = "중도";
else $sgubun = "";

$sql = "
	SELECT
		A.member_idx ,B.mb_name, B.mb_hp
	FROM
		cf_product_invest A
	LEFT JOIN
		g5_member B ON(A.member_idx=B.mb_no)
	WHERE 1
		AND A.product_idx='$prd_idx'
		AND A.invest_state='Y'
	ORDER BY
		B.mb_name";
$res = sql_query($sql);
$cnt = $res->num_rows;
//$cnt = 1;

$msg = "[헬로펀딩]
회원님이 투자하신 $rowp[start_num]호 상품이 ".$sgubun."상환되었습니다.
감사합니다.";
?>

<script>
function go_send_sms() {
	var yn = confirm("작업중입니다. 문자를 발송하시겠습니까?");
	if (yn) {
		var f = document.f;
		f.exe_mode.value = "Y";
		f.submit();
	}
}
</script>

<div style="width:100%">
	<div class="panel-body">

		<h1 style="min-width:100px; text-align:center;">상환 완료 문자 발송</h1>

		<div><pre><?=$msg?></pre></div>

		<div style="padding:10px 0;text-align:right;">
		<form method="POST" name="f">
		<input type="hidden" name="prd_idx" value="<?=$prd_idx?>" />
		<input type="hidden" name="exe_mode" value="" />
		총 <?=number_format($cnt)?> 명
		<input type="button" class="btn btn-sm btn-primary" style="" onclick="go_send_sms();" value="발송" />
		</form>
		</div>

<table class="table table-striped table-bordered table-hover" style="font-size:13px;">
	<tr>
		<th>No</th>
		<th>회원번호</th>
		<th>이름</th>
		<th>전화번호</th>
	</tr>
<?
$num = $cnt;

/*카카오톡 알림톡 추가*/
$tcode = "hello008";
$KaKao_Message_Send = new KaKao_Message_Send();
/*카카오톡 알림톡 추가*/

for($i=0; $i<$cnt; $i++) {

	$row = sql_fetch_array($res);
	$hp = masterDecrypt($row['mb_hp']);
	//$hp = "01086246176";
	//$hp = "01088944740";
	//echo "$row[member_idx] $row[mb_name] 010-****-".substr($hp,-4)."<br/>";

	if ($exe_mode=="Y") {

		//if( $sms_res = unit_sms_send_smtnt($from_hp, $hp, $msg, "", $send_id) ) $send_proc_count++;
		/*카카오톡 알림톡 추가*/
		$member["mb_no"]		=	$row["member_idx"];
		$member["mb_name"]	=	$row["mb_name"];
		$member["mb_hp"]		=	$hp;

		$KaKao_Message_Send->PRODUCT_NUMBER = $rowp["start_num"];
		$KaKao_Message_Send->MEMBER = $member;	// common.lib member 환경변수
		$KaKao_Message_Send->kakao_insert($tcode);
		$send_proc_count++;
		/*카카오톡 알림톡 추가*/
	}
?>
	<tr>
		<td style="text-align:center;"><?=$num--?></td>
		<td style="text-align:center;"><?=$row["member_idx"]?></td>
		<td style="text-align:center;"><?=$row["mb_name"]?></td>
		<td style="text-align:center;"><?=$hp?></td>
	</tr>
<?
}
?>
</table>

	</div>
</div>

<?
if ($send_proc_count) {

	$res = sql_query("SELECT * FROM cf_product_sms WHERE product_idx='$prd_idx'");
	$cnt = sql_num_rows($res);

	if ($cnt) {
		$row = sql_fetch_array($res);
		$up_sql = "UPDATE cf_product_sms SET sms_end='Y' , sms_end_date=NOW() where idx='$row[idx]'";
	} else {
		$up_sql = "INSERT INTO cf_product_sms SET product_idx='$prd_idx', sms_end='Y' , sms_end_date=NOW()";
	}
	sql_query($up_sql);
	?>
	<script>
	opener.location.reload();
	self.close();
	</script>
	<?
}
?>
<script>
<? if ($send_proc_count) { ?>
alert("<?=number_format($send_proc_count)?>건의 문자가 발송되었습니다.");
<? } ?>
</script>
