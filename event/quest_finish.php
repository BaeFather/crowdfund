<?
include_once('./_common.php');
include_once('./quest_config.php');

if(!$member['mb_no']) msg_replace("", "/bbs/login.php?url=".urlencode('/event/quest_finish.php'));

if(!$is_entered) msg_replace("이벤트 응모내역이 없습니다.", "/");

$g5['title'] = $ECONF['title'];

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}


add_stylesheet('	<link rel="stylesheet" href="/event/quest_event.css" />', 10);

?>

<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">

<?
if($is_entered=='1') {
	$print_name = ($member['member_type']=='2') ? $member['mb_co_name'] : $member['mb_name'];
	$print_text = "당첨금 <strong style='color:#0f81e8;font-size:16px;font-weight:600;'>" . number_format($DATA['point']) . "원</strong> 적립";
	$print_text.= ($DATA['paid']=='1') ? "  되었습니다." : "<strong>예정</strong> 입니다.";
?>
		<div id="newBiDiv" class="divLogin">
			<p style="text-align:center;"><strong style="color:#0f81e8;font-size:17px;font-weight:600;">이벤트 응모에 감사드립니다.</strong></p>
			<p style="text-align:center;margin-top:20px;">'<?=$print_name?>'님께</p>
			<p style="text-align:center;"><?=$print_text?></p>
			<div style="margin-top:20px;">
				<button type="button" class="next_button" onClick="location.href='/'">확 인</button>
			</div>
		</div>
<?
}
else {
	if($member['virtual_account2']=='' || $is_entered=='ready') {
		$print_name = ($member['member_type']=='2') ? $member['mb_co_name'] : $member['mb_name'];
		$print_text = "당첨금 <strong style='color:#0f81e8;font-size:16px;font-weight:600;'>" . number_format($DATA['point']) . "원</strong> 적립 <strong>예정</strong> 입니다.";
?>
		<div id="newBiDiv" class="divLogin">
			<p style="text-align:center;"><strong style="color:#0f81e8;font-size:18px;font-weight:600;">이벤트 응모에 감사드립니다.</strong></p>
			<p style="text-align:center;margin-top:20px; padding:20px 0; background:#e4f2ff; border:0px solid #aaa; border-radius:3px;">
				'<?=$print_name?>'님께<br/>
				<?=$print_text?><br/>
				당첨금이 적립될 가상계좌를 꼭 발급받으세요.
			</p>
			<p style="text-align:center;margin-top:20px;font-size:13px;font-family:NG;">
				<span style="display:block;margin-top:10px;color:#FF2222;"><b>!</b> 이벤트 참여 후 1시간내 가상계좌 미발급시,<br/>이벤트 응모내역 및 당첨금은 자동 소멸됩니다.
			</p>
			<div style="margin-top:20px;">
				<button type="button" class="next_button" onClick="vaOpen();">가상계좌 개설하고 당첨금 받기</button>
			</div>
		</div>
<?
	}
}
?>

	</div>

</div>
<!-- 본문내용 E N D -->

<?
// 가상계좌 팝업 소스 인클루드
if($member['virtual_account2']=='' || $is_entered=='ready') {
	include_once(G5_PATH . "/popup/inc_bank_account.php");
}
?>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>