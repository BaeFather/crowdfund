<?php

$sub_menu = "300710";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');
check_admin_token();

// 값에 따른 변수 생성
while(list($key, $value) = each($_REQUEST)) {
    if($_FILES) continue;
    if(!is_array($_REQUEST)) ${$key} = clean_xss_tage($value);
}

$img_path = G5_IMG_PATH.'/funding_story/';
$saveFileName = "";
$fileUploadYN = false;

// 입력 / 수정 / 삭제
switch($mode)
{
    case "insert" : case "edit" :

    try{

        // 유효성검사
        if(empty($subject)){
            alert("제목을 입력해주세요.");
        }else if(strlen($subject) > 100){
            alert("제목길이가 100자를 초과할 수 없습니다.");
        }

        if(!empty($target_link) && !filter_var($target_link, FILTER_VALIDATE_URL)){
            alert("URL 형식이 옳지 않습니다.");
        }

        // 대표이미지 이름, 업로드
        if(isset($_FILES["thumbnail"]["name"]) && !empty($_FILES["thumbnail"]["name"])){
            $thumbnail = md5(uniqid(mt_rand(), true)).'.'.pathinfo($_FILES["thumbnail"]["name"], PATHINFO_EXTENSION);
            $thumbnail_origin = $_FILES["thumbnail"]["name"];


            if(!UploadFile($img_path, '10', "thumbnail", null, $thumbnail, 'Y')){
                throw new Exception("이미지를 저장할 수 없습니다.");
            }
            $fileUploadYN = true;
        }

        // 정렬순서 중복제외
        $sortyn = sql_fetch("SELECT COUNT(*) FROM funding_story_list WHERE sort = {$sort}");

        if(isset($sortyn) && !empty($sortyn)){
            if(array_pop($sortyn) > 0 && array_pop($sortyn) != 0){
                throw new Exception("입력하신 정렬순서는 이미 지정되었습니다.");
            }
        }

        $display_yn    = strtoupper($display_yn);
        $contents      = trim($contents);
        $iframe_source = ($iframe_source) ? $iframe_source : "";

        if(!in_array($display_yn, array('Y', 'N'))){
            $display_yn = 'Y';
        }

        // 수정
        if(isset($id) && !empty($id))
        {
            $thumbnailData = sql_fetch("SELECT thumbnail FROM funding_story_list WHERE id = {$id}");

            if($delete_img){ // 이미지 삭제

                if(isset($thumbnailData["thumbnail"])){
                    if(file_exists($img_path.$thumbnailData["thumbnail"])){
                        unlink($img_path.$thumbnailData["thumbnail"]);
                        $thumbnail = null;
                        $thumbnail_origin = null;
                    }
                }
            }else if(!$fileUploadYN){
                $thumbnail = $thumbnailData["thumbnail"];
                $thumbnail_origin = $thumbnailData["thumbnail_origin"];
            }

            $best_review = strtoupper($best_review);

            // 수정
            $sql = "UPDATE
                            funding_story_list
                        SET
                            `thumbnail` = '{$thumbnail}',
                            `thumbnail_origin` = '{$thumbnail_origin}',
                            `type` = '{$type}',
                            `subject` = '{$subject}',
                            `subheading` = '{$subheading}',
                            `contents` = '{$contents}',
                            `iframe_source` = '{$iframe_source}',
                            `target_link` = '{$target_link}',
                            `display_yn` = '{$display_yn}'
                        WHERE id = '{$id}'";

            sql_query($sql);

            alert("수정되었습니다.","./funding_designer_list.php?page={$page}&id={$id}&type={$type}");
        }else{ // 등록

            // 저장
            $sql = "INSERT INTO
                        funding_story_list
                    SET
                        `thumbnail` = '{$thumbnail}',
                        `thumbnail_origin` = '{$thumbnail_origin}',
                        `type` = '{$type}',
                        `subject` = '{$subject}',
                        `subheading` = '{$subheading}',
                        `contents` = '{$contents}',
                        `iframe_source` = '{$iframe_source}',
                        `target_link` = '{$target_link}',
                        `display_yn` = '{$display_yn}',
                        `regdate` = NOW()
                    ";
            sql_query($sql);
            $id = sql_insert_id();

            if(!$id){
                throw new Exception("등록할 수 없습니다.");
            }

            alert("등록되었습니다.","./funding_designer_list.php?page={$page}&id={$id}type={$type}");
        }

    }catch(Exception $e){
        alert($e->getMessage());
        exit;
    }
    break;
    case "delete" :

        if(isset($id) && count($id) > 0)
        {
            $ids = implode(',', $id);

            // 이미지 삭제
            $sql = sql_query("SELECT id, thumbnail FROM funding_story_list WHERE id IN ({$ids})");

            while($row = sql_fetch_array($sql)){
                if(isset($row["thumbnail"]) && !empty($row["thumbnail"])){
                    unlink(G5_IMG_PATH.$row["thumbnail"]);

                    sql_query("UPDATE funding_story_list SET thumbnail = '', thumbnail_origin = ''; WHERE id = {$row["id"]}");
                }
            }

            $sql = "DELETE FROM funding_story_list WHERE id IN ({$ids})";

            $result = sql_query($sql);

            if($result){
                exit(json_encode(array("success"=>1, "message"=>"정상적으로 삭제되었습니다.")));
            }
        }
        break;
    default :
        alert("비정상적인 접근입니다.");
        break;
}

sql_close();
exit;