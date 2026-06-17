<?php
$sub_menu = '100600';
include_once('./_common.php');


$bn1_name = time()."_bn1";
$bn2_name = time()."_bn2";
$bn3_name = time()."_bn3";


if($_FILES['bn_img1']['name'] !='') {
	
	@unlink(G5_DATA_PATH."/main_banner/{$_POST['bn_img1_pre']}");

	$bn_img1_val .= $bn1_name;
	$dest_path = G5_DATA_PATH."/main_banner/".$bn1_name;
	@move_uploaded_file($_FILES['bn_img1']['tmp_name'], $dest_path);
	
}else {
	$bn_img1_val .= $_POST['bn_img1_pre'];
}


if($_FILES['bn_img2']['name'] !='') {

	@unlink(G5_DATA_PATH."/main_banner/{$_POST['bn_img2_pre']}");
	
	$bn_img2_val .= $bn2_name;
	$dest_path = G5_DATA_PATH."/main_banner/".$bn2_name;
	@move_uploaded_file($_FILES['bn_img2']['tmp_name'], $dest_path);
	@chmod($dest_path, G5_FILE_PERMISSION);

}else {
	$bn_img2_val .= $_POST['bn_img2_pre'];
}


if($_FILES['bn_img3']['name'] !='') {
	
	@unlink(G5_DATA_PATH."/main_banner/{$_POST['bn_img3_pre']}");

	$bn_img3_val .= $bn3_name;
	$dest_path = G5_DATA_PATH."/main_banner/".$bn3_name;
	@move_uploaded_file($_FILES['bn_img3']['tmp_name'], $dest_path);
	@chmod($dest_path, G5_FILE_PERMISSION);
	
}else {
	$bn_img3_val .= $_POST['bn_img3_pre'];
}

$update_col = "bn_img1 = '".$bn_img1_val."'";
$update_col .= ", bn_img2 = '".$bn_img2_val."'";
$update_col .= ", bn_img3 = '".$bn_img3_val."'";

$sql = " UPDATE g5_main_banner SET $update_col where idx = 1 ";

sql_query($sql);


goto_url("./main_banner_form.php");

?>