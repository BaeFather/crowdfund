<?
include_once('./_common.php');

include_once(G5_THEME_PATH.'/head.sub.php');

$strPost = ARRAY("price","rprice","si","gu","dong","rdo_apt","apt_name","apt_area","dong_num","ho_num","floor_num","apt_name2","pid");

FOR($i=0;$i<COUNT($strPost);$i++)
{
	${$strPost[$i]}  = $_POST[$strPost[$i]];
}

$dongArr = EXPLODE(",",$dong);
$apt_nameArr = EXPLODE(",",$apt_name);
$apt_areaArr = EXPLODE(",",$apt_area);
?>
<link href="css/loan.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="./aptloan.js?ver=2"></script>

<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->



<div id="content">
	<div id="web_loan">
		<div class="top_call">
			<ul>
				<li><a href="http://hellofunding.co.kr" target="_blank"><img src="img/logo.png"></a></li>
				<li>대출문의<span> 1588-5210</span></li>
			</ul>

		</div>

	</div>
	<div id="mobile_loan">
		<div class="top_call">
			<ul>
				<li><img src="img/logo.png"></li>
				<li class="cs_call"><a href="tel:1588-5210">대출문의 <span>1588-5210</span></a></li>
			</ul>
		</div>

	</div>
	<div id="loan">
		<div class="loan1">
			<h2>대출한도조회 및 대출 신청</h2>
			<p>한도조회는 신용등급에 영향을 주지 않습니다.</p>
		</div>


		<div class="result_loan">
			<div class="loan_form">
			<div class="loan_value">
		<?php IF($rdo_apt == "1") { ?>
		<?php IF(!$rprice || $rprice <= 0) { ?>

			<h3>대출 예상 한도 금액</h3>
			<p>대출 가능 한도가 없습니다.</p>
		<?php } ELSE { ?>
			<h3>대출 예상 한도 금액</h3>
			<p><?php ECHO price_cutting($rprice);?>원</p>
		<?php } ?>
		<?php } ELSE { ?>
			<h3>대출 예상 한도 금액</h3>
			<p>대출신청시 안내 가능합니다.</p>
		<?php } ?>
			</div>

		<?php IF($rdo_apt == "1") { ?>
		<?php IF(!$rprice || $rprice <= 0) { ?>
			<table>
				<tr>
					<th class="first">담보시세</th>
					<td class="first"><?php ECHO price_cutting($price);?>원 (KB시세 기준)</td>
				</tr>
				<tr>
					<th>가능금액</th>
					<td>0 원</td>
				</tr>
			</table>
		<?php } ELSE { ?>
			<table>
				<tr>
					<th class="first">담보시세</th>
					<td class="first"><?php ECHO price_cutting($price);?>원 (KB시세 기준)</td>
				</tr>
				<tr>
					<th>가능금액</th>
					<td><?php ECHO price_cutting($rprice);?>원</td>
				</tr>
				<tr>
					<th>연이자율</th>
					<td>6%~</td>
				</tr>
				<tr>
					<th class="last">대출기간</th>
					<td class="last">최소 1개월부터 ~ </td>
				</tr>
			</table>
			<span class="result_info">선순위 대출 및 기타 개인의 신용에 따라 대출 한도는 조정될 수 있습니다.</span>
		<?php } ?>
		<?php } ELSEIF($rdo_apt == "2") { ?>
			<table>
				<tr>
					<th class="first">담보시세</th>
					<td class="first">KB시세 확인중</td>
				</tr>
			</table>
		<?php } ?>



		<?php
			$strRequest = true;
			IF($rdo_apt == "1")
			{
				IF(!$rprice || $rprice <= 0)
				{
					$strRequest = false;
				}
			}
		?>
		<?php IF($strRequest == false) { ?>
		<div class="fail_loan4">
			<p class="text">
				회원님이 선택하신 아파트의 경우 대출가능 한도가 없는 것으로 확인됩니다.<br>
				<span>선순위 대출이 한도를 초과</span>하였거나, <span>대출승인 지역이 아닌 경우</span> 한도가 조회 되지 않습니다.<br>
				다시 조회를 원하시거나 내용 수정이 필요하신 경우 아래 버튼을 클릭해주세요.
			</p>
		</div>

		<div class="btn3">
			<a href="/aptloan/loan2.php" id="btn3">다시조회하기</a>
		</div>

		<?php } ELSE { ?>

		<form name="regfm" id="regfm">
		<input type="hidden"	name="kind"			value="save" />
		<input type="hidden"	name="si"			value="<?php ECHO $si;?>" />
		<input type="hidden"	name="gu"			value="<?php ECHO $gu;?>" />
		<input type="hidden"	name="dong"			value="<?php ECHO $dong;?>" />
		<input type="hidden"	name="apt_name"		value="<?php ECHO $apt_name;?>" />
		<input type="hidden"	name="apt_name2"	value="<?php ECHO $apt_name2;?>" />
		<input type="hidden"	name="apt_area"		value="<?php ECHO $apt_area;?>" />
		<input type="hidden"	name="price"		value="<?php ECHO $price;?>" />
		<input type="hidden"	name="dong_num"		value="<?php ECHO $dong_num;?>" />
		<input type="hidden"	name="ho_num"		value="<?php ECHO $ho_num;?>" />
		<input type="hidden"	name="floor_num"	value="<?php ECHO $floor_num;?>" />
		<input type="hidden"	name="rprice"		value="<?php ECHO $rprice;?>" />
		<input type="hidden"	name="rmount_or"	value="" />
		<input type="hidden"	name="rdo_apt"		value="<?php ECHO $rdo_apt;?>" />
		<input type="hidden"	name="pid"			value="<?php ECHO $pid;?>" />

		<div class="re_loan4">
			<?php IF($rdo_apt == "1") { ?>
			<p class="title">대출신청 <span class="info">*대출가능 금액은 <?php ECHO price_cutting($rprice);?>원입니다.</span></p>
			<?php } ?>
			<ul>
				<li class="td sum"><input class="loansum2" type="text" name="ramount" value="" placeholder="대출신청 금액을 입력해주세요" required itemname='대출신청 금액' OnKeyUp="fn_ramount(this.value);NumberFormatHan(this,'wamt_txt');"><div class="won">만원</div>
				<div style="text-align:right;padding-right:40px;" id="wamt_txt"></div>
				</li>
			</ul>
			<ul>
				<li><input class="name" type="text" name="rname" value="" required itemname='이름'  placeholder="이름을 입력해주세요"></li>
				<li><input class="tel" type="text" name="rphone" value="" placeholder="연락처를 '-' 없이 입력해주세요"  required itemname='연락처'  OnKeyUp="fn_check_number('rphone',this.value);"></li>
			</ul>
			<ul>
				<li class="re_label_check"><label><input type="checkbox" name="check01" id="check01" value="Y" required itemname='개인정보 수집 및 이용'><span>개인정보 수집 및 이용에 동의합니다.</span></label></li>
			</ul>
				</li>
			</ul>
		</div>

		<div class="btn2">
			<a href="javascript:void(0);" id="btn2" OnClick="check_request_form('regfm',event);">대출신청하기</a>
		</div>
		</form>
		<?php } ?>

		<div class="call">
			<ul>
				<li>
					아파트 담보대출상담이 필요하시면 언제든지 연락주세요!<br>
					<span>운영시간 (월 ~목 : 10시 - 19시 , 금 : 10시 - 17시)</span>
				</li>
				<li>
					<img src="img/call.png">
				</li>
				<li>
					1588-5210
				</li>

			</ul>
			</div>
			<div class="m_call">
			<ul>
				<li>
					상담이 필요하시면 언제든지 연락주세요!<br>
					<span>운영시간 (월 ~목 : 10시 - 19시 , 금 : 10시 - 17시)</span>
				</li>
				<li>
					<img src="img/call.png"><span>1588-5210</span>
				</li>

			</ul>
			</div>

			</div>
			</div>
		</div>

		<div id="footer">
			<div class="footer">
			 	<ul>
					<li>(주) 헬로핀테크</li>
					<li><img src="img/p2p_btn01.png"></li>
					<li>헬로펀딩은 한국P2P금융협회 회원사로 협회 규정을 준수하고 있습니다.</li>
				</ul>
				<p>대표 : 채영민&emsp;|&emsp;사업자번호 : 789-81-00529 <br>
				   주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 7층&emsp;|&emsp;상담문의 : 1588-5210<br>
					<br>
					대출금리 : 연19%이내, 연체 이자율 : 약정금리 +3% (법정최고금리 24%이내), <br>
					플랫폼 이용료, 법무비 등 부대비용은 추가될 수 있습니다. 중개수수료를 요구하거나 받는 행위는 불법입니다. <br>
					과도한 빚은 당신에게 큰 불행을 안겨 줄 수 있습니다. 대출 시 귀하의 신용등급이 하락할 수 있습니다.<br>
					<br>
					폭언, 성희롱 등 업무방해 행위는 형법 및 정보통신망법 등에 의해 처벌받을 수 있습니다. [′18.10.18 산업안전보건법 고객 응대 근로자 보호 조치 시행]
					<br><br>
					Copyright(c) 2016 HELLOFUNDING All rights reserved

				</p>
			</div>
		</div>

		<div id="m_footer">
			<div class="footer">
			 	<h4>(주) 헬로핀테크</h4>
				<ul>
					<li><img src="img/p2p_btn01.png"></li>
					<li>헬로펀딩은 한국P2P금융협회 회원사로<br>협회 규정을 준수하고 있습니다.</li>
				</ul>
				<p>대표 : 채영민&emsp;|&emsp;사업자번호 : 789-81-00529 <br>
				   주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 7층&emsp;|&emsp;<a href="tel:1588-5210">상담문의 : 1588-5210</a><br>
					<br>
					대출금리 : 연19%이내, 연체 이자율 : 약정금리 +3% (법정최고금리 24%이내), <br>
					플랫폼 이용료, 법무비 등 부대비용은 추가될 수 있습니다. 중개수수료를 요구하거나 받는 행위는 불법입니다.
					과도한 빚은 당신에게 큰 불행을 안겨 줄 수 있습니다. 대출 시 귀하의 신용등급이 하락할 수 있습니다.
					<br><br>
					폭언, 성희롱 등 업무방해 행위는 형법 및 정보통신망법 등에 의해 처벌받을 수 있습니다. [′18.10.18 산업안전보건법 고객 응대 근로자 보호 조치 시행]
					<br><br>
					Copyright(c) 2016 HELLOFUNDING All rights reserved
				</p>
			</div>
		</div>


</div>


