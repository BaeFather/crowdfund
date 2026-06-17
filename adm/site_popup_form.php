<?php
include_once('./_common.php');
$sub_menu = "300780";
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

$g5['title'] = "팝업 생성";
include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql = "select * from $g5[site_popup_table] where no=$no";
$result = sql_query($sql);
$view = sql_fetch_array($result);
//$content = stripslashes($view[content]);
$content = $view[content];
$reg_date = str_replace("-","",$view[reg_date]);
$end_date = str_replace("-","",$view[end_date]);
$gigan = $view[gigan];
$check_use = $view[check_use];

?>


<style type="text/css">
	input[type=file]#img-upload {display: none;}
	.pop-upload-btn {padding: 6px 17px; background-color: #337ab7; border-radius: 5px; color: #fff; cursor: pointer; font-weight: 500; font-size: 12px; line-height: 16px; margin-bottom: 0;}
</style>


<form name="form1" method="post" action="./site_popup_update.php" enctype="multipart/form-data" autocomplete="off">

	<input type="hidden" name="token" value='<?=$token?>'>
	<input type="hidden" name="mode" value='<?=$mode?>'>
	<input type="hidden" name="no" value='<?=$no?>'>
	<input type="hidden" name="content" value='<?=$content?>'>

	<div class="tbl_frm01 tbl_wrap">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<colgroup width="100" class="col1 pad1 bold right">
			<colgroup class="col2 pad2">
			<tr class="ht">
				<td><b>제목</b></td>
				<td><input type="text" name="title" style="width:100%" value='<?=$view[title]?>' class="frm_input"></td>
			</tr>
			<tr class="ht">
				<td><b>팝업형식</b></td>
				<td>
				<input type="radio" name="type" value="레이어" <?if(!$view[type] || $view[type]=='레이어') echo"checked";?>> 레이어형식
				&nbsp;&nbsp;
				<input type="radio" name="type" value="팝업창" disabled <?if($view[type]=='팝업창') echo"checked";?>> 새창형식
				</td>
			</tr>
			<tr class="ht">
				<td rowspan='2'><b>팝업이미지</b></td>
				
				<td>
					<input type="text" id="fileName" class="frm_input" readonly />
					<label for="img-upload" class="pop-upload-btn">업로드</label>
					<input type="file" name="content" id="img-upload" value="" />
				</td>
				
				<!--------------------------------------------------------------------------------
				<td colspan="2" valign="top" bgcolor="#fff">
					<span id="submenu_0" style="position:relative;left:0px;top:0px;">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td align="center" style="border-bottom: 1px solid #fff; border-top: 1px solid #fff;"><?php echo editor_html("content", get_text($content, 0)); ?></td>
							</tr>
						</table>
					</span>
				</td>
				---------------------------------------------------------------------------------->
			</tr>
			
			<tr>
				<td>
					<? if($view['content']) { ?>
					<img src="/data/popup_img/<?=$view['content']?>" alt="<?=$view['origin_content']?>" />
					<? } else { ?>
					등록된 이미지가 없습니다.
					<? } ?>
				</td>
			</tr>
			
			<tr class="ht">
				<td><b>적용조건</b></td>
				<td>&nbsp;&nbsp;
				<select name="level" size=1 >
					<option value="0" <?if(!$view[level]||$view[level]=='0')echo "selected";?> >전체</option>
					<option value="1" <?if($view[level]=='1')echo "selected";?> >1 Level</option>
					<option value="2" <?if($view[level]=='2')echo "selected";?> >2 Level</option>
					<option value="3" <?if($view[level]=='3')echo "selected";?> >3 Level</option>
					<option value="4" <?if($view[level]=='4')echo "selected";?> >4 Level</option>
					<option value="5" <?if($view[level]=='5')echo "selected";?> >5 Level</option>
					<option value="6" <?if($view[level]=='6')echo "selected";?> >6 Level</option>
					<option value="7" <?if($view[level]=='7')echo "selected";?> >7 Level</option>
					<option value="8" <?if($view[level]=='8')echo "selected";?> >8 Level</option>
					<option value="9" <?if($view[level]=='9')echo "selected";?> >9 Level</option>
					<option value="10" <?if($view[level]=='10')echo "selected";?> >10 Level</option>
				</select>&nbsp;회원에게 적용
				</td>
			</tr>
			<tr class="ht">
				<td><b>시작날짜</b></td>
				<td>&nbsp;
					<input class="frm_input required" type="text" id="reg_date" name='reg_date' size="8" maxlength="8" value='<?=$reg_date?>'>
					&nbsp;
					<span style="color:#FF8000;">[미입력시 현재날짜로 설정됨]</span>
				</td>
			</tr>
			<tr class="ht">
				<td><b>종료날짜</b></td>
				<td>&nbsp;
					<input class="frm_input required" type="text" id="end_date" name='end_date' size="8" maxlength="8" value='<?=$end_date?>'>
					&nbsp;
					<span style="color:#FF8000;">[미입력시 현재날짜의 다음날로 설정됨]</span>
				</td>
			</tr>
			<tr class="ht">
				<td><b>노출여부</b></td>
				<td>&nbsp;
					<input type="radio" name="check_use" value="Y" <?if(!$check_use||$check_use=="Y") echo "checked";?>> 노출&nbsp;
					<input type="radio" name="check_use" value="N" <?if($check_use=="N") echo"checked";?>> 미노출&nbsp;
				</td>
			</tr>
		</table>
	</div>

	<div class="btn_confirm01 btn_confirm">
		<input type="button" value="등록" class="btn_submit" onclick="javascript:check_submit();" >&nbsp;&nbsp;
		<a href="javascript:;" onclick="javascript:history.go(-1);" class="btn_cancel">취소</a>
	</div>

</form>


<script language="javascript">

function selectMenu(name) {
	if(name == 0) {
		submenu_0.style.display = '';
		submenu_1.style.display = 'none';
	} else if(name == 1) {
		submenu_0.style.display = 'none';
		submenu_1.style.display = '';
	}
}

function check_submit() {

  <? //echo get_editor_js("content"); ?>

	var form = document.form1;

	if(!form.title.value) {
		alert("제목을 입력하세요!");
		form.title.focus();
		return;
	}

	form.target = "_self";
	form.action = "./site_popup_form_update.php";
	form.submit();
}

$(function(){
	
	$("#reg_date, #end_date").datepicker({
		changeMonth: true, 
		changeYear: true, 
		dateFormat: "yymmdd", 
		showButtonPanel: true
	});

});

function linkUrl() {

	var str = '<?=$content?>';

	// HTMLDocument 객체가 반환되며, 이 또한 Document 객체이기도 함.
	var parser = new DOMParser();
	var temp = parser.parseFromString(str, "text/html");  
	
	// 컨텐츠에 저장된 html 안에서 a태그 찾아서 href 속성 값을 가지고 옴.
	var a_tag = temp.getElementsByTagName('a');
	var link_url = $(a_tag).attr('href');  

	$("#linkURL").val(link_url);

} linkUrl();


$("#img-upload").on('change',function(){
	var fileName = $(this).val().split('/').pop().split('\\').pop();
	$("#fileName").val(fileName);
});

</script>

<?php
include_once("./admin.tail.php");
?>

