<header id="hd">
	<h1 id="hd_h1"><?=$g5['title']?></h1>
	<div class="to_content"><a href="#container">본문 바로가기</a></div>

<?
	if(defined('_INDEX_')) { // index에서만 실행
		include G5_MOBILE_PATH.'/newwin.inc.php'; // 팝업레이어
	}
	add_stylesheet('<link rel="stylesheet" href="/css/mobile.css?ab">', 0);
?>

	<div id="hd_wrapper">


		<div id="logo">
			<a href="<?=G5_URL?>"><img src="/img/main_m/logo.png" alt="<?=$config['cf_title']?>"></a>
		</div>

		<button type="button" id="gnb_open" class="hd_opener">
			<span class="sound_only2">메뉴</span>
		</button>

		<div id="gnb" class="hd_div">

			<div class="gnb_title">
				<span><a href="#" class="hd_closer"><img src="/img/main_m/btn_close.png" alt="close" width="15"/> 닫기</a></span>
			</div>

			<ul  class="cho_leftmenu">

				<li>투자상품보기
					<ul class="cl_m1">
						<li>- <a href="/investment/invest_list.php">전체</a></li>
						<li>- <a href="/investment/invest_list.php?CA=A">부동산</a></li>
						<li>- <a href="/investment/invest_list.php?CA=A2">주택담보</a></li>
						<li>- <a href="/investment/invest_list.php?CA=B">동산</a></li>
					</ul>
				</li>
				<li><a href="/investment/guide.php">투자방법안내</a></li>
				<li><a href="/etc/epilogue.php">투자후기</a></li>
				<li><a href="/etc/faq.php">도움말</a></li>
				<li><a href="/etc/question.php">문의하기</a></li>
			</ul>


			<div class="hd_sigin">
<?
function get_product_type($cat, $mor='') {
	if($cat=="1") {
		$prd_type = "동산";
	}
	else if($cat=="2") {
		$prd_type = ($mor=="1") ? "주택담보" : "부동산";
	}
	else if($cat=="3") {
		$prd_type = "확정매출채권";
	}
	else {
		$prd_type = "";
	}
	return $prd_type;
}

// 모집중인 상품 추출
$ing_sql = "
	SELECT
		A.idx, A.title, A.recruit_amount, A.category, A.mortgage_guarantees, A.advance_invest, A.advance_invest_ratio, A.invest_return, A.invest_period,
		SUM(B.amount) AS total_invest_amount,
		A.main_image, A.detail_image
	FROM
		cf_product A,
		( SELECT product_idx, amount FROM cf_product_invest WHERE invest_state='Y' ) B
	WHERE 1
		AND A.state='' AND A.display='Y' AND A.state='' AND isTest='' AND only_vip=''
		AND A.idx = B.product_idx
	GROUP BY
		A.idx HAVING A.recruit_amount > total_invest_amount";

$ing_res = sql_query($ing_sql);
$ing_cnt = sql_num_rows($ing_res);

if($ing_cnt) {
	?>
				<!--모집중상품-->
				<div class="together">모집중 상품</div>
					<div class="today_product">
						<div class="swiper-container s7">
							<div class="swiper-wrapper">
	<?
	for($i=0 ; $i<$ing_cnt ; $i++) {
		$ing_row = sql_fetch_array($ing_res);
		$cnt_ptn = preg_match('/\[(.*?)\]/', $ing_row['title'], $ho);
		$prd_type = get_product_type($ing_row['category'], $ing_row['mortgage_guarantees']);

		$tmp88 = getNumberArr($ing_row['recruit_amount']);
		$tmp88_amt = array('amount'=>$tmp88[0], 'unit'=>$tmp88[1]);
		?>
								<div class="swiper-slide content">
									<ul class="pro_back">
										<li>
											<a href="/investment/investment.php?prd_idx=<?=$ing_row['idx']?>">
											<img src="/data/product/<?=$ing_row['main_image']?>" width="100%" height="75px" alt="" /></a>
										</li>
										<li>
											<a href="/investment/investment.php?prd_idx=<?=$ing_row['idx']?>">
											<div class="t_p_num"><?=$ho[1]?></div>
											<div class="t_p_tag"><?=$prd_type?></div>
											<div class="t_p_percent">사전투자<?=number_format($ing_row['advance_invest_ratio'])?>%</div>
											<p><?=number_format($ing_row['invest_return'],1)?>% | <?=$ing_row['invest_period']?>개월 | <?=$tmp88_amt['amount']?><?=$tmp88_amt['unit']?>원</p>
											</a>
										</li>
									</ul>
									<!--
									<div class="clearfix"></div>
									<div class="naviga">
										<div class="swiper-button-next l-menu-button-next"></div>
										<div class="swiper-button-prev l-menu-button-prev"></div>
									</div>
									-->
								</div><!-- swiper-slide content -->
<?
	}
?>
							</div><!-- swiper-wrapper -->
						</div><!-- swiper-container s7 -->
					</div><!-- today_product -->
<?
}
?>

					<div class="call_numb">
						<p><span>1588-6760</span>
						<span>(평일 09:00 ~ 18:00)</span></p>
						<p class="m_sns_btn">
							<a href="https://blog.naver.com/hellofunding" target="_blank"><img src="/img/main_m/blog_icon01.png" alt="네이버 블로그"/></a>
							<a href="https://www.facebook.com/hellofunding" target="_blank"><img src="/img/main_m/facebook_icon01.png" alt="페이스북"/></a>
							<a href="https://story.kakao.com/ch/hellofunding" target="_blank"><img src="/img/main_m/kakao_icon_m01.png" alt="카카오톡"/></a>
							<!--a href="https://www.instagram.com/hellofunding" target="_blank"><img src="<?=G5_THEME_URL?>/img/main_m/insta_icon01.png" alt="인스타그램"/></a-->
						</p>
					</div>


