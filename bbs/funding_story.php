<?php
/**
 * 펀딩디자이너 스토리
 * User: 김국현
 * Date: 2018-02-01
 * Time: 오후 5:56
 */

include_once('./_common.php');

if($CONF['flatform']=='app') {
	header("Location: /");
}

// 값에 따른 변수 생성
while(list($key, $value) = each($_REQUEST)) {
    if($_FILES) continue;
    if(!is_array($_REQUEST)) ${$key} = clean_xss_tage($value);
}

if(isset($type)){
    if(!in_array($type, array("tv", "column", "seminar"))){
        alert("비정상적인 접근입니다.");
    }else{
        $where = " AND `type` = '{$type}'";
    }
}

$table = "funding_story_list";

// 출력개수
if(G5_IS_MOBILE){
    $page_rows = 2;
}else{
    $page_rows = 6; // 노출 줄 개수
}

// Paging
$tvListCount      = sql_fetch("SELECT COUNT(*) AS cnt FROM {$table} WHERE TYPE = 'tv';")["cnt"];
$columnListCount  = sql_fetch("SELECT COUNT(*) AS cnt FROM {$table} WHERE TYPE = 'column';")["cnt"];
$seminarListCount = sql_fetch("SELECT COUNT(*) AS cnt FROM {$table} WHERE TYPE = 'seminar';")["cnt"];

if($page < 1) { // 페이지가 없으면 첫 페이지 (1 페이지)
	$page = 1;
}

$total_page  = ceil($total_count / $page_rows);  // 전체 페이지 계산
$from_record = ($page - 1) * $page_rows; // 시작 열을 구함

$latest_tv_data = array(); // 최근 방송출연분
$tv_list = array(); // 방송출연
$column_list = array(); // 칼럼&인터뷰
$seminar_list = array(); // 세미나&강연

// 투자후기 조회
$sql = sql_query("SELECT * FROM
                    (
                      (SELECT * FROM {$table} WHERE `type` = 'tv' ORDER BY regdate DESC LIMIT {$from_record}, {$page_rows})
                      UNION
                      (SELECT * FROM {$table} WHERE `type` = 'column' ORDER BY regdate DESC LIMIT {$from_record}, {$page_rows})
                      UNION
                      (SELECT * FROM {$table} WHERE `type` = 'seminar' ORDER BY regdate DESC LIMIT {$from_record}, {$page_rows})
                    ) list
                  WHERE display_yn = 'Y' {$where}");

$img_url = G5_IMG_URL . "/funding_story/";
$img_dir = G5_IMG_PATH . "/funding_story/";

$nLoop = 1;
while($row = sql_fetch_array($sql))
{
    $row["iframe"] = "";
    switch($row["type"]){
        case 'tv' : // tv는 iframe 노출
            preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', stripslashes($row["iframe_source"]), $matches);
            $row["iframe"] = (isset($matches[1])) ? $matches[1] : ""; // http://www.youtube.com/embed/IIYeKGNNNf4?rel=0)
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

    $row["contents"] = get_text(strip_tags(html_clean($row["contents"])));
    $row["contents"] = cut_str(trim($row["contents"]), 140);

    if($row["type"] == "tv"){
        array_push($tv_list, $row);
    }else if($row["type"] == "column"){
        array_push($column_list, $row);
    }else if($row["type"] == "seminar"){
        array_push($seminar_list, $row);
    }

    $nLoop++;
}

// 첫번째 글 가져오기
$latest_tv_data = (count($tv_list) > 0 ) ? reset($tv_list) : "";

$list = array_merge($tv_list, $column_list, $seminar_list);


// 스킨설정
if (G5_IS_MOBILE) {
    if(isset($is_ajax) && $is_ajax){
        exit(json_encode(array("success"=>'1', "list"=>$list, "page"=>$page)));
    }else{
        $funding_story_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/funding_story/'.$match[1];
        $funding_story_skin_url = str_replace(G5_PATH, G5_URL, $funding_story_skin_path);
    }
} else {
    if(isset($is_ajax) && $is_ajax){
        exit(json_encode(array("success"=>'1', "list"=>$list, "page"=>$page)));
    }else {
        $funding_story_skin_path = G5_THEME_PATH . '/' . G5_SKIN_DIR . '/funding_story/' . $match[1];
        $funding_story_skin_url = str_replace(G5_PATH, G5_URL, $funding_story_skin_path);
    }
}

include_once('./_head.php');

include_once($funding_story_skin_path.'/funding_story.skin.php');

include_once('./_tail.php');

