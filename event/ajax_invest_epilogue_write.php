<?
include_once("_common.php");

while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

/////////////////////////////
// 이자 상환 이력 조회
/////////////////////////////
$sql = "
	SELECT
		A.member_idx,
		COUNT(A.idx) AS invest_count,
		SUM(B.invest_amount) AS repay_amount_sum
	FROM
		cf_product_invest A,
		cf_product_give B
	WHERE 1
		AND A.invest_state='Y'
		AND A.idx=B.invest_idx
		AND A.member_idx='".$member['mb_no']."'";
$r = sql_fetch($sql);
if(!$r['repay_amount_sum']) {
	echo "unqualified_request";
	exit;
}
/////////////////////////////


if(preg_match("/new|edit/", $mode)) {
	$subject = addSlashes($subject);
	$text1 = addSlashes($text1);
	$text2 = addSlashes($text2);
	$text3 = addSlashes($text3);
	$text4 = addSlashes($text4);
	$text5 = addSlashes($text5);
	$text6 = addSlashes($text6);
}

if($mode=="new") {

	$sql = "
		INSERT INTO
			invest_users_epilogue
		SET
			member_idx  = '".$member['mb_no']."',
			subject = '$subject',
			text1 = '$text1',
			text2 = '$text2',
			text3 = '$text3',
			text4 = '$text4',
			text5 = '$text5',
			text6 = '$text6',
			ip = '".$_SERVER['REMOTE_ADDR']."',
			rdate = NOW()";
	if(sql_query($sql)) {
		echo "
			<script>
			alert('응답하라 투자후기 이벤트에 참여 하셨습니다.');
			top.location.reload();
			</script>\n";
	}

}
else if($mode=="edit") {
	$sql = "
		UPDATE
			invest_users_epilogue
		SET
			subject = '$subject',
			text1 = '$text1',
			text2 = '$text2',
			text3 = '$text3',
			text4 = '$text4',
			text5 = '$text5',
			text6 = '$text6',
			ip = '".$_SERVER['REMOTE_ADDR']."',
			last_edit_date = NOW()
		WHERE
			idx = '$idx'";
	if(sql_query($sql)) {
		echo "
			<script>
			alert('내용이 수정 되었습니다.');
			top.location.reload();
			</script>\n";
	}

}
else {

	$sql  = "SELECT idx, display, subject, text1, text2, text3, text4, text5, text6, rdate, last_edit_date FROM invest_users_epilogue WHERE idx='$idx'";
	$DATA = sql_fetch($sql);

?>

<div class="title">투자후기</div>
<img src="/images/btn_close.gif" alt="close" class="close" />
<div style="height:10px"></div>

<div class="type03" style="margin:0 10px 10px 10px; height:<?=(G5_IS_MOBILE)?'320':'700';?>px;overflow-x:hidden;overflow-y:scroll;">
	<form id="frm_epilogue" name="frm_epilogue" method="post" action="<?=$_SERVER['PHP_SELF']?>" target="axFrame" onSubmit="return formCheck(this);" style="margin:0">
		<input type="hidden" name="mode"        value="<?=($DATA['idx'])?'edit':'new'?>">
		<input type="hidden" name="idx"         value="<?=$idx?>">
	<table>
		<colgroup>
			<col style="width:<?=(G5_IS_MOBILE)?'10':'15';?>%">
			<col style="width:<?=(G5_IS_MOBILE)?'90':'85';?>%">
		</colgroup>
	  <tr>
		  <th style="text-align:center">제목</th>
		  <td style="text-align:left"><input type="text" name="subject" id="subject" value="<?=stripSlashes($DATA['subject'])?>" class="text" style="width:96%;height:22px;"></td>
		</tr>
		<tr>
		  <td colspan="2" style="margin:0; padding:10px 0 10px 0">
					<table style="border:1px solid #ccc; width:99%">
						<tr>
							<td style="text-align:left;background-color:#FDFECB;color:#284893">1. 헬로펀딩은 어떻게 알게 되셨나요?</td>
						</tr>
						<tr>
							<td><textarea name="text1" id="text1" style="margin:0;width:100%;height:60px;border:0;"><?=stripSlashes($DATA['text1'])?></textarea></td>
						</tr>
					</table>
					<div style="height:8px;"></div>
					<table style="border:1px solid #ccc; width:99%">
						<tr>
							<td style="text-align:left;background-color:#FDFECB;color:#284893">2. 투자포인트는 무엇인가요?</td>
						</tr>
						<tr>
							<td><textarea name="text2" id="text2" style="margin:0;width:100%;height:60px;border:0;"><?=stripSlashes($DATA['text2'])?></textarea></td>
						</tr>
					</table>
					<div style="height:8px;"></div>
					<table style="border:1px solid #ccc; width:99%">
						<tr>
							<td style="text-align:left;background-color:#FDFECB;color:#284893">3. 현재 헬로펀딩에서 안전투자를 위한 투자자보호제도 (1. 사내투자심의위원회, 2. 법무법인, 감정평가법인 등 외부전문가의 권리분석, 3. 채권매입계약)가 있다는 것을 알고계시나요?</td>
						</tr>
						<tr>
							<td><textarea name="text3" id="text3" style="margin:0;width:100%;height:60px;border:0;"><?=stripSlashes($DATA['text3'])?></textarea></td>
						</tr>
					</table>
					<div style="height:8px;"></div>
					<table style="border:1px solid #ccc; width:99%">
						<tr>
							<td style="text-align:left;background-color:#FDFECB;color:#284893">4. 헬로펀딩에서 지급받은 수익금은 어떻게 활용하고 있나요?</td>
						</tr>
						<tr>
							<td><textarea name="text4" id="text4" style="margin:0;width:100%;height:60px;border:0;"><?=stripSlashes($DATA['text4'])?></textarea></td>
						</tr>
					</table>
					<div style="height:8px;"></div>
					<table style="border:1px solid #ccc; width:99%">
						<tr>
							<td style="text-align:left;background-color:#FDFECB;color:#284893">5. 헬로펀딩과 타 업체와의 차이점은 무엇이라고 생각하시나요?</td>
						</tr>
						<tr>
							<td><textarea name="text5" id="text5" style="margin:0;width:100%;height:60px;border:0;"><?=stripSlashes($DATA['text5'])?></textarea></td>
						</tr>
					</table>
					<div style="height:8px;"></div>
					<table style="border:1px solid #ccc; width:99%">
						<tr>
							<td style="text-align:left;background-color:#FDFECB;color:#284893">6. 헬로펀딩에 하고 싶은 말</td>
						</tr>
						<tr>
							<td style="border:0;"><textarea name="text6" id="text6" style="margin:0;width:100%;height:60px;border:0;"><?=stripSlashes($DATA['text6'])?></textarea></td>
						</tr>
					</table>
			</td>
		</tr>
	</table>
	<div style="margin-top:10px;<?=(G5_IS_MOBILE)?'padding-bottom:130px':''?>; text-align:center">
	  <input type="submit" value="<?=($DATA['idx'])?'수정':'등록'?>하기" id="submit_button" class="btn_blue">
	</div>
	</form>
</div>

<script type="text/javascript">
function formCheck(f){
<? if($DATA['display']=="" || $DATA['display']=='N') { ?>
	var subjectVal = trim(f.subject.value);
	var text1Val = trim(f.text1.value);
	var text2Val = trim(f.text2.value);
	var text3Val = trim(f.text3.value);
	var text4Val = trim(f.text4.value);
	var text5Val = trim(f.text5.value);
	var text6Val = trim(f.text6.value);

	if(!subjectVal)    { alert('제목을 입력하십시요.');  f.subject.focus(); return false; }
	else if(!text1Val) { alert('필수 입력 항목입니다.'); f.text1.focus(); return false; }
	else if(!text2Val) { alert('필수 입력 항목입니다.'); f.text2.focus(); return false; }
	else if(!text3Val) { alert('필수 입력 항목입니다.'); f.text3.focus(); return false; }
	else if(!text4Val) { alert('필수 입력 항목입니다.'); f.text4.focus(); return false; }
	else if(!text5Val) { alert('필수 입력 항목입니다.'); f.text5.focus(); return false; }
	else if(!text6Val) { alert('필수 입력 항목입니다.'); f.text6.focus(); return false; }
	else {
		if(!confirm('<?=($DATA['idx'])?'수정':'등록'?> 하시겠습니까?')) {
			return false;
		}
	}
<? } else { ?>
	return false;
<? } ?>
}
</script>
<?
}
?>