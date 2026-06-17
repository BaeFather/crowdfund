<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

$apiNo = "4.4.1";
$apiTitle = "투자잔액 조회";

if ($member_idx) {

	$mem_chk = sql_fetch("select mb_id from g5_member where mb_no='$member_idx'");
	$mem_id = $mem_chk["mb_id"];

	$res = get_p2pctr_limit_ori($mem_id, $product_idx);

}

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');

?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?></h3>

	<table style="width:100%;border:0;">
		<tr>
			<td style="text-align:center; border:0;">
				<form name="ff" method="post">
					<input type=hidden name="mode" value=""/>
					회원번호 <input type="text" name="member_idx" value="<?=$member_idx?>" class="input-sm" style="width:70px;"/>&nbsp;&nbsp;&nbsp;
					상품번호 <input type="text" name="product_idx" value="<?=$product_idx?>" class="input-sm" style="width:70px;"/>&nbsp;&nbsp;&nbsp;
					<input type="button" class="btn btn-sm btn-default" onclick="go_send();" value="조회"/>
				</form>
			</td>
		</tr>
	</table>

	<br/>

	<table class="table table-bordered table-condensed">
<?
for ($i=0 ; $i<count($res["goods_balance_list"]) ; $i++) {
	if ($res["goods_balance_list"][$i]["goods_type"]=="P000") $gbn_txt="전체 투자액";
	else if ($res["goods_balance_list"][$i]["goods_type"]=="P110") $gbn_txt="부동산 PF 연계대출";
	else if ($res["goods_balance_list"][$i]["goods_type"]=="P120") $gbn_txt="부동산 담보 연계대출";
	else if ($res["goods_balance_list"][$i]["goods_type"]=="P210") $gbn_txt="어음·매출채권 담보 연계대출";
	else if ($res["goods_balance_list"][$i]["goods_type"]=="P220") $gbn_txt="기타 담보 연계대출";
	else if ($res["goods_balance_list"][$i]["goods_type"]=="P230") $gbn_txt="개인 신용 연계대출";
	else if ($res["goods_balance_list"][$i]["goods_type"]=="P240") $gbn_txt="법인 신용 연계대출";
	else $gbn_txt="";
?>
		<tr>
			<td style="text-align:center;vertical-align:middle;"><?=$gbn_txt?></th>
			<td style="text-align:right;vertical-align:middle;"><?=number_format($res["goods_balance_list"][$i]["balance"])?></th>
		</tr>
<?
}
?>
<? if ($res["balance_per_borrower"]) { ?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">동일차주 투자금액</th>
			<td style="text-align:right;vertical-align:middle;"><?=number_format($res["balance_per_borrower"])?></th>
		</tr>
<? } ?>
	</table>

<?
if (count($res["buyer_info"]["investment_history_list"])) {
?>
	<br/>

	<h3>투자기록</h3>

	<table class="table table-bordered table-condensed">
<?
	for ($i=0 ; $i<count($res["buyer_info"]["investment_history_list"]) ; $i++) {
?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">투자일시</th>
			<td style="text-align:center;vertical-align:middle;">
				<?=date("Y-m-d H:i:s", strtotime($res["buyer_info"]["investment_history_list"][$i]["contract_dtm"]))?>
			</th>
		</td>
<?
	}
?>
	</table>
<?
}
?>

</div>

<script>
function go_send() {
	var f = document.ff;
	f.mode.value="send";
	f.submit();
}
</script>

<?

function get_p2pctr_limit_ori($mb_id, $product_idx="") {

	if (!$mb_id) return;

	global $p2p_host;

	$apiNo = "4.4.1";
	$apiTitle = "투자잔액 조회";

	$url  = $p2p_host . "investments/inquiry";
	$method = "POST";

	if ($product_idx) {
		$psql = "SELECT idx, recruit_amount, loan_mb_no FROM cf_product WHERE idx = '".$product_idx."'";
		$prow = sql_fetch($psql);
		$product_idx = ($prow['idx']) ? $prow['idx'] : '';
	}

	$sql = "SELECT mb_no, member_type, member_investor_type FROM g5_member WHERE mb_id='".$mb_id."' AND mb_level BETWEEN 1 AND 5";
	$row = sql_fetch($sql);
	if (!$row['mb_no']) return;
	$mno = $row['mb_no'];

	$inv_info = get_inv_info($mno);
	if($product_idx) $brw_info = get_brw_info($prow['loan_mb_no']);


	$data = array();
	$data['investor_identity_no'] = $inv_info['inv_idno'];
	if ($brw_info['brw_idno']) $data['borrower_identity_no'] = $brw_info['brw_idno'];

	//echo "<pre>"; print_r($data); echo "</pre>";

	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, $mno);
	$resj = json_decode($curl_res['body'] , true);

	return $resj;
}

?>