<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }


$apiNo = "4.2.2";
$apiTitle = "대출신청 기록";

$url  = $p2p_host . "loans/register";
//if ($product_idx=="3107") $url  = $p2p_host . "data/loans/register";  // 이관용
$method = "POST";


$data = array();
$data["loan_register_info"] = array();   // 투자신청 정보 필수O

// 2021-09-29 이철규 주임이 김관우 주임과 검토후 금융위에 통보후 프로그램 수정후 등록
if ($product_idx=="7055" OR $product_idx=="7272" OR $product_idx=="7554" OR $product_idx=="7845") {
	$data["loan_info_detail"]   = array();   // 대출신청 정보 - 부가정보 필수X
	$data["loan_info_detail"]["limit_check_yn"] = false;
	$data["loan_info_detail"]["limit_check_exception_note"] = "LR40"; // LR20 국가, 지방자치단체 등 공공기관에서 대출하는 경우 , LR30 그 외 금융위원회가 인정하는 경우 , LR40 대환대출 신청 시 예외 처리가 필요한 경우
}

$data["borrower_info"] = array();   // 차입자 정보 필수O


$prd_sql = "
	SELECT
		idx, loan_register_id, start_date, invest_period, loan_mb_no, invest_days, recruit_amount, loan_interest_rate, insert_date
	FROM
		cf_product
	WHERE
		idx='$product_idx'";
$prd_row = sql_fetch($prd_sql);

if (!$prd_row['idx']) die("상품 없음");


//echo get_new_id("loan_register_id");
//echo get_new_id("goods_id");
//echo get_new_id("investment_register_id");
//echo get_new_id("loan_contract_id");
//echo get_new_id("contract_id");

$loan_register_id = $prd_row["loan_register_id"];
$bank_inquiry_id  = $prd_row["idx"];

// P2P온투업자 대출신청 ID 설정
if ($mode=="send" AND !$loan_register_id) {
	$loan_register_id = get_new_id("loan_register_id");
}

$loan_term_days = get_term_days($prd_row["start_date"], $prd_row["invest_period"], $prd_row["invest_days"]);

$data["loan_register_info"]["loan_register_id"]     = $loan_register_id;													// P2P온투업자 대출신청 ID
$data["loan_register_info"]["bank_inquiry_id"]      = $bank_inquiry_id;														// (2022-03-18 수정 배부장) 예치기관 대출정보 조회번호 Y String Max: 40bytes
//$data["loan_register_info"]["bank_inquiry_id"]      = $loan_register_id;													// 예치기관 대출정보 조회번호 Y String Max: 40bytes
$data["loan_register_info"]["goods_type"]           = get_goods_type($product_idx);
$data["loan_register_info"]["loan_register_amount"] = intval($prd_row["recruit_amount"]);					// 대출 신청금액
$data["loan_register_info"]["loan_term_days"]       = intval($loan_term_days);										// 대출 기간 (일단위)
$data["loan_register_info"]["status"]               = "T100";																			// 신청상태 T100 신청중
$data["loan_register_info"]["status_note"]          = "";																					// 신청상태 사유 max128 bytes
$data["loan_register_info"]["loan_interest_rate"]   = floatval($prd_row["loan_interest_rate"]);		// 대출금리
//$data["loan_register_info"]["loan_penalty_rate"] = "";																						// 연체금리
//$data["loan_register_info"]["fee_etc"] = "";																											// 수수료 및 제비용
//$data["loan_register_info"]["fee_rate"] = "";																											// 수수료율
$data["loan_register_info"]["loan_register_dtm"]    = preg_replace('/[^0-9]/','', $prd_row["insert_date"]);				// 대출신청일시



