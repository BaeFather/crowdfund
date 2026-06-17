<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.4.6";
$apiTitle = "투자계약 조회";
?>
<?
$sql = "SELECT product_idx, member_idx FROM cf_product_invest WHERE contract_id='$contract_id'";
$row = sql_fetch($sql);
$product_idx = $row["product_idx"];
$member_idx = $row["member_idx"];

$url  = $p2p_host . "investments/contract/".$contract_id;
$method = "REST_GET";

$data = array();
$res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, $member_idx);

$resj = json_decode($res["body"],true);
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?></h3>
<table class="table table-bordered table-condensed">
	<tr>
		<th>조회결과</th>
		<td style="text-align:center;vertical-align:middle;"><?=$resj["rsp_code"]=="A0000"?"성공":"실패";?></td>
	</tr>
	<tr>
		<th>투자계약ID</th>
		<td style="text-align:center;vertical-align:middle;"><?=$resj["investment_contract_info"]["investment_contract_id"]?></td>
	</tr>
	<tr>
		<th>예치기관 투자정보<br/>조회번호</th>
		<td style="text-align:center;vertical-align:middle;"><?=$resj["investment_contract_info"]["bank_inquiry_id"]?></td>
	</tr>
	<tr>
		<th>투자금액</th>
		<td style="text-align:center;vertical-align:middle;"><?=number_format($resj["investment_contract_info"]["contract_amount"])?> 원</td>
	</tr>
	<tr>
		<th>양도금액</th>
		<td style="text-align:center;vertical-align:middle;"><?=number_format($resj["investment_contract_info"]["transfer_sell_amount"])?> 원</td>
	</tr>
	<tr>
		<th>원리금지급액</th>
		<td style="text-align:center;vertical-align:middle;"><?=number_format($resj["investment_contract_info"]["pay_p_amount_total"])?> 원</td>
	</tr>
	<tr>
		<th>투자잔액</th>
		<td style="text-align:center;vertical-align:middle;"><?=number_format($resj["investment_contract_info"]["invest_balance"])?> 원</td>
	</tr>
	<tr>
		<th>원리금 실 지급금액 합계</th>
		<td style="text-align:center;vertical-align:middle;"><?=number_format($resj["investment_contract_info"]["actual_pay_amount_total"])?> 원</td>
	</tr>
	<tr>
		<th>최근 원리금 지급기록 회차</th>
		<td style="text-align:center;vertical-align:middle;"><?=number_format($resj["investment_contract_info"]["latest_securities_n_count"])?> 회차</td>
	</tr>
	<tr>
		<th>투자계약 체결일시</th>
		<td style="text-align:center;vertical-align:middle;"><?=$resj["investment_contract_info"]["contract_dtm"]?></td>
	</tr>
	<tr>
		<th>투자계약 상태</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$resj["investment_contract_info"]["status"]?><br/>(S100 상환중 / S150 연체중 / S3** 상환완료)
		</td>
	</tr>
	<tr>
		<th>무결성 검증값</th>
		<td style="text-align:center;vertical-align:middle;"><?=$resj["investment_contract_info"]["document_hash_value"]?></td>
	</tr>


	<tr>
		<th>반복투자 여부</th>
		<td style="text-align:center;vertical-align:middle;"><?=$resj["investment_contract_info"]["system_auto_investment_info"]["system_auto_investment_yn"]?></td>
	</tr>
	<tr>
		<th>반복투자 기간(일 단위)</th>
		<td style="text-align:center;vertical-align:middle;"><?=$resj["investment_contract_info"]["system_auto_investment_info"]["term_days"]?></td>
	</tr>
	<tr>
		<th>반복투자 건당 투자금액</th>
		<td style="text-align:center;vertical-align:middle;"><?=$resj["investment_contract_info"]["system_auto_investment_info"]["unit_amount"]?></td>
	</tr>
	<tr>
		<th>반복투자 기간 중 총 투자금액</th>
		<td style="text-align:center;vertical-align:middle;"><?=$resj["investment_contract_info"]["system_auto_investment_info"]["total_register_amount"]?></td>
	</tr>


	<tr>
		<th>투자자 식별번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=substr($resj["investor_info"]["identity_no"],0,7)?><?=str_repeat("*",strlen(substr($resj["investor_info"]["identity_no"],7)))?>
		</td>
	</tr>
	<tr>
		<th>투자자 이름</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$resj["investor_info"]["name"]?>
		</td>
	</tr>
	<tr>
		<th>투자자 유형</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$resj["investor_info"]["type"]?><br/>
			I110 일반개인투자자 / I120 소득적격투자자 개인 / I130 개인전문투자자<br/>
			I310 법인투자자 / I320 여신금융기관 법인 / I330 P2P온투업
		</td>
	</tr>
	<tr>
		<th>투자자 사업자 번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$resj["investor_info"]["business_register_no"]?>
		</td>
	</tr>
	<tr>
		<th>투자자 본인확인 방법</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$resj["investor_info"]["investor_audit_type"]?>
		</td>
	</tr>



	<tr>
		<th>P2P온투업자 상품관리번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$resj["goods_info"]["goods_id"]?>
		</td>
	</tr>
	<tr>
		<th>투자 상품유형</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$resj["goods_info"]["goods_type"]?><br/><br/>
			<div style="width:100%;text-align:left;">
			(부동산)P110 부동산 프로젝트파이낸싱 연계대출 상품<br/>
			(부동산)P120 부동산 담보 연계대출 상품<br/>
			(매출채권)P210 어음·매출채권 담보 연계대출 상품<br/>
			(주택담보)P220 기타 담보 연계대출 상품(어음·매출채권 제외)<br/>
			(신용)P230 개인 신용 연계대출 상품 / (신용)P240 법인 신용 연계대출 상품<br/>
			(전체)P000 전체 상품 ALL
			</div>
		</td>
	</tr>

</table>
	<?
	//echo "<pre>"; print_r($rbody); echo "</pre>";
	?>
</div>