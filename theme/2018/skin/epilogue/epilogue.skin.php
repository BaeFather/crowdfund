<?

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('	<link rel="stylesheet" type="text/css" href="'.$epilogue_skin_url.'style.css">', 10);

?>

<div id="content">
	<div class="location">
		<span><a href="<? echo G5_URL;?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">투자후기</b>
	</div>

	<div class="content">
		<div class="review_tit_r">
			<p>헬로펀딩 투자후기</p>
			<p>헬로펀딩에 투자하신 회원님들께서 블로그,SNS,카페 등에 남긴 생생한 후기입니다.</p>
		</div>

		<div class="review_best_list">
			<img src="<? echo G5_IMAGES_URL."/review/best_review_title.png";?>" alt="BEST 투자후기" class="review_best_title"/>
			<div class="swiper-container">
				<div class="swiper-wrapper">
<?
	if(isset($best_review) && count($best_review) > 0) {

		$best_review = array_reverse($best_review);

		foreach ($best_review as $best) {
?>
					<div class="swiper-slide">
						<div class="review">
							<div class="review_box">
								<span class="subject"><?php ECHO nl2br(stripslashes($best["subject"]))?></span>
								<span class="mem_name"><?=$best["mem_name"].' (ID: '.$best["mem_id"].')';?></span>
								<div class="thumbnail">
									<img src="<?=$best["thumb_url"];?>" width="100%" height="203" alt="<?=$best['thumbnail_origin']?>"/>
								</div>
								<span class="contents"><?=$best["contents"]?></span>
								<div class="link">
									<a href="<?=$best["target_link"]?>" target="<?php echo $best["target_att"];?>">자세히 보기</a>
								</div>
							</div>
						</div>
					</div>
<?
		}
	}
?>
				</div>
			</div>
			<div class="swiper-pagination" onFocus="blur();"></div>
			<div class="swiper-button-next" onFocus="blur();"></div>
			<div class="swiper-button-prev" onFocus="blur();"></div>
		</div>

		<div class="review_list">
<?
	if(isset($review) && count($review) > 0) {
?>
			<ul>
<?
		$nLoop = 0;
		foreach ($review as $review)
		{

			if($nLoop++ % 3 == 0) {
				echo '</ul><ul>';
			}

?>
				<li>
					<div class="review">
						<div class="review_box">
							<span class="subject"><?=nl2br(stripslashes($review["subject"]));?></span>
							<span class="mem_name"><?=$review["mem_name"].' (ID: '.$review["mem_id"].')';?></span>
							<div class="thumbnail">
								<img src="<?=$review["thumb_url"];?>" width="100%" height="203" alt="<?=$review['thumbnail_origin'];?>"/>
							</div>
							<span class="contents"><?=$review["contents"];?></span>
							<div class="link">
								<a href="<?=$review["target_link"];?>" target="_blank">자세히 보기</a>
							</div>
						</div>
					</div>
				</li>
<?
		}

		if($nLoop < 6) {
			for($i = 0; $i < (6 - $nLoop); $i++){
				echo "<li class='review_empty'>&nbsp;</li>";
			}
		}
?>
			</ul>
<?
	}
?>
		</div>

		<?=get_paging($page_rows, $page, $total_page-1, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');?>

		<br>

	</div>
</div>

<script type="text/javascript">
	var swiper_2 = null;
	$(document).ready(function() {
		swiper_2 = new Swiper('.swiper-container', {
			loop: true,
			slidesPerView: 3,
			slidesPerGroup: 3,
			spaceBetween: 0,
			pagination: {
				el: '.swiper-pagination',
				type: 'bullets',
				clickable: true
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev'
			}
		});
	});
</script>
