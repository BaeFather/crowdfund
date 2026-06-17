<?php
include_once('./_common.php');

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

include_once('../../lib/function_prc.php');
include_once('../review.class.php');

$strReview = new strReviewClass();

?>
<link href="reviewers.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>
<script type="text/javascript">
	var mb = "<?php ECHO $member["mb_no"];?>";
</script>
<script type="text/javascript" src="../review.js?ver=1"></script>
<!-- 본문내용 START -->
<div id="content">

	<div id="review_event">
		<div class="header">
			<div class="date">진행중 이벤트<span>2020.09.01 ~ 종료시까지</span></div>
			<div class="title">언제나 옳은 선택, 헬로펀딩 투자! 회원님의 ‘투자스토리’를 기다립니다.
		</div>
</div>


		<div class="contents">
			<div class="banner"><img src="img/banners.jpg" alt="헬로리뷰어 이벤트"></div>
			<div class="m_banner"><img src="img/mobile_bs.jpg" alt="헬로리뷰어 이벤트"></div>
			<div class="desc">추천평 작성과 인터뷰 신청으로 투자스토리를 공유해주세요!<br>
			<span>커피쿠폰</span>과<span> 신세계상품권 10만원</span>을 드립니다.</div>



			<div class="info_title_1"></div>
			<ul>
				
				<li class="sub">참여기간</li>
				<li class="text">2020년 09월 01일 ~ 종료시까지</li>
				
				<li class="sub">참여대상</li>
				<li class="text">헬로펀딩에 최소 1건 이상 투자 이력이 있는 모든 투자자</li>


				<li class="sub">경품안내</li>
				<li class="text">추천평 작성 - <span>스타벅스 아메리카노 기프티콘</span><br>인터뷰 완료 - <span>신세계상품권 10만원</span></li>

				<li class="sub">지급방법</li>
				<li class="text mg-b30">추천평작성 - 작성월 익월 10일이내<span class="m_none" style="font-weight: 300"> 개별안내 및</span> 지급<br>인터뷰신청 - 인터뷰 완료 후 익월 10일이내<span class="m_none"  style="font-weight: 300"> 개별안내 및</span> 지급</li>
			</ul>


			<div class="info_title_2">참여방법</div>
			<p class="point">두가지 공유 방법 중 선택 참여와 두방법 모두 참여도 가능합니다.</p>
			<ul class="event1">
				<li class="tx1">선택 1. 추천평 작성</li>
				<li class="tx2">[회원님의 한마디가 헬로에겐 큰힘이 됩니다.]</li>
				<li class="tx3">정성껏 작성해 주신 분에 한해 스타벅스 아메리카노 커피 쿠폰을 보내드립니다.<br class="br">사진은<span class="m_none"> 얼굴이 가려지지 않은</span> 본인 정면사진만 인정됩니다.</li>
			</ul>
			<div class="bt1 btnServiceOpen2">추천평 작성하기</div>
			<ul class="event2">
				<li class="tx1">선택 2. 인터뷰 신청</li>
				<li class="tx2">[회원님과 헬로펀딩의 만남부터 현재까지! 헬로펀딩 스토리를 들려주세요.]</li>
				<li class="tx3">인터뷰 진행 시 동영상 촬영이 함께 진행됩니다.<br>양식에 맞춰 신청서를 작성해 주세요.<br class="br">인터뷰를 완료하신 투자자 분께는 신세계상품권 10만원을 드립니다. </li>
			</ul>
			<div class="bt2 btnServiceOpen1">인터뷰 신청하기</div>

		<div class="info_title_2">꼭! 읽어주세요. </div>

		<div class="notice">
			    <p class="notice dot color">진행 중인 타 이벤트와 중복 참여 가능합니다.</p>
				<p class="notice dot color">추천평, 인터뷰 신청 각 1회씩 참여 가능합니다.</p>
				<p class="notice dot color">후기 작성 시 마케팅 정보 활용에 동의 할 경우에만 경품이 지급됩니다.</p>
				<p class="notice dot color">다음과 같은 경우에는 참여 대상 및 당첨에서 제외 됩니다.</p>
				<ul class="notice_detail">
					<li>- 실제로 투자가 이루어지지 않은 경우</li>
					<li>- 투자 후기에 적합하지 않은 내용을 작성한 경우</li>
					<li>- 조건에 맞지 않는 이미지를 올린 경우</li>
					<li>- 헬로펀딩 회원이 아닌 경우</li>
					<li>- 회원가입 정보(이름,연락처등)가 부정확한 경우</li>
					<li>- 허위로 후기 작성을 하는 경우</li>
				</ul>
			    <p class="notice dot color">작성글과 인증 사진은 헬로펀딩 홈페이지 및 SNS채널의 마케팅 용도로 사용할 수 있습니다.</p>
				<p class="notice dot color">해당 이벤트는 당사 사정에 따라 조기 종료될 수 있음을 알려드립니다.</p>
		</div>

		<div class="bt3"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<div class="m_bt3"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/m_bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>
</div>


