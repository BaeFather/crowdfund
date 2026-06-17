<?php
/**
 * 투자후기 개편
 * User: 김국현
 * Date: 2018-01-12 ~ 2018-01-16
 * Time: 오후 4:40
 */

include_once('./_common.php');

// Paging
$query = "SELECT COUNT(*) AS cnt FROM epilogue_list WHERE display_yn = 'Y' ORDER BY sort ASC";
$review_data = sql_fetch($query);
$total_count = $review_data['cnt'];
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)

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
$sql = sql_query("(SELECT * FROM epilogue_list WHERE display_yn = 'Y' AND  best_review = 'N' ORDER BY `sort` ASC, `regdate` DESC  LIMIT {$from_record}, {$page_rows})
                  UNION DISTINCT (SELECT * FROM epilogue_list WHERE display_yn = 'Y' AND best_review = 'Y' ORDER BY sort ASC )");

while($row = sql_fetch_array($sql))
{
    if (!empty($row["thumbnail"]) && file_exists(G5_IMG_PATH."/review/".$row["thumbnail"])) {
        $row["thumb_url"] = HF_IMG_URL."/review/".$row["thumbnail"];
    }else{
        $row["thumb_url"] = G5_IMAGES_URL.'/review/sumnail_img01.jpg';
    }
	//$row["thumb_url"] = HF_IMG_URL."/review/".$row["thumbnail"];
	$row["thumb_url"] = "/root_img/review/".$row["thumbnail"];
    $row["contents"] = get_text(strip_tags(html_clean($row["contents"])));
    $row["contents"] = utf8_strcut(trim($row["contents"]), 153);
    if($row["best_review"] == 'Y'){
        array_push($best_review, $row);
        continue;
    }
    array_push($review, $row);
}


include_once(HF_PATH.'/hf_head.php');

include_once(HF_PATH.'/view/question.skin.php');

include_once(HF_PATH.'/_tail.php');