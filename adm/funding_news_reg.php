<?php
	$sub_menu = '300300';
	include_once('./_common.php');
	include_once(G5_EDITOR_LIB);

	auth_check($auth[$sub_menu], "w");


	if(isset($_GET['idx']) && $_GET['idx']) {		// 수정모드
		$mode_txt = ' 수정';
		$mode_type = 'modi';

		// 메인 배너이미지 정보
		$sql = " select * from funding_news_list where idx = '{$_GET['idx']}' ";
		$row = sql_fetch($sql);
	}else {											// 등록모드
		$mode_txt = ' 등록';
		$mode_type = 'inst';
	}

	$html_title = "헬로펀딩 소식";
	$g5['title'] = $html_title.$mode_txt;

	include_once (G5_ADMIN_PATH.'/admin.head.php');


	$mb_dir = str_replace('','', G5_DATA_PATH."/funding_news/");

?>
<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$("textarea").keyup(function(){
		var numChar = $(this).val().length;
		var maxNum = 255;
		var charRemain = maxNum - numChar;
		$("div > em").text(charRemain);
		if(charRemain < 0){
			$(this).val($(this).val().substring(0, 255));
			$("div > em").text('0');
		}
	});

});
</script>

<form name="frmcontentform" action="./funding_news_update.php" onsubmit="return chk_frm(this);" method="post" enctype="MULTIPART/FORM-DATA" >

	<input type="hidden" name="mode_type" value="<?php echo $mode_type;?>" />
	<input type="hidden" name="page" value="<?php echo $_GET['page'];?>" />
<?php if($mode_type == 'modi') { ?>
	<input type="hidden" name="modi_idx" value="<?php echo $row['idx'];?>" />
	<input type="hidden" name="pre_thumbnail" value="<?php echo $row['thumbnail'];?>" />
	<input type="hidden" name="pre_news_logo" value="<?php echo $row['news_logo'];?>" />
<?php } ?>

	<div class="tbl_frm01 tbl_wrap">
		<table>
			<caption><?php echo $g5['title']; ?></caption>
			<colgroup>
				<col class="grid_4">
				<col>
			</colgroup>
			<tbody>
			<?php if($mode_type == 'modi') { ?>
				<tr>
					<td colspan="2">이미지를 교체하시려면 파일선택 하셔서 업로드 해주세요.</td>
				</tr>
			<?php } ?>
				<tr>
					<th scope="row"><label for="subject">제목</label></th>
					<td>
						<input type="text" class="frm_input" name="subject" id="subject" size="100" value="<?php echo $row['subject'];?>" title="제목을 입력해주세요."/>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="subject">출처</label></th>
					<td>
						<input type="text" class="frm_input" name="press" id="press" size="40" value="<?php echo $row['press'];?>" title="출처를 입력해주세요."/>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="bn_img1">대표이미지 등록</label></th>
					<td>
						<input type="file" name="bn_img1" id="bn_img1" title="대표이미지를 등록해주세요.">
						<? //(업로드 이미지 사이즈: 218px X 143px) ?>
						(업로드 이미지 사이즈: 320px X 180px)
						<?php
							if($mode_type == 'modi') {
								$bimg1 = $row['thumbnail'];
								if (file_exists(G5_PATH.$bimg1)) {
									//$bimg1_str = '<img src="'.$bimg1.'" width="218" height="143" alt="">';
									$bimg1_str = '<img src="'.$bimg1.'" width="320" height="180" alt="">';
								}
								if ($bimg1_str) {
									echo '<div class="banner_or_img">';
									echo $bimg1_str;
									echo '</div>';
								}
							}
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="bn_img2">언론사로고 / 이미지 등록</label></th>
					<td>
						<input type="file" name="bn_img2" id="bn_img2" title="언론사로고 / 이미지를 등록해주세요.">
						(업로드 이미지 사이즈: 165px X 40px)
						<?php
							if($mode_type == 'modi') {
								$bimg2 = $row['news_logo'];
								if (file_exists(G5_PATH.$bimg2)) {
									$bimg2_str = '<img src="'.$bimg2.'" width="165" height="40" alt="">';
								}
								if ($bimg1_str) {
									echo '<div class="banner_or_img">';
									echo $bimg2_str;
									echo '</div>';
								}
							}
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="show_date">표시날짜</label></th>
					<td>
						<input type="text" class="frm_input datepicker" name="show_date" id="show_date" size="10" value="<?php echo $row['show_date'];?>" title="표시날짜를 입력해주세요." />
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="news_link">뉴스링크</label></th>
					<td>
						<input type="text" class="frm_input" name="news_link" id="news_link" size="80" value="<?php echo $row['news_link'];?>" title="뉴스링크를 입력해주세요." />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="contents">요약글 내용</label></th>
					<td>
						<div style="margin-bottom:5px;">※ 총 255자(띄어쓰기 포함) 까지 입력 가능하며, 3줄 초과의 텍스트는 출력되지 않습니다.</div>
						<textarea name="contents" id="contents" rows="3" style="width:800px;" title="요약글 내용을 입력해주세요."><?php echo $row['contents'];?></textarea>
						<div><em>255</em> / 255</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="btn_confirm01 btn_confirm">
		<input type="submit" value="등록" class="btn_submit" accesskey="s">
		<input type="button" value="취소" class="btn_submit" onclick="document.location.href='./funding_news_list.php?page=<?php echo $_GET['page'];?>';" style="background-color:#383A3F;">
	</div>
</form>
<?php
	include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>

<script>

	function chk_frm(obj) {

		if(chk_value(obj.subject) == -1) {
			return false;
		}

	<?php if($mode_type == 'inst') { ?>
		if(chk_value(obj.bn_img1) == -1) {
			return false;
		}
/*
		if(chk_value(obj.bn_img2) == -1) {
			return false;
		}*/
	<?php } ?>

		if(chk_value(obj.show_date) == -1) {
			return false;
		}

		if(chk_value(obj.news_link) == -1) {
			return false;
		}

		if(chk_value(obj.contents) == -1) {
			return false;
		}

		return true;

	}

	function chk_value(obj2) {

		if(obj2.value == '') {
			alert(obj2.title);
			obj2.focus();
			return -1;
		}else {
			return 1;
		}

	}



</script>