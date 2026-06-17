<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.3.1";
$apiTitle = "상품모집 기록";
$intStime = time();
?>
<?
$url  = $p2p_host . "goods";
$method = "POST";
$strApiTrxNo  = get_p2pord_no(); // API거래고유번호
$strApiTrxDtm = get_dtm_no();    // 거래일시 (밀리세컨드)

$headers[] = "Content-Type: application/json; charset=UTF-8";
ARRAY_PUSH($headers,"Authorization: Bearer ".$access_token);
ARRAY_PUSH($headers,"api_trx_no: ".$strApiTrxNo);
ARRAY_PUSH($headers,"api_trx_dtm: ".$strApiTrxDtm);
?>
<?
$data = array();
$data["goods_info"] = array();
$data["loan_register_info"] = array();
$data["borrower_info"] = array();

$ClassProduct = new Product_Class($link);
$strProduct = $ClassProduct->product_id($product_idx); 

$prd_sql = "SELECT goods_id, title,recruit_amount,start_date, end_date,
				   loan_interest_rate, loan_usefee, invest_usefee, invest_return, loan_mb_no,
				   loan_register_id
			  FROM cf_product 
			 WHERE idx='$product_idx'";
$prd_res = sql_query($prd_sql);
$prd_row = sql_fetch_array($prd_res);

$goods_id = $prd_row["goods_id"];
if (!$goods_id AND $mode=="send") $goods_id = get_new_id("goods_id");

$fee_etc = ($prd_row["recruit_amount"] * ($prd_row["loan_usefee"] / 100)) + ($prd_row["recruit_amount"] * ($prd_row["invest_usefee"] / 100));

$strData["goods_info"]["goods_id"] = $goods_id;   // P2P온투업자 상품ID String ANS(40) (필수)
$strData["goods_info"]["goods_type"] = get_goods_type($product_idx);  // 투자 상품유형 String AN(4) (필수)
$strData["goods_info"]["goods_name"] = $prd_row["title"];   // 상품명 String max: 255 (필수)
//$strData["goods_info"]["goods_status"] = get_status($product_idx);  // 상품모집 상태 String AN(4) (필수)  T200:모집중 , T210:모집완료
$strData["goods_info"]["goods_status"] = "T210";  // 상품모집 상태 String AN(4) (필수)  T200:모집중 , T210:모집완료
$strData["goods_info"]["offering_amount_limit"] = (int)$prd_row["recruit_amount"]; // 모집금액 Number N(19) (필수)
$strData["goods_info"]["offering_start_date"] = check_int($prd_row["start_date"]); // 모집 시작일 String Date (필수)
$strData["goods_info"]["offering_end_date"] = check_int($prd_row["end_date"]); // 모집 종료일 String Date (필수)
$strData["goods_info"]["loan_interest_rate"] = (float)$prd_row["loan_interest_rate"]; // 대출금리 Number N(5,2) (필수)
$strData["goods_info"]["fee_etc"] = (int)$fee_etc; // 수수료 및 제비용 Number N(19)
$strData["goods_info"]["profit_rate"] = (float)$prd_row["invest_return"]; // 수익률 Number N(5, 2) (필수)
//$strData["goods_info"]["goods_detail_info"]["collection_info"]["collection_type"] = "CO99";  // 채권추심 방식 AN(4)  CO99 채권추심 기타
//$strData["goods_info"]["goods_detail_info"]["collection_info"]["collection_fee"] = 0;   // 채권추심 수수료 Number (19)
//$strData["goods_info"]["goods_detail_info"]["collection_info"]["collection_note"] = "";   // 채권추심 기타 내용 max:128byte
$strData["goods_info"]["goods_detail_info"]["split_yn"] = false;  // 분할모집상품 여부 true / false


$strData["loan_register_info"]["loan_register_id"] = $prd_row["loan_register_id"]; // P2P온투업자 대출신청 ID ANS(40) (필수)


$brw_info = get_brw_info($prd_row["loan_mb_no"]);

$strData["borrower_info"]["identity_no"] = $brw_info["brw_idno"]; // 차입자 string 고유식별번호 N(13)
$strData["borrower_info"]["name"] = $brw_info["brw_name"]; // 차입자 성명 string max 255
$strData["borrower_info"]["type"] = $brw_info["brw_type"]; // 차입자 유형 string an(4)
if ($strData["borrower_info"]["type"]=="B300") $strData["borrower_info"]["business_register_no"] = $brw_info["business_register_no"]; // 차입자 유형 string an(4)

