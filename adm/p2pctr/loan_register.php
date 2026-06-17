<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.2.2";
$apiTitle = "대출신청 기록";
$intStime = time();
?>
<?
$url  = $p2p_host . "loans/register";
$method = "POST";
$strApiTrxNo  = get_p2pord_no(); // API거래고유번호
$strApiTrxDtm = get_dtm_no();    // 거래일시 (밀리세컨드)

$headers[] = "Content-Type: application/json; charset=UTF-8";
ARRAY_PUSH($headers,"Authorization: Bearer ".$access_token);
ARRAY_PUSH($headers,"api_trx_no: ".$strApiTrxNo);
ARRAY_PUSH($headers,"api_trx_dtm: ".$strApiTrxDtm);

$data = array();
$data["loan_register_info"] = array();   // 투자신청 정보 필수O
//$data["loan_info_detail"]   = array();   // 대출신청 정보 - 부가정보 필수X
$data["borrower_info"]      = array();   // 차입자   정보 필수O
?>
<?
$ClassProduct = new Product_Class($link);
$strProduct = $ClassProduct->product_id($product_idx);
//$strBorrower = $ClassProduct->product_loan_request($product_idx);

$prd_sql = "SELECT loan_register_id, bank_inquiry_id, start_date, invest_period, loan_mb_no, invest_days FROM cf_product WHERE idx='$product_idx'";		// 2022-03-18 수정 배부장
$prd_row = sql_fetch($prd_sql);

$brw_sql = "SELECT mb_no, mb_name, member_type, mb_co_name, corp_num, mb_co_reg_num FROM g5_member WHERE mb_no='".$prd_row["loan_mb_no"]."'";
$brw_row = sql_fetch($brw_sql);

if ($brw_row["member_type"]==2) { // 법인
	$brw_type = "B300";
	$brw_idno = $brw_row["corp_num"];
	$brw_name = $brw_row["mb_co_name"];

} else {  // 개인
	$brw_type="B100";
	$brw_idno = getJumin($prd_row["loan_mb_no"]);
	$brw_name = $brw_row["mb_name"];

}



$loan_register_id = $prd_row["loan_register_id"];
if ($mode=="send" AND !$loan_register_id) $loan_register_id = get_new_id("loan_register_id");

//echo $prd_row["start_date"]. " ". $prd_row["invest_period"]." $loan_register_id<br/>";


$loan_term_days = get_term_days($prd_row["start_date"], $prd_row["invest_period"], $prd_row["invest_days"]);


$data["loan_register_info"]["loan_register_id"]     = $loan_register_id;        // P2P온투업자 대출신청 ID
$data["loan_register_info"]["bank_inquiry_id"]      = $prd_row['bank_inquiry_id'];			// 2022-03-18 수정 배부장. 예치기관 대출정보 조회번호 Y String Max: 40bytes
//$data["loan_register_info"]["bank_inquiry_id"]      = $loan_register_id;        // 예치기관 대출정보 조회번호 Y String Max: 40bytes
$data["loan_register_info"]["goods_type"]           = get_goods_type($product_idx); //= $ClassProduct->product_code($strProduct["category"], $strProduct["mortgage_guarantees"]);  // 상품 유형
$data["loan_register_info"]["loan_register_amount"] = intval($strProduct["recruit_amount"]);   // 대출 신청금액
$data["loan_register_info"]["loan_term_days"]       = intval($loan_term_days);  // 대출 기간 (일단위)
$data["loan_register_info"]["status"]               = "T100";   // 신청상태 T100 신청중
$data["loan_register_info"]["status_note"]          = "";   // 신청상태 사유 max128 bytes
$data["loan_register_info"]["loan_interest_rate"]   = floatval($strProduct["loan_interest_rate"]);   // 대출금리
//$data["loan_register_info"]["loan_penalty_rate"] = "";  // 연체금리
//$data["loan_register_info"]["fee_etc"] = "";    // 수수료 및 제비용
//$data["loan_register_info"]["fee_rate"] = "";    // 수수료율
$data["loan_register_info"]["loan_register_dtm"]    = date("Ymdhis");          // 대출신청일시

$data["borrower_info"]["identity_no"]               = $brw_idno; //$strBorrower["brw_identity_no"]; // 차입자 string 고유식별번호 N(13)
$data["borrower_info"]["name"]                      = $brw_name; //$strBorrower["brw_name"]; // 차입자 성명 string max 255
$data["borrower_info"]["type"]                      = $brw_type; //$strBorrower["brw_type"]; // 차입자 유형 string an(4) B100 개인 , B200 개인사업자 , B300 개인
if ($brw_row["member_type"]==2) $data["borrower_info"]["business_register_no"] = $brw_row["mb_co_reg_num"];


if ($mode=="send" AND !$prd_row["loan_register_id"]) {


	$res = curl_p2pctr($url , $method , $data , $headers);

	$resj = json_decode($res["body"] , true);

	if ($resj["rsp_code"] == "A0000") {
		$up_sql = "UPDATE cf_product SET loan_register_id = '$loan_register_id' WHERE idx = '$product_idx' AND loan_register_id='' ";
		sql_query($up_sql);
		?>
		<script>
		opener.location.reload(true);
		alert("등록완료\n대출신청 ID : <?=$loan_register_id?>");
		self.close();
		</script>
		<?
	} else {
		echo "기록 실패<br/><br/>";
		echo "<pre>"; print_r($res); echo "</pre>";
	}


	$intEtime = time();
	$thrSec = $intStime - $intEtime;
	fn_log($apiNo, $apiTitle, $mb_no, $url, $res["req_body"] , $res["body"], $res["http_code"], $thrSec);

}
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
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
			<?=substr($data["borrower_info"]["identity_no"],0,6)?>-<?=str_repeat("*",strlen(substr($data["borrower_info"]["identity_no"],6)))?>
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
	<? if ($brw_row["member_type"]==2) { ?>
	<tr>
		<th>차주 사업자번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["borrower_info"]["business_register_no"]?>
		</td>
	</tr>
	<? } ?>
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