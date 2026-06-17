<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.4.2";
$apiTitle = "투자신청 기록";

if ($imsi=="Y") $p2p_host=$p2p_host."data/";

$url  = $p2p_host . "investments/register";
//if ($product_idx=="3107") $url  = $p2p_host . "data/investments/register";  // 이관용
$method = "POST";
?>
<?
$sql = "SELECT idx, goods_id, bank_inquiry_id FROM cf_product where idx='$product_idx'";		// 2022-03-18 수정 배부장
$row = sql_fetch($sql);

$goods_type = get_goods_type($product_idx);

$sqli = "SELECT * FROM cf_product_invest WHERE product_idx='$product_idx' AND invest_state='Y' ORDER BY idx DESC";
$resi = sql_query($sqli);
$cnti = sql_num_rows($resi);
$no = $cnti;
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?><?=strpos($url,'data')!==false?" (이관용)":""?></h3>
<table class="table table-bordered table-condensed">
	<tr>
		<th>No</th>
		<th>회원번호</th>
		<th>이름</th>
		<th>투자자타입</th>
		<th>인증번호</th>
		<th>금액</th>
		<th>투자신청일시</th>
		<th>투자신청ID</th>
		<th>조회</th>
		<th>결과</th>
	</tr>
<?
$tot = 0;
for ($i=0 ; $i<$cnti ; $i++) {

	$rowi = sql_fetch_array($resi);

	$data = array();

	$data["investment_register_info"] = array();
	$data["investor_info"] = array();
	$data["goods_info"] = array();

	$investment_register_id = $rowi["investment_register_id"];
	if (!$investment_register_id AND $mode=="send") $investment_register_id = get_new_id("investment_register_id");

	$data["investment_register_info"]["investment_register_id"] = $investment_register_id;
	$data["investment_register_info"]["bank_inquiry_id"] = $rowi['prin_rcv_no'];			// <======= 2022-03-18 수정 배부장
	$data["investment_register_info"]["investment_amount"] = (int)$rowi["amount"];
	$data["investment_register_info"]["investment_register_dtm"] = preg_replace('/[^0-9]/','', $rowi["insert_date"]).preg_replace('/[^0-9]/','', $rowi["insert_time"]);
	$data["investment_register_info"]["status"] = "T100"; // T100 투자신청중
	$data["investment_register_info"]["investments_document_info"]["document_confirm_date"] = preg_replace('/[^0-9]/','', $rowi["insert_date"]);
	$data["investment_register_info"]["investments_document_info"]["document_type"] = "DP99"; // DP01  (전자문서 형식의 파일)  DP99  (전자문서 이외의 파일)

	$inv_info = get_inv_info($rowi["member_idx"]);
	$data["investor_info"]["identity_no"] = $inv_info["inv_idno"];
	$data["investor_info"]["name"] = $inv_info["inv_name"];
	$data["investor_info"]["type"] = $inv_info["inv_type"];
	if (substr($data["investor_info"]["type"] , 0 , 2) == "I3") $data["investor_info"]["business_register_no"] = $inv_info["business_register_no"];
	$data["goods_info"]["goods_id"] = $row["goods_id"];
	$data["goods_info"]["goods_type"] = $goods_type;

	$typeText=get_inv_type_txt($data["investor_info"]["type"]);

	//echo "<pre>"; print_r($data); echo "</pre>";

	unset($resj);

	if ($mode=="send" AND !$rowi["investment_register_id"]) {
		//echo $rowi["member_idx"]." ".$rowi["investment_register_id"];

		$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, $rowi["member_idx"]);
		$resj = json_decode($curl_res["body"] , true);

		if ($resj["rsp_code"] == "A0000") {
			//$up_sql = "UPDATE cf_product_invest_detail SET investment_register_id = '$investment_register_id' WHERE idx = '$rowi[idx]' AND investment_register_id='' ";
			$up_sql = "UPDATE cf_product_invest SET investment_register_id = '$investment_register_id' WHERE idx = '$rowi[idx]' AND investment_register_id='' ";
			sql_query($up_sql);
		} else {
			echo "기록 실패<br/><br/>";
			echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";
		}
	}


	$tot = $tot + $data["investment_register_info"]["investment_amount"];
	?>
	<tr>
		<td style="text-align:center;"><?=$no--?></td>
		<td style="text-align:center;"><?=$rowi["member_idx"]?></td>
		<td style="text-align:center;"><?=$data["investor_info"]["name"]?></td>
		<td style="text-align:center;"><?=$typeText?></td>
		<td style="text-align:center;"><?=substr($data["investor_info"]["identity_no"],0,6)?>-<?=str_repeat(" *",strlen(substr($data["investor_info"]["identity_no"],6)))?></td>
		<td style="text-align:right;"><?=number_format($data["investment_register_info"]["investment_amount"])?></td>
		<td style="text-align:center;"><?=$data["investment_register_info"]["investment_register_dtm"]?></td>
		<td style="text-align:center;">
			<a onclick="go_srch_invest('<?=$rowi['contract_id']?>');" style="cursor:pointer;">
			<?=$data["investment_register_info"]["investment_register_id"]?></a>
		</td>
		<td style="text-align:center;">
			<? if ($rowi['contract_id']) { ?>
			<input type="button" class="btn btn-sm btn-warning" onclick="go_srch_invest('<?=$rowi['contract_id']?>');" value="투자계약 조회"/>
			<? } ?>
		</td>
		<td style="text-align:center;"><?=$resj["rsp_code"]?><?=$resj["rsp_code"]<>"A0000"?"<br/>$resj[rsp_message]":""?></td>
	</tr>
	<?
}
?>
	<tr>
		<th colspan=5>합 계</th>
		<td style="text-align:right; background:#e5ecef;"><?=number_format($tot)?></td>
		<th colspan=4></th>
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

