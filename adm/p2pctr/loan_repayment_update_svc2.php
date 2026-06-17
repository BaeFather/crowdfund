<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

if (!$product_idx) die("상품번호 또는 회차 오류");
?>
<?
$apiNo = "4.2.8";
$apiTitle = "대출상환 예정정보 갱신";
?>
<?
$prd_sql = "SELECT loan_contract_id, title
			  FROM cf_product 
			 WHERE idx='$product_idx'";
$prd_res = sql_query($prd_sql);
$prd_row = sql_fetch_array($prd_res);

$loan_contract_id = $prd_row["loan_contract_id"];

if ($mturn_s and $mturn_e) {

	$url  = $p2p_host . "loans/repayment/".$loan_contract_id;
	$method = "PUT";

	$data = array(); 
	$data["drop_loan_schd_repayment_info"] = array();
	$data["replace_loan_schd_repayment_list"] = array();

	$data["drop_loan_schd_repayment_info"]["drop_start_repayment_n_count"] = (int)$mturn_s;
	$data["drop_loan_schd_repayment_info"]["drop_end_repayment_n_count"]   = (int)$mturn_e;

	$REPAY = get_repay_cal($product_idx, $mturn_s, $mturn_e);

	for ($i=0 ; $i<count($REPAY) ; $i++) {		
	
		$data["replace_loan_schd_repayment_list"][$i]["repayment_n_count"] = (int)$REPAY[$i]["repayment_n_count"];
		$data["replace_loan_schd_repayment_list"][$i]["schd_date"] = $REPAY[$i]["schd_date"];
		$data["replace_loan_schd_repayment_list"][$i]["schd_p_amount"] = (int)$REPAY[$i]["p_amount"];
		$data["replace_loan_schd_repayment_list"][$i]["schd_interest"] = (int)$REPAY[$i]["interest"];
	
	}

	//echo "<pre>"; print_r($REPAY) ; echo "</pre>";
	//echo "<pre>"; print_r($data) ; echo "</pre>";


	if ($mode=="send" and $mturn_s and $mturn_e) {

		$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx);

	}
}
?>

