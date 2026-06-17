<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

////////////////////////////////////////////////////////////////
// 투자후기
////////////////////////////////////////////////////////////////
if(preg_match('/\/index.php|\/event_invest\/|\/investment\//', $_SERVER['PHP_SELF'])) {
	include_once(G5_PATH.'/popup/inc_users_epilogue_blog.php');
}

if (G5_IS_MOBILE) {
	include_once(G5_THEME_MOBILE_PATH.'/tail.php');
	return;
}

?>

<div style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>;width:1150px; margin:10px auto 10px;">
	<!--일반 실행 확인 영역-->
	<? if(preg_match("/".$_SERVER['HTTP_HOST']."/", $_SERVER['HTTP_REFERER'])) { ?><iframe name="axFrame" style="width:100%;height:200px;"></iframe><? } ?>
	<!--ajax 실행 확인 영역-->
	<textarea id="ajax_return_txt" style="width:100%;height:200px;border:1px solid #EEE;background-color:#F7F7F7"></textarea>
</div>

<!--제휴사 로고 시작-->
<div id="client_logo_wrap">
	<div class="client_logo_zone">
		<ul class="client_list">
			<li><img src="/images/main/partner_logo/09.jpg" alt="신한은행"></li>
			<li ><img src="/images/main/partner_logo/08.jpg"  alt="한국p2p금융협회"></li>
			<li><img src="/images/main/partner_logo/05.jpg" alt="하나자산신탁"></li>
			<!--li><img src="/images/main/partner_logo/02.jpg" alt="세틀뱅크"></li-->
			<li><img src="/images/main/partner_logo/04.jpg" alt="서울신용평가"></li>
			<li><img src="/images/main/partner_logo/10.jpg" alt="한국부동산리츠투자자문협회"></li>
			<li><img src="/images/main/partner_logo/11.jpg" alt="교보리얼코"></li>

		</ul>
	</div>
</div>
<!--제휴사 로고 끝-->

<!-- 하단 시작 -->
<footer>
	<div id="foot_wrap">
		<div id="foot">
			<ul class="footlink">
				<li><a href="/company.php">헬로펀딩소개</a></li>
				<li><a href="<?=G5_BBS_URL?>/content.php?co_id=provision">이용약관</a></li>
				<li><a href="<?=G5_BBS_URL?>/content.php?co_id=privacy">개인정보처리방침</a></li>
				<li><a href="<?=G5_BBS_URL?>/content.php?co_id=provision2">투자이용약관</a></li>
				<li><a href="<?=G5_URL?>/reserve/contact_email.php">제휴문의</a></li>
			</ul>
			<div class="info">

				<div class="comp1">
					<address>
					<span class="fintech">(주)헬로핀테크</span><br><br>
					서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 4층<br>
					E-mail : hellofunding@gmail.com<br><br>
					헬로펀딩은 투자원금과 수익을 보장하지 않으며, <br>투자손실에 대한 책임은 모두 투자자에게 있습니다.<br>
					</address>
					<ul class="sns2">
						<li><a href="https://www.facebook.com/hellofunding" target="_blank"><img src="<?=G5_THEME_URL?>/img/facebook_icon.png" alt="Facebook"></a></li>
						<li><a href="http://blog.naver.com/hellofunding" target="_blank"><img src="<?=G5_THEME_URL?>/img/blog_icon.png" alt="Naver"></a></li>
						<li><a href="https://story.kakao.com/ch/hellofunding" target="_blank"><img src="<?=G5_THEME_URL?>/img/kakao_icon.png" alt="KakaoStory"></a></li>
						<li><a href="https://www.instagram.com/hellofunding" target="_blank"><img src="<?=G5_THEME_URL?>/img/insta_icon.png" alt="instagram"></a></li>
						<li><a href="#"><img src="<?=G5_THEME_URL?>/img/call_icon.png" alt="call"></a></li>
						<li><a href="javascript:;" onClick="alert('모바일로 접속시 지원하는 기능입니다.');"><img src="<?=G5_THEME_URL?>/img/yellow_icon.png" alt="Yellow ID hellopunding" width="80%"></a></li>
					</ul>

				</div>
				<div class="comp2">
					<address>
					<span class="flatform">플랫폼 사업자</span><br><br>
					플랫폼 사업자 : (주)헬로핀테크<br>
					대표자 : 남기중 | 사업자 등록번호 : 789-81-00529<br>
					<?=( preg_match('/(^\/index\.php|^\/loan\/loan\.php)/', $_SERVER['PHP_SELF']) ) ? '' : 'Copyright ⓒ hellofunding All Rights reserved.'; ?>
					</address>

					<ul>
						<li><a href="http://p2plending.or.kr/" target="_blank"><img src="<?=G5_THEME_URL?>/img/b_logo01.png" width="90%"></a></li>
						<li><a href="http://p2plending.or.kr/page_msgp52" target="_blank"><img src="<?=G5_THEME_URL?>/img/b_logo02.png" ></a></li>
					</ul>


				</div>
				<div class="comp4">
					<div class="opentime">대표전화 : 1588-6760<br><br></div>
					<ul>
						<li>업무시간 안내 <img src="<?=G5_THEME_URL?>/img/down_icon.png" style="padding:8px 0 0 3px;"></li>
						<li>평일 : 10시~19시   점심시간 :  13시~14시</li>
						<li>토.일.공휴일 : 휴무</li>
					</ul>

				</div>
<? if( preg_match('/(^\/index\.php|^\/loan\/loan\.php)/', $_SERVER['PHP_SELF']) ) { ?>
				<div class="comp3">
					<address>
					<span class="crowd">(주)헬로크라우드대부</span><br><br>
					대표자 : 김동일 | 사업자등록번호 : 610-87-00472 | 등록번호 : 2018-금감원-1347(P2P연계대부업) | 등록기관 : 금융감독원<br>
					주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 4층 | 대표전화 : 1588-6760<br><br>
					<span class="crowd_info">
						■ 대부이자율(연 이자율)및 연체이자율은 연24%를 초과할 수 없습니다<br>
						■ 대출금리 연19.9% 이내 플랫폼 이용료 외 중개수수료 등 기타 부대비용 없습니다.<br>
						■ 채무의 조기상환 수수료율등 조기상환 조건이 없습니다.<br>
						■ 중개수수료를 요구하거나 받는 행위는 불법입니다. 과도한 빚은 당신에게 큰 불행을 안겨줄 수 있습니다.<br>
						■ 대출시 귀하의 신용등급이 하락할 수 있습니다.<br><br>
					</span>
					㈜신한은행은 제3자 예치금 신탁은행으로서 신탁계약에서 정해진 업무 범위 내에서 예치금을 보관 및 관리하며, <br>직접 대출 취급, 대출 상환 및 대출 관리나 회수 의무를 부담하지 않습니다.<br><br>
					Copyright ⓒ hellofunding All Rights reserved.
					</address>
				</div>
<? } ?>
			</div>
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


<? if(G5_DEVICE_BUTTON_DISPLAY && !G5_IS_MOBILE && preg_match("/220\.117\.134/", $_SERVER['REMOTE_ADDR'])) { ?>
<!--a href="<? echo get_device_change_url(); ?>" id="device_change">모바일 버전으로 보기</a-->
<? } ?>

<?

if ($config['cf_analytics']) { echo $config['cf_analytics']; }


include_once(G5_THEME_PATH."/tail.sub.php");

?>