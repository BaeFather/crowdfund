<?
include_once('./_common.php');
include_once('./quest_config.php');

if(!G5_IS_MOBILE) { msg_go("본 이벤트는 모바일을 통해서만 진행중입니다.\\n휴대폰 또는 테블릿을 이용하여 주시기 바랍니다."); }


if( in_array($is_entered, array('ready','1')) ) {
	if($is_entered=='ready') {
		$href_script = "alert('현재 참여중인 이벤트 입니다.');location.href='/event/quest_finish.php';";
	}
	else {
		$msg = "이미 참여하신 이벤트 입니다.";
		$msg.= (!$member['mb_no']) ? "\\n로그인 후 결과를 확인하실 수 있습니다." : "";
		$href_script = "alert('".$msg."');location.href='/event/quest_finish.php';";
	}
}
else {
	$href_script = "location.href='/event/quest.php';";
}


$g5['title'] = $ECONF['title'];

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

?>

<link rel="stylesheet" href="/event/quest_event.css" />

<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">

<? if(date('s')%2==0) { ?>
		<div class="hello_lucky01" >
			<p class="start_btn01" onClick="<?=$href_script?>">시작하기</p>
			<ul class="sns_btn">
				<li><a href="#" data-toggle="sns_share" data-service="facebook" data-title="페이스북 SNS공유"><img src="/images/event/facebook.png"/></a></li>
				<li><a href="#" data-toggle="sns_share" data-service="instagram" data-title="인스타그램 SNS공유"><img src="/images/event/insta.png"/></a></li>
				<li><a href="#" data-toggle="sns_share" data-service="naver" data-title="네이버 SNS공유"><img src="/images/event/blog.png"/></a></li>
				<li><a href="#" data-toggle="sns_share" data-service="kakaostory" data-title="카카오스토리 SNS공유"><img src="/images/event/kakao.png"/></a></li>
			</ul>
		</div>
<? } else { ?>
		<div class="hello_lucky02">
			<p class="start_btn02" onClick="<?=$href_script?>">시작하기</p>
			<ul class="sns_btn">
				<li><a href="#" data-toggle="sns_share" data-service="facebook" data-title="페이스북 SNS공유"><img src="/images/event/facebook.png"/></a></li>
				<li><a href="#" data-toggle="sns_share" data-service="naver" data-title="네이버 SNS공유"><img src="/images/event/blog.png"/></a></li>
				<li><a href="#" data-toggle="sns_share" data-service="kakaostory" data-title="카카오스토리 SNS공유"><img src="/images/event/kakao.png"/></a></li>
				<li><a href="#" data-toggle="sns_share" data-service="instagram" data-title="인스타그램 SNS공유"><img src="/images/event/insta.png"/></a></li>
			</ul>
		</div>
<? } ?>
	</div>
</div>
<!-- 본문내용 E N D -->

<script>
$("a[data-toggle='sns_share']").click(function(e) {
	e.preventDefault();
	var current_url = window.location.href;
	var _this       = $(this);
	var sns_type    = _this.attr('data-service');
	var href        = current_url;
	var title       = _this.attr('data-title');
	var img         = $("meta[name='og:image']").attr('content');
	var loc         = "";

	if( ! sns_type || !href || !title) return;

	if(sns_type == 'facebook') { loc = '//www.facebook.com/sharer/sharer.php?u='+href+'&t='+title; }
	else if(sns_type == 'twitter') { loc = '//twitter.com/home?status='+encodeURIComponent(title)+' '+href; }
	else if(sns_type == 'google') { loc = '//plus.google.com/share?url='+href; }
	else if(sns_type == 'pinterest') { loc = '//www.pinterest.com/pin/create/button/?url='+href+'&media='+img+'&description='+encodeURIComponent(title); }
	else if(sns_type == 'kakaostory') { loc = 'https://story.kakao.com/share?url='+encodeURIComponent(href); }
	else if(sns_type == 'band') { loc = 'http://www.band.us/plugin/share?body='+encodeURIComponent(title)+'%0A'+encodeURIComponent(href); }
	else if(sns_type == 'naver') { loc = "http://share.naver.com/web/shareView.nhn?url="+encodeURIComponent(href)+"&title="+encodeURIComponent(title); }
	else if(sns_type == 'url_copy') { copy_trackback(href); }
	else if(sns_type == 'instagram') { alert("현재 지원하지 않는 기능입니다."); loc = ""; return false; }
	else { return false; }

	if(sns_type != 'url_copy') { window.open(loc); }

	return false;
});
</script>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>