<?php
/**
 * Created by PhpStorm.
 * User: 김국현
 * Date: 2018-01-16
 * Time: 오후 1:00
 */

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$epilogue_skin_url.'/style.css?ver=1.1">', 0);


?>
<div id="content">
    <div class="location">
        <span><a href="<?php echo G5_URL;?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">투자후기</b>
    </div>

    <div class="content">
        <div class="review_tit_r">
            <p>헬로펀딩 투자후기</p>
            <p>헬로펀딩에 투자하신 회원님들께서 블로그,SNS,카페 등에 남긴 생생한 후기입니다.</p>
        </div>

        <div class="review_best_list">
            <img src="<?php echo G5_IMAGES_URL."/review/best_review_title.png";?>" alt="BEST 투자후기" class="review_best_title"/>
            <div class="swiper-container review-cont">
                <?php if (isset($best_review) && count($best_review) > 0){ ?>
                <div class="swiper-wrapper">
                    <?php $best_review=array_reverse($best_review); foreach ($best_review as $best) { ?>
                        <div class="swiper-slide">
                            <div class="review">
                                <div class="review_box">
                                    <span class="subject"><?php echo nl2br(stripslashes($best["subject"]));?></span>
                                    <span class="mem_name"><?php echo $best["mem_name"].' (ID: '.$best["mem_id"].')';?></span>
                                    <div class="thumbnail">
                                        <img src="<?php echo $best["thumb_url"];?>" width="100%" height="203" alt="<?php echo $best['thumbnail_origin'];?>"/>
                                    </div>
                                    <span class="contents"><?php echo $best["contents"];?></span>
                                    <div class="link">
                                        <a href="<?php echo $best["target_link"];?>" target="<?php echo $best["target_att"];?>">자세히 보기</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <div class="swiper-pagination review-pagination"></div>
            </div>
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
                                    <span class="subject"><?php echo nl2br(stripslashes($review["subject"]));?></span>
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

        <div class="paging">
            <?php echo m_get_paging($page_rows, $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page='); ?>
        </div>
        <div class="m_more_list_loading">
            <img src="/shop/img/loading.gif" alt="loading.." width="20px"/>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var swiper_1 = new Swiper ('.review-cont', {
            loop: true,
            slidesPerView: 1,
            slidesPerGroup: 1,
            pagination: {
                el: '.review-pagination',
                type: 'bullets',
                clickable: true
            }
        });
    });
    $(document).on("click", ".m_more_list", function(){
       $(this).hide();
       var page = $(this).attr("data-target");
       $(".m_more_list_loading").hide().show();
       $.ajax({
           url : g5_bbs_url + "/epilogue.php",
           type: "POST",
           data : {is_ajax_m:1,page:page},
           dataType: "json",
           success: function(data, textStatus, jqXHR)
           {
               if(data.error){
                   alert(alert.message);
                   return false;
               }else{
                   setTimeout(function(){
                       $(".m_more_list_loading").hide();
                       $(".paging").empty();

                       for(var index in data.review)
                       {
                           var html = '<li><div class="review"> \ <div class="review_box"> \ <span class="subject">' + data.review[index].subject+'</span>\ <span class="mem_name">' + data.review[index].mem_name+' ('+data.review[index].mem_id+')</span>\ <div class="thumbnail">\ <img src="' + data.review[index].thumb_url + '" width="100%" height="203" alt="'+data.review[index].thumbnail_origin+'"/>\ </div>\ <span class="contents">' + data.review[index].contents+'</span>\ <div class="link">\ <a href="' + data.review[index].target_link + '" target="_blank">자세히 보기</a>\ </div>\ </div>\ </div></li>';
                           $(".review_list ul").append(html);
                       }
                       $(".paging").html(data.paging);
                   }, 1000);
                   page++;
                   return true;
               }
           },
           error: function (jqXHR, textStatus, errorThrown)	{
               console.log(jqXHR);
           }
       });
    });
</script>
