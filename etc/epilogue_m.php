<div id="content">
	<div class="location_top">
		<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
		<div class="content" style="min-height:500px">

			<div class="review_tit_s">
				<p style="width:100%;text-align:left">헬로펀딩 투자후기 <a href="/etc/epilogue_blog.php"><img src="/images/review/btn06.png" height="30"></a></p>
			</div>
			<div id="reviews_area_s">
				<div class="reviews_cont_s">
					<ul>
						<li>
							<img id="review1" data-idx='57' src="/images/main/review1_m.jpg" />
						</li>
						<li>
							<img id="review2" data-idx='47' src="/images/main/review2_m.jpg" />
						</li>
						<li>
							<img id="review3" data-idx='64' src="/images/main/review3_m.jpg" />
						</li>
					</ul>
				</div>
			</div>
			<!-- 투자후기 팝업 -->
			<div id="epilogue_popup">
				<div class="title">헬로펀딩 투자후기 <img src="/images/btn_close.gif" alt="close" class="close" /></div>
				<div class="gap"></div>
				<div class="con" id="epilogue_con">
					<!-- 내용 -->
				</div>
			</div>

			<style>
			.review_list .text .question { line-height:25px;font-family:NG;color:#000;font-size:14px;text-align:left; }
			.review_list .text .answer { margin-bottom:10px; border:1px solid #bbb;background:#ffedbd;border-radius:3px; padding:8px; line-height:18px; font-family:'gulimche'; font-size:12px; width:99%;}
			</style>
		   <!-- 투자후기 리스트-->
			<div class="list_info">
				<p style='text-align:left;'><!--Total <?=number_format($total_count)?>개--></p>
				<p style='text-align:left;font-family:gulim;font-size:11px;color:#999'>* '응답하라 투자후기' 이벤트에 응모해주신 전원의 투자후기입니다.<br>(게시글에 삽입된 실명, 아이디 등을 제외하고 일절의 수정을 하지 않았습니다.)</p>
			</div>
			<div class="review_list">
<? for($i=0; $i<count($LIST); $i++) { ?>
				<dl>
					<dt class="title">
						<div style="float:left;width:30px;height:48px;"><?=($LIST[$i]['status']=='2')?'<img src="/images/main/medal.jpg" height="25">':'';?></div>
						<div style="float:left;width:95px;height:48px;line-height:48px; color:#4a6fe2;padding-right:15px;"><?=$LIST[$i]['mb_name']?> (<?=$LIST[$i]['age']?>세/<?=$LIST[$i]['gender']?>)</div>
						<div style="width:50%;height:48px;line-height:48px;overflow-y:hidden;"><?=$LIST[$i]['subject']?></div>
					</dt>
					<dd class="text">
						<div class="question"><?=$QUESTION[0]?></div>
						<div class="answer"><?=nl2br(htmlSpecialChars(stripSlashes($LIST[$i]['text1'])));?></div>

						<div class="question"><?=$QUESTION[1]?></div>
						<div class="answer"><?=nl2br(htmlSpecialChars(stripSlashes($LIST[$i]['text2'])));?></div>

						<div class="question"><?=$QUESTION[2]?></div>
						<div class="answer"><?=nl2br(htmlSpecialChars(stripSlashes($LIST[$i]['text3'])));?></div>

						<div class="question"><?=$QUESTION[3]?></div>
						<div class="answer"><?=nl2br(htmlSpecialChars(stripSlashes($LIST[$i]['text4'])));?></div>

						<div class="question"><?=$QUESTION[4]?></div>
						<div class="answer"><?=nl2br(htmlSpecialChars(stripSlashes($LIST[$i]['text5'])));?></div>

						<div class="question"><?=$QUESTION[5]?></div>
						<div class="answer"><?=nl2br(htmlSpecialChars(stripSlashes($LIST[$i]['text6'])));?></div>
					</dd>
				</dl>
<? } ?>
			</div>

			<div style="width:100%; text-align: center;">
				<ul class="pagination">
					<?=get_paging($config['cf_mobile_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>
				</ul>
			</div>

		</div>
	</div>
</div>

<script type="text/javascript">
//질문 클릭시 내용 오픈
$(document).ready(function(){
	$('.review_list:eq(0)').show();
	$('.review_list dl').click(function(){
		$(this).css({background:'url(/images/bbs/arrow_down.gif) no-repeat right top'}).find('dd').slideToggle();
		//$(this).css({background:'url(/images/bbs/arrow_up.gif) no-repeat right top'}).find('dd').slideDown('fast');
		//$(this).siblings().css({background:'url(/images/bbs/arrow_down.gif) no-repeat right top'}).find('dd').slideUp('fast');
	});
});

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
				css: { top:'16%',width:'600px',height:'680px',border:'1px solid #AAA',cursor:'default' }
				<? } ?>
			});
		},
		error: function () {
			alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
		}
	});
});
</script>

<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>