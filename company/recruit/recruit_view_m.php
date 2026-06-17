<?
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

?>

<div id="content">
	<div class="location">
		<span></span>
		<b class="blue">채용안내</b>
	</div>
	<br/>

	<article id="bo_v" style="width:100%">
		<header>
			<h1 id="bo_v_title"><?=$row["wr_subject"]?></h1>
		</header>
		<section id="bo_v_info">
			<span style="font_size:11px;color:#aaa"><?=$row["wr_1"]=="기간내"?$row["wr_2"]." ~ ".$row["wr_3"]:$row["wr_1"]?></span>
		</section>


		<section id="bo_v_atc">
			<div id="bo_v_con">
					<?=get_view_thumbnail($row['content'])?>
			</div>
		</section>

		<section style="text-align:right;">
			<a class="btn_b01" style="margin-right:10px;" onclick="url_copy()">공유하기</a>
			<a class="btn_b01" style="margin-right:20px;" href="/company/recruit/recruit.php#list">목록</a>
		</section>
	</artical>
</div>
<?
if($co['co_include_tail']){
	@include_once($co['co_include_tail']);
} else {
	include_once('../_tail.php');
}
?>