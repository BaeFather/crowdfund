<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }


$apiNo = "4.2.3";
$apiTitle = "대출신청 갱신";


$prd_sql = "
	SELECT idx, loan_register_id, start_date, invest_period, loan_mb_no, invest_days, recruit_amount, loan_interest_rate, insert_date
	FROM cf_product
	WHERE idx = '$product_idx'";
$prd_row = sql_fetch($prd_sql);

if (!$prd_row['idx']) die("상품 없음");

$url  = $p2p_host . "loans/register/".$prd_row["loan_register_id"];
$method = "PUT";

$data = array();
$data["status"] = "T150";

if ($mode=="send" and $prd_row["loan_register_id"]) {

	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx);
	$resj = json_decode($curl_res["body"] , true);

	if ($resj["rsp_code"] == "A0000") {

		$up_sql = "UPDATE cf_product SET loan_register_id = '' WHERE idx = '$product_idx'  ";
		sql_query($up_sql);

		$main_sql = "SELECT * FROM p2pctr_product WHERE product_idx='$product_idx'";
		$main_res = sql_query($main_sql);
		$main_cnt = $main_res->num_rows;

		if ($main_cnt) {
			$main_row = sql_fetch_array($main_res);
			$main_up_sql = "
				UPDATE
					p2pctr_product
				SET
					loan_register_id = '',
					loan_register_datetime = NOW()
				WHERE
					idx = '".$main_row['idx']."'";
			sql_query($main_up_sql);
		}

?>
		<script>
		opener.location.reload(true);
		alert("취소완료");
		self.close();
		</script>
<?
	}
	else {
		echo "기록 실패<br/><br/>";
		echo "<pre>"; print_r($resj); echo "</pre>";
	}
}

?>

<?

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');

?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?></h3>

<table class="table table-bordered table-condensed">
	<tr>
		<th>대출신청 ID</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$prd_row["loan_register_id"]?>
		</td>
	</tr>
	<tr>
		<th>변경할 상태</th>
		<td style="text-align:center;vertical-align:middle;">
			신청 취소
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