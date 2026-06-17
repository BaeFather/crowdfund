<?php
include_once('_common.php');
// 처리페이지

if ($_SERVER["REQUEST_METHOD"]!="GET") { echo "ERROR-DATA"; exit; }
if (!$member["mb_id"]){ echo "ERROR-LOGIN"; exit; }

$strMemberType = $member["member_type"];
$mb_no		   = $member["mb_no"];
$strName	   = "";
$strNumber     = "";
$strTxt1 = "";
$strTxt2 = "";

$strVirtualAccount2 = $member["virtual_account2"];

$strVirtualAccountKind = "";
IF($strVirtualAccount2)
{
	$strVirtualAccountKind = "1";
}

SWITCH($strMemberType)
{
	CASE "1" : //일반
		$strName = $member["mb_name"];
		$strNumber = SUBSTR(STR_REPLACE("-","",$member["mb_birth"]),2,6)."-*******";

		$strTxt1 = "이름";
		$strTxt2 = "주민등록번호";
	BREAK;
	CASE "2" : //사업자
		$strName = $member["mb_co_name"];
		$strNumber = $member["mb_co_reg_num"];

		$strTxt1 = "상호명";
		$strTxt2 = "사업자등록번호";
	BREAK;
}
?>
<link rel="stylesheet" href="./withholding.css?ver=1" />
<script type="text/javascript" src="/js/withholding.js"></script>

<div class="widthholding_title_area">
	<h3>원천징수영수증 신청</h3>
</div>

<form name="regfm" id="regfm">
<input type="hidden" name="member_type" value="<?php ECHO $strMemberType;?>" />
<input type="hidden" name="mb_no" value="<?php ECHO $mb_no;?>" />
<input type="hidden" name="mb_virtualkind" value="<?php ECHO $strVirtualAccountKind;?>" />
<div class="widthholding_title_content">
	<table class="widthholding_table">
	<tr>
		<th class="th1"><?php ECHO $strTxt1;?></th>
		<td><input type="text" name="mb_name" class="input01" placeholder="" readonly value="<?php ECHO $strName;?>" /> </td>
	</tr>
	<tr>
		<th class="th1"><?php ECHO $strTxt2;?></th>
		<td><input type="text" name="mb_jumin" class="input01" placeholder="" readonly value="<?php ECHO $strNumber?>" /></td>
	</tr>
	<tr>
		<th class="th1">신청 년/월</th>
		<td><input type="text" name="s_date" class="input02 dateym" placeholder="시작년월" value="" required itemname="신청 시작년/월" /> 월 ~ <input type="text" name="e_date" class="input02 dateym" placeholder="" value="종료년월" required itemname="신청 종료 년/월" /> 월</td>
	</tr>
	<tr>
		<th class="th1">유형선택</th>
		<td>
			<input type="radio" name="rkind" value="1" required itemname="유형" /><span> 귀속</span> &nbsp;
			<input type="radio" name="rkind" value="2" required itemname="유형" /><span> 지급</span> &nbsp;<span class="hiddenbr"></span>(귀속은 이자 발생 월, 지급은 이자 지급 월을 뜻합니다.)
		</td>
	</tr>
	<tr>
		<th class="th1">이메일</th>
		<td><input type="text" name="mb_email" class="input03" placeholder="이메일" value="<?php ECHO $member["mb_email"]?>" required itemname="이메일" /> (이메일을 수정하시면 수정된 이메일로 발송 됩니다.)</td>
	</tr>
	<tr>
		<th class="th1">메모</th>
		<td>
			<textarea name="content" class="text01"></textarea>
			<br />
			작성된 메모는 담당자에게 전달 됩니다.
		</td>
	</tr>
	</table>

	<div class="widthholding_txt">
		원천징수영수증 발급까진 3~5 영업일 이상 소요 될 수 있습니다.

	</div>
</div>

<div class="widthholding_title_btn">
	<button type="button" name="wbtn" class="btn_big_blue" OnClick="check_w_form('regfm',event);">신청</button>
</div>
</form>

<?

@sql_close();
exit;

?>