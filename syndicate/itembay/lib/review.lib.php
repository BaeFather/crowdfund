<?php
//if (!defined('_GNUBOARD_')) exit;

// 투자후기 최신글 추출
// $cache_time 캐시 갱신시간
function review ($skin_dir='', $rows=10, $subject_len=40, $cache_time=1, $options='')
{
    global $hf;

    if (!$skin_dir) $skin_dir = 'basic';

    if(preg_match('#^theme/(.+)$#', $skin_dir, $match)) {

        if (G5_IS_MOBILE) {
            $latest_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
            if(!is_dir($latest_skin_path))
                $latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
            $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
        } else {
            $latest_skin_path = HF_URL.'/theme/2018/latest/'.$match[1];
            $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
        }
        $skin_dir = $match[1];
    } else {
        if(G5_IS_MOBILE) {
            $latest_skin_path = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
            $latest_skin_url  = G5_MOBILE_URL.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
        } else {
            $latest_skin_path = G5_SKIN_PATH.'/latest/'.$skin_dir;
            $latest_skin_url  = G5_SKIN_URL.'/latest/'.$skin_dir;
        }
    }

    $cache_fwrite = false;
    if(HF_USE_CACHE) {
        $cache_file = HF_DATA_PATH."/cache/latest-review-{$skin_dir}-{$rows}-{$subject_len}.php";

        if(!file_exists($cache_file)) {
            $cache_fwrite = true;
        } else {
            if($cache_time > 0) {
                $filetime = filemtime($cache_file);
                if($filetime && $filetime < (G5_SERVER_TIME - 3600 * $cache_time)) {
                    @unlink($cache_file);
                    $cache_fwrite = true;
                }
            }

            if(!$cache_fwrite)
                include($cache_file);
        }
    }


    if(!HF_USE_CACHE || $cache_fwrite) {

        $bestReview = array();
        $bestReviewImgUrl = "/root_img/review/";
        $bestReviewNullImgUrl = HF_IMAGES_URL."/review/";
        $bestReviewImgPath = "/home/crowdfund/public_html/syndicate/r114/root_img/review/";
        $bestReviewNullImgPath = G5_IMAGES_URL."/reivew/";



//    var_dump(date("Y-m-d H:i:s", (G5_SERVER_TIME - 3600 * $cache_time)));
//    var_dump(date("Y-m-d H:i:s", filemtime($cache_file)));
//    exit;

//    var_dump($cache_fwrite); exit;


        $sql = sql_query("SELECT `id`, `thumbnail`, `thumbnail_origin`, `mem_id`, `mem_name`, `subject`, `contents`, `target_link`, `regdate` FROM epilogue_list WHERE display_yn = 'Y' AND best_review = 'Y' ORDER BY sort ASC LIMIT 6");
        $list = array();
        while($row = sql_fetch_array($sql))
        {
            if ($subject_len)
                $row['subject'] = conv_subject($row['subject'], $subject_len, '…');
            else
                $row['subject'] = conv_subject($row['subject'], 100, '…');


            if (!empty($row["thumbnail"]) && file_exists($bestReviewImgPath.$row["thumbnail"])) {
                $row["thumb_url"] = $bestReviewImgUrl.$row["thumbnail"];
            }else{
                $row["thumb_url"] = $bestReviewNullImgUrl.'sumnail_img01.jpg';
            }
            $row["contents"] = get_text(strip_tags(html_clean($row["contents"])));
            $row["contents"] = utf8_strcut(trim($row["contents"]), 153);

            $list[] = $row;
        }

        if($cache_fwrite && count($list) > 0) {
            $handle = fopen($cache_file, 'w');
            $cache_content = "<?php\nif (!defined('_GNUBOARD_')) exit;\n\$list=".var_export($list, true)."?>";
            fwrite($handle, $cache_content);
            fclose($handle);
        }
    }

    ob_start();
    include HF_PATH.'/view/review.skin.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}