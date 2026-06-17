<?php

/**
 * 투자후기 관리 가능
 * User: 김국현
 * Date: 2018-01-12
 */

include_once('./_common.php');

$sub_menu = "300700";
auth_check($auth[$sub_menu], 'w');
check_admin_token();

// 값에 따른 변수 생성
while(list($key, $value) = each($_REQUEST)) {
    if($_FILES) continue;
    if(!is_array($_REQUEST)) ${$key} = clean_xss_tage($value);
}

$img_path = G5_IMG_PATH.'/review/';
$saveFileName = "";
$fileUploadYN = false;

// 입력 / 수정 / 삭제
switch($mode)
{
    case "insert" : case "edit" :

        try{

            // 유효성검사
			/*
            if(empty($subject)){
                alert("제목을 입력해주세요.");
            }else if(strlen($subject) > 105){
                alert("제목길이가 105자를 초과할 수 없습니다.");
            }
			*/

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
            $sortyn = sql_fetch("SELECT COUNT(*) FROM epilogue_list WHERE sort = {$sort}");

            if(isset($sortyn) && !empty($sortyn)){
                if(array_pop($sortyn) > 0 && array_pop($sortyn) != 0){
                    throw new Exception("입력하신 정렬순서는 이미 지정되었습니다.");
                }
            }

            $display_yn = strtoupper($display_yn);
            $contents = trim($contents);

            // 수정
            if(isset($id) && !empty($id))
            {
                $thumbnailData = sql_fetch("SELECT thumbnail FROM epilogue_list WHERE id = {$id}");

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
                            epilogue_list
                        SET
                            thumbnail = '{$thumbnail}',
                            thumbnail_origin = '{$thumbnail_origin}',
                            mem_id = '{$mem_id}',
                            mem_name = '{$mem_name}',
                            subject = '".addslashes($subject)."',
                            contents = '{$contents}',
                            target_link = '{$target_link}',
                            display_yn = '{$display_yn}',
                            sort = '0',
                            best_review = '{$best_review}',
							target_att='{$target_att}',
							section='3',
							snskind='{$snskind}',
							content2='{$content2}',
							content2m='{$content2m}',
							content2txt='{$content2txt}',
							reg_date = '{$reg_date}'
                        WHERE id = '{$id}'";
                sql_query($sql);

                alert("수정되었습니다.","./recommend_list.php?page={$page}&id={$id}");
            }else{ // 등록


                // 저장
                $sql = "INSERT INTO
                        epilogue_list
                    SET
                        thumbnail = '{$thumbnail}',
                        thumbnail_origin = '{$thumbnail_origin}',
                        mem_id = '{$mem_id}',
                        mem_name = '{$mem_name}',
                        subject = '".addslashes($subject)."',
                        contents = '{$contents}',
                        target_link = '{$target_link}',
                        display_yn = '{$display_yn}',
						target_att='{$target_att}',
                        regdate = NOW(),
						section='3',
						snskind='{$snskind}',
						content2='{$content2}',
						content2m='{$content2m}',
						content2txt='{$content2txt}',
						reg_date = '{$reg_date}'
                    ";
                sql_query($sql);
                $id = sql_insert_id();

                if(!$id){
                    throw new Exception("등록할 수 없습니다.");
                }

                alert("등록되었습니다.","./recommend_list.php?page={$page}&id={$id}");
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
            $sql = sql_query("SELECT id, thumbnail FROM epilogue_list WHERE id IN ({$ids})");

            while($row = sql_fetch_array($sql)){
                if(isset($row["thumbnail"]) && !empty($row["thumbnail"])){
                    unlink(G5_IMG_PATH.'/review/'.$row["thumbnail"]);

                    sql_query("UPDATE epilogue_list SET thumbnail = '', thumbnail_origin = ''; WHERE id = {$row["id"]}");
                }
            }

            $sql = "DELETE FROM epilogue_list WHERE id IN ({$ids})";

            $result = sql_query($sql);

            if($result){
                exit(json_encode(array("success"=>1, "message"=>"정상적으로 삭제되었습니다.")));
            }
        }
        break;
    case "sort" :
        if(isset($id) && count($id) > 0)
        {
            $from_record = (int)($from_record + 1);
            $nLoop = $from_record;

            foreach($id as $index){
                $sql = "UPDATE epilogue_list SET sort = '{$nLoop}' WHERE id = {$index}";
                $result = sql_query($sql);
                $nLoop += 1;
            }

            $ids = implode(',', $id);
            $sql = sql_query("SELECT id FROM epilogue_list WHERE id NOT IN ({$ids}) AND sort >= {$nLoop} AND best_review = 'N' ORDER BY sort ASC regdate DESC");
            $idss = array();
            for($i=0; $row=sql_fetch_array($sql); $i++)
            {
                $idss[] = $row['id'];
            }

            foreach($idss as $index){
                $usql = "UPDATE epilogue_list SET sort = '{$nLoop}' WHERE id = {$index}";
                $result = sql_query($usql);
                $nLoop += 1;
            }

            exit(json_encode(array("success"=>1, "message"=>"정상적으로 수정되었습니다.")));
        }
        break;
	case "update" :

		if(isset($id) && count($id) > 0)
        {
            $ids = implode(',', $id);

			$sql = "UPDATE epilogue_list SET section='".$section."' ";
			IF($snskind)
			{
				$sql .= " , snskind='".$snskind."'";
			}
			$sql .= " WHERE id IN ({$ids})";

            $result = sql_query($sql);

            if($result){
                exit(json_encode(array("success"=>1, "message"=>"정상적으로 구분이 변경되었습니다.")));
            }
        }
		break;
    default :
        alert("비정상적인 접근입니다.");
        break;
}

sql_close();
exit;