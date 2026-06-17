<?

$sub_menu = '300730';
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

	$sql = " SELECT * FROM media_tv_list WHERE id = '{$_GET['id']}' ";
	$row = sql_fetch($sql);

} else {											// 등록모드
	$mode_txt = ' 등록';
	$mode_type = 'insert';
}

$html_title = "헬로라이브TV";
$g5['title'] = $html_title . $mode_txt;

include_once(G5_ADMIN_PATH . '/admin.head.php');
$img_dir = G5_IMG_PATH . "/live_tv/";
?>
<form name="frmcontentform" action="./tv_update.php" onsubmit="return chk_frm(this);" method="post" enctype="MULTIPART/FORM-DATA">
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
			<? if ($mode_type == 'edit') { ?>
				<tr>
					<td colspan="2">이미지를 교체하시려면 파일선택 하셔서 업로드 해주세요.</td>
				</tr>
			<? } ?>
			<tr>
				<th scope="row">
					<label for="thumbnail">대표이미지 등록</label>
				</th>
				<td>
					<?
					if (isset($row['thumbnail']) && !empty($row['thumbnail']))
					{
						if (file_exists($img_dir . $row['thumbnail'])) {
							$thumbnail = '<img src="' . G5_IMG_URL."/live_tv/".$row['thumbnail'] . '" width="313" height="203" alt="'.$row['thumbnail_origin'].'"/>';
						}
						if ($thumbnail) {
							echo '<div class="banner_or_img">';
							echo $thumbnail;
							echo '<br/><label><input type="checkbox" name="delete_img" value="1"/> 삭제</label>';
							echo '</div>';
						}
					}else{
						?>
						<input type="file" name="thumbnail" id="thumbnail" title="대표이미지를 등록해주세요."/>
						(업로드 이미지 사이즈: 313px x 203px) / *10MB 크기의 이미지까지만 추가가 가능합니다.
						<?
					}
					?>
				</td>
			</tr>
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
							<video poster="<? echo $row["video_link"];?>" controls preload stretching="fill"></video>
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
				<th scope="row"><label for="video_link">스트림 공유 주소</label></th>
				<td>
					<textarea name="video_link" id="video_link"><? echo $row['video_link'];?></textarea><br/>
					*헬로라이브TV 스트림 주소를 입력하세요.
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="btn_confirm01 btn_confirm">
		<input type="submit" value="<? echo ($mode_type == "insert") ? '등록' : '수정'; ?>" class="btn_submit" accesskey="s"/>
		<input type="button" value="취소" class="btn_cancel" onclick="document.location.href='./tv_list.php?page=<? echo $page; ?>';"/>
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

		/*if (obj.target_link.value == '') {
			alert("URL을 입력해주세요.");
			obj.target_link.focus();
			return false;
		}else if(!isURL(obj.target_link.value)){
			alert("URL 형식이 옳지않습니다.");
			return false;
		}*/

		var contents = document.getElementById("video_link").value;
		if( contents == ""  || contents == null || contents == '&nbsp;' || contents == '<p>&nbsp;</p>')  {
			alert("라이브TV 스트림 주소를 입력해주세요.");
			return false;
		}

		return true;
	}
</script>