<!--추천평팝업-->
<div id="myModal" class="modal recom_pop">
	<form name="form1" id="form1" enctype="multipart/form-data">
	<input type="hidden" name="kind" value="s1">
		<div class="pop_cont">
			<div class="btn1 btnServiceClose2"><img src="img/x.png" alt=""></div>
			<p class="tt">헬로펀딩 <span>추천평 작성</span></p>
			<div class="req_list">
				<ul>
					<li>
						<ul class="recomB">
							<li class="th"><span class="mark"></span>선호상품</li>
							<li class="td">
							<?php ECHO fn_general_select("","",$strReview->fn_product(),"선호상품을 선택해주세요","sns"," class='my_sns' required itemname='선호상품'","");?>
							</li>
						</ul>
					</li>
					<li>
						<ul class="recomB">
							<li class="th"><span class="mark"></span>이미지</li>
							<li class="td filebox">
								<input class="upload-name" value="사진을 첨부해주세요" disabled="disabled">
								<label for="ex_filename">파일첨부</label>
								<input type="file" name="thumbnail" id="ex_filename" class="upload-hidden" required itemname='파일첨부'>
							</li>
						</ul>
						<p class="nt">※본인 정면 사진만 인정되며, 타인 사진 도용시 발생하는 책임은 본인에게 있습니다.</p>
					</li>

					<li>
						<ul class="recomC">
							<li class="th"><span class="mark"></span>추천평 작성</li>
							<li class="td"><textarea name="content" class="in_add" type="textarea" name="address" value="" placeholder="추천평은 최소 50자 이상, 최대 500자 이하까지 작성 가능합니다." required itemname="추천평"></textarea></li>
						</ul>
					</li>
					<li class="ck">
						<ul class="check">
							<li class="re_label_check"><label><input type="checkbox" name="check01" id="check01" value="Y" required itemname="개인정보 활용 및 마케팅 활용에 동의"><span>개인정보 활용 및 마케팅 활용에 동의합니다.</span></label></li>
						</ul>
					</li>
				</ul>
			</div>

			<div class="btn2">
			<button OnClick="check_review_w_form('form1',event);">작성완료</button>
			</div>
		</div>
	</form>
</div>



<!--인터뷰팝업-->
<div id="myModal1" class="modal recom_pop">
	<form name="form2" id="form2">
	<input type="hidden" name="mno" id="mno" />
	<input type="hidden" name="kind" value="s2">
		<div class="pop_cont">
			<div class="btn1 btnServiceClose1"><img src="img/x.png" alt=""></div>
			<p class="tt">헬로펀딩 <span>인터뷰 신청</span></p>
			<div class="req_list">
				<ul>
					<li class="interA">
						<ul>
							<li class="names"><span class="mark"></span><span id="name_area"></span></li>
							<li class="tel"><span class="mark"></span><span id="phone_area"></span></li>
						</ul>
					</li>

					<li class="interck">
						<ul class="check">
							<li class="re_label_check"><label><input type="checkbox" name="check01"  value="Y" required itemname="개인정보 활용 및 마케팅 활용에 동의합니다."><span>개인정보 활용 및 마케팅 활용에 동의합니다.</span></label></li>
							<li class="re_label_check"><label><input type="checkbox" name="check02"  value="Y" required itemname="동영상 및 사진촬영에 동의합니다."><span>동영상 및 사진촬영에 동의합니다.</span></label></li>
						</ul>
					</li>
					<p class="internt">※인터뷰 일정은 담당자를 통해 추후 협의 가능합니다.</p>
				</ul>
			</div>

			<div class="btn3">
			<button OnClick="check_review_w2_form('form2',event);">신청하기</button>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
$('.btnServiceOpen2').click(function() {
	$.ajax({
		type : 'POST',
		url : "../m_auth.php",
		dataType: 'json',
		success : function(data)
		{
			if(data.retcode == "OK")
			{
				$.blockUI({
					message: $('#myModal'),
					css: { width:'0px',height:'0px',border:'0px' }
				});
			}
			else if(data.retcode == "X" || data.retcode == "XX") {
				var stralert = decodeURIComponent(data.retalert);
				alert(stralert.replace("+"," "));
				if(data.retcode == "XX") {
					$("#loginform").attr("action","/bbs/login.php");
					$("#loginform").attr("method","post");
					$("#loginform").submit();
				}
			}
			return false;
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
			return false;
		}
	});
});

$('.btnServiceClose2').click(function(){
	$.unblockUI();
	return false;
});
</script>


<script type="text/javascript">
$('.btnServiceOpen1').click(function() {

	$.ajax({
		type : 'POST',
		url : "../m_auth.php",
		dataType: 'json',
		success : function(data)
		{
			if(data.retcode == "OK")
			{
				$("#name_area").html("성함 : "+data.retval["mb_name"]);
				$("#phone_area").html("연락처 : "+data.retval["mem_phone"]);
				$("#mno").val(data.retval["mem_no"]);

				$.blockUI({
					message: $('#myModal1'),
					css: { width:'0px',height:'0px',border:'0px' }
				});
			}
			else if(data.retcode == "X") {
				var stralert = decodeURIComponent(data.retalert);
				alert(stralert.replace("+"," "));
				$("#loginform").attr("action","/bbs/login.php");
				$("#loginform").attr("method","post");
				$("#loginform").submit();
			}
			return false;
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
			return false;
		}
	});
});

$('.btnServiceClose1').click(function(){
	$.unblockUI();
	return false;
});
</script>
<form name="loginform" id="loginform">
	<input type="hidden" name="login_url" value="<?php ECHO G5_URL."/review/review_event/"; ?>" />
</form>

<?php
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>