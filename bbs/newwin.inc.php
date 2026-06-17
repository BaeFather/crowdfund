<?

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가


$pop_division = (!defined('_SHOP_')) ? 'comm' : 'shop';

$nwSql = "
	SELECT
		*
	FROM
		".$g5['new_win_table']."
  WHERE
		'".G5_TIME_YMDHIS."' BETWEEN nw_begin_time AND nw_end_time
		AND nw_device IN('both','pc')
		AND nw_division IN('both', '".$pop_division."')
	ORDER BY
		nw_id ASC";
$nwRes  = sql_query($nwSql, false);
$nwRows = sql_num_rows($nwRes);

?>
<!-- 팝업레이어 시작 -->
<?
if($nwRows) {
?>
<div id="hd_pop">
	<h2>팝업레이어 알림</h2>
<?
	for($i=0; $i<$nwRows; $i++) {

		$nw = sql_fetch_array($nwRes);

		// 이미 체크 되었다면 Continue
		if($_COOKIE["hd_pops_{$nw['nw_id']}"]) continue;

?>

	<div id="hd_pops_<?=$nw['nw_id']?>" class="hd_pops" style="top:<?=$nw['nw_top']?>px;left:<?=$nw['nw_left']?>px">
		<div class="hd_pops_con" style="width:<?=$nw['nw_width']?>px;height:<?=$nw['nw_height']?>px"><?=conv_content($nw['nw_content'], 1);?></div>
		<div class="hd_pops_footer">
			<button class="hd_pops_reject hd_pops_<?=$nw['nw_id']?> <?=$nw['nw_disable_hours']?>"><strong><?=$nw['nw_disable_hours']?></strong>시간 동안 다시 열람하지 않습니다.</button>
			<button class="hd_pops_close hd_pops_<?=$nw['nw_id']?>">닫기</button>
		</div>
	</div>
<?
	}
?>
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
<?
}
else {
/*
?>
<div id="hd_pop">
	<h2>팝업레이어 알림</h2>
	<span class="sound_only">팝업레이어 알림이 없습니다.</span>
</div>
<?
*/
}
?>
<!-- 팝업레이어 끝 -->