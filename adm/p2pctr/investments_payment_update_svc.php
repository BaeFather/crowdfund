<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
//include_once($_SERVER["DOCUMENT_ROOT"].'/lib/repay_calculation_new.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

if (!$product_idx or !$turn) die("상품번호 또는 회차 오류");
?>
<?
$apiNo = "4.4.8";
$apiTitle = "원리금지급 갱신";
?>
<?
$sql = "SELECT goods_id  FROM cf_product where idx='$product_idx'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

$url  = $p2p_host . "investments/payment/".$row["goods_id"];
$method = "PUT";

echo "품번 : ".$product_idx." 회차 ".$turn." <br/><br/>";

?>
<?
$give_sql = "SELECT DATE, turn, turn_sno, SUM(interest) sum_int , SUM(principal) sum_prin, SUM(interest_tax) int_tax, SUM(local_tax) loc_tax, SUM(fee) sum_fee, banking_date 
			   FROM cf_product_give WHERE product_idx='$product_idx' and turn='$turn' and turn_sno='$turn_sno' GROUP BY turn, turn_sno ORDER BY turn, turn_sno desc";
$give_res = sql_query($give_sql);
$give_cnt = sql_num_rows($give_res);

if ($give_cnt) $grow = sql_fetch_array($give_res);

$data = array();
$data1 = array();
$data["pni_payment_common_info"] = array();
$data["pni_payment_list"] = array();

$data["pni_payment_common_info"]["goods_id"] = $row["goods_id"];
$data["pni_payment_common_info"]["securities_n_count"] = (int)$grow["turn"];
$data["pni_payment_common_info"]["pay_date"] = check_int(substr($grow["banking_date"],0,10));

$wh1 = " AND A.is_overdue='N' ";
$wh1 = "";

if ($mode=="send") {


	$sqld = "SELECT A.* , B.contract_id 
			   FROM cf_product_give A
		  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
			  WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' AND A.turn_sno='$grow[turn_sno]'
				AND A.p2pCtr_date='' AND A.idx<>'933095'
		   ORDER BY A.idx LIMIT 100";


	$sqld = "SELECT A.member_idx,
					sum(A.principal) sum_principal , 
					SUM(if(A.is_overdue='Y', A.interest, 0)) overdue_sum_interest,
					SUM(if(A.is_overdue='N', A.interest, 0)) sum_interest,
					SUM(A.interest+A.principal) give_sum,
					B.contract_id 
			   FROM cf_product_give A
		  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
			  WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' AND A.turn_sno='$grow[turn_sno]'				
				$wh1
		   GROUP BY A.member_idx
		   ORDER BY A.idx LIMIT 100";

} else {

	$sqld = "SELECT A.member_idx,
					sum(A.principal) sum_principal , 
					SUM(if(A.is_overdue='Y', A.interest, 0)) overdue_sum_interest,
					SUM(if(A.is_overdue='N', A.interest + A.interest_tax + A.local_tax + A.fee , 0)) sum_interest,
					SUM(A.interest+A.principal) give_sum,
					B.contract_id 
			   FROM cf_product_give A
		  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
			  WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' AND A.turn_sno='$grow[turn_sno]'
				AND A.idx<>'933095'
				$wh1
		   GROUP BY A.member_idx
		   ORDER BY A.idx ";		   
}

$sqld = "SELECT A.member_idx,
				sum(A.principal) sum_principal , 
				SUM(if(A.is_overdue='Y', A.interest + A.interest_tax + A.local_tax + A.fee , 0)) overdue_sum_interest,
				SUM(if(A.is_overdue='N', A.interest + A.interest_tax + A.local_tax + A.fee , 0)) sum_interest,
				SUM(A.interest+A.principal) give_sum,
				B.contract_id 
		   FROM cf_product_give A
	  LEFT JOIN cf_product_invest B ON(B.product_idx=A.product_idx AND B.member_idx=A.member_idx AND B.invest_state='Y')
		  WHERE A.product_idx='$product_idx' AND A.turn='$grow[turn]' AND A.turn_sno='$grow[turn_sno]'
	   GROUP BY A.member_idx
	   ORDER BY A.idx";
	   //limit 201,100";		 

