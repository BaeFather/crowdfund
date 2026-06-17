<?

$sub_menu = '300710';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

// 값에 따른 변수 생성
while(list($key, $value) = each($_REQUEST)) {
    if($_FILES) continue;
    if(!is_array($_REQUEST)) ${$key} = clean_xss_tage($value);
}

$row = array();
if (isset($id) && $id) {
    $mode_txt = ' 수정';
    $mode_type = 'edit';

    // 메인 배너이미지 정보
    $sql = " SELECT * FROM funding_story_list WHERE id = '{$_GET['id']}' ";
    $row = sql_fetch($sql);

} else {                                            // 등록모드
    $mode_txt = ' 등록';
    $mode_type = 'insert';
}

$html_title = "펀딩디자이너 Story 등록";
$g5['title'] = $html_title . $mode_txt;

include_once(G5_ADMIN_PATH . '/admin.head.php');
$img_dir = G5_IMG_PATH . "/funding_story/";

?>
<form name="frmcontentform" action="./funding_designer_update.php" onsubmit="return chk_frm(this);" method="post" enctype="MULTIPART/FORM-DATA">
    <input type="hidden" name="mode" value="<?=$mode_type?>"/>
    <input type="hidden" name="page" value="<?=$page?>"/>
    <input type="hidden" name="id" value="<?=$id?>"/>
    <input type="hidden" name="type" value="<?=$type?>"/>

    <div class="tbl_frm01 tbl_wrap">
        <table>
            <caption><?=$g5['title']?></caption>
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
                <th scope="row"><label for="subject">제목</label></th>
                <td>
                    <input type="text" class="frm_input" name="subject" id="subject" size="100" maxlength="105" value="<?=$row['subject']?>" placeholder="제목을 입력해주세요."/>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="subject">소제목</label></th>
                <td>
                    <input type="text" class="frm_input" name="subheading" id="subheading" size="100" maxlength="105" value="<?=$row['subheading']?>" placeholder="소제목을 입력해주세요."/>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="subject">표시구분</label></th>
                <td>
                    <select name="type" required="required">
                        <option value="tv" <? echo get_selected("tv", $row["type"]);?>>TV출연</option>
                        <option value="column" <? echo get_selected("column", $row["type"]);?>>칼럼</option>
                        <option value="seminar" <? echo get_selected("seminar", $row["type"]);?>>세미나&강의</option>
                    </select>
                    *표시할 영역을 지정하세요.
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="thumbnail">대표이미지 등록</label>
                </th>
                <td>
                    <?
                    if (isset($row['thumbnail']) && !empty($row['thumbnail']))
                    {
                        if (file_exists($img_dir . $row['thumbnail'])) {
                            $thumbnail = '<img src="' . G5_IMG_URL."/funding_story/".$row['thumbnail'] . '" width="313" height="203" alt="'.$row['thumbnail_origin'].'"/>';
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
                    <input type="text" class="frm_input" name="target_link" id="target_link" size="80" value="<?=$row['target_link']?>" placeholder="URL 주소를 입력하세요."/>
                    *URL 형식이 맞지않으면 등록할 수 없습니다.
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="news_link">iframe 소스</label></th>
                <td>
                    <textarea name="iframe_source" rows="3"><?=stripSlashes($row['iframe_source'])?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="contents">내용</label></th>
                <td>
                    <textarea name="contents" rows="5"><?=$row['contents'];?></textarea>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="<? echo ($mode_type == "insert") ? '등록' : '수정';?>" class="btn_submit" accesskey="s"/>
        <input type="button" value="취소" class="btn_cancel" onclick="document.location.href='./funding_designer_list.php?page=<?=$page?>&type=<?=$type?>'"/>
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
        }else if(obj.subject.length > 100){
            alert("제목은 100자까지 입력가능합니다.");
            obj.subject.focus();
            obj.subject.select();
            return false;
        }

        if (obj.target_link.value != '' && !isURL(obj.target_link.value)){
            alert("URL 형식이 옳지않습니다.");
            obj.target_link.focus();
            return false;
        }

        oEditors.getById["contents"].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.
        var contents = document.getElementById("contents").value;
        if( contents == ""  || contents == null || contents == '&nbsp;' || contents == '<p>&nbsp;</p>')  {
            alert("내용을 입력해주세요.");
            oEditors.getById["contents"].exec("FOCUS"); //포커싱
            return false;
        }


        return true;
    }
</script>