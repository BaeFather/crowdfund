<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
//include_once($_SERVER["DOCUMENT_ROOT"].'/lib/repay_calculation_new.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

if (!$product_idx or !$turn) die("상품번호 또는 회차 오류");

//if ($_SESSION["ss_mb_id"]<>"admin_romrom" and $_SESSION["ss_mb_id"]<>"admin_hellosiesta") die("전승찬 작업중");
?>
<?
$apiNo = "4.4.7";
$apiTitle = "원리금지급 기록";
?>
<?
$url  = $p2p_host . "investments/payment";
//if ($product_idx=="3107") $url  = $p2p_host . "data/investments/payment";  // 이관용
$method = "POST";

$sql = "SELECT goods_id  FROM cf_product where idx='$product_idx'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

// 2022-01-12 연체중 원금 일부상환 임의처리
if ($product_idx=="8068" or $product_idx=="8081") { $turn=1 ; $turn_sno=1; }

$inv_chk_sql = "SELECT COUNT(*) inv_cnt FROM cf_product_invest WHERE product_idx='$product_idx' AND invest_state='Y'";
$inv_chk_row = sql_fetch($inv_chk_sql);
$inv_cnt = $inv_chk_row["inv_cnt"];


$give_sql = "SELECT DATE, turn, turn_sno, SUM(interest) sum_int , SUM(principal) sum_prin, SUM(interest_tax) int_tax, SUM(local_tax) loc_tax, SUM(fee) sum_fee 
			   FROM cf_product_give WHERE product_idx='$product_idx' and turn='$turn' and turn_sno='$turn_sno' GROUP BY turn, turn_sno ORDER BY turn, turn_sno desc";
if ($product_idx=="59331111111111") {
$give_sql = "SELECT DATE, turn, turn_sno, SUM(interest) sum_int , SUM(principal) sum_prin, SUM(interest_tax) int_tax, SUM(local_tax) loc_tax, SUM(fee) sum_fee 
			   FROM cf_product_give WHERE product_idx='$product_idx' 
			   and ((turn='$turn' and turn_sno='$turn_sno') or (turn='8' and turn_sno='1' ))
			   GROUP BY turn, turn_sno ORDER BY turn, turn_sno desc";
}
$give_res = sql_query($give_sql);
$give_cnt = sql_num_rows($give_res);
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">

	<h3><?=$apiNo?> <?=$apiTitle?><?=strpos($url,'data')!==false?" (이관용)":""?></h3>

<table class="table table-bordered table-condensed">

