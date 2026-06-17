<?
if(!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가





?>

	<div style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>;width:1150px; margin:10px auto 10px;">
		<!--일반 실행 확인 영역-->
		<? if(preg_match("/".$_SERVER['HTTP_HOST']."/", $_SERVER['HTTP_REFERER'])) { ?><iframe name="axFrame" style="width:100%;height:200px;"></iframe><? } ?>
		<!--ajax 실행 확인 영역-->
		<textarea id="ajax_return_txt" style="width:100%;height:200px;border:1px solid #EEE;background-color:#F7F7F7"></textarea>
	</div>

	<!--하단내비 시작-->
	<? if (preg_match('/IPHONE/i',$_SERVER['HTTP_USER_AGENT'])) {?>
	<div class="bottom_guide_iphone">
		<ul>
			<li onclick="history.back()"><img src="<?=G5_THEME_IMG_URL?>/mobile/left_btn.png" style="cursor:pointer" alt="이전"/></a></li>
			<li><a href="/investment/invest_list.php"><img src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img01.png" style="cursor:pointer" alt="투자상품보기"/></a></li>
			<li><img id="reqsms_btn" src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img04.png" style="cursor:pointer" alt="투자상품알림받기"/></li>
			<!--<li><a href="/loan/loan.php"><img src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img02.png" style="cursor:pointer" alt="대출신청하기"/></a></li>-->
			<!--<li><img id="quick_guide_btn" src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img03.png" style="cursor:pointer" alt="투자순서안내"/></li>-->
		</ul>
	</div>
	<? } else { ?>
	<div class="bottom_guide">
		<ul>
			<li>
				<a href="/investment/invest_list.php">투자상품 보기 <!--<img src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img01.png" style="cursor:pointer" alt="투자상품보기" />--></a>
			</li>
			<!--<li><a href="/loan/loan.php"><img src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img02.png" style="cursor:pointer" alt="대출신청하기"/></a></li>-->
			<!--<li><img id="quick_guide_btn" src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img03.png" style="cursor:pointer" alt="투자순서안내"/></li>-->
			<li>
				<span id="reqsms_btn">투자상품 알림받기</span>
				<!--<img id="reqsms_btn" src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img04.png" style="cursor:pointer" alt="투자상품알림받기"/> -->
			</li>
		</ul>
	</div>
	<? } ?>
	<!--하단내비 끝-->
</div>

<div id="new_ft">
	<div id="ft_copy">
		<div id="ft_company">
			<div class="foot_wrap">
				<ul class="company_info">
					<li><a href="<?=G5_URL?>/bbs/content.php?co_id=provision">이용약관</a></li>
					<li><a href="<?=G5_URL?>/bbs/content.php?co_id=privacy">개인정보처리방침</a></li>
					<li><a href="<?=G5_URL?>/bbs/content.php?co_id=provision2">투자이용약관</a></li>
					<li><a href="<?=G5_URL?>/company/codeofethics.php">윤리강령</a></li>
				</ul>
				<ul class="sns_btn">
					<li><a href="https://www.facebook.com/hellofunding" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/new/face_icon.png" alt="<?=$g5['title']?> 페이스북으로 이동"></a></li>
					<li><a href="https://www.instagram.com/hellofunding" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/new/insta_icon.png" alt="<?=$g5['title']?> 인스타그램으로 이동"></a></li>
					<li><a href="https://blog.naver.com/hellofunding" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/new/blog_icon.png" alt="<?=$g5['title']?> 블로그로 이동"></a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="ft_company_info">
		<div class="cs_info">
			<h3>고객센터 : 1588-6760 <a href="http://pf.kakao.com/_xgAdWu" target="_blank"><img src="<?=G5_THEME_URL?>/img/new/kakao.png" alt="카카오톡 친구추가"/></a></h3>
			<p>월 - 목 : 10시 - 19시 | 금 : 10시 - 17시 (점심 : 13시 - 14시)</p>
		</div>

		<div class="line"></div>

		<h3>(주)헬로핀테크</h3>
		<p>대표 : 채영민 | 사업자번호 : 789-81-00529 | 주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 7층</p>
		<br>

		<div class="clearfix"></div>

		<div>
			<h3>(주)헬로크라우드대부</h3>
			<p>대표 : 채영민 | 금융감독원 등록번호 : 2018-금감원-1347(P2P연계대부업) | 주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 7층</p>
			<span class="loan">
			대출금리 연19.9% 이내(연체금리 연24% 이내) | 플랫폼 수수료, 법무비 등 부대비용이 발생할 수 있으며, 대출유형에 따라 중도상환수수료 등 조기상환 조건이 적용될 수 있습니다.<br/>
			중개수수료를 요구하거나 받는 행위는 불법입니다. 대출시 귀하의 신용등급이 하락할 수 있습니다. 과도한 빚은 당신에게 큰 불행을 안겨줄 수 있습니다.<br/>
			</span><br/>
			헬로펀딩은 투자원금과 수익을 보장하지 않으며, 투자손실에 대한 책임은 모두 투자자에게 있습니다.
		</div>

		<div class="p2p_association">
			<ul>
				<li><a href="http://p2plending.or.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/new/p2p.png" alt="한국P2p금융협회"/></a></li>
			</ul>
		</div>
	</div>

<?
if( !preg_match("/(\/investment\/|\/member\/|\/bbs\/login\.php)/i", $_SERVER['PHP_SELF']) ) {
	if(false) {
?>
	<!-- 퀴즈이벤트배너 시작 //-->
	<div id="float_btn" ><a href="javascript:void(0);" onclick="showHide('event_ban');return false;"><img src="<?=G5_THEME_URL?>/img/main_m/float_btn01.gif" height="60"/></a></div>
	<div id="event_ban" style="display:none;">
		<p class="close_btn"><a href="javascript:void(0);" onclick="showHide('event_ban');return false;"><img src="<?=G5_THEME_URL?>/img/main_m/close_btn01.png"/></a></p>
		<a href="/event/quest_intro.php"><img src="<?=G5_THEME_URL?>/img/main_m/event_ban01.png" /></a>
	</div>
	<script type="text/javascript">
	var cc=0
	function showHide(id) {
		if(cc==1) {
			cc=0
			document.getElementById(id).style.display="none";
		} else {
			cc=1
			document.getElementById(id).style.display="block";
		}
	}
	</script>
	<!-- 퀴즈이벤트배너 끝 //-->
<?
	}
}
?>

	<!--<button type="button" id="top_btn"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only">상단으로</span></button>-->
</div>
<!-- 하단 끝 -->


<?
include_once(G5_PATH . "/popup/inc_biz_info_noti.php");
?>

<? if( defined('_INDEX_') ) { ?>
<script type="application/ld+json">
{
	"@context": "<?=$_SERVER['REQUEST_SCHEME']?>://schema.org",
	"@type": "Person",
	"name": "헬로펀딩",
	"url": "<?=G5_URL?>",
	"sameAs": [
		"https://www.facebook.com/hellofunding",
		"https://blog.naver.com/hellofunding",
		"https://www.instagram.com/hellofunding"
	]
}
</script>
<? } ?>

<?
/*
// 긴급공지
if( $member['mb_id']=='' && defined('_INDEX_') ) {
?>
<style>
#noticeDiv { disnotice:none; position:fixed; z-index:1000000; width:98%; top:15%; left:1%; padding:0; }
#noticeDiv div.closeArea { margin:0 0 8px auto; width:24px; height:24px; text-align:right; }
#noticeDiv div.noticeerArea { width:100%; height:320px; background:#000 }
</style>
<div id="noticeDiv" style="display:none;">
	<div class="closeArea" onClick="offNotice();"><img src="/images/cancel_w.png" height="24" style="opacity:1;" alt="취소"></div>
	<div class="imageArea"><img src="/images/system_notice_banking.png?ver=20191008" style="width:100%"></div>
</div>

<script>
function openNotice() {
	$.blockUI({
		message: $('#noticeDiv'),css:{ border:'0' }
	});
}
//setTimeout(function(){ openNotice(); }, 500);
function offNotice() {
	$.unblockUI(); return false;
}
</script>
<?
}
*/
?>

<?

//if($config['cf_analytics']) { echo $config['cf_analytics']; }

include_once(G5_THEME_PATH."/tail.sub.test.php");

if( defined('_INDEX_') ) {
	if( $_SERVER['REMOTE_ADDR']=='220.117.134.164' || $_SERVER['REMOTE_ADDR']=='211.248.149.48' ) {
		/*
		print_rr($_SERVER);
		echo $_SERVER['QUERY_STRING'];
		echo "<br><br>";
		echo "token = " . base64_decode($_GET[md5('token')]) . "<br><br>\n";
		echo "ver = " . base64_decode($_GET[md5('ver')]) . "<br><br>\n";
		echo "<br><br><br><br>\n";
		*/
		//include_once(G5_PATH."/popup/app_setup_please.php");
	}
}

?>