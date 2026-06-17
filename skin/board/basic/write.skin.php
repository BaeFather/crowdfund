<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

?>
<script>
$(function(){
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesShort: ['일' ,'월', '화', '수', '목', '금', '토']
	});
});
</script>

<div id="content">
	<div class="location">
<?
if($bo_table=='notice') {
	//echo '<span><a href="'.G5_URL.'/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">공지사항</b>' . PHP_EOL;//
}
else if($bo_table=='recruit') {
	echo "<span></span><b class=\"blue\">채용안내</b>\n";
}

//IF(!$w)
IF(!$w AND $bo_table=='notice')
{
	$wr_3 = DATE("Y-m-d");
	$wr_4 = "2099-12-31";
}
?>
	</div>

	<div class="content">

    <!-- 게시물 작성/수정 시작 { -->
    <form name="fwrite" id="fwrite" action="<? echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="width:<? echo $width; ?>">
    <input type="hidden" name="uid" value="<? echo get_uniqid(); ?>">
    <input type="hidden" name="w" value="<? echo $w ?>">
    <input type="hidden" name="bo_table" value="<? echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<? echo $wr_id ?>">
    <input type="hidden" name="sca" value="<? echo $sca ?>">
    <input type="hidden" name="sfl" value="<? echo $sfl ?>">
    <input type="hidden" name="stx" value="<? echo $stx ?>">
    <input type="hidden" name="spt" value="<? echo $spt ?>">
    <input type="hidden" name="sst" value="<? echo $sst ?>">
    <input type="hidden" name="sod" value="<? echo $sod ?>">
    <input type="hidden" name="page" value="<? echo $page ?>">
    <?
    $option = '';
    $option_hidden = '';
    if ($is_notice || $is_html || $is_secret || $is_mail) {
        $option = '';
        if ($is_notice) {
            $option .= "\n".'<input type="checkbox" id="notice" name="notice" value="1" '.$notice_checked.'>'."\n".'<label for="notice">공지</label>';
        }

        if ($is_html) {
            if ($is_dhtml_editor) {
                $option_hidden .= '<input type="hidden" value="html1" name="html">';
            } else {
                $option .= "\n".'<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="html">html</label>';
            }
        }

        if ($is_secret) {
            if ($is_admin || $is_secret==1) {
                $option .= "\n".'<input type="checkbox" id="secret" name="secret" value="secret" '.$secret_checked.'>'."\n".'<label for="secret">비밀글</label>';
            } else {
                $option_hidden .= '<input type="hidden" name="secret" value="secret">';
            }
        }

        if ($is_mail) {
            $option .= "\n".'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'."\n".'<label for="mail">답변메일받기</label>';
        }
    }

    echo $option_hidden;

		if($bo_table=='notice') {
			$wr_1_checked = ($wr_1) ? 'checked' : '';
			$notice_wr_1 .= "\n".'<input type="checkbox" id="wr_1" name="wr_1" value="Y" '.$wr_1_checked.'>'."\n".'<label for="mail">메인 투자상품 관련 소식 리스팅</label>';
		}

		?>

    <div class="tbl_frm01 tbl_wrap" style="padding:49px;">
        <table>
        <tbody>
        <? if ($is_name) { ?>
        <tr>
            <th scope="row"><label for="wr_name">이름<strong class="sound_only">필수</strong></label></th>
            <td><input type="text" name="wr_name" value="<? echo $name ?>" id="wr_name" required class="frm_input required" size="10" maxlength="20"></td>
        </tr>
        <? } ?>

        <? if ($is_password) { ?>
        <tr>
            <th scope="row"><label for="wr_password">비밀번호<strong class="sound_only">필수</strong></label></th>
            <td><input type="password" name="wr_password" id="wr_password" <? echo $password_required ?> class="frm_input <? echo $password_required ?>" maxlength="20"></td>
        </tr>
        <? } ?>

        <? if ($is_email) { ?>
        <tr>
            <th scope="row"><label for="wr_email">이메일</label></th>
            <td><input type="text" name="wr_email" value="<? echo $email ?>" id="wr_email" class="frm_input email" size="50" maxlength="100"></td>
        </tr>
        <? } ?>

        <? if ($is_homepage) { ?>
        <tr>
            <th scope="row"><label for="wr_homepage">홈페이지</label></th>
            <td><input type="text" name="wr_homepage" value="<? echo $homepage ?>" id="wr_homepage" class="frm_input" size="50"></td>
        </tr>
        <? } ?>

        <? if ($option) { ?>
        <tr>
            <th scope="row">옵션</th>
            <td><?=$option?> &nbsp; <?=$notice_wr_1?></td>
        </tr>
        <? } ?>

        <? if ($is_category) { ?>
        <tr>
            <th scope="row"><label for="ca_name">분류<strong class="sound_only">필수</strong></label></th>
            <td>
                <select name="ca_name" id="ca_name" required class="required" >
                    <option value="">선택하세요</option>
                    <? echo $category_option ?>
                </select>
            </td>
        </tr>
        <? } ?>

		<?php if ($bo_table=="recruit") { ?>
        <tr>
            <th scope="row"><label for="ca_name">채용기간<strong class="sound_only">필수</strong></label></th>
            <td>
                <select name="wr_1" id="wr_1" >
                    <option value="">선택하세요</option>
                    <option value="기간내" <?=$wr_1=="기간내"?"selected":""?> >기간내</option>
					<option value="채용시마감" <?=$wr_1=="채용시마감"?"selected":""?> >채용시마감</option>
					<option value="상시채용" <?=$wr_1=="상시채용"?"selected":""?> >상시채용</option>
					<option value="마감" <?=$wr_1=="마감"?"selected":""?> >마감</option>
                </select>
				<input type="text" name="wr_2" id="wr_2" value="<?=$wr_2?>" size="8" style="margin-left:20px;" />
				~
				<input type="text" name="wr_3" id="wr_3" value="<?=$wr_3?>" size="8" />
            </td>
        </tr>
		<tr>
			<th scope="row"><label for="ca_name">표시<strong class="sound_only">필수</strong></label></th>
			<td>
				<select name="wr_4" id="wr_4" class="required">
					<option value="N" <?=$wr_4<>'Y'?'selected':''?> >N</option>
					<option value="Y" <?=$wr_4=='Y'?'selected':''?>>Y</option>
				</select>
			</td>
		</tr>
		<? } ?>

        <tr>
            <th scope="row"><label for="wr_subject">제목<strong class="sound_only">필수</strong></label></th>
            <td>
                <div id="autosave_wrapper">
                    <input type="text" name="wr_subject" value="<? echo $subject ?>" id="wr_subject" required class="frm_input required" size="50" maxlength="255">
                    <? if ($is_member) { // 임시 저장된 글 기능 ?>
                    <script src="<? echo G5_JS_URL; ?>/autosave.js"></script>
                    <? if($editor_content_js) echo $editor_content_js; ?>
                    <button type="button" id="btn_autosave" class="btn_frmline">임시 저장된 글 (<span id="autosave_count"><? echo $autosave_count; ?></span>)</button>
                    <div id="autosave_pop">
                        <strong>임시 저장된 글 목록</strong>
                        <div><button type="button" class="autosave_close"><img src="<? echo $board_skin_url; ?>/img/btn_close.gif" alt="닫기"></button></div>
                        <ul></ul>
                        <div><button type="button" class="autosave_close"><img src="<? echo $board_skin_url; ?>/img/btn_close.gif" alt="닫기"></button></div>
                    </div>
                    <? } ?>
                </div>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="wr_content">내용<strong class="sound_only">필수</strong></label></th>
            <td class="wr_content">
                <? if($write_min || $write_max) { ?>
                <!-- 최소/최대 글자 수 사용 시 -->
                <p id="char_count_desc">이 게시판은 최소 <strong><? echo $write_min; ?></strong>글자 이상, 최대 <strong><? echo $write_max; ?></strong>글자 이하까지 글을 쓰실 수 있습니다.</p>
                <? } ?>
                <? echo $editor_html; // 에디터 사용시는 에디터로, 아니면 textarea 로 노출 ?>
                <? if($write_min || $write_max) { ?>
                <!-- 최소/최대 글자 수 사용 시 -->
                <div id="char_count_wrap"><span id="char_count"></span>글자</div>
                <? } ?>
            </td>
        </tr>

        <? for ($i=1; $is_link && $i<=G5_LINK_COUNT; $i++) { ?>
        <tr>
            <th scope="row"><label for="wr_link<? echo $i ?>">링크 #<? echo $i ?></label></th>
            <td><input type="text" name="wr_link<? echo $i ?>" value="<? if($w=="u"){echo$write['wr_link'.$i];} ?>" id="wr_link<? echo $i ?>" class="frm_input" size="50"></td>
        </tr>
        <? } ?>
		<?php
		if($bo_table=='notice') {
		?>
		 <tr>
            <th scope="row"><label for="wr_2">제목 외부링크</label></th>
            <td><input type="text" name="wr_2" value="<?php ECHO $wr_2?>" class="frm_input" size="100"></td>
        </tr>
		<tr>
            <th scope="row"><label for="wr_2">시작일</label></th>
            <td><input type="text" name="wr_3" value="<?php ECHO $wr_3?>" class="frm_input datepicker" size="100"></td>
        </tr>
		<tr>
            <th scope="row"><label for="wr_2">종료일</label></th>
            <td><input type="text" name="wr_4" value="<?php ECHO $wr_4?>" class="frm_input datepicker" size="100"></td>
        </tr>
		<?
		}
		?>

        <? for ($i=0; $is_file && $i<$file_count; $i++) { ?>
        <tr>
            <th scope="row">파일 #<? echo $i+1 ?></th>
            <td>
                <input type="file" name="bf_file[]" title="파일첨부 <? echo $i+1 ?> : 용량 <? echo $upload_max_filesize ?> 이하만 업로드 가능" class="frm_file frm_input">
                <? if ($is_file_content) { ?>
                <input type="text" name="bf_content[]" value="<? echo ($w == 'u') ? $file[$i]['bf_content'] : ''; ?>" title="파일 설명을 입력해주세요." class="frm_file frm_input" size="50">
                <? } ?>
                <? if($w == 'u' && $file[$i]['file']) { ?>
                <input type="checkbox" id="bf_file_del<? echo $i ?>" name="bf_file_del[<? echo $i;  ?>]" value="1"> <label for="bf_file_del<? echo $i ?>"><? echo $file[$i]['source'].'('.$file[$i]['size'].')';  ?> 파일 삭제</label>
                <? } ?>
            </td>
        </tr>
        <? } ?>

        <? if ($is_guest) { //자동등록방지  ?>
        <tr>
            <th scope="row">자동등록방지</th>
            <td>
                <? echo $captcha_html ?>
            </td>
        </tr>
        <? } ?>

        </tbody>
        </table>
    </div>

    <div class="btn_confirm" style="margin-bottom:49px;">
        <input type="submit" value="작성완료" id="btn_submit" accesskey="s" class="btn_blue" style="border:0px;">
        <input type="button" value="취소" onClick="location.href='./board.php?bo_table=<?=$bo_table?>'" class="btn_blue" style="border:0px; background-color:#ababab;">
    </div>
    </form>

    <script>
    <? if($write_min || $write_max) { ?>
    // 글자수 제한
    var char_min = parseInt(<? echo $write_min; ?>); // 최소
    var char_max = parseInt(<? echo $write_max; ?>); // 최대
    check_byte("wr_content", "char_count");

    $(function() {
        $("#wr_content").on("keyup", function() {
            check_byte("wr_content", "char_count");
        });
    });

    <? } ?>
    function html_auto_br(obj)
    {
        if (obj.checked) {
            result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
            if (result)
                obj.value = "html2";
            else
                obj.value = "html1";
        }
        else
            obj.value = "";
    }

    function fwrite_submit(f)
    {
        <? echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

        var subject = "";
        var content = "";
        $.ajax({
            url: g5_bbs_url+"/ajax.filter.php",
            type: "POST",
            data: {
                "subject": f.wr_subject.value,
                "content": f.wr_content.value
            },
            dataType: "json",
            async: false,
            cache: false,
            success: function(data, textStatus) {
                subject = data.subject;
                content = data.content;
            }
        });

<? if(!$is_admin) { ?>
        if (subject) {
            alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
            f.wr_subject.focus();
            return false;
        }

        if (content) {
            alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
            if (typeof(ed_wr_content) != "undefined")
                ed_wr_content.returnFalse();
            else
                f.wr_content.focus();
            return false;
        }

<? } ?>
        if (document.getElementById("char_count")) {
            if (char_min > 0 || char_max > 0) {
                var cnt = parseInt(check_byte("wr_content", "char_count"));
                if (char_min > 0 && char_min > cnt) {
                    alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                    return false;
                }
                else if (char_max > 0 && char_max < cnt) {
                    alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                    return false;
                }
            }
        }

        <? echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }
    </script>

	</div>
</div>

<!-- } 게시물 작성/수정 끝 -->