<?
for ($i=0 ; $i<$give_cnt ; $i++) {

	$grow = sql_fetch_array($give_res);

	$strApiTrxNo  = get_p2pord_no(); // API거래고유번호
	$strApiTrxDtm = get_dtm_no();    // 거래일시 (밀리세컨드)

	unset($headers);
	$headers[] = "Content-Type: application/json; charset=UTF-8";
	ARRAY_PUSH($headers,"Authorization: Bearer ".$access_token);
	ARRAY_PUSH($headers,"api_trx_no: ".$strApiTrxNo);
	ARRAY_PUSH($headers,"api_trx_dtm: ".$strApiTrxDtm);

	$data = array(); 
	$data1 = array();
	$data["pni_payment_common_info"] = array();
	$data["pni_payment_list"] = array();

	$data["pni_payment_common_info"]["goods_id"] = $row["goods_id"];
	$data["pni_payment_common_info"]["securities_n_count"] = (int)$grow["turn"];
	if ($product_idx=="5933") $data["pni_payment_common_info"]["securities_n_count"] = (int)$grow["turn"]+1;
	if ($product_idx=="4945") $data["pni_payment_common_info"]["securities_n_count"] = (int)$grow["turn"]+1;
	//if ($product_idx=="5863") $data["pni_payment_common_info"]["securities_n_count"] = 8;
	if ($product_idx=="6150") $data["pni_payment_common_info"]["securities_n_count"] = (int)$grow["turn"]+1;
	$data["pni_payment_common_info"]["pay_date"] = check_int($grow["DATE"]);


	if ($product_idx=="8068" or $product_idx=="8081") {
		$data["pni_payment_common_info"]["securities_n_count"] = 2;
		$data["pni_payment_common_info"]["pay_date"] = "20220311";
	}
	if ($product_idx=="8109") $data["pni_payment_common_info"]["pay_date"] = "20220311";
	/*
	$sqld = "SELECT A.* , B.contract_id 
			   FROM cf_product_give A
		  LEFT JOIN cf_product_invest_detail B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state<>'N' AND)
		      WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' 
		   ORDER BY A.idx";
	$sqld = "SELECT A.* 
			   FROM cf_product_give A
		      WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' 
		   ORDER BY A.idx";

	$sqld = "SELECT A.* , B.contract_id 
			   FROM cf_product_give A
		  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
		      WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' 
		   ORDER BY A.idx";
	*/

	if ($mode=="send") {
		/*
		$sqld = "SELECT A.* , B.contract_id 
				   FROM cf_product_give A
			  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
				  WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' AND A.turn_sno='$grow[turn_sno]'
				    AND A.p2pCtr_date='' AND A.idx<>'933095'
			   ORDER BY A.idx LIMIT 100";
		*/
		$sqld = "SELECT A.idx, A.date, A.invest_idx, A.product_idx, A.member_idx, A.turn, A.turn_sno, A.invest_amount,
						SUM(A.interest) sum_interest,
						SUM(A.principal) sum_principal,
						SUM(A.interest_tax) sum_interest_tax,
						SUM(A.local_tax) sum_local_tax,
						SUM(A.fee) sum_fee,
						B.contract_id
				   FROM cf_product_give A
			  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
				  WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' AND A.turn_sno='$grow[turn_sno]'
				    AND A.p2pCtr_date=''
			   GROUP BY A.member_idx
			   ORDER BY A.idx LIMIT 100";
		if ($product_idx=="8068" or $product_idx=="8081") {
		$sqld = "SELECT A.idx, A.date, A.invest_idx, A.product_idx, A.member_idx, A.turn, A.turn_sno, A.invest_amount,
						SUM(A.interest) sum_interest,
						SUM(A.principal) sum_principal,
						SUM(A.interest_tax) sum_interest_tax,
						SUM(A.local_tax) sum_local_tax,
						SUM(A.fee) sum_fee,
						B.contract_id
				   FROM cf_product_give A
			  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
				  WHERE A.product_idx='$product_idx' AND A.turn_sno='0'
				    AND A.p2pCtr_date=''
			   GROUP BY A.member_idx
			   ORDER BY A.idx LIMIT 100";
		}
		if ($product_idx=="5933" ) {
		$sqld = "SELECT A.idx, A.date, A.invest_idx, A.product_idx, A.member_idx, A.turn, A.turn_sno, A.invest_amount,
						SUM(A.interest) sum_interest,
						SUM(A.principal) sum_principal,
						SUM(A.interest_tax) sum_interest_tax,
						SUM(A.local_tax) sum_local_tax,
						SUM(A.fee) sum_fee,
						B.contract_id
				   FROM cf_product_give A
			  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
				  WHERE A.product_idx='$product_idx' AND ((A.turn='$grow[turn]' AND A.turn_sno='$grow[turn_sno]') or (A.turn='8' and A.turn_sno='1'))
				    AND A.p2pCtr_date=''
			   GROUP BY A.member_idx
			   ORDER BY A.idx ";
		}
	} else {
		/*
		$sqld = "SELECT A.* , B.contract_id 
				   FROM cf_product_give A
			  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
				  WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' 
			   ORDER BY A.idx ";
		$sqld = "SELECT A.* , B.contract_id 
				   FROM cf_product_give A
			  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
				  WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' AND A.turn_sno='$grow[turn_sno]'
				    AND A.idx<>'933095'
			   ORDER BY A.idx ";
		*/
		$sqld = "SELECT A.idx, A.date, A.invest_idx, A.product_idx, A.member_idx, A.turn, A.turn_sno, A.invest_amount,
						SUM(A.interest) sum_interest,
						SUM(A.principal) sum_principal,
						SUM(A.interest_tax) sum_interest_tax,
						SUM(A.local_tax) sum_local_tax,
						SUM(A.fee) sum_fee,
						B.contract_id
				   FROM cf_product_give A
			  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
				  WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' AND A.turn_sno='$grow[turn_sno]'
			   GROUP BY A.member_idx
			   ORDER BY A.idx ";
		if ($product_idx=="8068" or $product_idx=="8081") {
		$sqld = "SELECT A.idx, A.date, A.invest_idx, A.product_idx, A.member_idx, A.turn, A.turn_sno, A.invest_amount,
						SUM(A.interest) sum_interest,
						SUM(A.principal) sum_principal,
						SUM(A.interest_tax) sum_interest_tax,
						SUM(A.local_tax) sum_local_tax,
						SUM(A.fee) sum_fee,
						B.contract_id
				   FROM cf_product_give A
			  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
				  WHERE A.product_idx='$product_idx' AND A.turn_sno='0'
			   GROUP BY A.member_idx
			   ORDER BY A.idx ";
		}
		if ($product_idx=="5933") {
		$sqld = "SELECT A.idx, A.date, A.invest_idx, A.product_idx, A.member_idx, A.turn, A.turn_sno, A.invest_amount,
						SUM(A.interest) sum_interest,
						SUM(A.principal) sum_principal,
						SUM(A.interest_tax) sum_interest_tax,
						SUM(A.local_tax) sum_local_tax,
						SUM(A.fee) sum_fee,
						B.contract_id
				   FROM cf_product_give A
			  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
				  WHERE A.product_idx='$product_idx' AND ((A.turn_sno='0') or (A.turn='8' and A.turn_sno='1'))
			   GROUP BY A.member_idx
			   ORDER BY A.idx ";
		}

			   
	}

	$resd = sql_query($sqld);
	$cntd = sql_num_rows($resd);

	$total_pay_p_amount=0; $total_pay_interest=0 ; $total_actual_pay_amount=0;

	$rdate = "";


if (!$mode AND $inv_cnt<>$cntd) {
	?>
	<script>alert("투자자수 오류입니다. <?=$inv_cnt?> <?=$cntd?>");</script>
	<div style="text-align:center;height:50px;"><br/>투자자수 오류 !!! </div>
	<?
}


	for ($j=0 ; $j<$cntd ; $j++) {

		$rowd = sql_fetch_array($resd);

		if (!$rdate) $rdate = preg_replace('/[^0-9]/','', $rowd["date"]);
		$data1["pni_payment_list"][$j]["member_idx"] = $rowd["member_idx"];
		$data1["pni_payment_list"][$j]["turn"] = $rowd["turn"];
		$data1["pni_payment_list"][$j]["turn_sno"] = $rowd["turn_sno"];
		$data1["give_idx"][$j] = $rowd["idx"];
		$data1["contract_id"][$j] = $rowd["contract_id"];

		$data["pni_payment_list"][$j]["investment_contract_id"] = $rowd["contract_id"];
		$data["pni_payment_list"][$j]["pay_p_amount"] = (int)$rowd["sum_principal"];
		$data["pni_payment_list"][$j]["pay_interest"] = (int)$rowd["sum_interest"];
		$data["pni_payment_list"][$j]["actual_pay_amount"] = (int)($rowd["sum_principal"]+$rowd["sum_interest"]);

		$total_pay_p_amount += $data["pni_payment_list"][$j]["pay_p_amount"];
		$total_pay_interest += $data["pni_payment_list"][$j]["pay_interest"];
		$total_actual_pay_amount += $data["pni_payment_list"][$j]["actual_pay_amount"];

		if ($product_idx=="8068") {
		}

	}



	unset($resj);
	if ($mode=="send" and count($data["pni_payment_list"])) {

		//$res = curl_p2pctr($url , $method , $data , $headers);
		$res = curl_p2pctr2($apiNo, $apiTitle , $url , $method , $data,  $product_idx);
		
		$resj = json_decode($res["body"] , true);

		if ($resj["rsp_code"] == "A0000") {

			/*
			for ($k=0 ; $k<count($data1["give_idx"]) ; $k++) {
				$up_sql = "UPDATE cf_product_give SET p2pCtr_contract_id='".$data1["contract_id"][$k]."', p2pCtr_date='".$rdate."' WHERE idx='".$data1["give_idx"][$k]."'";
				sql_query($up_sql);
			}
			*/

			for ($k=0 ; $k<count($data1["pni_payment_list"]) ; $k++) {
				$up_sql = "UPDATE cf_product_give SET p2pCtr_contract_id='".$data1["contract_id"][$k]."', p2pCtr_date='".$rdate."' 
							WHERE product_idx='$product_idx'
							  AND member_idx='".$data1["pni_payment_list"][$k]["member_idx"]."' 
							  AND turn='".$data1["pni_payment_list"][$k]["turn"]."'
							  AND turn_sno='".$data1["pni_payment_list"][$k]["turn_sno"]."'";
				sql_query($up_sql);
				//echo $up_sql."<br/>";
			}

			echo "기록 성공<br/><br/>";
			echo "<pre>"; print_r($res); echo "</pre><br/><br/>";		
		} else {
			echo "기록 실패<br/><br/>";
			echo "<pre>"; print_r($res); echo "</pre><br/><br/>";
		}

	}
	$nno = count($data["pni_payment_list"]);
	?>
	<tr>
		<td style="text-align:center;vertical-align:top;">
			<?=$data["pni_payment_common_info"]["securities_n_count"]?>
		</td>
		<td style="text-align:center;vertical-align:top;">
			<?=$data["pni_payment_common_info"]["pay_date"]?>
		</td>
		<td style="text-align:center;vertical-align:middle;">
			<table>
				<tr>
					<th>no</th>
					<th>회원번호</th>
					<th>투자계약ID</th>
					<th>원금</th>
					<th>이자</th>
					<th>실지급액</th>
				</tr>
			<? for ($k=0 ; $k<count($data["pni_payment_list"]) ; $k++) { ?>
				<tr>
					<td style="text-align:center;vertical-align:middle;">
						<?=$nno--?>
					</td>
					<td style="text-align:center;vertical-align:middle;">
						<?=$data1["pni_payment_list"][$k]["member_idx"]?>
					</td>
					<td style="text-align:center;vertical-align:middle;">
						<?=$data["pni_payment_list"][$k]["investment_contract_id"]?>
					</td>
					<td style="text-align:right;vertical-align:middle;">
						<?=number_format($data["pni_payment_list"][$k]["pay_p_amount"])?>
					</td>
					<td style="text-align:right;vertical-align:middle;">
						<?=number_format($data["pni_payment_list"][$k]["pay_interest"])?>
					</td>
					<td style="text-align:right;vertical-align:middle;">
						<?=number_format($data["pni_payment_list"][$k]["actual_pay_amount"])?>
					</td>
				</tr>
			<? } ?>
				<tr>
					<td style="text-align:center;vertical-align:middle;" colspan=3>합 계</td>
					<td style="text-align:right;vertical-align:middle;"><?=number_format($total_pay_p_amount)?></td>
					<td style="text-align:right;vertical-align:middle;"><?=number_format($total_pay_interest)?></td>
					<td style="text-align:right;vertical-align:middle;"><?=number_format($total_actual_pay_amount)?></td>
				</tr>
			</table>
		</td>
	</tr>
	<?
	//echo "<pre>"; print_r($data); echo "</pre>";

}
?>

</table>

<table style="width:100%;border:0;">
	<tr>
		<td style="text-align:center; border:0;">
			<form method="post" name="ff">
			<input type=hidden name="mode" value=""/>
			<input type=hidden name="product_idx" value="<?=$product_idx?>"/>
			<input type=hidden name="turn" value="<?=$turn?>"/>
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