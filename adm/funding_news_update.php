<?php
$sub_menu = '300300';
include_once('./_common.php');



$data = $_POST;



$thumbnail = time()."_thumbnail";
$news_logo = time()."_logo";


if($_FILES['bn_img1']['name'] !='') {

	@unlink("/home/crowdfund/public_html{$_POST['pre_thumbnail']}");

	$bn_img1_val = "/data/funding_news/".$thumbnail;
	$thumbnail_path = G5_DATA_PATH."/funding_news/".$thumbnail;
	@move_uploaded_file($_FILES['bn_img1']['tmp_name'], $thumbnail_path);

}else {
	$bn_img1_val = $_POST['pre_thumbnail'];
}


if($_FILES['bn_img2']['name'] !='') {

	@unlink("/home/crowdfund/public_html{$_POST['pre_news_logo']}");

	$bn_img2_val = "/data/funding_news/".$news_logo;
	$news_logo_path = G5_DATA_PATH."/funding_news/".$news_logo;
	@move_uploaded_file($_FILES['bn_img2']['tmp_name'], $news_logo_path);
	@chmod($dest_path, G5_FILE_PERMISSION);

}else {
	$bn_img2_val = $_POST['pre_news_logo'];
}

$update_col = "subject = '".$data['subject']."'";
$update_col .= ", news_link = '".$data['news_link']."'";
$update_col .= ", contents = '".$data['contents']."'";
$update_col .= ", thumbnail = '".$bn_img1_val."'";
$update_col .= ", news_logo = '".$bn_img2_val."'";
$update_col .= ", show_date = '".$data['show_date']."'";
$update_col .= ", press = '".$data['press']."'";

if($mode_type == 'inst') {

	$update_col .= ", regdate = NOW()";

	$sql = "
			INSERT INTO funding_news_list set {$update_col}
	";

}else {

	$sql = "
			UPDATE funding_news_list set {$update_col} where idx = {$data['modi_idx']}
	";

}

sql_query($sql);



alert("등록되었습니다.","./funding_news_list.php?page={$data['page']}");



?>