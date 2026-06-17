<link rel="stylesheet" href="/css/epilogue<?=(G5_IS_MOBILE)?'_m':''?>.css">

<div id="content">
	<div class="content">

<? if(G5_IS_MOBILE) { ?>
		<div>
			<div class="location">
				<span></span><b class="blue">투자후기</b>
			</div>
		</div>
<? } else { ?>
		<div class="location">
			<span></span><b class="blue">투자후기</b>
		</div>
<? } ?>

		<div class="review_tit_r">
			<p>헬로펀딩 투자후기</p>
			<p>헬로펀딩에 투자하신 회원님들께서 블로그,SNS,카페 등에 남긴 생생한 후기입니다.</p>
		</div>

		<div class="review_list">
<?
	if( count($review) ) {
?>
			<ul>
<?
		$nLoop = 0;
		foreach ($review as $review)
		{
			if($nLoop++ % 2 == 0){
				echo "			</ul>\n";
				echo "			<ul>\n";
			}
?>
				<li>
					<div class="review">
						<div class="review_box">
							<span class="subject"><?=$review["subject"]?></span>
							<span class="mem_name"><?=$review["mem_name"].' (ID: '.$review["mem_id"].')';?></span>
							<div class="thumbnail">
								<img src="<?=$review["thumb_url"];?>" width="100%" alt="<?=$review['thumbnail_origin'];?>" />
							</div>
							<span class="contents"><? echo $review["contents"];?></span>
							<div class="link">
								<a href="<? echo $review["target_link"];?>" target="_blank">자세히 보기</a>
							</div>
						</div>
					</div>
				</li>
<?
		}

		if($nLoop < 6) {
			for($i=0; $i<(6-$nLoop); $i++) {
				echo "				<li class='review_empty'>&nbsp;</li>\n";
			}
		}
?>
			</ul>
<?
	}
?>
		</div>

		<div id="paging_start">
			<div id="paging_span">
				<? paging($total_count, $page, $page_rows); ?>
			</div>
		</div>

		<div class="m_more_list_loading">
			<img src="/shop/img/loading.gif" alt="loading.." width="20px"/>
		</div>

	</div>
</div>

<script type="text/javascript">
$(document).on('click', '#paging_span span.btn_paging', function() {
	var url = '<?=$_SERVER['SCRIPT_NAME']?>?<?=$qstr?>&page=' + $(this).attr('data-page');
	$(location).attr('href', url);
});
</script>
