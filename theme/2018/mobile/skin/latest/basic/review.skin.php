<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


$latest_skin_dir = str_replace(G5_URL, '', $latest_skin_url);
add_stylesheet('	<link rel="stylesheet" href="'.$latest_skin_dir.'/style.css?ver="'.time().'" />', 0);		// fundingNews.skin.php 파일에 정의된 스타일을 공유한다.
?>
	<div class="review_wrap">
		<div class="swiper-container s2">
<?	if(count($list) > 0) {	?>
			<div class="swiper-wrapper review_list">
<?
	FOR($i=0;$i<1;$i++)
	{
?>
				<div class="swiper-slide content" OnClick="check_review('<?php ECHO $list[$i]["id"]?>');">
					<p class="review_cont">
						<span class="review_img">
							<img src="<?=$list[$i]["thumb_url"]?>" alt="<?=$list[$i]['thumbnail_origin']?>"  style="max-width:100%;">
						</span>
						<span class="review_tit2">투자자 <?=$list[$i]['mem_name']?> 회원님</span>
						<span class="review_ti"><?=$list[$i]['subject']?></span>
						<span class="review_tit"><?=$list[$i]['contents']?></span>
						<br>
						<span class="review_btn"><a href="/review/">더 많은 인터뷰 보기</a></span>
					</p>
				</div>
<?		}	?>
			</div>
	    <div class="swiper-pagination review-pagination" style="position:relative;"></div>
<?	} ?>
		</div>
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