<?

//2017-04-24 : 개인회원 상품별 금액 제한 관련 내용 추가

include_once("_common.php");

if($is_member) {

	if($member['mb_id']=='fintech01') $special_print_name = "NH투자증권<br>(피델리스 Fin Tech<br>전문투자형 사모투자신탁<br>제1호 신탁업자 지위)";
	if($member['mb_id']=='fintech02') $special_print_name = "NH투자증권<br>(피델리스 Fin Tech<br>전문투자형 사모투자신탁<br>제2호 신탁업자 지위)";
	if($member['mb_id']=='fintech03') $special_print_name = "NH투자증권<br>(피델리스 대신 P2P<br>전문투자형 사모투자신탁<br>제1호 신탁업자 지위)";
	if($member['mb_id']=='fintech04') $special_print_name = "피델리스 P2P<br>전문투자형사모투자신탁 제1호";
	if($member['mb_id']=='fintech05') $special_print_name = "피델리스 핀테크인컴<br>전문투자형 사모투자신탁 제1호";

	if($member['member_type']=='1') {
		$print_mb_name = "<a>".$member["mb_name"]."</a>";
		$invest_possible_amount = (in_array($member['member_investor_type'], array('1','2'))) ? price_cutting($member['invest_possible_amount'])."원" : "제한 없음";
		$invest_possible_amount_prpt = (in_array($member['member_investor_type'], array('1'))) ? price_cutting($member['invest_possible_amount_prpt'])."원" : "제한 없음";
	}
	else {
		$print_mb_name = "<a>".$member["mb_co_name"]."</a>";
		$invest_possible_amount = "제한 없음";
		$invest_possible_amount_prpt = "제한 없음";
	}

	if($member['bank_code'] && $member['account_num'] && $member['va_bank_code2'] && $member['virtual_account2']) $bank_ok = true;

}

?>
<div class="navDiv" id="navDiv">
	<nav id="aside" class="menu" style="background-color:#fff">
		<article style="overflow-y:scroll; max-height:100%;">
			<ul>
				<li class="menu_top">
					<p>당신의 설레는 내일 헬로펀딩</p>
					<p class="btn_close" style="margin:-18px -13px; width:20px; height:19px; padding:18px 10px"><img src="<?=G5_THEME_URL?>/img/mobile/close_btn01.png"></p>
				</li>
			</ul>

			<div class="member">
