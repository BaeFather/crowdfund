<?

$sub_menu = '300720';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

// 값에 따른 변수 생성
while(list($key, $value) = each($_REQUEST)) {
	if($_FILES) continue;
	if(!is_array($_REQUEST)) ${$key} = clean_xss_tage(trim($value));
}

$row = array();
if (isset($id) && $id) {
	$mode_txt = ' 수정';
	$mode_type = 'edit';

	$sql = " SELECT * FROM media_video_list WHERE id = '{$_GET['id']}' ";
	$row = sql_fetch($sql);

} else {											// 등록모드
	$mode_txt = ' 등록';
	$mode_type = 'insert';
}

$html_title = "헬로비디오";
$g5['title'] = $html_title . $mode_txt;

include_once(G5_ADMIN_PATH . '/admin.head.php');

?>
<form name="frmcontentform" action="./video_update.php" onsubmit="return chk_frm(this);" method="post" enctype="MULTIPART/FORM-DATA">
	<input type="hidden" name="mode" value="<? echo $mode_type; ?>"/>
	<input type="hidden" name="page" value="<? echo $page; ?>"/>
	<input type="hidden" name="id" value="<? echo $id; ?>"/>

	<div class="tbl_frm01 tbl_wrap">
		<table>
			<caption><? echo $g5['title']; ?></caption>
			<colgroup>
				<col class="grid_4">
				<col>
			</colgroup>
			<tbody>
			<tr>
				<th scope="row"><label for="subject">제목</label></th>
				<td>
					<input type="text" class="frm_input" name="subject" id="subject" size="100" maxlength="105" value="<? echo $row['subject']; ?>" placeholder="제목을 입력해주세요."/>
				</td>
			</tr>
			<? if($mode_type == "edit" && $row["sort"] >= 0) {?>
				<tr>
					<th scope="row"><label for="sort">순서</label></th>
					<td>
						<input type="text" class="frm_input" name="sort" id="sort" size="1" maxlength="3" value="<? echo $row['sort']; ?>" onKeyUp="onlyDigit(this);"/>
					</td>
				</tr>
				<tr>
					<th scope="row">비디오</th>
					<td>
						<? if($row["video_link"]) { ?>
						<iframe src="<? echo $row["video_link"];?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
						<? } ?>
					</td>
				</tr>
			<? } ?>
			<tr>
				<th scope="row"><label for="show_date">노출여부</label></th>
				<td>
					<input type="radio" name="display_yn" id="displayY" value="y" <? echo ($row["display_yn"] == 'Y') ? "checked" : "";?>/>
					<label for="displayY">예</label>

					<input type="radio" name="display_yn" id="displayN" value="n" <? echo ($row["display_yn"] == 'N') ? "checked" : "";?>/>
					<label for="displayN">아니오</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="news_link">URL</label></th>
				<td>
					<input type="text" class="frm_input" name="target_link" id="target_link" size="80" value="<? echo $row['target_link']; ?>" placeholder="URL 주소를 입력하세요."/><br/>
					*URL 형식이 맞지않으면 등록할 수 없습니다.
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="video_link">비디오 공유 주소</label></th>
				<td>
					<textarea name="video_link" id="video_link"><? echo $row['video_link'];?></textarea><br/>
					*유투브, 네이버, 다음등 iframe 공유주소를 등록하세요.
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="btn_confirm01 btn_confirm">
		<input type="submit" value="<? echo ($mode_type == "insert") ? '등록' : '수정'; ?>" class="btn_submit" accesskey="s"/>
		<input type="button" value="취소" class="btn_cancel" onclick="document.location.href='./video_list.php?page=<? echo $page; ?>';"/>
	</div>
</form>

<? include_once(G5_ADMIN_PATH . '/admin.tail.php'); ?>

<style type="text/css">
	input[type="radio"] {vertical-align: initial;}
</style>
<script type="text/javascript">

	function chk_frm(obj) {

		if (obj.subject.value == '') {
			alert("제목을 입력해주세요.");
			obj.subject.focus();
			return false;
		}else if(obj.subject.length > 105){
			alert("제목은 105자까지 입력가능합니다.");
			obj.subject.focus();
			obj.subject.select();
			return false;
		}

		if (obj.target_link.value == '') {
			alert("URL을 입력해주세요.");
			obj.target_link.focus();
			return false;
		}else if(!isURL(obj.target_link.value)){
			alert("URL 형식이 옳지않습니다.");
			return false;
		}

		var contents = document.getElementById("video_link").value;
		if( contents == ""  || contents == null || contents == '&nbsp;' || contents == '<p>&nbsp;</p>')  {
			alert("비디오 공유 주소를 입력해주세요.");
			return false;
		}

		return true;
	}
</script>