<?
$sqls = "SELECT A.*, B.mb_name , B.mb_co_name
		   FROM cf_product_invest A
		   LEFT JOIN g5_member B ON(B.mb_no=A.member_idx)
		  WHERE A.product_idx='$product_idx'
		  order by A.idx desc";
$ress = sql_query($sqls);
$cnts = sql_num_rows($ress);
$nos = $cnts;
?>
<br/><br/>
<h3>투자이력</h3>
<table class="table table-bordered table-condensed">
	<tr>
		<th>No</th>
		<th>회원번호</th>
		<th>이름</th>
		<th>투자상태</th>
		<th>금액</th>
		<th>투자신청(취소)일시</th>
		<th>투자신청ID</th>
		<th>취소</th>
	</tr>
<?
$tots = 0;
for ($p=0 ; $p<$cnts ; $p++) {
	$rows = sql_fetch_array($ress);

	if ($rows["invest_state"]=="Y") $tots = $tots + $rows["amount"];
	?>
	<tr>
		<td style="text-align:center;"><?=$nos--?></td>
		<td style="text-align:center;"><?=$rows["member_idx"]?></td>
		<td style="text-align:center;"><?=$rows["mb_co_name"]?$rows["mb_co_name"]:$rows["mb_name"]?></td>
		<td style="text-align:center;"><?=$rows["invest_state"]=="N"?"취소":$rows["invest_state"]?></td>
		<td style="text-align:right;">
			<? if ($rows["invest_state"]=="N") { ?>
			<font style="text-decoration: red  line-through 2px;">
			<? } ?>
			<?=number_format($rows["amount"]);?></font>
		</td>
		<td style="text-align:center;"><?=$rows["invest_state"]=="N"?$rows["cancel_date"]:$rows["insert_datetime"]?></td>
		<td style="text-align:center;"><?=$rows["investment_register_id"]?></td>
		<td style="text-align:center;">
		<? if ($rows["investment_register_id"]) { ?>
		<input type="button" class="btn btn-sm btn-warning" onclick="go_canc('<?=$product_idx?>','<?=$rows['member_idx']?>','<?=$rows["investment_register_id"]?>');" value="취소"/>
		<? } ?>
		</td>
	</tr>
	<?
}
?>
	<tr>
		<th colspan=4>합 계</th>
		<td style="text-align:right; background:#e5ecef;"><?=number_format($tots)?></td>
		<th colspan=3></th>
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

function go_srch_invest(contract_id) {
	if (!contract_id) {
		alert("투자계약 ID 가 없습니다.");
		return;
	}
	window.open("/adm/p2pctr/invest_register_srch_svc.php?contract_id="+contract_id,"","width=800, height=800");
}

function go_canc(product_idx, member_idx, investment_register_id) {
	if (!product_idx || !member_idx) {
		alert("상품번호나 회원번호가 없습니다.");
		return;
	}
	var yn = confirm("회원번호 "+member_idx+" 의 투자신청을 취소하시겠습니까?");
	if (!yn) return;

	window.open("/adm/p2pctr/invest_register_canc_svc.php?product_idx="+product_idx+"&member_idx="+member_idx+"&canc_inv_id="+investment_register_id ,"","width=800, height=800");
}
</script>
<?
//echo "<pre>"; print_r($data); echo "</pre>";
?>