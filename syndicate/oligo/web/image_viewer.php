<?
include_once("../syndication_config.php");

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }

$PRDT = sql_fetch("SELECT category, mortgage_guarantees, main_image, main_image_m FROM cf_product WHERE idx='".$prd_idx."'");

if($PRDT['main_image'] && $PRDT['main_image_m']) {
	$image_path = G5_DATA_PATH . "/product/" . $PRDT['main_image'];
	$image_size = @fileSize($image_path);
	/*
	if(G5_IS_MOBILE) {
		$image_path = G5_DATA_PATH . "/product/" . $PRDT['main_image_m'];
		$image_size = @fileSize($image_path);
	}
	*/
}
else {
	if($PRDT['main_image']) {
		$image_path = G5_DATA_PATH . "/product/" . $PRDT['main_image'];
		$image_size = @fileSize($image_path);
	}
}

if($image_size > 0) {

	$IMAGE = getImageSize($image_path);

	header('Content-Type:'.$IMAGE['mime']);
	header('Content-Length: ' . $image_size);
	readfile($image_path);

}
else {
	header("HTTP/1.0 404 Not Found");
}

sql_close();
exit;

?>