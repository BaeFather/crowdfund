<?
$_REQUEST['bo_table'] = "recruit";
include_once('../../common.php');
$notice_array = explode(',', trim($board['bo_notice']));

set_session('ss_delete_token', $token = uniqid(time()));

// 한번 읽은글은 브라우저를 닫기전까지는 카운트를 증가시키지 않음
$ss_name = 'ss_view_'.$bo_table.'_'.$wr_id;

if (!get_session($ss_name)) {
	$hit_sql = "update g5_write_recruit set wr_hit = wr_hit + 1 where wr_id = '{$wr_id}'";
    sql_query($hit_sql);

    set_session($ss_name, TRUE);
}

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('../_head.php');
}

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>
<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>
<?
$sql = "SELECT * FROM g5_write_recruit WHERE wr_id='$wr_id'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

$html = 0;
if (strstr($row['wr_option'], 'html1'))
    $html = 1;
else if (strstr($row['wr_option'], 'html2'))
    $html = 2;

$row['content'] = conv_content($row['wr_content'], $html);
?>
<input type="text" id="ShareUrl" style="position:absolute;top:0;left:0;width:1px;height:1px;margin:0;padding:0;border:0;">
<script>
function url_copy() {
	var obShareUrl = document.getElementById("ShareUrl");
	obShareUrl.value = window.document.location.href;  // 현재 URL 을 세팅해 줍니다.
	obShareUrl.select();  // 해당 값이 선택되도록 select() 합니다
	try {
		document.execCommand("copy"); // 클립보드에 복사합니다.
	} catch(err) {
		alert("이 브라우저는 지원되지 않습니다.");
		return false;
	}
	obShareUrl.blur(); // 선택된 것을 다시 선택안된것으로 바꿈니다.
	alert("주소가 복사되었습니다.");

	return;
}
</script>
<?
if(G5_IS_MOBILE) {
	include_once("recruit_view_m.php");
	return;
}
?>
<div id="content">
	<div class="content">
		<div class="type01 bbs">
			<table class="notice">
				<tbody>
					<tr>
						<td colspan="2">
							<div class="subject"><?=$row["wr_subject"]?></div>
							<span class="date"><?=$row["wr_1"]=="기간내"?$row["wr_2"]." ~ ".$row["wr_3"]:$row["wr_1"]?></span>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="con">
							<?=$row["wr_content"]?>
						</td>
					</tr>
				</btbody>
			</table>
		</div>
		<div class="btnArea alignR">
			<a onclick="url_copy()" style="cursor:pointer;margin-right:10px;" class="btn_blue" >공유하기</a>
			<? if ($is_admin) { ?>
			<a class="btn_blue" style="background-color: rgb(171, 171, 171);" href="/bbs/write.php?w=u&bo_table=recruit&wr_id=<?=$row['wr_id']?>&page=<?=$page?>">수정</a>
			<a href="/bbs/delete.php?bo_table=recruit&wr_id=<?=$row['wr_id']?>&token=<?=$token?>&page=<?=$page?>" class="btn_blue" style="background-color:#ababab;" onclick="del(this.href); return false;">삭제</a>
			<? } ?>
			<a href="/company/recruit/recruit.php#list" class="btn_blue">목록</a>
		</div>
	</div>
</div>
<?
if($co['co_include_tail']){
	@include_once($co['co_include_tail']);
} else {
	include_once('../_tail.php');
}
?>