<?
	if($is_member) {
		if($member['mb_level'] > 8) {
?>
				<div style="border-bottom:1px solid #adbcde;padding-bottom:20px;text-align:left;">
					<p style="float:left;margin:0 0 10px 10px;"><span style="font-size:14px;color:#1c3f9d;"><b style="font-size:16px;"><?=$member["mb_name"]?></b></span>님</p>
				</div>
<?
		}
		else {

			$badge_image_url = "/images/main/badge" . $member['member_type'];
			$badge_image_url.= ($member['member_type']=='1') ? $member['member_investor_type'] : "";
			$badge_image_url.= "_m.jpg";

?>
				<div style="border-bottom:1px solid #adbcde;padding-bottom:20px;text-align:left;">
					<p>
						<p id="pstatus_open1" style="float:left;margin:0 0 10px 10px;">
							<span style="font-size:14px;color:#1c3f9d;">
								<? if($special_print_name) { ?>
								<b style="font-size:13px;line-height:18px;"><?=$special_print_name?></b><br>
								<? } else { ?>
								<b style="font-size:16px;"><?=$print_mb_name?></b></span>님<br>
								<? } ?>
							</span>
							<img src="<?=$badge_image_url?>" height="24">
						</p>
						<p onClick="location.href='<?=G5_BBS_URL?>/member_confirm.php?url=/mypage/mypage.php';" style="float:right;color:#fff;background-color:#05c5b0;padding:5px 0;margin:10px 10px;width:80px;text-align:center;border-radius:3px;font-family:'NG';">회원정보</p>
					</p>
					<p id="pstatus_open2" style="clear:both;color:#3366FF;background-color:#EFEFEF;border-radius:3px; margin:0 10px; padding:8px 10px; font-size:13px; font-family:'NGB';">
						<span style="width:22%;display:inline-block;">예치금.</span>
						<span style="width:63%;display:inline-block;text-align:right;"><?=number_format($member['mb_point'])?>원</span>
						<span style="width:10%;display:inline-block;text-align:right;"><img src="<?=G5_THEME_URL?>/img/mobile/down_arrow01.png" width="12"></span>
					</p>
				</div>

				<div id="pstatus_zone" >
					<p class="my_info">내 투자정보</p>
					<div class="my_info_cont">
						<ul>
							<li class="invest_info1">
								<img src="/images/main/icon02_m.jpg" height="32"><br>
								투자잔액<br>
								<?=price_cutting($member['ing_invest_amount'])?>원
							</li>
							<li class="invest_info2">
								<!--<img src="/images/main/icon03_m.jpg" height="30"><br><br>-->
								<span class="invest_total">
									총 투자가능한도액<br>
									<?=$invest_possible_amount?>
								</span>
								<span class="pf_invest">
<? if($member['member_type']=='1' && $member['member_investor_type']=='1') { ?>
									부동산 상품 <span class="flag_btn" id="btn3">?</span><br>
									투자가능한도액<br>
									<?=$invest_possible_amount_prpt?>
<? } else { ?>
									<br><br><br>
<? } ?>
								</span>
							</li>

							<div id="conts3" style="width:82%;position:absolute;margin:106px 8px 0;padding:15px 15px;border-radius:10px;background-color:#000;color:#fff;font-size:13px;line-height:22px;text-align:left;z-index:150;opacity:0.9;display:none;">
								[P2P대출 가이드라인에 의한 개인투자자의 투자한도액]<br>
								1. 총 투자한도액 : 2,000만원<br>
								단, 부동산 상품(PF, 부동산 담보 등)은 1,000만원까지 투자 가능<br>
								<div id="close3" style="position:absolute;right:0;top:105px;margin:5px 5px 0 0;font-size:11px;font-family:'verdana';cursor:pointer;width:18px;height:18px;border:1px solid #fff;text-align:center;line-height:18px;color:#fff;">x</div>
							</div>
							<script type="text/javascript">
							$('#btn3, #close3').on('click', function() { $('#conts3').fadeToggle('slow'); });
							</script>

							<div style="display:inline-block;text-align:center;">
								<p style="float:left;"><a href="/deposit/deposit.php"><span class="invest_list_btn">투자내역</span></a></p>
								<p style="float:left;"><a href="/deposit/deposit.php?tab=4"><span class="invest_schedule">투자스케쥴러</span></a></p>
								<p style="clear:both;padding-top:1px;"><a href="/deposit/deposit.php?tab=5"><span class="invest_btn3">자동투자설정</span></a></p>
							</div>
						</li>

<? if($bank_ok) { ?>
						<li style="height:50px;margin:5px 0 0 10px;padding:0;">
							<p style="font:12px NG; color:#777;">⊙ 원리금 수취방식</p>
							<p class="rcv_info" style="text-align:left;">
								<span style="padding:0;float:left;"><?=($member['receive_method']=='2')?'예치금 상환':'환급계좌 상환'?></span>
								<span style="padding:0;float:right;"><button type="button" class="invest_btn4" onClick="location.href='<?=G5_BBS_URL?>/member_confirm.php?url=/mypage/mypage.php#bank_edit';">설정변경</button></span>
							</p>
						</li>

						<li style="height:50px;margin:5px 0 0 10px;padding:0;">
							<p style="font:12px NG; color:#777;">⊙ 예치금 가상계좌</p>
							<p class="count_info" style="text-align:left;">
								<span class="count_info_txt"><?=$BANK[$member['va_bank_code2']]?> <?=$member['virtual_account2']?>
								<!--<?=$member['va_private_name2']?>--></span>
							</p>
						</li>
						<li style="height:33px;margin:6px 0; padding:0 0 0 8px; text-align:left"><button type="button" class="btn_default" onClick="location.href='/deposit/deposit.php?tab=2'" style="width:231px;font-family:'NGB'">예치금 출금</button></li>
<? } ?>

					</div>
				</div>

<?
		}
	}
?>

				<ul style="clear:both;">
					<li class="btmborder" onClick="location.href='/company.php'">
						<p>회사소개</p>
						<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png"></p>
					</li>
					<li class="btmborder" onClick="location.href='/investment/invest_list.php'">
						<p>투자상품보기</p>
						<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png"></p>
					</li>
					<li class="btmborder" onClick="location.href='/loan/loan.php'">
						<p>대출신청하기</p>
						<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png"></p>
					</li>
					<li class="btmborder" onClick="location.href='/investment/guide.php'">
						<p>투자방법안내</p>
						<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png"></p>
					</li>
					<li class="btmborder s_menu_tit">
						<p>이용안내</p>
						<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png"></p>
					</li>
					<div class="s_menu_down">
						<p onClick="location.href='/bbs/board.php?bo_table=notice'">공지사항</p>
						<p onClick="location.href='/bbs/epilogue.php'">투자후기</p>
						<p onClick="location.href='/bbs/funding_story.php'">펀딩디자이너 스토리</p>
						<p onClick="location.href='/news/funding_news.php'">헬로펀딩 스토리</p>
						<p onClick="location.href='/bbs/faq.php?fm_id=1'">도움말</p>
					</div>
