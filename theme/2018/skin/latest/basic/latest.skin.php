<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


//$latest_skin_dir = str_replace(G5_URL, '', $latest_skin_url);
//add_stylesheet('	<link rel="stylesheet" href="'.$latest_skin_dir.'/style.css?ver=20190212" />', 0);		// fundingNews.skin.php 파일에 정의된 스타일을 공유한다.

?>
<table>
	<colgroup>
		<col style="width:80%;">
		<col style="width:20%;">
	</colgroup>
<tbody>
<?
for ($i = 0; $i < count($list); $i++) {
	$href = "";
?>
<tr>
	<td>
		<div>
			<a href="<? echo $list[$i]['href'];?>">
				<?
					if ($list[$i]['is_notice']){
				?>
						<strong><? echo $list[$i]['subject'];?></strong>
				<? }else{ ?>
						<? echo $list[$i]['subject'];?>
				<? } ?>
				<?
				if ($list[$i]['comment_cnt']){
					echo $list[$i]['comment_cnt'];
				}
				?>
			</a>
			<? if(isset($list[$i]['icon_new'])) echo " ".$list[$i]['icon_new']; ?>
		</div>
	</td>
	<td><? echo date("Y.m.d", strtotime($list[$i]['datetime'])); ?></td>
</tr>
<? }  ?>

<? if ($i == 0) { //게시물이 없을 때  ?>
	<tr>
		<td>게시물이 없습니다.</td>
	</tr>
<? }  ?>
</tbody>
</table>