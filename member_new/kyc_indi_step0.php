<?
include_once("_common.php");

if($member['mb_id']=='sori9th') {
	echo "<div style='text-align:center;font-size:12px;color:#333'>". $_SERVER['PHP_SELF'] . "</div>\n";
}

?>

<div class="confirm_form">

	<div class="top-title">
		<h2>본인확인</h2>
		<div class="page-num">
			<span class="active">&nbsp;</span>
		</div>
	</div>
	<hr/>

	<div class="frm-identify">
		<h3>신분증 선택</h3>
		<p>본인확인을 위해 촬영하거나 신분증 종류를 선택해주세요.</p>
		<ul class="card-box">
			<li id="jumin" onClick="selectIdCard('1')">
				<div class="img-bg">
					<img src="/theme/2018/img/member/id_card_img.png" alt="주민등록증" />
				</div>
				<p>주민등록증</p>
			</li>
			<li id="driver" onClick="selectIdCard('2')">
				<div class="img-bg">
					<img src="/theme/2018/img/member/driving_licence_img.png" alt="운전면허증" />
				</div>
				<p>운전면허증</p>
			</li>
		</ul>
		<ul class="btn-load-box btn-mg2">
			<li><button type="button" id="KYCNextButton" class="btn-next">다음</button></li>
			<li><button type="button" id="OCRBypass" onClick="noneOCRStart();" class="btn-none-auth">신분증 확인이 어려울 경우 본인 확인</button></li>
		</ul>
	</div>

</div>

<script>
// 신분증 선택
function selectIdCard(idcard_type) {
	$('#f_idType').val(idcard_type);
	if(idcard_type=='1') {
		<? if( date('YmdHi') >= '202209232000' && date('YmdHi') < '202209250700' ) { ?>
		msg = "정부24 점검으로 인해 주민등록증을 통한 본인확인이 불가능합니다.\n";
		msg+= "* 운전면허증은 정상 진행이 가능합니다.\n\n";
		msg+= "점검 시간 : 2022.09.23(금) 20:00 ~ 09.25(일) 07:00\n\n";
	//msg+= "자세한 내용은 공지사항을 참고하여 주세요.";
		alert(msg);
		<? } else { ?>
		$('#jumin').addClass('choice');
		$('#driver').removeClass('choice');
		<? } ?>
	}
	else {
		<? if( date('YmdHi') >= '202210062300' && date('YmdHi') < '202210070700' ) { ?>
		msg = "교통민원24 시스템 점검으로 인해 운전면허증을 통한 본인확인이 불가능합니다.\n";
		msg+= "* 주민등록증은 정상 진행이 가능합니다.\n\n";
		msg+= "점검 시간 : 2022.10.06(목) 23:00 ~ 10.07(금) 07:00\n\n";
	//msg+= "자세한 내용은 공지사항을 참고하여 주세요.";
		alert(msg);
		<? } else { ?>
		$('#jumin').removeClass('choice');
		$('#driver').addClass('choice');
		<? } ?>
	}
}

$(document).ready(function() { selectIdCard('1'); });

$('#KYCNextButton').on('click', function() {
	KYCStart($('#f_idType').val());
});
</script>