<? if($is_member) { ?>
					<div class="btn_default">
						<a href="/member/logout.php">로그아웃</a>
					</div>
<? } else { ?>
					<!--p><strong>헬로펀딩</strong>에 로그인 하시면</p>
					<p>다양한 혜택을 누리실 수 있습니다.</p>
					<br/-->
					<div class="btn_default">
						<a href="/member/login.php">로그인</a>
					</div>
					<div class="btn_blue">
						<a href="/member/join_info.php">회원가입</a>
					</div>
<? } ?>
			</div>
		</div>

<? if($is_member) { ?>
		<button type="button" id="user_btn" class="hd_opener">
			<img src="/img/mobile/my_page_icon.png" alt="MyPage" width="22px"/>
			<span class="sound_only">사용자메뉴</span>
		</button>
		<div class="hd_div" id="user_menu">

			<div class="user_menu_title">
				<p>당신의 설레는 내일 헬로펀딩</p>
				<span><a href="#" class="hd_closer"><img src="/img/mobile/btn_close.png" alt="close" width="40"/></a></span>
			</div>

			<? require_once(HF_PATH."/head.menu.php"); // 외부 로그인 ?>

		</div>
<? } else { ?>
			<a href="/member/login.php" id="user_login">로그인</a>
<? } ?>

		<script type="text/javascript">
			$(function() {
				//폰트 크기 조정 위치 지정
				var font_resize_class = get_cookie("ck_font_resize_add_class");
				if( font_resize_class == 'ts_up' ) {
					$("#text_size button").removeClass("select");
					$("#size_def").addClass("select");
				}
				else if (font_resize_class == 'ts_up2') {
					$("#text_size button").removeClass("select");
					$("#size_up").addClass("select");
				}

				$(".hd_opener").on("click", function() {
					var $this = $(this);
					var $hd_layer = $this.next(".hd_div");

					if($hd_layer.is(":visible")) {
						$hd_layer.hide();
						// $hd_layer.animate({left:'-280px'}, 500);
						$this.find("span").text("메뉴열기");
					} else {
						var $hd_layer2 = $(".hd_div:visible");
						$hd_layer2.prev(".hd_opener").find("span").text("메뉴열기");
						$hd_layer2.hide();
						// $hd_layer.animate({left: '0'}, 500);
						$hd_layer.show();
						$this.find("span").text("닫기");
					}
				});

				// 내부 영역클릭 시 레이어닫기
				$("#container").on("click", function() {
					$(".hd_div").hide();
				});

				// 2차 메뉴열기
				$(".btn_gnb_op_1").click(function(e){
					if(!e.target.getAttribute("href") || e.target.getAttribute("href") == "javascript:;"){
						$(this).next().toggleClass("btn_gnb_cl").next(".gnb_2dul").slideToggle(300);
					}
				});
				$(".btn_gnb_op_2").click(function(){
					$(this).toggleClass("btn_gnb_cl").next(".gnb_2dul").slideToggle(300);
				});

				$(".hd_closer").on("click", function(e) {
					var idx = $(".hd_closer").index($(this));
					// $(".hd_div:visible").animate({left:'-280px'}, 500, function(){$(".hd_div:visible").hide();});
					$(".hd_div:visible").hide();
					$(".hd_opener:eq("+idx+")").find("span").text("메뉴열기");
					e.preventDefault();
				});
			});

			var hd = $("#hd_wrapper");
			$(document).on("scroll", function(e) {
				if($(this).scrollTop() > 50) {
					hd.addClass("fix-header");
				}
				else {
					hd.removeClass("fix-header");
				}
			});
		</script>

	</div>

</header>
<div id="wrapper">
	<div id="container" class="container">
		<? if (!defined("_INDEX_") && false) { ?><h2 id="container_title" class="top" title="<? echo get_text($g5['title']); ?>"><? echo get_head_title($g5['title']); ?></h2><? } ?>

<?
if(count($latestProductList) > 0 && false) {
?>
			<div class="today_product">
				<div class="t_wrap" style="transform: translate3d(0px, 0px, 0px);">
<?
	$category = "";
	foreach($latestProductList as $product) {
		if($product['category'] == '1') {
			$category = "동산";
		}
		else if($product['category'] == '2') {
			$category= ($product['mortgage_guarantees']) ? "주택담보대출" : "부동산";
		}
		else if($product['category'] == '3') {
			$category = "확정매출채권";
		}
?>
					<div class="t_p_wrap">
						<ul>
							<li>
								<a href="<? echo $product['detail_url'];?>">
								<? if($product['main_image']){ ?>
									<img src="<? echo $product['title_image_url'];?>" alt="<? echo $product['title'];?>" width="55" height="50" onerror="this.src=g5_url+'/shop/img/no_image.gif';"/>
								<? }else{ ?>
									<img src="/shop/img/no_image.gif" alt="<? echo $product['title'];?>" width="55" height="50"/>
								<? } ?>
								</a>
							</li>
							<li>
								<div>
									<span class="t_p_num"><? echo $product["number"];?></span> <span class="t_p_tag"><? echo $category;?></span><span class="t_p_percent">사전투자 <? echo (int)$product['advance_invest_ratio'];?>%</span>
								</div>
								<p><? echo (int)$product['invest_return'];?>% | <? echo $product['invest_period'].$product['invest_period_unit'];?> | <? echo ($product['recruit_amount']);?></p>
							</li>
						</ul>
					</div>
<?
	}
?>
				</div>
			</div>
<?
}
?>
