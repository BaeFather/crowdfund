<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


//$latest_skin_dir = str_replace(G5_URL, '', $latest_skin_url);
//add_stylesheet('	<link rel="stylesheet" href="'.$latest_skin_url.'/style.css?ver=20190212_4" />', 0);

?>
<div class="funding-news-list">
	<div class="swiper-container s1" style="width:80%;">
		<div class="swiper-wrapper">
			<? for ($i = 0; $i < count($list); $i++) { ?>
				<div class="swiper-slide content" >
					<? if($list[$i]['thumbnail']) { ?>
						<img src="<? echo $list[$i]['thumbnail'];?>" alt="<? echo $list[$i]['subject'];?>" class="thumb"/>
					<? } ?>
					<div class="pull-left" >
						<? if($list[$i]['news_logo']) { ?>
							<img src="<? echo $list[$i]['news_logo'];?>" alt="<? echo $list[$i]['subject'];?>" class="logo" style="max-width:100%;"/>
						<? } ?>
					</div>
					<div class="pull-right">
						<span class="datetime">
							<? if($list[$i]['show_date']) { ?>
								<? echo $list[$i]['show_date'];?>
							<? } ?>
						</span>
					</div>
					<div class="clearfix"></div>
					<span class="subject">
						<a href="<? echo $list[$i]['news_link'];?>"> <? echo $list[$i]['subject'];?></a><? if(isset($list[$i]['icon_new'])) echo " ".$list[$i]['icon_new']; ?>
					</span>
				</div>
			<? }  ?>
			<div class="content swiper-slide">
				<div class="funding-news-list_btn">
					<a href="<?=G5_URL?>/news/funding_news.php">언론속의 헬로펀딩을<br/>만나보세요.<br/>
					<span>더보기 ></span></a>
				</div>
			</div>
		</div>
	</div>
	<div class="swiper-pagination funding-news-list-pagination" style="position:relative;"></div>
	<!--div class="swiper-button-next funding-news-button-next" style="margin-right:-16px;" onFocus="blur();"></div>
	<div class="swiper-button-prev funding-news-button-prev" style="margin-left:-16px;" onFocus="blur();"></div-->
</div>