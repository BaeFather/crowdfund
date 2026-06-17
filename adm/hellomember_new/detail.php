<style>
input.radioarea {float:left;margin-top:7px;margin-left:10px;}
.selectarea {width:180px;padding:5px 0;}
label {float:left;display:block;padding:5px 5px;}
.fred {color:#ff0000;}
</style>
	<script type="text/javascript" src="hellomember.js" /></script>

	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<form name="regfm" id="regfm" autocomplete="off" encType="multipart/form-data">
	<input type="hidden" name="kind" value="<?php ECHO $strKind;?>" />
	<input type="hidden" name="SE" value="<?php ECHO $idx;?>" />
	<input type="hidden" name="ST" value="<?php ECHO $ST;?>" />
	<input type="hidden" name="S2" value="<?php ECHO $S2;?>" />
	<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
	<input type="hidden" name="page" value="<?php ECHO $page;?>" />
	<input type="hidden" name="idor" value="<?php ECHO $hid;?>" />
	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="15%">
			<col width="35%">
			<col width="15%">
			<col width="35%">
		</colgroup>
		<tr>
			<th scope="col">업체명</th>
			<td colspan="3"><?php ECHO INPUT_FORM($strInputText1,"cname","","","required itemname='업체명' style='width:80%'",$cname);?></td>
		</tr>
		<tr>
			<th scope="col">아이디</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"hid","","","required itemname='아이디' OnBlur=\"hellloan_id_check(this.value,event);\"",$hid);?><span id="idtext"></div></td>
			<th scope="col">비밀번호</th>
			<td><?php ECHO INPUT_FORM($strPassword,"hpasswd","","","","");?></td>
		</tr>
		<tr>
			<th scope="col">사업자등록번호</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"co_number1","","","",$co_number1);?></td>
			<th scope="col">법인등록번호</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"co_number2","","","",$co_number2);?></td>
		</tr>
		<tr>
			<th scope="col">대표자명</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"rname","","","",$rname);?></td>
			<th scope="col">대표자연락처</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"rphone","","","",$rphone);?></td>
		</tr>
		<tr>
			<th scope="col">담당자명</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"hname","","","",$hname);?></td>
			<th scope="col">연락처</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"hphone","","","",$hphone);?></td>
		</tr>
		<tr>
			<th scope="col">로그인구분</th>
			<td><?php ECHO fn_general_select($recyn,$strRadioText,fn_hmember_recyn(),"","recyn","class='radioarea' required itemname='로그인구분'","");?></th>
			<th scope="col">사업자등록증</th>
			<td><? INPUT_FORM("file","i_file[]","input02","/data/hellomember","",$ifile);?></td>
		</tr>
	</table>

	<?php IF($RD == "2") { ?>
	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>&RD=3&idx=<?php ECHO $idx;?>';" class="btn btn-default">수정하기</button>
		&nbsp;&nbsp;
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>';" class="btn btn-default">목록보기</button>
	</div>
	<?php } ?>
	<?php IF($RD == "3") { ?>
	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="check_w_form('regfm',event);return false;" class="btn btn-default"><?php ECHO $strBtnTxt;?></button>
		&nbsp;&nbsp;
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>';" class="btn btn-default">목록보기</button>
	</div>
	<?php } ?>
	</form>