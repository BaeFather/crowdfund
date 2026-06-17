<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


$latest_skin_dir = str_replace(G5_URL, '', $latest_skin_url);
add_stylesheet('	<link rel="stylesheet" href="'.$latest_skin_dir.'/style.css?ver='.time().'" />', 0);		// fundingNews.skin.php 파일에 정의된 스타일을 공유한다.
?>

<div class="review_wrap">
	<div class="swiper-container s2">
			<? foreach ($list as $review){ ?>
			<div class="review_list" OnClick="check_review('<?php ECHO $review["id"]?>');">
				<p class="review_cont">
					<span class="review_img">
						<img src="<?=$review["thumb_url"]?>" alt="<?=$review['thumbnail_origin']?>" class="thumbnail" style="max-width:100%;">
					</span>
					<span class="review_ti">투자자 <?=$review['mem_name']?> 회원님</span>
					<span class="review_tit1"><?=$review['subject']?></span>
					<span class="review_tit2"><?=$review['contents']?></span>
				</p>
			</div>
			<? } ?>

		<div class="swiper-pagination review-pagination" style="position:relative;"></div>
	</div>

	<div class="b_r_btn_c"><span class="b_r_btn"><a href="/review/" class="btn_200">더 많은 인터뷰 보기 </a></span></div>
</div>

<script type="text/javascript">
	function check_review(obj)
	{
		if(obj)
		{
			$("#SE").val(obj);
			$("#reviewfm").attr("action","/review/");
			$("#reviewfm").attr("method","POST");
			$("#reviewfm").submit();
		} else {
			alert("접근이 올바르지 않습니다.");
		}
	}
</script>

<form name="reviewfm" id="reviewfm">
	<input type="hidden" name="SE" id="SE" value="" />
	<input type="hidden" name="RD" value="2" />
	<input type="hidden" name="page" value="1" />
	<input type="hidden" name="section" value="1" />
	<input type="hidden" name="viewy" value="0" />
</form>