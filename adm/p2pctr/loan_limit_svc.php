<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

$apiNo = "4.2.1";
$apiTitle = "대출금액 조회";

if ($member_idx) {

	$res = get_p2pctr_loan_limit_ori($member_idx);

}
else if ($member_id) {
	$mem_sql = "SELECT mb_no FROM g5_member WHERE mb_id='".$member_id."'";
	$mem_row = sql_fetch($mem_sql);
	$mb_no = $mem_row["mb_no"];

	if ($mb_no) $res = get_p2pctr_loan_limit_ori($mb_no);

}
//echo "<pre>"; print_r($res); echo "</pre>";

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');

?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?></h3>

	<table style="width:100%;border:0;">
		<tr>
			<td style="text-align:center; border:0;">
				<form name="ff" method="post">
					<input type=hidden name="mode" value=""/>
					회원번호  <input type="text" name="member_idx" value="<?=$member_idx?>" class="input-sm" style="width:100px;font-size:15px;"/>&nbsp;&nbsp;
					회원아이디 <input type="text" name="member_id" value="<?=$member_id?>" class="input-sm" style="width:150px;font-size:15px;"/>&nbsp;&nbsp;&nbsp;
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
if ($res["rsp_code"]=="A0000") {
?>
	<br/>
	<center>
	<h3><?=substr($res["borrower_info"]["identity_no"],0,6)?><?=str_repeat(" *",strlen(substr($res["borrower_info"]["identity_no"],6)))?> 대출금액 조회 </h3>

	<table class="table table-bordered table-condensed" style="width:600px;">
		<tr>
			<th style="text-align:center;vertical-align:middle;">상태</th>
			<th style="text-align:center;vertical-align:middle;">계약금액</th>
			<th style="text-align:center;vertical-align:middle;">대출잔액</th>
			<th style="text-align:center;vertical-align:middle;">상환/연체중</th>
		</tr>
		<tr>
			<td style="text-align:center;vertical-align:middle;">진행중</td>
			<td style="text-align:right;vertical-align:middle;"><?=number_format($res["in_progress_result_info"]["loan_contract_amount"])?></td>
			<td style="text-align:right;vertical-align:middle;"><?=number_format($res["in_progress_result_info"]["loan_balance"])?></td>
			<td style="text-align:right;vertical-align:middle;"><?=number_format($res["in_progress_result_info"]["in_progress_count"])?></td>
		</tr>
		<tr>
			<td style="text-align:center;vertical-align:middle;">상환완료</td>
			<td style="text-align:right;vertical-align:middle;"><?=number_format($res["complete_result_info"]["loan_contract_amount"])?></td>
			<td style="text-align:right;vertical-align:middle;"></td>
			<td style="text-align:right;vertical-align:middle;"><?=number_format($res["complete_result_info"]["complete_count"])?></td>
		</tr>
	</table>
	<br/>조회일시 : <?=date("Ymd His")?>
	</center>
<?
}
else {

	if($res) print_rr($res);

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

function get_p2pctr_loan_limit_ori($mb_no) {

	if (!$mb_no) return;

	global $p2p_host;

	$apiNo = "4.2.1";
	$apiTitle = "대출금액 조회";

	$url  = $p2p_host . "loans/inquiry";
	$method = "POST";

	$brw_info = get_brw_info($mb_no);

	$data = array();
	$data['borrower_identity_no'] = $brw_info['brw_idno'];

	//echo "<pre>"; print_r($data); echo "</pre>";


	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, $mb_no);
	$resj = json_decode($curl_res['body'] , true);

	return $resj;
}

?>