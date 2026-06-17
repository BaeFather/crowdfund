<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

if (!$loan_contract_id OR strlen($loan_contract_id)<>31) die("대출계약번호 오류");

$sql = "SELECT idx FROM cf_product WHERE loan_contract_id='$loan_contract_id'";
$row = sql_fetch($sql);
$product_idx = $row["idx"];
?>
<?
$apiNo = "4.2.5";
$apiTitle = "대출계약 조회";
?>
<?
$url  = $p2p_host . "loans/contract/".$loan_contract_id;
$method = "REST_GET";

$data = array();
$res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, "");

$rbody = json_decode($res["body"],true);
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
<table class="table table-bordered table-condensed">
	<tr>
		<th>처리결과</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$rbody["rsp_code"]?> <?=$rbody["rsp_message"]?>
		</td>
	</tr>
	<tr>
		<th>대출계약 상태</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=get_status_text($rbody["status"]);?> <?=$rbody["status_note"]?>
		</td>
	</tr>
	<tr>
		<th>대출신청 ID</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$rbody["loan_info"]["loan_register_id"];?>
		</td>
	</tr>
	<tr>
		<th>대출계약 ID</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$rbody["loan_info"]["loan_contract_id"];?>
		</td>
	</tr>
	<tr>
		<th>예치기관 대출정보 조회번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$rbody["loan_info"]["bank_inquiry_id"];?>
		</td>
	</tr>
	<tr>
		<th>상품관리번호 목록</th>
		<td style="text-align:center;vertical-align:middle;">
			<? for ($i=0 ; $i<count($rbody["loan_info"]["goods_id_list"]) ; $i++) {
					if ($i<>0) echo "<br/>";
					echo $rbody["loan_info"]["goods_id_list"][$i]["goods_id"];
			} ?>
		</td>
	</tr>
	<tr>
		<th>계약 금액</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=number_format($rbody["loan_info"]["loan_amount"]);?> 원
		</td>
	</tr>
	<tr>
		<th>계약 기간</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=number_format($rbody["loan_info"]["loan_term_days"]);?> 일
		</td>
	</tr>
	<tr>
		<th>대출금리</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$rbody["loan_info"]["loan_interest_rate"];?> %
		</td>
	</tr>
	<tr>
		<th>계약일시</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$rbody["loan_info"]["contract_dtm"];?>
		</td>
	</tr>
	<tr>
		<th>플랫폼 수수료등</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$rbody["loan_info"]["fee_etc"];?>
		</td>
	</tr>
	<tr>
		<th>차입자 고유식별번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=substr($rbody["borrower_info"]["identity_no"],0,6)?><?=str_repeat(" *",strlen(substr($rbody["borrower_info"]["identity_no"],6)))?>
		</td>
	</tr>
	<tr>
		<th> 예치기관 대출정보 조회번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$rbody["loan_info"]["bank_inquiry_id"];?>
		</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align:center;vertical-align:middle;">
			<table class="table table-bordered table-condensed">
				<tr>
					<th>회차</th>
					<th>상환유형</th>
					<th>상환일</th>
					<th>원금</th>
					<th>이자</th>
					<th>상환금</th>
				</tr>
			<? for ($i=0 ; $i<count($rbody["loan_repayment_list"]) ; $i++) { ?>
				<tr>
					<td style="text-align:center;vertical-align:middle;"><?=$rbody["loan_repayment_list"][$i]["repayment_n_count"]?> 회차</td>
					<td style="text-align:center;vertical-align:middle;"><?=get_repay_type_txt($rbody["loan_repayment_list"][$i]["repayment_type"])?></td>
					<td style="text-align:center;vertical-align:middle;"><?=$rbody["loan_repayment_list"][$i]["repayment_date"]?></td>
					<td style="text-align:right;vertical-align:middle; margin-right:8px;"><?=number_format($rbody["loan_repayment_list"][$i]["repayment_p_amount"])?></td>
					<td style="text-align:right;vertical-align:middle; margin-right:8px;"><?=number_format($rbody["loan_repayment_list"][$i]["repayment_interest"])?></td>
					<td style="text-align:right;vertical-align:middle; margin-right:8px;"><?=number_format($rbody["loan_repayment_list"][$i]["repayment_total_amount"])?></td>
				</tr>
			<? } ?>
			</table>
		</td>
	</tr>
</table>
<?
//echo "<pre>"; print_r($rbody); echo "</pre>";
?>