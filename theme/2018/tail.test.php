<?
if(!defined('_GNUBOARD_')) exit;





?>
</div>
<!-- } 콘텐츠 끝 -->

<div style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>;width:1150px; margin:10px auto 10px;">
	<!--일반 실행 확인 영역-->
	<? if(preg_match("/".$_SERVER['HTTP_HOST']."/", $_SERVER['HTTP_REFERER'])) { ?><iframe name="axFrame" style="width:100%;height:200px;"></iframe><? } ?>
	<!--ajax 실행 확인 영역-->
	<textarea id="ajax_return_txt" style="width:100%;height:200px;border:1px solid #EEE;background-color:#F7F7F7"></textarea>
</div>

<footer id="footer">
	<div class="foot_wrap">
		<ul class="company_info">
			<li><a href="<?=G5_URL?>/bbs/content.php?co_id=provision">이용약관</a></li>
			<li><a href="<?=G5_URL?>/bbs/content.php?co_id=privacy">개인정보처리방침</a></li>
			<li><a href="<?=G5_URL?>/bbs/content.php?co_id=provision2">투자이용약관</a></li>
			<li><a href="<?=G5_URL?>/company/codeofethics.php">윤리강령</a></li>
			<li class="sns_btn">
				<a href="https://www.facebook.com/hellofunding" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/new/face_icon.png" alt="<?=$g5['title']?> 페이스북으로 이동"></a>
				<a href="https://www.instagram.com/hellofunding" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/new/insta_icon.png" alt="<?=$g5['title']?> 인스타그램으로 이동"></a>
				<a href="https://blog.naver.com/hellofunding" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/new/blog_icon.png" alt="<?=$g5['title']?> 블로그로 이동"></a>
			</li>
		</ul>
	</div>
	<div class="foot_info">
		<div class="fintech">
			<ul>
				<li>
					<p class="cs">
						고객센터 : 1588-6760
						<a href="http://pf.kakao.com/_xgAdWu"><img src="<?=G5_THEME_IMG_URL?>/new/kakao.png" alt="<?=$g5['title']?> 플러스친구"></a>
					</p>
					<span>월 - 목 : 10시 - 19시   |  금 : 10시 - 17시  (점심 : 13시 - 14시)</span>
				</li>
				<li>
					<span class="p2p_btn">
						<a href="http://p2plending.or.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/new/p2p.png" alt="한국P2P금융협회" ></a>
					</span>
				</li>
			</ul>
			<div>
				<span class="comp01_tit">(주)헬로핀테크</span>
				<span class="comp01">대표 : 채영민 | 사업자번호 : 789-81-00529 | 주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 7층</span>
			</div>
		</div>

		<div class="loan">
			<span class="comp02_tit">(주)헬로크라우드대부</span>
			<span class="comp02">대표 : 채영민 | 금융감독원 등록번호 : 2018-금감원-1347(P2P연계대부업) | 주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 7층</span><br><br>
			<span class="comp02_cont">
			대출금리 연19.9% 이내(연체금리 연24% 이내) | 플랫폼 수수료, 법무비 등 부대비용이 발생할 수 있으며,<br>
			대출유형에 따라 중도상환수수료 등 조기상환 조건이 적용될 수 있습니다.<br>
			중개수수료를 요구하거나 받는 행위는 불법입니다. 대출시 귀하의 신용등급이 하락할 수 있습니다.<br>
			과도한 빚은 당신에게 큰 불행을 안겨줄 수 있습니다.<br><br>
			<span>헬로펀딩은 투자원금과 수익을 보장하지 않으며, 투자손실에 대한 책임은 모두 투자자에게 있습니다.</span>
			</span>
		</div>

	</div>
</footer>

<?
include_once(G5_PATH . "/popup/inc_biz_info_noti.php");
?>

<? if(defined('_INDEX_')) { ?>
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

if($config['cf_analytics']) { echo $config['cf_analytics']; }

include_once(G5_THEME_PATH."/tail.sub.test.php");


if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
	//print_rr($_SERVER, 'font-size:12px');
}

?>
