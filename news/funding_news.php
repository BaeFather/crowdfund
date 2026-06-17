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

<link href="hello_news.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>


<div id="news_content">

	<div>
		<h2 class="top_title">헬로펀딩 NEWS</h2>
		<p class="top_text">언론에 보도된 헬로펀딩 최신 보도자료를 소개합니다.<br class="br"></p>
	</div>
	
	<div class="content">
		<div class="list_div">
		<div class="list-line"></div>	
		<?php
		$i = 1;
		while($row=sql_fetch_array($result)) {

			$show_date = str_replace('-','. ',substr($row['show_date'],0,10));

		?>

			<div class="list_warp">
				<div class="list_obj">
					<div class="thumbnail"><img src="<?php echo $row['thumbnail'];?>" /></div>
					<div class="subject"><a href="<?php echo $row['news_link'];?>" target="_blank"><?php echo $row['subject'];?></a> </div>
					<div class="contents"><span class="date"><?=$show_date?></span><span class="press"><?=$row['press']?></span>
						<br/>
						<a href="<?php echo $row['news_link'];?>" target="_blank"><?php echo nl2br($row['contents']);?></a>
					</div>
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
	<br><br>

</div>





<!-- 본문내용 E N D -->

<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>
