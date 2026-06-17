<?php
include_once('./_common.php');
?>

<!---------- 투자후기 시작 ---------->
<div class="review_tit">
	<p>헬로펀딩 투자후기</p><p><a href="/etc/epilogue.php"><img src="/images/main/review_more.jpg"></a></p>
</div>
<div id="reviews_area">
<? if(G5_IS_MOBILE) { ?>
	<div class="reviews_cont">
		<ul>
			<li>
				<img id="review1" data-idx='57' src="/images/main/review1_m.jpg">
			</li>
			<li>
				<img id="review2" data-idx='47' src="/images/main/review2_m.jpg">
			</li>
			<li>
				<img id="review3" data-idx='64' src="/images/main/review3_m.jpg">
			</li>
		</ul>
	</div>
<? } else { ?>
	<div class="reviews_cont">
		<ul>
			<li>
				<img src="/images/main/review1.jpg">
				<p><img id="review1" data-idx='57' src="/images/main/review1_btn.jpg" style="cursor:pointer"></p>
			</li>
			<li>
				<img src="/images/main/review2.jpg">
				<p><img id="review2" data-idx='47' src="/images/main/review2_btn.jpg" style="cursor:pointer"></p>
			</li>
			<li>
				<img src="/images/main/review3.jpg">
				<p><img id="review3" data-idx='64' src="/images/main/review3_btn.jpg" style="cursor:pointer"></p>
			</li>
		</ul>
	</div>
<? } ?>
</div>


<!-- 투자후기 팝업 -->
<div id="epilogue_popup">
	<div class="title">헬로펀딩 투자후기 <img src="/images/btn_close.gif" alt="close" class="close"></div>
	<div class="gap"></div>
	<div class="con" id="epilogue_con"><!-- 내용 --></div>
</div>

<script>
// 레이어 온 (투자후기)
$('#review1, #review2, #review3').click(function() {
	var idx = $(this).attr('data-idx');
	$.ajax({
		url : "<?=G5_THEME_URL?>/ajax_invest_epilogue.php",
		type: "POST",
		data: {idx:idx},
		success: function(data){
			$('#epilogue_con').html(data);

			$.blockUI({
				message: $('#epilogue_popup'),
				<? if(G5_IS_MOBILE) { ?>
				css: { top:'10%',width:'98%',height:'80%',border:'1px solid #AAA',cursor:'default', left:'1%' }
				<? } else { ?>
				css: { top:'10%',width:'600px',height:'680px',border:'1px solid #AAA',cursor:'default' }
				<? } ?>
			});
		},
		error: function () {
			alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
		}
	});
});
</script>
<!---------- 투자후기 끝 ---------->