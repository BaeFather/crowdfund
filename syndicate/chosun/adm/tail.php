    <? if(G5_IS_MOBILE) { ?>
	<!--하단내비 시작-->
		<div class="bottom_guide">
			<ul>
				<li><a href="/investment/invest_list.php"><img src="/img/mobile/bottom_img01.png" style="cursor:pointer" alt="투자상품보기"/></a></li>
				<!--<li><a href="/loan/loan.php"><img src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img02.png" style="cursor:pointer" alt="대출신청하기"/></a></li>-->
				<!--<li><img id="quick_guide_btn" src="<?=G5_THEME_IMG_URL?>/mobile/bottom_img03.png" style="cursor:pointer" alt="투자순서안내"/></li>-->
				<li><img id="reqsms_btn" src="/img/mobile/bottom_img04.png" style="cursor:pointer" alt="투자상품알림받기"/></li>
			</ul>
		</div>
		<!--하단내비 끝-->
    <? } else { ?>

	<!--하단내비 시작-->
		<div></div>
		<!--하단내비 끝-->
    <? } ?>


	<? if(G5_IS_MOBILE) { ?>
		<div></div>
    <? } else { ?>


    <? } ?>







	<? if(G5_IS_MOBILE) { ?>
	<!--하단내비 시작-->
		<button type="button" id="top_btn"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only">상단으로</span></button>
		<!--하단내비 끝-->
    <? } else { ?>

	<!--하단내비 시작-->
		<div></div>
		<!--하단내비 끝-->
    <? } ?>

	<br/><br/><br/>
	<div style="clear:both;"></div>


<script type="text/javascript">
$( document ).ready( function() {

	// 폰트 리사이즈 쿠키있으면 실행
	font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));

	//상단고정
	if( $(".top").length ){
		var jbOffset = $(".top").offset();
		$(window).scroll( function() {
			if ( $(document).scrollTop() > jbOffset.top ) {
				$('.top').addClass('fixed');
			}
			else {
				$('.top').removeClass('fixed');
			}
		});
	}

	//상단으로
	$("#top_btn").on("click", function() {
		$("html, body").animate({scrollTop:0}, '500');
		return false;
	});

});
</script>
</div><!-- class container -->
<?
/*
                <div id="rightArea" class="right_aside_area" style="width:320px;float:left;display:inline;">
                    <div class="inner">
                        <!-- 우측 공통 영역 -->
                        <script type="text/javascript" src="http://news.chosun.com/realty/resources/js/realty_right_area.js" charset="utf-8"></script>
                    </div>
                    <!-- //inner -->
                </div>
                <!-- //right_aside_area -->
*/
?>
			<!--footer id="footer" class="footer">
                <script type="text/javascript" src="http://realty.chosun.com/realty/resources/js/footer_pkg.js" charset="utf-8"></script>
            </footer--><!--footer-->

<!-- } 콘텐츠 끝 -->

<?
if( $is_member && !$is_admin && !preg_match('/wowstar/i', $_COOKIE['PHPSESSID']) ) {

	////////////////////////////////////////////////////////////////////////
	// 가상계좌 미발급 알림 및 환급계좌설정용 팝업호출 (인덱스에서만 출력)
	////////////////////////////////////////////////////////////////////////
	if(preg_match("/\/index\.php|\/deposit\/deposit\.php|\/invest_list\.php/is", $_SERVER['PHP_SELF'])) {
		include_once(HF_PATH . "/popup/inc_bank_account.php");
	}

	/////////////////////////////////
	// 실시간 입금안내
	/////////////////////////////////
	//include_once(G5_PATH . "/popup/inc_deposit_check_insidebank.php");	// 인사이드뱅크 데이터 기준

}
?>



	<script type="text/javascript">
		// 레이어 오프
		$(document).on("click", "#no, #closeLayer, .close, .close_button", function(){
			$.unblockUI();
			return false;
		});

<?
$special_user = ($is_admin=='super' || in_array($member['mb_id'], array('akorea','yr4msp','hellosiesta','sori9th','hellofunding','test070'))) ? true : false;

if(!$special_user) {
?>
		$(document).keydown(function(e) {
			key = (e) ? e.keyCode : event.keyCode;
			if(key == 116) { return false; }
		});
<?
}
?>
	</script>

</body>
</html>

<? if($is_member) { ?>
<script type="text/javascript" src="<?=($_SERVER['REQUEST_SCHEME']=='https')?"https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js":"http://dmaps.daum.net/map_js_init/postcode.v2.js";?>"></script>
<? } ?>

<?php echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다. ?>

<?php
sql_close();
?>