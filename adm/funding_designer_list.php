<?php

/**
 * 펀딩디자이너 스토리
 * User: 김국현
 * Date: 2018-02-01
 * Time: 오후 2:55
 */

include_once('./_common.php');

$sub_menu = '300710';
auth_check($sub_menu, "r"); // 메뉴 권한체크

$g5["title"] = "펀딩디자이너 스토리";
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 값에 따른 변수 생성
while(list($key, $value) = each($_REQUEST)) {
    if($_FILES) continue;
    if(!is_array($_REQUEST)) ${$key} = clean_xss_tage($value);
}

if(!isset($page) OR empty($page)){
    $page = 1;
}

$sql_common = " FROM `funding_story_list` ";

// 테이블의 전체 레코드수만 얻음
$sql = " SELECT COUNT(*) AS cnt " . $sql_common. " WHERE type = '{$type}'";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)

$from_record = ($page - 1) * $rows; // 시작 열을 구함

$type = ($type) ? $type : "tv";
$sql = "SELECT * $sql_common WHERE type = '{$type}' ORDER BY `regdate` DESC LIMIT $from_record, {$rows}";

$result = sql_query($sql);

$img_url = G5_IMG_URL . "/funding_story/";
$img_dir = G5_IMG_PATH . "/funding_story/";

$tvListCount = sql_fetch("SELECT COUNT(*) AS cnt FROM funding_story_list WHERE TYPE = 'tv';")["cnt"];
$columnListCount = sql_fetch("SELECT COUNT(*) AS cnt FROM funding_story_list WHERE TYPE = 'column';")["cnt"];
$seminarListCount = sql_fetch("SELECT COUNT(*) AS cnt FROM funding_story_list WHERE TYPE = 'seminar';")["cnt"];


?>

