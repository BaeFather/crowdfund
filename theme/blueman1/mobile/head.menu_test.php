<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<!-- 메뉴 슬라이드 시작 -->
<div class="navDiv" id="navDiv" style="border:2px solid red">
	<nav id="aside" class="menu">
		<ul>
			<li class="menu_top">
				<p>당신의 설레는 내일 <b>헬로펀딩</b></p>
				<p><a href="javascript:void(0);" class="btn_close" ><img src="<?=G5_THEME_URL?>/img/mobile/close_btn01.png" alt="" /></a></p>
			</li>
			<li onClick="location.href='/investment/invest_list.php'">
				<p>투자상품보기</p>
				<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png" /></p>
			</li>
			<li onClick="location.href='/loan/loan.php'">
				<p>대출신청하기</p>
				<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png" /></p>
			</li>
			<li onClick="location.href='/investment/guide.php'">
				<p>투자방법보기</p>
				<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png" /></p>
			</li>
			<li class="s_menu_tit">
				<p>이용안내</p>
				<p class="s_menu"><img src="<?=G5_THEME_URL?>/img/mobile/down_arrow01.png" style="width:13px;padding-top:3px;" /></p>
			</li>
			<div class="s_menu_down" >
				<p onClick="location.href='/bbs/board.php?bo_table=notice'">공지사항</p>
				<p onClick="location.href='/news/funding_news.php'">헬로펀딩 스토리</p>
				<p onClick="location.href='/bbs/faq.php?fm_id=1'">도움말</p>
			</div>
			<? if ($is_admin) { ?>
			<li onClick="location.href='<?=G5_ADMIN_URL?>'">
				<p>관리자페이지</p>
				<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png" /></p>
			</li>
			<? } ?>
			<script>
			$(document).ready(function(){
				$(".s_menu_tit").click(function(){
					$(".s_menu_down").slideToggle();
				});
			});
			</script>
		</ul>

		<div class="member">
			<? if(!$is_member) { ?>
			<span>헬로펀딩</span>에 로그인 하시면<br/>
			다양한 혜택들 누리실 수 있습니다.
			<div class="l_btn">
				<p><img id="left_login" src="<?=G5_THEME_URL?>/img/mobile/left_login.png"  alt="로그인"  /></p>
				<p onClick="location.href='/bbs/register_choice.php';" style="cursor:pointer"><img src="<?=G5_THEME_URL?>/img/mobile/left_join.png" alt="회원가입"  /></a></p>
			</div>
			<? } else { ?>
			<span style="font-size:12pt;"><b><?=$member["mb_name"]?></b></span>님<br/>
			<div class="l_btn">
				<p onClick="location.href='<?=G5_BBS_URL?>/member_confirm.php?url=/mypage/mypage.php';"><span class="btn_green" style="width:80px;height:28px;line-height:28px;border-radius:3px;cursor:pointer">회원정보</span></p>
				<p onClick="location.href='<?=G5_URL?>/deposit/deposit.php'"><span class="btn_green" style="width:80px;height:28px;line-height:28px;border-radius:3px;cursor:pointer">투자내역</span></p>
				<br><br>
				<p><img id="left_login" src="<?=G5_THEME_URL?>/img/mobile/<?=($is_member)?'left_logout.png':'left_login.png';?>"  alt="<?=($is_member)?'로그아웃':'로그인';?>" /></p>
			</div>
			<? } ?>
		</div>
		<div class="menu_end"></div>
	</nav>
</div>

<div id="mask"></div>

<style type="text/css">
#mask {width:100%; position:absolute; left:0; top:0px; z-index:900; background-color:#000;opacity: 0.6;display:none;}
#navCate {z-index:1; width:100%;}
#navScroll{
	z-index:1;
	width:100%;
	overflow:hidden;
	-webkit-tap-highlight-color: rgba(0,0,0,0);
	-webkit-transform: translateZ(0);
	-moz-transform: translateZ(0);
	-ms-transform: translateZ(0);
	-o-transform: translateZ(0);
	transform: translateZ(0);
	-webkit-touch-callout: none;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	-webkit-text-size-adjust: none;
	-moz-text-size-adjust: none;
	-ms-text-size-adjust: none;
	-o-text-size-adjust: none;
	text-size-adjust: none;
}
</style>

<script type="text/javascript" src="<?=G5_THEME_JS_URL?>/iscroll.js"></script>
<script type="text/javascript">
var cliHeight = document.documentElement.clientHeight;
var handler = function(e){e.preventDefault();}
$(document).ready(function(){
	$('#aside_btn').click(function(){
		$('#navScroll').height(cliHeight-80);
		navOpen();
		var myScroll = new iScroll('navDiv', {
			scrollbars: false,
			mouseWheel: false,
			interactiveScrollbars: false,
			shrinkScrollbars: 'scale',
			fadeScrollbars: true,
			bounce:true,
			hideScrollbar:true
		});

		myScroll.scrollTo(0,0,1000);

		$('body').bind('touchmove', handler);
	});
	$('.btn_close').click(function(){
		navClose();
		$('body').unbind('touchmove', handler);
	});
	$('#mask').click(function(){
		navClose();
		$('body').unbind('touchmove', handler);
	});
	$('#navScroll').css("height", cliHeight);
});

function navOpen(){
	var maskHeight = $(document).height();
	var maskWidth = $('body').width();
	var Windowtop = $('body').scrollTop();
	$('#aside').css({'top':Windowtop-0, 'box-shadow': '2px 0 8px rgba(0,0,0,.7)'}).animate({left:'0px'},'100');
	$('#mask').fadeIn('slow').css({'width':maskWidth,'height':maskHeight,'display':'block'});
}

function navClose(){
	var maskHeight = $(document).height();
	var maskWidth = $('body').width();
	var Windowtop = $('body').scrollTop();
	var asideTop = $('#aside').css("top");
	$('#aside').css({'top':asideTop, 'box-shadow':''}).animate({left:'-250px'},'100');
	$('#mask').fadeOut("slow");
}

</script>

<!-- 메뉴 슬라이드 끝 -->