<?
function get_repay_cal($product_idx, $sturn, $eturn) {
	//echo "<br/><br/>function get_repay_cal() START -------------------- <br/><br/>";

	$bill_table = getBillTable($product_idx);

	$turn_sql = "SELECT turn, turn_sno FROM $bill_table 
				  WHERE product_idx='$product_idx'
				    AND turn>=$sturn AND turn<=$eturn
			   GROUP BY turn, turn_sno
			   ORDER BY IF(turn_sno=0,repay_date,bill_date) ASC";

	$turn_res = sql_query($turn_sql);
	$turn_cnt = $turn_res->num_rows;

	$REPAY = array();
	$r = array();
	
	$start_turn = $sturn-1;

	for  ($i=0 ; $i<$turn_cnt; $i++) {
		$turn_row = sql_fetch_array($turn_res);
		$start_turn = $start_turn+1;
		$REPAY[$i]["repayment_n_count"] = $start_turn; 
		$r[$i]["turn"] = $turn_row["turn"];
		$r[$i]["turn_sno"] = $turn_row["turn_sno"];

	}

	//echo "<pre>"; print_r($r) ; echo "</pre>";

/*
$sql="SELECT sum(day_interest) invest_interest FROM $bill_table WHERE product_idx='$product_idx' AND turn='10' and member_idx='39426'";
$res = sql_query($sql);
$row = sql_fetch_array($res);
$intr = floor(customRoundOff($row['invest_interest']));
echo "39426 10회차 이자 ".number_format($intr)." 원<br/>";
*/

	$repayment_n_count=1;

	for ($i=0 ; $i<count($r) ; $i++) {
	//for ($i=0 ; $i<2 ; $i++) {
		//echo $r[$i]["turn"]." ".$r[$i]["turn_sno"]."<br/>";

		if ($r[$i]["turn_sno"]>0) $sturn = $r[$i]["turn"]+1;
		else $sturn = $r[$i]["turn"];


		$sqlm="SELECT member_idx,invest_idx FROM $bill_table WHERE product_idx='$product_idx' AND turn='".$r[$i]["turn"]."' 
			 GROUP BY invest_idx";
		$resm = sql_query($sqlm);
		$cntm = $resm->num_rows;

		$member_inter = 0;
		$member_pamt  = 0;

		for ($j=0 ; $j<$cntm ; $j++) {
		//for ($j=0 ; $j<1 ; $j++) {

			$rowm = sql_fetch_array($resm);

			$sql = "
				SELECT
					repay_date, turn, turn_sno, invest_idx, member_idx, invest_amount, partial_principal, remain_principal,
					IFNULL(SUM(day_interest),0) AS invest_interest,
					IFNULL(SUM(fee),0) AS fee,
					( SELECT IFNULL(MIN(remain_principal), 0) FROM $bill_table WHERE invest_idx='".$rowm['invest_idx']."' ) AS min_remain_amount
				FROM
					$bill_table
				WHERE 1
					AND product_idx='$product_idx'
					AND invest_idx = '".$rowm['invest_idx']."'
					AND turn = '". $r[$i]["turn"]."'
					AND is_overdue = 'N'
				ORDER BY
					idx DESC
				LIMIT 1";

			$row = sql_fetch($sql);

			$sql2 = "
				SELECT
					repay_date, turn, turn_sno, invest_idx, member_idx, invest_amount, partial_principal, remain_principal,
					IFNULL(SUM(day_interest),0) AS invest_interest,
					IFNULL(SUM(fee),0) AS fee,
					( SELECT IFNULL(MIN(remain_principal), 0) FROM $bill_table WHERE invest_idx='".$rowm['invest_idx']."' ) AS min_remain_amount
				FROM
					$bill_table
				WHERE 1
					AND product_idx='$product_idx'
					AND invest_idx = '".$rowm['invest_idx']."'
					AND turn = '". $r[$i]["turn"]."' and turn_sno='1'
					AND is_overdue = 'N'
				ORDER BY
					idx DESC
				LIMIT 1";			
			$row2 = sql_fetch($sql2);


			
			$member_inter = floor(customRoundOff($row['invest_interest']));

			if ($r[$i]["turn"]<$r[count($r)-1]["turn"]) {					
				if ($r[$i]["turn_sno"]>0) {
					$member_inter = 0;
					$member_pamt  = $row2["partial_principal"] - $row["partial_principal"];
					//$member_pamt  = $row["invest_amount"]-$row["remain_principal"];
				} else {
					$member_pamt=0;
				}

			} else {
				$member_pamt = $row["min_remain_amount"];
			}

//if ($rowm["invest_idx"]=="351849") echo "<br/><br/>----------------------<br/>$sql<br/><br/>$sql2<br/>$member_pamt))------------------------<br/><br/>";			

			$REPAY[$i]["schd_date"] = preg_replace('/[^0-9]/','', $row["repay_date"]);
			$REPAY[$i]["interest"] += $member_inter;
			$REPAY[$i]["p_amount"] += $member_pamt;

		}

		if ($r[$i]["turn_sno"]>0) {
			$sqld = "SELECT bill_date FROM $bill_table WHERE product_idx='$product_idx' AND turn = '".$r[$i]["turn"]."' AND turn_sno = '".$r[$i]["turn_sno"]."' AND is_overdue = 'N' ORDER BY bill_date LIMIT 1";
			$rowd = sql_fetch($sqld);
			$REPAY[$i]["schd_date"] = preg_replace('/[^0-9]/','', $rowd["bill_date"]);
		}

	}

	// 2022-01-12 연체중 원금 일부상환 임의처리
	/*
	if ($product_idx=="3107") {
		unset($REPAY);
		$REPAY = array();

		$REPAY[0]["repayment_n_count"] = 14;
		$REPAY[0]["schd_date"] = "20210508";
		$REPAY[0]["interest"] = 0;
		$REPAY[0]["p_amount"] = 189541980;

		$REPAY[1]["repayment_n_count"] = 15;
		$REPAY[1]["schd_date"] = "20210508";
		$REPAY[1]["interest"] = 4564383;
		$REPAY[1]["p_amount"] = 1210458020;

	}
	*/
	if ($product_idx=="8068") {
		unset($REPAY);
		$REPAY = array();

		$REPAY[0]["repayment_n_count"] = 1;
		$REPAY[0]["schd_date"] = "20220207";
		$REPAY[0]["interest"] = 0;
		$REPAY[0]["p_amount"] = 11317336;

		$REPAY[1]["repayment_n_count"] = 2;
		$REPAY[1]["schd_date"] = "20220207";
		$REPAY[1]["interest"] = 70284;
		$REPAY[1]["p_amount"] = 10062664;

	}

	if ($product_idx=="8081") {
		unset($REPAY);
		$REPAY = array();

		$REPAY[0]["repayment_n_count"] = 1;
		$REPAY[0]["schd_date"] = "20220208";
		$REPAY[0]["interest"] = 0;
		$REPAY[0]["p_amount"] = 19700593;

		$REPAY[1]["repayment_n_count"] = 2;
		$REPAY[1]["schd_date"] = "20220208";
		$REPAY[1]["interest"] = 33853;
		$REPAY[1]["p_amount"] = 7819407;

	}

	//echo "<pre>"; print_r($REPAY) ; echo "</pre>";

	//echo "<br/><br/> function get_repay_cal() END -------------------- <br/><br/>";
	//echo $sqld."<br/>";
	return $REPAY;
}
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$product_idx?> <?=$prd_row["title"]?></h3>
	<h3><?=$apiNo?> <?=$apiTitle?></h3>


