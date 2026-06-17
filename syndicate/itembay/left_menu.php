		<ul class="cho_leftmenu">
			<li><span style="color:<?=(preg_match("/invest_list/i",$_SERVER['SCRIPT_NAME']))?'#3366FF':'#000000';?>">투자상품보기</span>
				<ul class="cl_m1">
					<li>- <a href="/investment/invest_list.php" style="color:<?=(preg_match("/invest_list/i",$_SERVER['SCRIPT_NAME']) && $CA=='')?'#3366FF':'#000000';?>">전체</a></li>
					<li>- <a href="/investment/invest_list.php?CA=A" style="color:<?=($CA=='A')?'#3366FF':'#000000';?>">부동산</a></li>
					<li>- <a href="/investment/invest_list.php?CA=A2" style="color:<?=($CA=='A2')?'#3366FF':'#000000';?>">주택담보</a></li>
					<!--<li>- <a href="/investment/invest_list.php?CA=B" style="color:<?=($CA=='B')?'#3366FF':'#000000';?>">동산</a></li>-->
					<li>- <a href="/investment/invest_list.php?CA=C" style="color:<?=($CA=='C')?'#3366FF':'#000000';?>">확정매출채권</a></li>
				</ul>
			</li>
			<li><a href="/investment/guide.php" style="color:<?=(preg_match("/\/guide.php/i", $_SERVER['SCRIPT_NAME']))?'#3366FF':'#000000';?>">투자방법안내</a></li>
			<li><a href="/etc/epilogue.php" style="color:<?=(preg_match("/\/epilogue.php/i", $_SERVER['SCRIPT_NAME']))?'#3366FF':'#000000';?>">투자후기</a></li>
			<li><a href="/etc/faq.php" style="color:<?=(preg_match("/\/faq.php/i", $_SERVER['SCRIPT_NAME']))?'#3366FF':'#000000';?>">도움말</a></li>
			<li><a href="/etc/question.php" style="color:<?=(preg_match("/\/question.php/i", $_SERVER['SCRIPT_NAME']))?'#3366FF':'#000000';?>">문의하기</a></li>

<?
	if(!$is_member) {
?>
			<li style="padding-top:10px;">
				<a href="/member/login.php" class="login_btn">로그인</a>
				<a href="/member/join_info.php" class="join_btn">회원가입</a>
			</li>
<?
	}
?>

<?
	if($is_member) {
?>
			<!--투자내역-->

			<div id="invest_info_zone" class="invest_zone">

				<div class="body">
					<p><label>나의 투자정보</label></p>
					<div class="header">
						<div class="deposit">
							<span class="pull-left"><strong>예치금</strong></span>
							<span class="pull-right"><strong><?=number_format($member['mb_point'])?>원</strong></span>
						</div>
					</div>

					<div class="invest_amount">
						<img src="/images/main/icon02.jpg" alt="투자잔액"><br/>
						<span>투자잔액</span>
						<span><?=price_cutting($member['ing_invest_amount'])?>원</span>
					</div>

					<div class="invest_amount_detail">
						<p>
						총 투자가능액 <br/>
						<?=$invest_possible_amount?>원
						</p>

						<p>
						부동산 상품 <span class="d_flag_btn" id="d_flag_btn">?</span><br/>
						투자가능액<br/>
						<?=$invest_possible_amount_prpt;?>원
						</p>

						<div id="d_flag" class="d_flag_description">
							[P2P대출 가이드라인에 의한 개인투자자의 투자한도액]<br>
							1. 총 투자한도액 :<br> 2,000만원<br>
							단, 부동산 상품(PF, 부동산 담보 등)은 1,000만원까지 투자 가능<br>
							<div id="d_flag_close" class="d_flag_close">x</div>
						</div>

					</div><!-- class invest_amount_detail -->


					<ul>
						<li>
							<a href="/deposit/deposit.php"><span class="invest_btn1">투자내역</span></a>
						</li>
						<li>
							<a href="/deposit/deposit.php?tab=4"><span class="invest_btn2">투자스케쥴러</span></a>
						</li>
						<li>
							<a href="/deposit/deposit.php?tab=5"><span class="invest_btn3">자동투자설정</span></a>
						</li>
					</ul>

				</div><!-- class body -->

				<div class="footer">
					<p>⊙ 원리금 수취방식</p>
					<div>
					<span><?=($member['receive_method']=='2')?'예치금 상환':'환급계좌 상환'?></span>
				</div><!-- class footer -->

				<button type="button" class="btn_default2 per100" onClick="location.href='/member/member_confirm.php?url=/mypage/mypage.php#bank_edit';">설정변경</button>
				<p>⊙ 예치금 가상계좌</p>
				<div>
					신한은행<br/>56212670605130
				</div>
				<button type="button" class="btn_default2 per100" onClick="location.href='/deposit/deposit.php?tab=2'">예치금 출금</button>
				<ul>
					<li>
						<a href="/member/member_confirm.php?url=/mypage/mypage.php"><span class="btn_green">회원정보</span></a>
					</li>
					<li>
						<a href="/member/logout.php"><span class="btn_blue">로그아웃</span></a>
					</li>
				</ul>
			</div><!-- #invest_info_zone -->
<?
	}
?>
		</ul><!-- .cho_leftmenu -->
