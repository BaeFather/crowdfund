<?php
include_once('./_common.php');

$g5['title'] = '언론보도';

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

$sql = " select count(*) as cnt FROM funding_news_list";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

//$rows = $config['cf_page_rows'];
$rows = 10;

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT * FROM funding_news_list ORDER BY regdate DESC limit {$from_record}, {$rows}";
$result = sql_query($sql);

?>
<!-- 본문내용 START -->
<style>

	.list_warp {
		/*border:2px solid #EEEEEE;*/
		/*padding:10px;*/
		margin-bottom:20px;
	}

	.list_warp * {
		color: #707070 !important;
	}


	.list_obj {
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

	.news_logo img {
		width:165px;
		height:40px;
	}

	.subject {
		font-weight:bold;
		font-size:15px;
		padding-bottom:15px;
		margin-top:30px;
	}

	.contents {
		line-height:18px;
		height:70px;
		overflow:hidden;
	}

	.list-line {
		height:2px;
		background-color:#EEEEEE;
		margin:15px 0px;
	}

	.date {
	    display: inline-block;
		margin-left: 25px;
		padding-left: 20px;
		font-size: 13px;
		background: url('../images/bbs/icon_date.gif') no-repeat left center;
	}

</style>

<div id="content">

	<div class="location"><span><a href="<?=G5_URL?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">
		<div class="list_div">
		<?php
		$i = 1;
		while($row=sql_fetch_array($result)) {

			$show_date = str_replace('-','. ',substr($row['show_date'],0,10));

		?>

			<div class="list_warp">
				<div class="list_obj">
					<div class="thumbnail"><img src="<?php echo $row['thumbnail'];?>" /></div>
					<div class="subject"><a href="<?php echo $row['news_link'];?>" target="_blank"><?php echo $row['subject'];?></a> <span class="date"><?=$show_date?></span></div>
					<div class="contents"><a href="<?php echo $row['news_link'];?>" target="_blank"><?php echo nl2br($row['contents']);?></a></div>
				</div>
				<div style="clear:both;"></div>
			</div>

		<?php
			if($result->num_rows > $i) {
		?>

			<div class="list-line"></div>
		<?php
			}

			$i++;
		}
		?>
		</div>

		<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>
	</div>

</div>


<!-- 본문내용 E N D -->

<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>
