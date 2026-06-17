<?php
include_once('./_common.php');


$g5['title']      = "응답하라 투자후기";
$g5['top_bn']     = "";
$g5['top_bn_alt'] = "";

if($_REQUEST['mode']!='test') {
	if(G5_TIME_YMDHIS < '2016-12-21 11:00:00') alert("대기중인 이벤트 입니다.", "/");
	else if(G5_TIME_YMDHIS > '2016-12-25 23:59:59') alert("종료된 이벤트 입니다.", "/");
}


if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

// 본인 투자후기 내역 확인
$r = sql_fetch("SELECT idx FROM invest_users_epilogue WHERE member_idx='".$member['mb_no']."'");

?>
<!-- 본문내용 START -->

<div id="content">
	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">

	<? if(G5_IS_MOBILE) { ?>
		<div style="width:96%; padding:10px 2% 10px 2%;" >
			<div id="event_image" data-idx="<?=$r['idx']?>" style="width:100%; margin:0 auto;">
				<img src="/images/investment/reply_detail_m.jpg" width="100%" >
			</div>
		</div>
	<? } else { ?>
		<div style="width:80%; padding:10px 10% 10px 10%;">
			<div id="event_image" data-idx="<?=$r['idx']?>" style="width:773px; margin:0 auto; cursor:pointer;">
				<img src="/images/investment/reply_detail.jpg" width="100%">
			</div>
		</div>
	<? } ?>

	</div>
</div>

<div id="apply_no" apply-idx="<?=$r['idx']?>" style="display:none"></div>
<div id="epilogue_area"><!--투자후기영역--></div>

<!-- 투자후기창 스크립트 //-->
<script>
$('#event_image').on('click', function() {
	var idx = $(this).attr("data-idx");
	$.ajax({
		url : "/event/ajax_invest_epilogue_write.php",
		type:"GET",
		data:{ idx:idx },
		success: function(data){
			$("#ajax_return_txt").val(data);
			if(data=='unqualified_request') {
				alert('본 이벤트는 투자 수익을 지급받은 이력이 있는 분들만 참여가능합니다.');
				<? if(!$member['mb_id']) { ?>location.href="/bbs/login.php?url=<?=urlencode('/event/invest_epilogue.php')?>";<? } ?>
				return;
			}
			else {

				var apply_no = $('#apply_no').attr("apply-idx");
				if(apply_no) {
					if(! confirm('이미 본 이벤트에 응모하셨습니다.\n\n게시글을 수정 하시겠습니까?')) {
						return;
					}
				}

				$("#epilogue_area").html(data);
				$.blockUI({
					message: $("#epilogue_area"),
	<? if(G5_IS_MOBILE) { ?>
					css: { top:'3%', left:'1%', width:'98%', overflow:'auto', border:'0', cursor:'default' }
	<? } else { ?>
					css: { top:'10%',left:'25%',width:'800px',border:0, cursor:'default' }
	<? } ?>
				});
			}
		},
		error: function ()	{
			alert('통신에러 입니다. 잠시후 다시 시도하십시요.');
		}
	});
});
</script>


<!-- 본문내용 E N D -->
<?php

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>