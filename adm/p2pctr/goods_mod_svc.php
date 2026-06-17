<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.3.3";
$apiTitle = "상품모집 갱신";
?>
<?
$prd_sql = "SELECT goods_id
			  FROM cf_product 
			 WHERE idx='$product_idx'";
$prd_res = sql_query($prd_sql);
$prd_row = sql_fetch_array($prd_res);

$goods_id = $prd_row["goods_id"];

if ($mod_stat=="canc") $status = "T150";      // T150 모집취소
else if($mod_stat=="end") $status = "T210";   // T210 모집완료
else if($mod_stat=="ing") $status = "T200";   // T200 모집중
else die("갱신요청 코드 오류");

//if ($product_idx=="7438") $status = "T200";
//if ($product_idx=="8353") $status = "T200";
//if ($product_idx=="8618") $status = "T200";

?>
<?
$url  = $p2p_host . "goods/".$goods_id;
//if ($product_idx=="3107") $url  = $p2p_host . "data/goods/".$goods_id;  // 이관용
$method = "PUT";
?>
<?
$data = array();
$data["status"] =  $status; // 상품모집상태 AN(4) (필수)

if ($mode=="send" and $goods_id) {

	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx);
	$resj = json_decode($curl_res["body"] , true);


	if ($resj["rsp_code"] == "A0000") {

		if ($status == "T150") {
			//$up_sql = "UPDATE cf_product SET goods_id = '' WHERE idx = '$product_idx' AND goods_id='$goods_id' ";
			//sql_query($up_sql);
		}

		$up2_sql = "UPDATE p2pctr_product SET goods_status='$status' WHERE product_idx = '$product_idx' ";
		sql_query($up2_sql);

		echo "기록 성공<br/><br/>";
		echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";

	} else {
		echo "기록 실패<br/><br/>";
		echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";
	}

}


?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?><?=strpos($url,'data')!==false?" (이관용)":""?></h3>
<table class="table table-bordered table-condensed">
	<tr>
		<th>상품 ID</th>
		<td><?=$goods_id?></td>
	</tr>
	<tr>
		<th>상품 상태</th>
		<td>
			<?=$data["status"]?><br/><br/>
			T100 신청중 / T150 신청취소 / T200 모집중 / T210 모집완료 
		</td>
	</tr>
</table>

<table style="width:100%;border:0;">
	<tr>
		<td style="text-align:center; border:0;">
			<form method="post" name="ff">
			<input type=hidden name="mode" value=""/>
			<input type=hidden name="product_idx" value="<?=$product_idx?>"/>
			<input type=hidden name="mod_stat" value="<?=$mod_stat?>"/>
			<input type="button" class="btn btn-sm btn-warning" onclick="go_send();" value="전송"/>
			</form>
		</td>
	</tr>
</table>

<script>
function go_send() {
	var yn = confirm("이대로 전송하시겠습니까?");
	if (!yn) return;

	var f = document.ff;
	f.mode.value="send";
	f.submit();
}
</script>