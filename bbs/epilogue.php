<?php
header("Location: http://www.hellofunding.co.kr/review/");
exit;
include_once('./_common.php');

// Paging
$query = "SELECT COUNT(*) AS cnt FROM epilogue_list WHERE display_yn = 'Y' ORDER BY sort ASC";
$review_data = sql_fetch($query);
$total_count = $review_data['cnt'];
if($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)

if(G5_IS_MOBILE){
	$page_rows = 4;
}else{
	$page_rows = 15; // 노출 줄 개수
}
$total_page  = ceil($total_count / $page_rows);  // 전체 페이지 계산
$from_record = ($page - 1) * $page_rows; // 시작 열을 구함
$review = array();
$best_review = array();

// 투자후기 조회
$sql = "
	(SELECT * FROM epilogue_list WHERE display_yn = 'Y' AND  best_review = 'N' ORDER BY `sort` ASC, `regdate` DESC  LIMIT {$from_record}, {$page_rows})
	UNION DISTINCT (SELECT * FROM epilogue_list WHERE display_yn = 'Y' AND best_review = 'Y' ORDER BY sort ASC )";
$res = sql_query($sql);

while($row = sql_fetch_array($res))
{
    if (!empty($row["thumbnail"]) && file_exists(G5_IMG_PATH."/review/".$row["thumbnail"])) {
        $row["thumb_url"] = G5_IMG_URL."/review/".$row["thumbnail"];
    }else{
        $row["thumb_url"] = G5_IMAGES_URL.'/review/sumnail_img01.jpg';
    }
    $row["contents"] = get_text(strip_tags(html_clean($row["contents"])));
    $row["contents"] = utf8_strcut(trim($row["contents"]), 153);
    if($row["best_review"] == 'Y'){
        array_push($best_review, $row);
        continue;
    }
    array_push($review, $row);
}

// 스킨설정
if (G5_IS_MOBILE) {
    if(isset($is_ajax_m) && $is_ajax_m){
        $paging = m_get_paging($page_rows, $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');
        exit(json_encode(array("success"=>'1', "review"=>$review, "paging"=>$paging)));
    }else{
        $epilogue_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/epilogue/'.$match[1];
        $epilogue_skin_url = str_replace(G5_PATH, G5_URL, $epilogue_skin_path);
    }
} else {
    $epilogue_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/epilogue/'.$match[1];
    $epilogue_skin_url = str_replace(G5_PATH, G5_URL, $epilogue_skin_path);
}

include_once('./_head.php');

include_once($epilogue_skin_path.'/epilogue.skin.php');
include_once('./_tail.php');

