<?
include_once('./_common.php');

$g5['title'] = "2천억 돌파기념 이벤트";

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$event_start_date = "2019-10-14";
$event_end_date   = "2019-11-30";

if( date('Y-m-d') < $event_start_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_start_date)) . "」부터 시작합니다.\\n많은 참여 부탁드립니다.');";
	$join_link2 = $join_link;
}
else if( date('Y-m-d') > $event_end_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_end_date)) . "」에 종료 되었습니다.\\n다음 이벤트도 많은 참여 부탁드립니다.');";
	$join_link2 = $join_link;
}
else {
	$join_link = ($member['mb_no']) ? "javascript:alert('죄송합니다. 신규가입자 이벤트 입니다.');" : "/member/join_info.php?tab=p";
	$join_link2 = "/investment/invest_list.php";
}


if(G5_IS_MOBILE) {
	include_once("2000event_m.php");
	return;
}

?>

<style>
#event {width:100%; margin:0; padding:0 }
#event .aa {width:1150px;text-align:center;}
</style>


<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">

		<div id="event">
			<div class="aa"><img src="/images/event/2000event/2000_web_01.jpg"></div>
			<div class="aa"><img src="/images/event/2000event/2000_web_02.jpg" usemap="#imgmap20191011193332"></div>
			<div class="aa"><img src="/images/event/2000event/2000_web_03.jpg" usemap="#imgmap20191011194412"></div>
		</div>

		<map id="imgmap20191011193332" name="imgmap20191011193332"><area shape="rect" coords="729,622,1009,691" href="<?=$join_link;?>" onFocus="blur();" /></map>
		<map id="imgmap20191011194412" name="imgmap20191011194412"><area shape="rect" coords="150,277,431,345" href="<?=$join_link2;?>" onFocus="blur();" /></map>

	</div>
</div>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>