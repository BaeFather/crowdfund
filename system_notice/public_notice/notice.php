<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/common.php");

if(@!$NOTICONF['sdate'] && @!$NOTICONF['edate']) {
	$NOTICONF['sdate'] = date('Y-m-d H', strtotime('-1 HOUR')) . ':00:00';
	$NOTICONF['edate'] = date('Y-m-d H', strtotime('+1 HOUR')) . ':00:00';
}


$NOTI_TITLE = array('시스템 점검', '시스템 업데이트', '연계기관 점검');

$noti_title = $NOTI_TITLE['1'];
$core_string = "헬로펀딩 홈페이지 리뉴얼";

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSansNeo.css' rel='stylesheet' type='text/css'>
<title>서버점검안내</title>
<style>
#web_pop {display:block;}
#m_pop {display:none;}

#web_pop {margin:250px auto 0; width:680px; height: 400px; text-align: left; background-color: #404355; border-radius: 20px; box-sizing: border-box; font-family: 'Spoqa Han Sans', 'Spoqa Han Sans JP', 'Sans-serif'; background-image: url("/system_notice/public_notice/img/set_img.png"); background-position: right bottom; background-repeat: no-repeat; }
#web_pop .title {padding:40px 0 0 45px; font-family: 'Spoqa Han Sans Neo', 'sans-serif';font-weight:700; color:#fff; font-size:42px;}
#web_pop .title span {color:#00f3f0;}
#web_pop .date {padding:20px 0 0 45px; font-size: 20px; color:#fff; font-weight: 400;}
#web_pop .text {padding:20px 0 0 45px; font-size: 16px; color:#fff; font-weight: 300; line-height: 22px;}
#web_pop .text span {font-family: 'Spoqa Han Sans Neo', 'sans-serif';font-weight:500; color:#00f3f0; }


#m_pop {width:100%; margin:15% auto 0;  padding: 0 5% 0; text-align:center; background-color: #404355; border-radius: 20px; box-sizing: border-box; font-family: 'Spoqa Han Sans', 'Spoqa Han Sans JP', 'Sans-serif'; }
#m_pop .m_img {padding-top:40px;}
#m_pop .title{padding-top:20px; font-family: 'Spoqa Han Sans Neo', 'sans-serif';font-weight:700; color:#fff; font-size:34px; letter-spacing: -.5px;}
#m_pop .title span {color:#00f3f0;}
#m_pop .date {padding-top:20px; font-size: 16px; color:#fff; font-weight: 400;}
#m_pop .text {padding:30px 0 50px; font-size: 15px; color:#fff; font-weight: 400; line-height: 21px; }
#m_pop .text span {font-family: 'Spoqa Han Sans Neo', 'sans-serif';font-weight:500; color:#00f3f0; }


@media all and (max-width: 700px) {
	#web_pop {display:none;}
	#m_pop {display:block;}
}
</style>

</head>

<body>

<!-- PC용 메세지 //-->
<div id="web_pop">
	<div class="title"><span><?=$noti_title?></span> 안내</div>
	<div class="date"><?=date("Y년 m월 d일 H:i", strtotime($NOTICONF['sdate']))?> ~ <?=date("m월 d일 H:i", strtotime($NOTICONF['edate']))?></div>
	<div class="text">
		<?=$core_string?>로 인하여, 서비스 이용이<br>
		불가하오니 양해 부탁드립니다.<br><br>
		점검내용 : <span style="color:yellow"><?=$core_string?></span><br><br>

		해당 기간 동안 예치금 입출금이 제한 될 수 있습니다.<br>
		원활한 서비스 제공을 위하여 빠른 시간내에<br>
		완료될 수 있도록 최선을 다하겠습니다.
	</div>
</div>

<!-- 모바일용 메세지 //-->
<div id="m_pop">
	<div class="m_img"><img src="/system_notice/public_notice/img/m_set_img.png"></div>
	<div class="title"><span><?=$noti_title?></span> 안내</div>
	<div class="date">
		<?=date("Y년 m월 d일 H:i", strtotime($NOTICONF['sdate']))?> &nbsp;&nbsp;&nbsp;&nbsp;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp; ~ <?=date("m월 d일 H:i", strtotime($NOTICONF['edate']))?>
	</div>
	<div class="text">
		<?=$core_string?>로 인하여, 서비스 이용이 불가하오니 양해 부탁드립니다.<br><br>
		점검내용 : <span><?=$core_string?></span><br><br>

		해당 기간 동안 예치금 입출금이 제한 됩니다.<br><br>
		원활한 서비스 제공을 위하여 빠른 시간내에 완료될 수 있도록 최선을 다하겠습니다.
	</div>
</div>

</body>
</html>