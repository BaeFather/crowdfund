<?php
$sub_menu = '300300';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "헬로펀딩 소식";
$g5['title'] = $html_title.' 설정';

include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql = " select count(*) as cnt FROM funding_news_list";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
//$rows = 2;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함



$sql = "SELECT * FROM funding_news_list ORDER BY regdate DESC limit {$from_record}, {$rows}";
$result = sql_query($sql);

?>

<style>

	.list_warp {
		border:2px solid #EEEEEE;
		padding:10px;
		margin-bottom:20px;
	}

	.list_div {
		padding:15px;
	}

	.list_obj {
		width:85%;
		float:left;
	}

	.thumbnail{
		float:left;
		margin-right:10px;
	}

	.thumbnail img {
		width:218px;
		height:143px;
		display:inline-block;
	}

	.news_logo{
		margin-bottom:15px;
	}

	.news_logo img {
		width:165px;
	}

	.subject {
		font-weight:bold;
		font-size:15px;
		padding-bottom:15px;
	}

	.contents {
		line-height:18px;
		height:55px;
		overflow:hidden;
	}

	.btn_obj {
		float:right;
	}

	.btn_obj button {
		border:0px;
	}

</style>

<script>

	function go_url(url) {
		document.location.href = url+"&page=<?php echo $page;?>";
	}

	function go_dele(idx) {

		if(confirm("정말 삭제하시겠습니까?")) {
			document.location.href = "./funding_news_delete.php?idx="+idx;
		}else {
			return false;
		}

	}
</script>


<div style="padding:15px; width:70%; margin:3px auto; text-align:right;">
	<a href="./funding_news_reg.php" style="background-color:#2D4664; color:#FFFFFF; padding:5px;">소식 등록</a>
</div>

<div class="list_div" style="width:70%; margin:3px auto;">
<?php
for ($i=0; $row=sql_fetch_array($result); $i++) {

	$show_date = "<span style='margin-left:15px; font-size:12px;'><img src='/images/Calendar-icon.png' width='13px' align='absmiddle' /> ".str_replace('-','. ',substr($row['show_date'],0,10))."</span>";

?>

	<div class="list_warp">
		<div class="list_obj">
			<div class="thumbnail"><img src="<?php echo $row['thumbnail'];?>" /></div>
			<div class="news_logo"><img src="<?php echo $row['news_logo'];?>" /></div>
			<div class="subject"><a href="<?php echo $row['news_link'];?>" target="_blank"><?php echo $row['subject'];?></a> <?php echo $show_date;?></div>
			<div class="contents"><a href="<?php echo $row['news_link'];?>" target="_blank"><?php echo nl2br($row['contents']);?></a></div>
		</div>
		<div class="btn_obj">
			<div style="margin-bottom:5px;"><button type="button" onclick="go_url('./funding_news_reg.php?idx=<?php echo $row['idx'];?>');" style="background-color:#383A3F; color:#FFFFFF; padding:5px;">수정</button></div>
			<div><button type="button" onclick="go_dele('<?php echo $row['idx'];?>');" style="background-color:#FF4747; color:#FFFFFF; padding:5px;">삭제</button></div>
		</div>
		<div style="clear:both;"></div>
	</div>


<?php
}
?>
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