<div class="local_ov01 local_ov">
    <?php if ($page > 1) {?><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>">처음으로</a><?php } ?>
    <span>전체 펀딩스토리 <?php echo intval($tvListCount + $columnListCount + $seminarListCount); ?>건</span>
</div>

<div class="local_desc01 local_desc">
    <ol>
        <li>홈 > 이용안내 > 펀딩디자이너 스토리를 관리할 수 있습니다.</li>
        <li><strong>새로추가</strong>를 눌러 펀딩디자이너 스토리 글을 생성합니다.</li>
        <li>마우스로 드래그하여 순서를 변경할 수 있습니다.</li>
        <li>새로추가 후 선택수정을 하면 순서가 정렬됩니다.</li>
    </ol>
    <a href="/bbs/funding_story.php">펀딩디자이너 스토리 이동</a>
    <br/>
    <br/>
</div>

<div class="compare_left btn_list">
    <a href="#" onclick="location.href='/adm/funding_designer_list.php?type=tv'" target="_self" class="btn_confirm">TV출연(<?php echo $tvListCount;?>)</a>
    <a href="#" onclick="location.href='/adm/funding_designer_list.php?type=column'" target="_self" class="btn_confirm">칼럼(<?php echo $columnListCount;?>)</a>
    <a href="#" onclick="location.href='/adm/funding_designer_list.php?type=seminar'" target="_self" class="btn_confirm">세미나&강연(<?php echo $seminarListCount;?>)</a>
</div>

<div class="btn_add01 btn_add">
    <a href="javascript:del_this();">선택삭제</a>
    <a href="./funding_designer_reg.php">새로추가</a>
</div>

<div style="clear: both;content:'';"></div>

<form name="epilogue_list" method="post">
    <input type="hidden" name="from_record" value="<?php echo $from_record;?>"/>
    <div class="tbl_head01 tbl_wrap">
        <table>
            <colgroup>
                <col width="30px"/>
                <col width="50px"/>
                <col width="300px"/>
                <col width="*"/>
                <col width="170px"/>
            </colgroup>
            <thead>
            <tr>
                <th scope="col"><input type="checkbox" name="chk" id="chk"/></th>
                <th scope="col">ID</th>
                <th scope="col">동영상/이미지</th>
                <th scope="col">제목/내용</th>
                <th scope="col">관리</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $nLoop = abs($from_record);
            for ($i=1; $row = sql_fetch_array($result); $i++)
            {
                $nLoop++;

                $iframe_url = "";
                switch($row["type"]){
                    case 'tv' : // tv는 iframe 노출
                        preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', stripslashes($row["iframe_source"]), $matches);
                        $iframe_url = (isset($matches[1])) ? $matches[1] : ""; // http://www.youtube.com/embed/IIYeKGNNNf4?rel=0)
                        break;
                    case 'column' : // 칼럼
                    case 'seminar' : // 세미나는 이미지 형식

                        if (!empty($row["thumbnail"]) && file_exists($img_dir.$row["thumbnail"])) {
                            $row["thumb_url"] = $img_url.$row["thumbnail"];
                        }else{
                            $row["thumb_url"] = G5_IMAGES_URL.'/funding_story/no_image.jpg';
                        }
                        break;
                }

                ?>
                <tr data-sort-order="<?php echo $nLoop;?>">
                    <td class="text-center">
                        <input type="checkbox" name="chk[]" value="<?php echo $row["id"];?>"/>
                    </td>
                    <td class="text-center">
                        <?php echo $row["id"];?>
                    </td>
                    <td class="text-center">
                        <?php if(!empty($iframe_url)) { ?>
                            <iframe src="<?php echo $iframe_url;?>" frameborder="0" allowfullscreen="allowfullscreen" width="100%" height="200"></iframe>
                        <?php }else if($row["thumb_url"] != ""){ ?>
                            <img src="<?php echo $row["thumb_url"];?>" alt="<?php echo $row["subject"];?>" width="320px"/>
                        <?php }else{ ?>
                            없음
                        <?php } ?>
                    </td>
                    <td>

                        <?php if(isset($row["target_link"]) && !empty($row["target_link"])) : ?>
                            <a href="<?php echo $row["target_link"]; ?>" target="_blank"><?php echo $row["subject"];?></a><br/>
                        <?php else : ?>
                            <strong><?php echo $row["subject"];?></strong><br/>
                        <?php endif; ?>
                        <br/>
                        <span><?php echo $row["subheading"];?></span><br/>
                        <p><?php echo $row["contents"];?></p>
                        <a href="<?php echo $row["target_link"];?>" target="_blank"> <?php echo $row["content"];?> </a>
                    </td>
                    <td class="text-center">
                        <button type="button" onclick="go_link(<?php echo $row["id"];?>, 'edit');">수정</button>
                        <button type="button" onclick="del_this(<?php echo $row["id"];?>);">삭제</button>
                    </td>
                </tr>
                <?php
            }

            if ($i <= 1){
                echo '<tr><td colspan="6" class="empty_table"><span>자료가 한건도 없습니다.</span></td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</form>
<script type="text/javascript">

    // 수정
    function go_link(id, mode){
        document.location.href = '/adm/funding_designer_reg.php?id='+id+'&page=<?php echo $page;?>&mode='+mode+'&type=<?php echo $type;?>'
    }

    // 삭제
    function del_this(id){
        if(confirm("정말 삭제하시겠습니까?")){

            var send_array = Array();
            var send_cnt = 0;

            if(!id){
                var chkbox = $("input:checkbox[name='chk[]']");

                for (var i = 0; i < chkbox.length; i++) {
                    if (chkbox[i].checked == true) {
                        send_array[send_cnt] = chkbox[i].value;
                        send_cnt++;
                    }
                }
            }else{
                send_array.push(id);
            }

            if(send_array.length <= 0){
                alert("삭제하실 후기글을 선택해주세요.");
                return false;
            }

            var token = get_ajax_token();
            $.ajax({
                url: g5_admin_url + "/funding_designer_update.php?mode=delete",
                type: "POST",
                data: {id: send_array, token: token},
                dataType: "JSON",
                async: false,
                cache: false,
                success: function(data, textStatus) {
                    if (data.error) {
                        alert(data.message);
                    } else {
                        alert(data.message);
                        document.location.reload();
                    }
                }
            });
        }
    }

    // 선택
    $('input:checkbox[name="chk"]').on("click", function(){
        if($(this).prop("checked"))
        {
            $('input:checkbox[name="chk[]"]').prop("checked",true);
        } else {
            $('input:checkbox[name="chk[]"]').prop("checked",false);
        }
    });
</script>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page=&type=".$type); ?>
<?php include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>
