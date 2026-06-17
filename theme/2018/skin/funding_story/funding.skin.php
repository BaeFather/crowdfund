<?php
/**
 * Created by PhpStorm.
 * User: 김국현
 * Date: 2018-01-16
 * Time: 오후 1:00
 */

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$epilogue_skin_url.'/style.css">', 0);

?>

<div id="content">
    <div class="location">
        <span><a href="<?=G5_URL?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">투자후기</b>
    </div>
    
    <div class="content">
        <div class="review_tit_r">
            <p>헬로펀딩 투자후기</p>
            <p>헬로펀딩에 투자하신 회원님들께서 블로그,SNS,카페 등에 남긴 생생한 후기입니다.</p>
        </div>
    
        <div class="review_best_list">
            <img src="<?php echo G5_IMAGES_URL."/review/best_review_title.png";?>" alt="BEST 투자후기" class="review_best_title"/>
            
            <?php
                if(isset($best_review) && count($best_review) > 0){
            ?>
            <ul>
                <?php foreach ($best_review as $best) { ?>
                    <li>
                        <div class="review">
                            <div class="review_box">
                                <span class="subject"><?php echo $best["subject"];?></span>
                                <span class="mem_name"><?php echo $best["mem_name"].' (ID: '.$best["mem_id"].')';?></span>
                                <div class="thumbnail">
                                    <img src="<?php echo $best["thumb_url"];?>" width="100%" height="203" alt="<?php echo $best['thumbnail_origin'];?>"/>
                                </div>
                                <span class="contents"><?php echo $best["contents"];?></span>
                                <div class="link">
                                    <a href="<?php echo $best["target_link"];?>" target="_blank">자세히 보기</a>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
            <?php } ?>
        </div>
    
        <div class="review_list">
            <?php
            if(isset($review) && count($review) > 0){
                ?>
                <ul>
                    <?php
                    $nLoop = 0;
                    foreach ($review as $review)
                    {
                        if($nLoop++ % 3 == 0){
                            echo '</ul><ul>';
                        }
                        ?>
                        <li>
                            <div class="review">
                                <div class="review_box">
                                    <span class="subject"><?php echo $review["subject"];?></span>
                                    <span class="mem_name"><?php echo $review["mem_name"].' (ID: '.$review["mem_id"].')';?></span>
                                    <div class="thumbnail">
                                        <img src="<?php echo $review["thumb_url"];?>" width="100%" height="203" alt="<?php echo $review['thumbnail_origin'];?>"/>
                                    </div>
                                    <span class="contents"><?php echo $review["contents"];?></span>
                                    <div class="link">
                                        <a href="<?php echo $review["target_link"];?>" target="_blank">자세히 보기</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php
                    }
                    
                    if($nLoop < 6){
                        for($i = 0; $i < (6 - $nLoop); $i++){
                            echo "<li class='review_empty'>&nbsp;</li>";
                        }
                    }
                    ?>
                </ul>
            <?php } ?>
        </div>
    
        <?php echo get_paging($page_rows, $page, $total_page-1, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page='); ?>
    </div>
</div>