<?
	if ($is_admin) {
?>
					<li onClick="location.href='<?=G5_ADMIN_URL?>'">
						<p>관리자페이지</p>
						<p><img src="<?=G5_THEME_URL?>/img/mobile/right_arrow01.png"></p>
					</li>
<?
	}
?>
				</ul>

<?
	if($is_member) {
?>
				<div style="width:100%;margin:0 auto;text-align:center;margin:25px 0 20px 0;">
					<a href="<?=G5_BBS_URL?>/logout.php" target="_self" class="btn_gray" style="font-size:14px;color:#fff;width:90%;">로그아웃</a>
				</div>
<?
	}
	else {
?>
				<div style="height:15px;"></div>
				<span>헬로펀딩</span>에 로그인 하시면<br> 다양한 혜택을 누리실 수 있습니다.
				<div class="l_btn">
					<p><img id="left_login" src="<?=G5_THEME_URL?>/img/mobile/left_login.png"  alt="로그인" ></p>
					<p onClick="location.href='/bbs/register_choice.php';" style="cursor:pointer"><img src="<?=G5_THEME_URL?>/img/mobile/left_join.png" alt="회원가입"></a></p>
				</div>
<?
	}
?>
			</div>
			<div class="menu_end"></div>
		</article>
	</nav>
</div>

<div id="mask"></div>

<script>
$('#pstatus_open1, #pstatus_open2').click(function() {
	$('#pstatus_zone').slideToggle();
});

$(".s_menu_tit").click(function(){
	$(".s_menu_down").slideToggle();
});

$("#s_menu_tit2").click(function(){
	$("#s_menu_down2").slideToggle();
});

$('#aside_btn, .btn_menu').click(function() {
  $('.menu').css('overflow-y', 'hidden');
  $('.menu').css('display', 'block');
  $('.menu').css('position', 'fixed');
  $('body').css('overflow-y', 'hidden');
  $('body').css('position', 'relative');
  $('body').css('height', 'auto');
  $('body').bind('touchmove', function(e) {
     e.preventDefault()
  });
  $('.menu').on('touchmove touchstart', function (e) {
       e.stopPropagation();
   });
});
$('.btn_close, #mask').click(function() {
	$('body').css('overflow-y', 'scroll');
  $('body').css('display', 'block');
  $('body').css('overflow-y', 'scroll');
  $('body').css('position', 'static');
  $('body').css('height', 'auto');
  $('body').unbind('touchmove');
//$('#pstatus_zone, .s_menu_down').hide();
});
</script>
<style type="text/css">
#mask {width:100%; position:absolute; left:0; top:0px; z-index:900; background-color:#000;opacity: 0.6;display:none;}
/*
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
*/
</style>

<!--
<script type="text/javascript" src="<?/*=G5_THEME_JS_URL*/?>/iscroll.js"></script>
<script type="text/javascript">
	var myScroll;
	function loaded() {
        myScroll = new IScroll('.contents');
	}
	document.addEventListener('DOMContentLoaded', loaded, false);
</script>
//-->

<script type="text/javascript">
var cliHeight = document.documentElement.clientHeight;

$(document).ready(function(){
	$('#aside_btn').click(function(){
		navOpen();
		/*var myScroll = new IScroll('#navDiv', {
			scrollbars: true,
			mouseWheel: false,
			interactiveScrollbars: false,
			shrinkScrollbars: 'scale',
			fadeScrollbars: true,
			bounce:false,
			hideScrollbar:true,
		});*/
		$('body').bind('touchmove');
	});
	$('.btn_close').click(function(){
		navClose();
		$('body').unbind('touchmove');
	});
	$('#mask').click(function(){
		navClose();
		$('body').unbind('touchmove');
	});
	$('#navScroll').css("height", cliHeight);
});

function navOpen(){
	var maskHeight = $(document).height();
	var maskWidth = $('body').width();
	var Windowtop = $('#head_top').scrollTop();
	$('#aside').css({'top':Windowtop-0, 'box-shadow': '2px 0 8px rgba(0,0,0,.7)'}).animate({left:'0px'},'100');
	$('#mask').fadeIn('slow').css({'width':maskWidth,'height':maskHeight,'display':'block'});
}

function navClose(){
	var maskHeight = $(document).height();
	var maskWidth = $('body').width();
	var Windowtop = $('#head_top').scrollTop();
	var asideTop = $('#aside').css("top");
	$('#aside').css({'top':asideTop, 'box-shadow':''}).animate({left:'-250px'},'100');
	$('#mask').fadeOut("slow");
}
</script>

<!-- 메뉴 슬라이드 끝 -->
