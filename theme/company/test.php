<!doctype html>
<html lang="ko">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="imagetoolbar" content="no">
		<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">
		<title>Hello Funding</title>
		<link rel="stylesheet" href="http://hellofunding.co.kr/theme/company/css/default.css">
		<!-- hellofunding 전용 START -->
		<link rel="stylesheet" type="text/css" href="http://hellofunding.co.kr/theme/company/css/layout.css">
		<!-- hellofunding 전용 E N D -->
		<!--[if lte IE 8]>
		<script src="http://hellofunding.co.kr/js/html5.js"></script>
		<![endif]-->
		<script>
			// 자바스크립트에서 사용하는 전역변수 선언
			var g5_url       = "http://hellofunding.co.kr";
			var g5_bbs_url   = "http://hellofunding.co.kr/bbs";
			var g5_is_member = "1";
			var g5_is_admin  = "super";
			var g5_is_mobile = "";
			var g5_bo_table  = "";
			var g5_sca       = "";
			var g5_editor    = "";
			var g5_cookie_domain = "";
			var g5_admin_url = "http://hellofunding.co.kr/adm";
		</script>
		<script type="text/javascript" src="http://hellofunding.co.kr/js/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="/js/jquery.bxslider.min.js"></script>
		<script src="http://hellofunding.co.kr/js/jquery.menu.js"></script>
		<script src="http://hellofunding.co.kr/js/common.js"></script>
		<script src="http://hellofunding.co.kr/js/wrest.js"></script>
	</head>
	<body class="body">
		<!-- 상단 시작 { -->
		<div id="wrap">
			<div id="header">
				<!-- 팝업레이어 시작 { -->
				<div id="hd_pop">
					<h2>팝업레이어 알림</h2>
					<span class="sound_only">팝업레이어 알림이 없습니다.</span>
				</div>
				<script>
					$(function() {
					    $(".hd_pops_reject").click(function() {
					        var id = $(this).attr('class').split(' ');
					        var ck_name = id[1];
					        var exp_time = parseInt(id[2]);
					        $("#"+id[1]).css("display", "none");
					        set_cookie(ck_name, 1, exp_time, g5_cookie_domain);
					    });
					    $('.hd_pops_close').click(function() {
					        var idb = $(this).attr('class').split(' ');
					        $('#'+idb[1]).css('display','none');
					    });
					    $("#hd").css("z-index", 1000);
					});
				</script>
				<!-- } 팝업레이어 끝 -->
				<div class="logo">
					<!--a href="http://hellofunding.co.kr"><img src="http://hellofunding.co.kr/theme/company/img/logo.jpg" alt="Hello Funding"></a-->
					<h1 class="logo"><a href="http://hellofunding.co.kr">HELLOFUNDING</a></h1>
				</div>
				<ul class="navi">
					<li>
						<a href="/bbs/content.php?co_id=company" target="_self" >헬로펀딩</a>
					</li>
					<li>
						<a href="#" target="_self" >투자하기</a>
					</li>
					<li>
						<a href="#" target="_self" >대출하기</a>
					</li>
					<li>
						<a href="/bbs/board.php?bo_table=notice" target="_self" >이용안내</a>
					</li>
					<li class="mem"><a href="http://hellofunding.co.kr/adm"><b>관리자</b></a></li>
					<li><a href="http://hellofunding.co.kr/bbs/logout.php">로그아웃</a></li>
				</ul>
				<div class="subMenu">
					<div>
						<img src="/images/sub01.jpg" alt="헬로펀딩" />
						<a href="/bbs/content.php?co_id=company">헬로펀딩</a>
						<ul class="sub">
							<li><a href="/bbs/content.php?co_id=company" target="_self">회사소개</a></li>
							<li><a href="/bbs/board.php?bo_table=funding_news" target="_self">헬로펀딩소식</a></li>
						</ul>
					</div>
					<div>
						<img src="/images/sub02.jpg" alt="투자하기" />
						<a href="#">투자하기</a>
						<ul class="sub">
						</ul>
					</div>
					<div>
						<img src="/images/sub03.jpg" alt="대출하기" />
						<a href="#">대출하기</a>
						<ul class="sub">
						</ul>
					</div>
					<div>
						<img src="/images/sub04.jpg" alt="이용안내" />
						<a href="/bbs/board.php?bo_table=notice">이용안내</a>
						<ul class="sub">
							<li><a href="/bbs/board.php?bo_table=notice" target="_self">공지사항</a></li>
							<li><a href="/bbs/content.php?co_id=funding_guide" target="_self">투자가이드</a></li>
							<li><a href="/bbs/faq.php?fm_id=1" target="_self">FAQ</a></li>
							<li><a href="/bbs/board.php?bo_table=qa" target="_self">Q&A</a></li>
							<li><a href="/bbs/qalist.php" target="_self">제휴문의</a></li>
						</ul>
					</div>
					<div>
						<img src="/images/sub05.jpg" alt="로그인" />
						<a href="http://hellofunding.co.kr/adm"><b>관리자</b></a>
					</div>
					<img src="/images/nav_close.png" alt="close" class="nav_close" />
				</div>
			</div>
			<script>
				$(document).ready(function(){
					Mainslider = $('.slider').bxSlider({
						mode:'fade',
						auto: true,
						pause: 3000,
						slideMargin: 0,
						controls:false,
						onSlideAfter: function(){
							// do mind-blowing JS stuff here
							Mainslider.startAuto();
						}
					});
					
					$('.navi').mouseenter(function(){
						$('.subMenu').slideDown('fast');
					});
				
					$('.nav_close').click(function(){
						$('.subMenu').slideUp('fast');
					});
				
				
				    var mouse_is_inside = false;
				
				    $('.subMenu').hover(function(){
				    mouse_is_inside = true;
				    }, function(){
				    mouse_is_inside = false;
				    });
				
				    $("body").mouseup(function(){
				    if( ! mouse_is_inside){
				    $('.subMenu').slideUp('fast');
				    }
				    });
				
				});
			</script>
			<!-- } 상단 끝 -->
			<div id="container" style=" min-height:685px;">
				<!-- MAIN START -->
				<!-- 비주얼 -->
				<div class="visual">
					<div class="visualArea">
						<ul class="slider">
							<li>
								<div class="promise">
									<div class="title">투자자의 안전을 위한 헬로펀딩의 첫번째 약속!</div>
									- 담보의 평가가치를 초과한 대출이 없는 금융 플랫폼을 만들겠습니다.
								</div>
							</li>
							<li>
								<div class="promise">
									<div class="title">투자자의 안전을 위한 헬로펀딩의 두번째 약속!</div>
									- 예치금, 투자금, 수익금의 흐름이 투명한 금융 플랫폼을 만들겠습니다.
								</div>
							</li>
							<li>
								<div class="promise">
									<div class="title">투자자의 안전을 위한 헬로펀딩의 세번째 약속!</div>
									- 해킹, 디도스 등에 대비한 보안시스템을 갖춘 안전한 금융 플랫폼을<br> 만들겠습니다.
								</div>
							</li>
						</ul>
					</div>
					<!-- 비주얼 하단내용 -->
					<ul class="info">
						<li>
							평균 수익률
							<div>12.82%</div>
						</li>
						<li>
							누적액
							<div>758,818,810원</div>
						</li>
						<li>
							누적 상환액
							<div>56,818,810원</div>
						</li>
						<li>
							부실액
							<div>0000%</div>
						</li>
					</ul>
				</div>
				<!-- 투자하기 -->
				<div class="rMenu">
					<div class="menu01">
						<h2>투자하기</h2>
						<div class="text">당신도 투자자가 될 수 있습니다.<br>헬로펀딩과 함께 하세요</div>
						<a href="#" class="btn_link">바로가기</a>
					</div>
					<!-- 대출하기 -->
					<div class="menu02">
						<h2>대출하기</h2>
						<div class="text">헬로펀딩을 통해 <br>투자자를 모집하세요.</div>
						<a href="#" class="btn_link">바로가기</a>
					</div>
					<!-- 이용안내 -->
					<div class="menu03">
						<h2>이용안내</h2>
						<div class="text">헬로펀딩에서 알려드립니다.</div>
						<a href="#" class="btn_link">바로가기</a>
					</div>
				</div>
				<!-- MAIN E N D -->
				<!--a href="#hd" id="top_btn">상단으로</a-->
			</div>
		</div>
		<div id="footer">
			<div class="footLogo">HELLOFUNDING</div>
			헬로펀딩
			<ul class="footLink">
				<li><a href="http://hellofunding.co.kr/bbs/content.php?co_id=provision">이용약관</a></li>
				<li><a href="http://hellofunding.co.kr/bbs/content.php?co_id=privacy">개인정보취급방침</a></li>
				<li><a href="http://hellofunding.co.kr/bbs/content.php?co_id=provision2">담보채권원리금수취권매매서비스약관</a></li>
			</ul>
			<div class="contact"><b>Contact Us</b>(주) 헬로핀테크     주소 : 서울시 강남구 선릉로87번길 8  7층 701호(역삼동,페넌트타워)     사업자 등록번호 : 789-81-00529     대표자 : 남기중,송재준     개인정보보호책임자 : ???</div>
			<ul class="SNS">
				<li><a href="#"><img src="/images/btn_face.gif" alt="facebook" /></a></li>
				<li><a href="#"><img src="/images/btn_naver.gif" alt="naver" /></a></li>
				<li><a href="#"><img src="/images/btn_k.gif" alt="k" /></a></li>
				<li><a href="#"><img src="/images/btn_call.gif" alt="call" /></a></li>
			</ul>
		</div>
		<!-- <div style='float:left; text-align:center;'>RUN TIME : 0.003741979598999<br></div> -->
		<!-- ie6,7에서 사이드뷰가 게시판 목록에서 아래 사이드뷰에 가려지는 현상 수정 -->
		<!--[if lte IE 7]>
		<script>
			$(function() {
			    var $sv_use = $(".sv_use");
			    var count = $sv_use.length;
			
			    $sv_use.each(function() {
			        $(this).css("z-index", count);
			        $(this).css("position", "relative");
			        count = count - 1;
			    });
			});
		</script>
		<![endif]-->
	</body>
</html>