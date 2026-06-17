<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr.lib.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/repay_calculation_new.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.3.3";
$apiTitle = "상품모집 갱신";
$intStime = time();
?>
<?
$prd_sql = "SELECT goods_id
			  FROM cf_product 
			 WHERE idx='$product_idx'";
$prd_res = sql_query($prd_sql);
$prd_row = sql_fetch_array($prd_res);

$goods_id = $prd_row["goods_id"];

$url  = $p2p_host . "goods/".$goods_id;
$method = "PUT";
$strApiTrxNo  = get_p2pord_no(); // API거래고유번호
$strApiTrxDtm = get_dtm_no();    // 거래일시 (밀리세컨드)

$headers[] = "Content-Type: application/json; charset=UTF-8";
ARRAY_PUSH($headers,"Authorization: Bearer ".$access_token);
ARRAY_PUSH($headers,"api_trx_no: ".$strApiTrxNo);
ARRAY_PUSH($headers,"api_trx_dtm: ".$strApiTrxDtm);

$status = "T210";
$data["status"] =  $status; // 상품모집상태 AN(4) (필수)

if ($mode=="send") {
	$res = curl_p2pctr($url , $method , $data , $headers);

	$intEtime = time();
	$thrSec = $intStime - $intEtime;
	fn_log($apiNo, $apiTitle, $mb_no, $url, $res["req_body"] , $res["body"], $res["http_code"], $thrSec);

	$resj = json_decode($res["body"] , true);
	if ($resj["rsp_code"] == "A0000") {
		echo "기록 성공<br/><br/>";
		echo "<pre>"; print_r($res); echo "</pre><br/><br/>";
	} else {
		echo "기록 실패<br/><br/>";
		echo "<pre>"; print_r($res); echo "</pre><br/><br/>";
	}
}


?>

<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
<table class="table table-bordered table-condensed">
	<tr>
		<th>상품 ID</th>
		<td><?=$goods_id?></td>
	</tr>
	<tr>
		<th>상품 상태</th>
		<td>
			<?=$data["status"]?><br/><br/>
			T100 신청중 / T210 모집완료
		</td>
	</tr>
</table>

<table style="width:100%;border:0;">
	<tr>
		<td style="text-align:center; border:0;">
			<form method="post" name="ff">
			<input type=hidden name="mode" value=""/>
			<input type=hidden name="product_idx" value="<?=$product_idx?>"/>
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