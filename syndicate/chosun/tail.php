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

	<!--우측 땅집고 광고배너 영역	-->
			<div class="ad_ban">
		<!--section style="padding:16px 0; color:#999999 ; font-size:14px ; text-align:right ; word-break:keep-all; ">
			<div>
			※본 페이지는 땅집고와 제휴된 <span style='color:black;'>헬로핀딩</span>에서 제공하는 서비스입니다.
			</div>
		</section-->
				<iframe src="http://realty.chosun.com/realty/dhtm/right_iframe.html" width="320" height="1100" frameborder="0" scrolling="no"></iframe>
			</div>
    <? } ?>





	</div><!-- class cho -->

	<? if(G5_IS_MOBILE) { ?>
	<!--하단내비 시작-->
		<button type="button" id="top_btn"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only">상단으로</span></button>
		<!--하단내비 끝-->
    <? } else { ?>

	<!--하단내비 시작-->
		<div></div>
		<!--하단내비 끝-->
    <? } ?>

	
	<div style="clear:both;"></div>
	<script type="text/javascript" src="http://realty.chosun.com/realty/resources/js/footer_pkg.js" charset="utf-8"></script>


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

<?php
include_once(HF_PATH."/tail.sub.php");
sql_close();
?>