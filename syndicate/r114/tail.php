	</div><!-- class cho -->

	<? if(G5_IS_MOBILE && preg_match("/investment\//", $_SERVER['SCRIPT_NAME'])) { ?>
	<button type="button" id="top_btn"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only">상단으로</span></button>
	<script>
	//상단으로
	$("#top_btn").on("click", function() {
		$("html, body").animate({scrollTop:0}, '500');
		return false;
	});
	</script>
	<? } ?>

</div><!-- class container -->

<footer id="footer">
	<div class="foot_info">
		<div class="fintech">
			<ul class="fintech_ul">
				<li>
					<address>
						<span>고객센터 : 1588-6760  <a href="http://pf.kakao.com/_xgAdWu"><img src="<?=G5_THEME_IMG_URL?>/main/kakao_plus_btn02.png" alt="<?=$g5['title']?> 플러스친구"  style="padding-left:5px;"></a></span>
						(평일 : 10시~19시  |  점심시간 : 13시~14시 토.일.공휴일 : 휴무)<br>
					</address>
				</li>
				<li>
					<span class="p2p_btn">
						<a href="http://p2plending.or.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/p2p_btn01.png" alt="한국P2P금융협회" ></a>
						<span>헬로펀딩은 한국P2P금융협회 회원사로 협회 규정을 준수하고 있습니다.</span>
					</span>
				</li>
			</ul>
			<div class="fintech_div">
				<span class="comp01_tit">(주)헬로핀테크</span>
				<span class="comp01">대표 : 남기중 | 사업자번호 : 789-81-00529 | 주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 4층</span>
			</div>
		</div>

	</div>
</footer>

<script type="text/javascript">

var filter = "win16|win32|win64|mac";
var strDomain = "https://www.r114.com";
var intHeight = $('#content').height();
intHeight = parseInt(intHeight) + 300;

if(navigator.platform)
{
	if(0 > filter.indexOf(navigator.platform.toLowerCase()))
	{
		strDomain = "https://m.r114.com";
	} else {
		intHeight = parseInt(intHeight) + 600;
	}
}

function Size2Parent()
{
	parent.postMessage(intHeight,strDomain);
}

function Size2Parent2()
{
	intHeight = $('#content').height() + 300;
	parent.postMessage(intHeight,strDomain);
}

function Size2ParentSend()
{
	setTimeout(Size2Parent2,1000);
}

/* 상위프레임 으로 본 페이지 사이즈 전송 */
$(document).ready(function(){
	setTimeout(Size2Parent,1000);
});
</script>
<?
// 로그인 팝업
include_once(HF_PATH."/member/login.form.php");

include_once(HF_PATH."/tail.sub.php");

sql_close();
exit;

?>