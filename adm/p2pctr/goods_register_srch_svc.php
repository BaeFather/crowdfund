<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.3.4";
$apiTitle = "상품 조회";
?>
<?
$prd_sql = "SELECT goods_id
			  FROM cf_product 
			 WHERE idx='$product_idx'";
$prd_res = sql_query($prd_sql);
$prd_row = sql_fetch_array($prd_res);


$goods_id = $prd_row["goods_id"];
?>
<?
$url  = $p2p_host . "goods/".$goods_id;
$method = "REST_GET";

$data = array();
$res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx);

$rbody = json_decode($res["body"],true);
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?></h3>
	<?
	echo "<pre>"; print_r($rbody); echo "</pre>";
	?>
</div>

<div class="tbl_head02 tbl_wrap" style="margin-top:15px;">
goods_status => T100 신청중 , T200 모집중 , T210 모집완료
</div>