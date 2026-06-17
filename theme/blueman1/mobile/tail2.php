<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

////////////////////////////////////////////////////////////////
// 투자후기
////////////////////////////////////////////////////////////////
if(preg_match('/\/index.php|\/event_invest\/|\/investment\//', $_SERVER['PHP_SELF'])) {
	include_once(G5_PATH.'/popup/inc_users_epilogue_blog.php');
}

if (!G5_IS_MOBILE) {
	include_once(G5_THEME_PATH.'/tail.php');
	return;
}

?>

<div style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>;width:95%; margin:10px auto 10px">
	<!--일반 실행 확인 영역-->
	<iframe name="axFrame" style="width:100%;height:200px;"></iframe>
	<!--ajax 실행 확인 영역-->
	<textarea id="ajax_return_txt" style="width:100%;height:200px;border:1px solid #EEE;background-color:#F7F7F7"></textarea>
</div>

</article>

<!--제휴사 로고 시작-->
<div id="client_logo_wrap">
	<center style="padding-top:5px;">
		<img src="<?=G5_THEME_URL?>/img/mobile/client_logo7.png" height="25">
		<img src="<?=G5_THEME_URL?>/img/mobile/client_logo6.png" height="20">
		<img src="<?=G5_THEME_URL?>/img/mobile/client_logo5.jpg" height="30">
		<img src="<?=G5_THEME_URL?>/img/mobile/client_logo4.jpg" height="27">
		<!--img src="<?=G5_THEME_URL?>/img/mobile/client_logo2.jpg" height="30"-->
		<img src="<?=G5_THEME_URL?>/img/mobile/client_logo3.jpg" height="28">
		<img src="<?=G5_THEME_URL?>/img/mobile/client_logo6.jpg" height="27">

	</center>
</div>
<!--제휴사 로고 끝-->

<div id="scroll" style="position:fixed;bottom:75px;right:15px;z-index:100;"><img src="<?=G5_THEME_URL?>/img/mobile/top_btn.png" width="50" style="opacity:0.8;"></div>
 <script type="text/javascript">
	$(document).ready(function(){
		$(window).scroll(function(){
			if ($(this).scrollTop() > 250) {
				$('#scroll').fadeIn();
			} else {
				$('#scroll').fadeOut();
			}
		});
		$('#scroll').click(function(){
			$("html, body").animate({ scrollTop: 0 }, 600);
			return false;
		});
	});
	</script>

<!--하단내비 시작-->
<div class="bottom_guide<?=($is_member)?'1':''?>">
	<ul>
		<li><a href="/investment/invest_list.php"><img src="<?=G5_THEME_URL?>/img/mobile/bottom_img01.png" style="cursor:pointer" alt="투자상품보기"/></a></li>
		<li><a href="/loan/loan.php"><img src="<?=G5_THEME_URL?>/img/mobile/bottom_img02.png" style="cursor:pointer" alt="대출신청하기"/></a></li>
		<li><img id="quick_guide_btn" src="<?=G5_THEME_URL?>/img/mobile/bottom_img03.png" style="cursor:pointer" alt="투자순서안내"/></li>
		<? if(!$is_member) { ?><li><img id="reqsms_btn" src="<?=G5_THEME_URL?>/img/mobile/bottom_img04.png" style="cursor:pointer" alt="투자상품알림받기"/></li><? } ?>
	</ul>
</div>
<!--하단내비 끝-->

