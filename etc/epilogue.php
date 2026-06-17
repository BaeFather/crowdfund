<?

header("HTTP/1.0 404 Not Found");
exit;

include_once('./_common.php');

$g5['title'] = '투자후기';
//$g5['top_bn'] = "/images/investment/sub_investment.jpg";
$g5['top_bn_alt'] = "투자후기";

$inc_head = ($co['co_include_head']) ? $co['co_include_head'] : './_head.php';
include_once($inc_head);

$sql = "
	SELECT
		COUNT(A.idx) AS cnt_idx
	FROM
		invest_users_epilogue A,
		g5_member B
	WHERE
		A.member_idx=B.mb_no AND A.display='Y'";
$row = sql_fetch($sql);
$total_count = $row['cnt_idx'];
$page_rows  = 10;
$total_page = ceil($total_count / $page_rows);							// 전체 페이지 계산
if ($page < 1) $page = 1;																		// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;										// 시작 열을 구함

$sql = "
	SELECT
		A.*,
		B.mb_id, B.mb_name, B.mb_birth, B.mb_sex
	FROM
		invest_users_epilogue A,
		g5_member B
	WHERE
		A.member_idx=B.mb_no AND A.display='Y'
	ORDER BY
		idx	DESC
	LIMIT
		$from_record, $page_rows";
$res = sql_query($sql);
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {

	$LIST[$i] = sql_fetch_array($res);

	$LIST[$i]['mb_name'] = ($member['mb_no'] != $LIST[$i]['member_idx']) ? substr($LIST[$i]['mb_name'], 0, 3)."**" : $LIST[$i]['mb_name'];

	if($LIST[$i]['birth']) {
		$birth_year = substr($LIST[$i]['birth'], 0, 4);
		$LIST[$i]['age'] = date(Y) - $birth_year + 1;

		if($LIST[$i]['mb_sex']=='m') {
			$LIST[$i]['gender'] = "남";
		}
		else if($LIST[$i]['mb_sex']=='w') {
			$LIST[$i]['gender'] = "여";
		}
	}

}


$QUESTION = array(
	"1. 헬로펀딩은 어떻게 알게 되셨나요?",
	"2. 투자포인트는 무엇인가요?",
	"3. 현재 헬로펀딩에서 안전투자를 위한 투자자보호제도 (1. 사내투자심의위원회, 2. 법무법인, 감정평가법인 등 외부전문가의 권리분석, 3. 채권매입계약)가 있다는 것을 알고계시나요?",
	"4. 헬로펀딩에서 지급받은 수익금은 어떻게 활용하고 있나요?",
	"5. 헬로펀딩과 타 업체와의 차이점은 무엇이라고 생각하시나요?",
	"6. 헬로펀딩에 하고 싶은 말"
);

//모바일용 출력페이지
if(G5_IS_MOBILE){
	include_once("epilogue_m.php");
	return;
}

?>
<!-- 본문내용 START -->

<div id="content">
	<div class="location_top">
		<div class="location"><span><a href="">이용안내</a></span><b class="blue"><?=$g5['title']?></b></div>
		<div class="content">

			<div class="review_tit_s">
				<p style="width:100%;text-align:left">헬로펀딩 투자후기 <span><a href="/etc/epilogue_blog.php"><img src="/images/review/btn06.jpg" style="cursor:pointer"></a></span></p>
			</div>
			<div id="reviews_area_s">
				<div class="reviews_cont_s">
					<ul>
						<li>
							<img src="/images/main/review1.jpg" />
							<p><img id="review1" data-idx='57' src="/images/main/review1_btn.jpg" style="cursor:pointer" /></p>
						</li>
						<li>
							<img src="/images/main/review2.jpg" />
							<p><img id="review2" data-idx='47' src="/images/main/review2_btn.jpg" style="cursor:pointer" /></p>
						</li>
						<li>
							<img src="/images/main/review3.jpg" />
							<p><img id="review3" data-idx='64' src="/images/main/review3_btn.jpg" style="cursor:pointer" /></p>
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
			.review_list .text .question { line-height:25px;font-family:NG;color:#000;font-size:14px;}
			.review_list .text .answer { margin-bottom:10px; border:1px solid #bbb; background:#ffedbd;border-radius:3px; padding:8px; line-height:18px; font-family:'gulimche'; font-size:12px; color:#000; }
			</style>
		 <!-- 투자후기 리스트-->
			<div class="list_info">
				<p><!--Total <?=number_format($total_count)?>개--></p>
				<p style='font-family:gulim;font-size:12px;color:#999'>* '응답하라 투자후기' 이벤트에 응모해주신 전원의 투자후기입니다.(게시글에 삽입된 실명, 아이디 등을 제외하고 일절의 수정을 하지 않았습니다.)</p>
			</div>
			<div class="review_list" style="min-height:100px;">
<? for($i=0; $i<count($LIST); $i++) { ?>
				<dl>
					<dt class="title">
						<div style="float:left;width:34px;height:30px;"><?=($LIST[$i]['status']=='2')?'<img src="/images/main/medal.jpg" height="30">':'';?></div>
						<span style="float:left;color:#4a6fe2;padding-right:15px;"><?=$LIST[$i]['mb_name']?> (<?=$LIST[$i]['age']?>세/<?=$LIST[$i]['gender']?>)</span>
						<span><?=$LIST[$i]['subject']?></span>
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
					<?=get_paging($config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>
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
	//탭 기능
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

<?

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');

?>