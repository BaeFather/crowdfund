<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

require_once(G5_THEME_PATH.'/inc_quick_guide.php');

?>
    </div>

		<!--일반 실행 확인 영역-->
		<iframe name="axFrame" style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>;width:99.7%;height:200px;"></iframe>

		<!--ajax 실행 확인 영역-->
		<textarea id="ajax_return_txt" style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>;;width:100%;height:200px;border:1px solid #EEE""></textarea>
	</div>

	<!-- 제휴업체 로고 스크롤링 //-->
	<div style="position:relative; width:100%; background-color:#ffffff;">
		<div style="width:98%; margin:6px auto 6px;">
			<center>
				<img src="/images/main/partner_logo/01.jpg" height="28">&nbsp;
				<img src="/images/main/partner_logo/02.jpg" height="28">&nbsp;
				<img src="/images/main/partner_logo/03.jpg" height="28">&nbsp;
				<img src="/images/main/partner_logo/04.jpg" height="28">&nbsp;
				<img src="/images/main/partner_logo/05.jpg" height="28">
			<center>
		</div>
	</div>
	<!-- 제휴업체 로고 스크롤링 //-->

	<style>
	#footer { padding:15px; }
	#footer .info .box{ margin-right:10px; }
	</style>

	<div id="footer" style="padding-bottom:80px;">
		<ul class="footLink">
			<li><a href="<?=G5_BBS_URL?>/content.php?co_id=provision">이용약관</a></li>
			<li><a href="<?=G5_BBS_URL?>/content.php?co_id=privacy">개인정보취급방침</a></li>
			<li><a href="<?=G5_BBS_URL?>/content.php?co_id=provision2">투자이용약관</a></li>
			<li><a href="<?=G5_URL?>/reserve/contact_email.php">제휴문의</a></li>
		</ul>
		<div class="info">
			<div class="box">
				<address>플랫폼 사업자 (주)헬로핀테크<br>대표자 : 남기중<br>주소 : 서울시 강남구 역삼동 735-37 삼정빌딩 7층 <br>사업자 등록번호 : 789-81-00529<br>대표전화 : 1588-6760  E-mail : hellofunding@gmail.com</address>
			</div>
			<div class="box" style="margin-top:20px;">
				<address>여신회사 (주)헬로크라우드대부<br>대표자 : 김동일<br>주소 : 서울특별시 강남구 선릉로87길 8, 7층 <br>(역삼동, 페넌트타워)<br>사업자등록번호 : 610-87-00472<br>등록번호 : 2016-서울강남-0275<br>등록도시명칭 : 서울시 강남구청 (02) 3423-5522</address>

				<div class="customer">대표전화 : 1588-6760</div>
				<ul style="padding-top:15px;">
				  <li>업무시간 안내 <img src="/images/icon01.jpg" style="padding-top:2px;" alt="" / ></li>
				  <li>평일 : 10시~19시   점심시간 :  13시~14시</li>
				  <li>토.일.공휴일 : 휴무</li>
				</ul>
				<div class="notice">
					<b>대출금리 : 연 27%이내 (연체금리 최고 연 27.9%이내) 과도한 빚, 고통의 시작입니다.</b><br>
					<!--이자 외 별도로 중개수수료를 수취하는 것은 불법입니다.<br>-->
					플랫폼 이용료 외 취급수수료등 기타 부대비용은 없습니다.<br><br>
					헬로핀테크는 투자자 안전을 위해 ‘크라우드 펀딩 투자 보호 시스템’ 특허를 출원하였습니다.<br>
					(출원번호 : 10-2016-0120914)<br>

					<br>
					Copyright ⓒ hellofunding All Rights reserved.
				</div>
				<ul class="SNS">
					<li><a href="https://www.facebook.com/hellofunding" target="_blank"><img src="/images/btn_face.gif" alt="Facebook" /></a></li>
					<li><a href="http://blog.naver.com/hellofunding" target="_blank"><img src="/images/btn_naver.gif" alt="Naver" /></a></li>
					<li><a href="https://story.kakao.com/ch/hellofunding" target="_blank"><img src="/images/btn_kakao.gif" alt="KakaoStory" /></a></li>
					<li><a href="https://www.instagram.com/hellofunding" target="_blank"><img src="/images/btn_insta.gif" alt="instagram" /></a></li>
					<li><a href="tel:1588-6760"><img src="/images/btn_call.gif" alt="call" /></a></li>
				</ul>
			</div>
		</div>
		<div style="text-align:left;padding:15px 50px 5px 0;">
			<a href="http://m.kakao.com/s/157918" target="_blank"><img src="/images/img_kakao.gif" alt="Yellow ID hellopunding" style="width:50%;" /></a>
		</div>
		<div style="padding-top:25px;line-height:20px;font-size:14px;">헬로펀딩은 부동산/동산 전문가의 프로젝트를 투자심의위원회가 심사/평가하고 투자안전장치를 마련하여 투자상품으로 출시하므로, 누구나 쉽고 안전하게 소액을 투자하여 수익을 창출할 수 있는 P2P 금융 플랫폼입니다.</div>
					<div style="padding-top:15px;line-height:20px;font-size:14px;">헬로펀딩은 투자자의 원리금 손실에 대한 책임을 지지 않습니다.투자자의 투자금액에 대한 원리금 보장 시 유사수신행위로 간주되어 법적 처벌을 받게 됩니다.</div>
		<a href="#" class="top_btn">상단으로</a>
	</div>

  <script>
  $(function() {
    // 폰트 리사이즈 쿠키있으면 실행
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
  });
  </script>

<?
	if ($config['cf_analytics']) {
		echo $config['cf_analytics'];
	}
?>

<?php
include_once(G5_THEME_PATH."/tail.sub.php");
?>