<!-- 하단 시작 -->
<footer>
	<div class="foot_wrap2">
	<div class="foot">

		<ul class="foot_link1">
			<li><a href="/company.php">헬로펀딩소개</a></li>
			<li><a href="/bbs/content.php?co_id=provision">이용약관</a></li>
			<li><a href="/bbs/content.php?co_id=privacy">개인정보처리방침</a></li>

		</ul>
		<ul class="foot_link2">
			<li><a href="/bbs/content.php?co_id=provision2">투자이용약관</a></li>
			<li><a href="/reserve/contact_email.php">제휴문의</a></li>
		</ul>

		<address>
		<span class="fintech">(주)헬로핀테크</span><br>
		<div style="padding-top:10px;">서울시 강남구 대치동 945-10(테헤란로 98길 8)<br> KT&G 대치타워 4층 <br>
		E-mail : hellofunding@gmail.com<br><br>
		헬로펀딩은 투자원금과 수익을 보장하지 않으며, <br>
		투자손실에 대한 책임은 모두 투자자에게 있습니다.<br></div>
		</address>

		<div class="yellow_id"><a href="http://m.kakao.com/s/157918" target="_blank"><img src="<?=G5_THEME_URL?>/img/mobile/yellow_icon.png"></a></div>
		<ul class="foot_btn">
			<li><a href="https://www.facebook.com/hellofunding" target="_blank"><img src="<?=G5_THEME_URL?>/img/mobile/facebook_icon.png"></a></li>
			<li><a href="http://blog.naver.com/hellofunding" target="_blank"><img src="<?=G5_THEME_URL?>/img/mobile/blog_icon.png"></a></li>
			<li><a href="https://story.kakao.com/ch/hellofunding" target="_blank"><img src="<?=G5_THEME_URL?>/img/mobile/kakao_icon.png"></a></li>
			<li><a href="https://www.instagram.com/hellofunding" target="_blank"><img src="<?=G5_THEME_URL?>/img/mobile/insta_icon.png"></a></li>
			<li><a href="tel:1588-6760" target="_blank"><img src="<?=G5_THEME_URL?>/img/mobile/call_icon.png"></a></li>
		</ul>

		<address class="flatform">
		<span class="flatform_info">플랫폼 사업자</span><br>
		<div style="padding-top:10px;">플랫폼 사업자 : (주)헬로핀테크<br>
		대표자 : 남기중 | 사업자 등록번호 : 789-81-00529<br>
		Copyright ⓒ hellofunding All Rights reserved.
		</div>
		</address>
		<ul class="p2p_logo">
			<li><a href="http://p2plending.or.kr/" target="_blank"><img src="<?=G5_THEME_URL?>/img/b_logo01.png" ></a></li>
			<li><a href="http://p2plending.or.kr/page_msgp52" target="_blank"><img src="<?=G5_THEME_URL?>/img/b_logo02.png" ></a></li>
		</ul>
		<div class="tel">대표전화 : 1588-6760</div>

		<address>
			업무시간 안내 <span class="info_icon"><img src="<?=G5_THEME_URL?>/img/mobile/info_icon.png"></span><br>
			평일 : 10시~19시 점심시간 : 13시~14시<br>
			토.일.공휴일 : 휴무<br><br>
		</address>



		<!--<address style="clear:both;">
			<span class="crowd">(주)헬로크라우드대부</span><br>
			<div style="padding-top:10px;">대표자 : 김동일<br>
			주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8)<br> KT&G 대치타워 4층<br>
			사업자등록번호 : 610-87-00472<br>
			등록번호 : 2018-금감원-1347(P2P연계대부업)<br>
			등록기관 : 금융감독원<br><br></div>
			<span class="crowd_info">
			■ 대부이자율(연 이자율)및 연체이자율은 연24%를 <br>초과할 수 없습니다<br>
			■ 대출금리 연19.9% 이내 플랫폼 이용료 외 중개수수료 <br>등 기타 부대비용 없습니다.<br>
			■ 채무의 조기상환 수수료율등 조기상환 조건이 없습니다.<br>
			■ 중개수수료를 요구하거나 받는 행위는 불법입니다. <br>
			■ 과도한 빚은 당신에게 큰 불행을 안겨줄 수 있습니다.<br>
			■ 대출시 귀하의 신용등급이 하락할 수 있습니다.<br><br>
			</span>
			㈜신한은행은 제3자 예치금 신탁은행으로서 신탁계약에서 <br>정해진 업무 범위 내에서 예치금을 보관 및 관리하며, <br>
			직접 대출 취급, 대출 상환 및 대출 관리나<br> 회수 의무를 부담하지 않습니다.<br><br>

		</address>-->

	</div>
	</div>
</footer>

<!-- 하단 끝 -->

<? if(preg_match("/\/index.php/i", $_SERVER['PHP_SELF'])) { ?>
<script type="application/ld+json">
{
	"@context": "http://schema.org",
	"@type": "Person",
	"name": "헬로펀딩",
	"url": "https://www.hellofunding.co.kr",
	"sameAs": [
		"https://www.facebook.com/hellofunding",
		"https://blog.naver.com/hellofunding",
		"https://www.instagram.com/hellofunding"
	]
}
</script>
<? } ?>

<script>
$(function() {
	// 폰트 리사이즈 쿠키있으면 실행
	font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?

if ($config['cf_analytics']) { echo $config['cf_analytics']; }


include_once(G5_THEME_PATH."/tail.sub.php");

?>