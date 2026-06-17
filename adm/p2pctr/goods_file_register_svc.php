<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.3.2";
$apiTitle = "상품설명서 파일 기록";

$url  = $p2p_host . "goods/document";
$method = "POST";
?>
<?
$prd_sql = "SELECT goods_id, title
			  FROM cf_product 
			 WHERE idx='$product_idx'";
$prd_res = sql_query($prd_sql);
$prd_row = sql_fetch_array($prd_res);

$goods_id = $prd_row["goods_id"];




if ($mode=="send" ) {

	//echo "<pre>"; print_r($_FILES); echo "</pre>";

	if (isset($_FILES["pdf_file"])) {
		$file = $_FILES["pdf_file"];

		$upfile_res = move_uploaded_file($file["tmp_name"], "pdf/" . $product_idx .".pdf");


		if ($upfile_res) {
			$data = array();
			$data["goods_id"] = $goods_id; // P2P온투업자 상품 ID ANS 40
			//$data["goods_document"] = '@'.$_SERVER["DOCUMENT_ROOT"]."/adm/p2pctr/pdf/$product_idx.pdf";	
			$data["goods_document"] = new CURLFile("pdf/$product_idx.pdf");

			$curl_res = curl_p2pctr2_file($apiNo, $apiTitle , $url , $method , $data,  $product_idx);

			$resj = json_decode($curl_res["body"] , true);

			if ($resj["rsp_code"] == "A0000") {
				echo "정상 처리<br/><br/>";
				echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";
			} else {
				echo "기록 실패<br/><br/>";
				echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";
			}
		}

	}
}
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?></h3>

<form name="ff" method="post" enctype="multipart/form-data">
	<input type=hidden name="mode" value=""/>
	<input type=hidden name="product_idx" value="<?=$product_idx?>"/>


<table class="table table-bordered table-condensed">
	<tr>
		<th>상품 ID</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$goods_id?><?//=$data["goods_info"]["goods_id"]?>
		</td>
	</tr>
	<tr>
		<th style="width:130px;">상품설명서</th>
		<td style="text-align:center;vertical-align:middle;">
			<input type="file" name="pdf_file">
		</td>
	</tr>
</table>

<div style="width:100%;text-align:center;">
	<input type="button" class="btn btn-sm btn-warning" style="display:inline; width:70px;margin:10 auto;" onclick="go_send();" value="전송"/>
</div>

</form>

</div>

<script>
function go_send() {
	var yn = confirm("이대로 전송하시겠습니까?");
	if (!yn) return;

	var f = document.ff;
	f.mode.value="send";
	f.submit();
}
</script>

<?
function curl_p2pctr2_file($apiNo, $apiTitle , $url , $method , $data,  $product_idx="", $member_idx="") {

	$intStime = time();

	$access_token = get_access_token();
	$strApiTrxNo  = get_p2pord_no(); // API거래고유번호
	$strApiTrxDtm = get_dtm_no();    // 거래일시 (밀리세컨드)

	$headers = array();
	ARRAY_PUSH($headers,"Content-Type: multipart/form-data;");
	ARRAY_PUSH($headers,"Authorization: Bearer ".$access_token);
	ARRAY_PUSH($headers,"api_trx_no: ".$strApiTrxNo);
	ARRAY_PUSH($headers,"api_trx_dtm: ".$strApiTrxDtm);

	$json_data = json_encode($data,JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRESERVE_ZERO_FRACTION);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);


	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


	curl_setopt($ch, CURLOPT_URL, $url);
	$result = curl_exec($ch);

	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = SUBSTR($result, 0, $header_size);
	$body = SUBSTR($result, $header_size);

	curl_close($ch);

	$ret = array();
	$ret["http_code"] = $http_code;
	$ret["head"] = $header;
	$ret["body"] = $body;
	$ret["req_body"] = $json_data;

	$intEtime = time();
	$thrSec = $intStime - $intEtime;

	fn_log($apiNo, $apiTitle, $mb_no, $url, $ret["req_body"] , $ret["body"], $ret["http_code"], $thrSec, $strApiTrxNo, $product_idx, $member_idx);	// 주석풀면 에러나는 경우 있음....

	//print_r($ret);

	return $ret;
}
?>