$brw_info = get_brw_info($prd_row["loan_mb_no"]);
$data["borrower_info"]["identity_no"] = $brw_info["brw_idno"];  // 차입자 string 고유식별번호 N(13)
$data["borrower_info"]["name"]        = $brw_info["brw_name"];  // 차입자 성명 string max 255
$data["borrower_info"]["type"]        = $brw_info["brw_type"];  // 차입자 유형 string an(4) B100 개인 , B200 개인사업자 , B300 법인
if ($data["borrower_info"]["type"]=="B300") { $data["borrower_info"]["business_register_no"] = $brw_info["business_register_no"]; }

if ($mode=="send" AND !$prd_row["loan_register_id"]) {

	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, "");
	$resj = json_decode($curl_res["body"] , true);

	if ($resj["rsp_code"] == "A0000") {
		$up_sql = "
			UPDATE
				cf_product
			SET
				loan_register_id = '$loan_register_id',
				bank_inquiry_id = '$bank_inquiry_id'
			WHERE
				idx = '$product_idx' AND loan_register_id=''";		// 2022-03-18 수정 배부장
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
					loan_register_id = '$loan_register_id',
					loan_register_status = 'T100',
					loan_register_datetime = NOW()
				WHERE
					idx = '".$main_row['idx']."'";
			sql_query($main_up_sql);
		}
		else {
			$main_ins_sql = "
				INSERT INTO
					p2pctr_product
				SET
					product_idx = '$product_idx',
					loan_register_id = '$loan_register_id',
					loan_register_status = 'T100',
					loan_register_datetime = NOW()";
			sql_query($main_ins_sql);
		}


?>
		<script>
		opener.location.reload(true);
		alert("등록완료\n대출신청 ID : <?=$loan_register_id?>");
		self.close();
		</script>
		<?
	} else {
		echo "기록 실패<br/><br/>";
		echo "<pre>"; print_r($resj); echo "</pre>";
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
		<th>대출신청 ID</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_register_info"]["loan_register_id"]?>
		</td>
	</tr>
	<tr>
		<th>상품타입</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_register_info"]["goods_type"]?><br/><br/>
			P110 부동산 프로젝트파이낸싱 연계대출 상품<br/>
			P120 부동산 담보 연계대출 상품<br/>
			P210 어음·매출채권 담보 연계대출 상품<br/>
			P220 기타 담보 연계대출 상품(어음·매출채권 제외)<br/>
			P230 개인 신용 연계대출 상품<br/>
			P240 법인 신용 연계대출 상품<br/>
			P000 전체 상품 ALL
		</td>
	</tr>
	<tr>
		<th>대출신청금액</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=number_format($data["loan_register_info"]["loan_register_amount"])?>
		</td>
	</tr>
	<tr>
		<th>대출기간</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=number_format($data["loan_register_info"]["loan_term_days"])?> 일
		</td>
	</tr>
	<tr>
		<th>신청상태</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_register_info"]["status"]?><br/><br/>
			T100 신청중 , T200 모집중 , T210 모집완료
		</td>
	</tr>
	<tr>
		<th>대출금리</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_register_info"]["loan_interest_rate"]?>
		</td>
	</tr>
	<tr>
		<th>대출신청일시</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_register_info"]["loan_register_dtm"]?>
		</td>
	</tr>
	<tr>
		<th>차주인증번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=substr($data["borrower_info"]["identity_no"],0,6)?>-<?=str_repeat(" *",strlen(substr($data["borrower_info"]["identity_no"],6)))?>
		</td>
	</tr>
	<tr>
		<th>차주명</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["borrower_info"]["name"]?>
		</td>
	</tr>
	<tr>
		<th>차주 유형</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["borrower_info"]["type"]?><br/><br/>
			B100 개인 , B200 개인사업자 , B300 법인
		</td>
	</tr>
	<? if ($data["borrower_info"]["type"]=="B300") { ?>
	<tr>
		<th>차주 사업자번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["borrower_info"]["business_register_no"]?>
		</td>
	</tr>
	<? } ?>
	<tr>
		<th>예치기관 투자정보 조회번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_register_info"]["bank_inquiry_id"]?>
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