$resd = sql_query($sqld);
$cntd = sql_num_rows($resd);
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
<table class="table table-bordered table-condensed">
	<tr>
		<th>No</th>
		<th>회원번호</th>
		<th>이자</th>
		<th>연체이자</th>
		<th>지급총액</th>
	</tr>
<?
$nno = $cntd;
for ($j=0 ; $j<$cntd ; $j++) {

	$rowd = sql_fetch_array($resd);

	$data1[$j]["member_idx"] = $rowd["member_idx"];
	$data1[$j]["contract_id"] = $rowd["contract_id"];

	$data["pni_payment_list"][$j]["investment_contract_id"] = $rowd["contract_id"];
	$data["pni_payment_list"][$j]["pay_interest"] = (int)$rowd["sum_interest"];
	$data["pni_payment_list"][$j]["pay_penalty"] = (int)$rowd["overdue_sum_interest"];
	$data["pni_payment_list"][$j]["actual_pay_amount"] = (int)$rowd["give_sum"];

	$tot_pay_interest += $data["pni_payment_list"][$j]["pay_interest"];
	$tot_pay_penalty += $data["pni_payment_list"][$j]["pay_penalty"];
	$tot_actual_pay_amount += $data["pni_payment_list"][$j]["actual_pay_amount"];

	$rdate = $data["pni_payment_common_info"]["pay_date"];

	//$up_sql = "UPDATE cf_product_give SET p2pCtr_contract_id='".$rowd["contract_id"]."', p2pCtr_date='".$rdate."' WHERE product_idx='$product_idx' AND turn='$turn' AND member_idx='".$rowd["member_idx"]."'";

	?>
	<tr>
		<td style="text-align:center;vertical-align:middle;">
			<?=$nno--?>
		</td>
		<td style="text-align:center;vertical-align:middle;">
			<?=$rowd["member_idx"]?>
		</td>
		<td style="text-align:right;vertical-align:middle;">
			<?=number_format($data["pni_payment_list"][$j]["pay_interest"])?>
		</td>
		<td style="text-align:right;vertical-align:middle;">
			<?=number_format($data["pni_payment_list"][$j]["pay_penalty"])?>
		</td>
		<td style="text-align:right;vertical-align:middle;">
			<?=number_format($data["pni_payment_list"][$j]["actual_pay_amount"])?>
		</td>
		<td><?=$up_sql?></td>
	</tr>
	<?
}
?>
	<tr>
		<td style="text-align:center;vertical-align:middle;">
		</td>
		<td style="text-align:center;vertical-align:middle;">
		</td>
		<td style="text-align:right;vertical-align:middle;">
			<?=number_format($tot_pay_interest)?>
		</td>
		<td style="text-align:right;vertical-align:middle;">
			<?=number_format($tot_pay_penalty)?>
		</td>
		<td style="text-align:right;vertical-align:middle;">
			<?=number_format($tot_actual_pay_amount)?>
		</td>
	</tr>
</table>
<?


	if ($mode=="send" and count($data["pni_payment_list"])) {

		echo "<pre>"; print_r($data); echo "</pre>";
		//die();

		//$res = curl_p2pctr2($apiNo, $apiTitle , $url , $method , $data,  $product_idx);
		
		$resj = json_decode($res["body"] , true);
		//$resj["rsp_code"] = "A0000";

		if ($resj["rsp_code"] == "A0000") {
			
			for ($k=0 ; $k<count($data1) ; $k++) {
				$up_sql = "UPDATE cf_product_give SET p2pCtr_contract_id='".$data1[$k]["contract_id"]."', p2pCtr_date='".$rdate."' 
							WHERE product_idx='$product_idx' AND turn='$turn' AND member_idx='".$data1[$k]["member_idx"]."'";
				echo $up_sql."<br/>";
				//sql_query($up_sql);
			}
			
			echo "기록 성공<br/><br/>";
			echo "<pre>"; print_r($res); echo "</pre><br/><br/>";		
		} else {
			echo "기록 실패<br/><br/>";
			echo "<pre>"; print_r($res); echo "</pre><br/><br/>";
		}

	}



?>