if ($mode=="send") {
	$res = curl_p2pctr($url , $method , $strData , $headers);



	$intEtime = time();
	$thrSec = $intStime - $intEtime;
	fn_log($apiNo, $apiTitle, $mb_no, $url, $res["req_body"] , $res["body"], $res["http_code"], $thrSec);

	$resj = json_decode($res["body"] , true);
	if ($resj["rsp_code"] == "A0000") {
		$up_sql = "UPDATE cf_product SET goods_id = '$goods_id' WHERE idx = '$product_idx' AND goods_id='' ";
		sql_query($up_sql);
		?>
		<script>
		opener.location.reload(true); 
		alert("등록완료\n상품 ID : <?=$goods_id?>");
		self.close();
		</script>
		<?
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
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["goods_info"]["goods_id"]?>
		</td>
		<td style="background-color:#F5F5F5;"></td>
	</tr>
	<tr>
		<th style="width:130px;">상품 타입</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["goods_info"]["goods_type"]?>
		</td>
		<td style="width:350px;background-color:#F5F5F5;">
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
		<th>상품명</th>
		<td style="text-align:left; vertical-align:middle;" colspan=2>
			<?=$strData["goods_info"]["goods_name"]?>
		</td>
	</tr>
	<tr>
		<th>상품상태</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["goods_info"]["goods_status"]?>
		</td>
		<td style="background-color:#F5F5F5;">T200:모집중 / T210 모집완료</td>
	</tr>
	<tr>
		<th>모집금액</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=number_format($strData["goods_info"]["offering_amount_limit"])?>
		</td>
		<td style="background-color:#F5F5F5;"></td>
	</tr>
	<tr>
		<th>모집일</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["goods_info"]["offering_start_date"]?> ~ <?=$strData["goods_info"]["offering_end_date"]?>
		</td>
		<td style="background-color:#F5F5F5;"></td>
	</tr>
	<tr>
		<th>대출금리</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["goods_info"]["loan_interest_rate"]?>
		</td>
		<td style="background-color:#F5F5F5;"></td>
	</tr>
	<tr>
		<th>수수료 및 제비용</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=number_format($strData["goods_info"]["fee_etc"])?>
		</td>
		<td style="background-color:#F5F5F5;"><?="($prd_row[recruit_amount] * ($prd_row[loan_usefee] / 100))<br/>+<br/>($prd_row[recruit_amount] * ($prd_row[invest_usefee] / 100))"?></td>
	</tr>
	<tr>
		<th>수익율</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["goods_info"]["profit_rate"]?>
		</td>
		<td style="background-color:#F5F5F5;"></td>
	</tr>
	<tr>
		<th>분할모집상품 여부</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=var_dump($strData["goods_info"]["goods_detail_info"]["split_yn"])?>
		</td>
		<td style="background-color:#F5F5F5;"></td>
	</tr>
	<tr>
		<th>대출신청 ID</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["loan_register_info"]["loan_register_id"]?>
		</td>
		<td style="background-color:#F5F5F5;"></td>
	</tr>
	<tr>
		<th>차주 유형</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["borrower_info"]["type"]?>
		</td>
		<td style="background-color:#F5F5F5;">B100 (개인), B200 (개인사업자), B300 (법인)</td>
	</tr>
	<tr>
		<th>차주 고유식별번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["borrower_info"]["identity_no"]?>
		</td>
		<td style="background-color:#F5F5F5;">
			개인(13자리) - 주민등록번호 , 외국인등록번호 <br/>
			법인(13자리) - 법인등록번호<br/>
			법인등록번호가 없는 비영리 법인의 경우(13자리) :<br/>&nbsp;&nbsp;&nbsp;&nbsp; 사업자등록번호(10) + ‘000’
		</td>
	</tr>
	<tr>
		<th>차주 이름</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["borrower_info"]["name"]?>
		</td>
		<td style="background-color:#F5F5F5;"></td>
	</tr>
	<? if ($strData["borrower_info"]["type"]=="B300") { ?>
	<tr>
		<th>사업자 등록번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$strData["borrower_info"]["business_register_no"]?>
		</td>
		<td style="background-color:#F5F5F5;"></td>
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
