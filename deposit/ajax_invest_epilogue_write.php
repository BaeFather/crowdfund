<?
include_once("_common.php");

while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

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
			cf_invest_users_epilogue
		SET
			member_idx  = '".$member['mb_no']."',
			product_idx = '$product_idx',
			invest_idx  = '$invest_idx',
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
			alert('응답하라 투자후기 이벤트에 참여하셨습니다.\\n투자후기는 각 상품별 작성이 가능합니다.\\n많은 참여 부탁드립니다.');
			top.location.reload();
			</script>\n";
	}

}
else if($mode=="edit") {
	$sql = "
		UPDATE
			cf_invest_users_epilogue
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

	$sql = "
		SELECT
			A.amount, A.member_idx, A.product_idx, B.title
		FROM
			cf_product_invest A,
			cf_product B
		WHERE
			1=1
			AND A.product_idx = B.idx
			AND A.idx='$invest_idx'";
	$INVEST = sql_fetch($sql);


	$sql  = "SELECT idx, display, subject, text1, text2, text3, text4, text5, text6, rdate, last_edit_date FROM cf_invest_users_epilogue WHERE member_idx='".$INVEST['member_idx']."' AND product_idx='".$INVEST['product_idx']."' AND invest_idx='".$invest_idx."'";
	$DATA = sql_fetch($sql);

?>

<div class="title">투자후기</div>
<img src="../images/btn_close.gif" alt="close" class="close" />
<div style="height:10px"></div>

<div class="type03" style="margin:0 10px 10px 10px;">
	<form id="frm_epilogue" name="frm_epilogue" method="post" action="<?=$_SERVER['PHP_SELF']?>" target="axFrame" onSubmit="return formCheck(this);" style="margin:0">
		<input type="hidden" name="mode"        value="<?=($DATA['idx'])?'edit':'new'?>">
		<input type="hidden" name="idx"         value="<?=$DATA['idx']?>">
		<input type="hidden" name="product_idx" value="<?=$INVEST['product_idx']?>">
		<input type="hidden" name="invest_idx"  value="<?=$invest_idx?>">
	<table>
		<colgroup>
			<col style="width:<?=(G5_IS_MOBILE)?'10':'15';?>%">
			<col style="width:<?=(G5_IS_MOBILE)?'90':'85';?>%">
		</colgroup>
		<tr>
		  <th style="text-align:center">투자상품</th>
		  <td style="text-align:left"><?=$INVEST['title']?></td>
		</tr>
	  <tr>
		  <th style="text-align:center">투자금</th>
		  <td style="text-align:left"><?=number_format($INVEST['amount'])?>원</td>
		</tr>
	  <tr>
		  <th style="text-align:center">제목</th>
		  <td style="text-align:left"><input type="text" name="subject" id="subject" value="<?=stripSlashes($DATA['subject'])?>" class="text" style="width:96%;"></td>
		</tr>
	  <tr>
		  <td colspan="2" style="margin:0; padding:10px 0 10px 0">
				<div style="width:100%;height:<?=(G5_IS_MOBILE)?'300':'500';?>px;overflow-x:hidden;overflow-y:scroll;">
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
							<td><textarea name="text6" id="text6" style="margin:0;width:100%;height:60px;border:0;"><?=stripSlashes($DATA['text6'])?></textarea></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<div style="margin-top:10px; text-align:center">
	  <input type="submit" value="확인" id="submit_button" class="btn_blue">
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

	if(!subjectVal)    { alert('제목을 입력하십시요.');  return false; f.subject.focus(); }
	else if(!text1Val) { alert('필수 입력 항목입니다.'); return false; f.text1.focus(); }
	else if(!text2Val) { alert('필수 입력 항목입니다.'); return false; f.text2.focus(); }
	else if(!text3Val) { alert('필수 입력 항목입니다.'); return false; f.text3.focus(); }
	else if(!text4Val) { alert('필수 입력 항목입니다.'); return false; f.text4.focus(); }
	else if(!text5Val) { alert('필수 입력 항목입니다.'); return false; f.text5.focus(); }
	else if(!text6Val) { alert('필수 입력 항목입니다.'); return false; f.text6.focus(); }
	else {
		if(!confirm('등록 하시겠습니까?')) {
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