<table class="table table-bordered table-condensed">
	<tr>
		<td style="text-align:center; border:0;">
			<form method="post" name="ff">
			<input type=hidden name="mode" value=""/>
			<input type=hidden name="product_idx" value="<?=$product_idx?>"/>
			수정회차 
			<input type=text name="mturn_s" value="<?=$mturn_s?>" style="width:30px; text-align:center; font-size:12px;"/> 
			~
			<input type=text name="mturn_e" value="<?=$mturn_e?>" style="width:30px; text-align:center; font-size:12px;"/>&nbsp;&nbsp;
			<input type="button" class="btn btn-sm btn-warning" onclick="go_srch();" value="검색"/>
			<br/><br/>
<? if (is_array($data)) { ?>
			<table class="table table-bordered table-condensed">
				<tr>
					<th>회차</th>
					<th>상환일</th>
					<th>원금</th>
					<th>이자</th>
				</tr>
	<? for ($m=0 ; $m<count($data["replace_loan_schd_repayment_list"]) ; $m++) { ?>
				<tr>			
					<td style="text-align:center;"><?=$data["replace_loan_schd_repayment_list"][$m]["repayment_n_count"]?></td>
					<td style="text-align:center;"><?=$data["replace_loan_schd_repayment_list"][$m]["schd_date"]?></td>
					<td style="text-align:right; padding-right:10px;"><?=number_format($data["replace_loan_schd_repayment_list"][$m]["schd_p_amount"])?></td>
					<td style="text-align:right; padding-right:10px;"><?=number_format($data["replace_loan_schd_repayment_list"][$m]["schd_interest"])?></td>
				</tr>
	<? } ?>
			</table>
<? } ?>
			
			<input type="button" class="btn btn-sm btn-warning" onclick="go_send();" value="전송"/>
			</form>
		</td>
	</tr>
</table>

<script>
function go_srch() {
	var f = document.ff;
	f.submit();
}
function go_send() {

	var f = document.ff;
	if (!<?=count($data)?>) {
		alert("회차 검색을 먼저 해주세요.");
		return;
	}

	var yn = confirm("이대로 전송하시겠습니까?");
	if (!yn) return;

	
	f.mode.value="send";
	f.submit();
}
</script>

<?
if (is_array($curl_res)) {echo "<pre>"; print_r($curl_res); echo "</pre>";}
?>