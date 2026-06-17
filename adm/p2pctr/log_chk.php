<?
include_once('./_common.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>

<?
//$target_apiNO = "4.2.2";  // 대출신청 기록
//$target_apiNO = "4.2.4";  // 대출계약 기록
//$target_apiNO = "4.2.5";  // 대출계약 조회
//$target_apiNO = "4.2.7";  // 대출상환 기록
//$target_apiNO = "4.2.8";  // 대출상환 예정정보 갱신
//$target_apiNO = "4.3.3";  // 상품모집 갱신
//$target_apiNO = "4.3.4";  // 상품 조회
$target_apiNO = "4.4.2";  // 투자신청 기록
//$target_apiNO = "4.4.5";  // "투자계약 갱신"
//$target_apiNO = "4.4.6";  // 투자계약 조회


// 4.2.7  대출상환 기록
$sql = "SELECT * FROM p2pctr_request_log 
		 WHERE substring(toUrl,1,31)='https://openapi.p2pcenter.or.kr' 
		   AND apiNo='4.4.7'
		   AND qna_log='N' and rcv_http_code='206'
	  ORDER BY idx limit 1";
$sql = "SELECT * FROM p2pctr_request_log 
		 WHERE substring(toUrl,1,31)='https://openapi.p2pcenter.or.kr' 
		   AND apiNo='4.4.7'
		   AND qna_log='N' and reqJson like '%K210500031_210903_GR_0000000006%' and idx='28038'
	  ORDER BY idx limit 1";

$sql = "SELECT * FROM p2pctr_request_log 
		 WHERE substring(toUrl,1,31)='https://openapi.p2pcenter.or.kr' 
		   AND apiNo='4.2.7'
		   AND qna_log='N' 
	  ORDER BY idx limit 1";

$sql = "SELECT * FROM p2pctr_request_log 
		 WHERE substring(toUrl,1,31)='https://openapi.p2pcenter.or.kr' 
		   AND apiNo='$target_apiNO' 
		   AND product_idx='6965'
	  ORDER BY idx";

$sql = "SELECT * FROM p2pctr_request_log 
		 WHERE substring(toUrl,1,31)='https://openapi.p2pcenter.or.kr' 
		   AND apiNo='$target_apiNO' 
		   AND product_idx=''
	  ORDER BY idx";

$res = sql_query($sql);
$cnt = sql_num_rows($res);

$num = $cnt;
$num2= 1;
?>
<table border=1>
<?
for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	//echo "p2pctr_request_log.idx = ".$row["idx"]." time ".$row["rdate"]."-".$row["rtime"]."<br/><br/>";

	$reqD = json_decode($row["reqJson"], true);
	$resD = json_decode($row["rcvJson"], true);


	//echo $resD["rsp_code"]."<br/>";

	$cfm = "succ";

	$rdate_rtime = preg_replace('/[^0-9]/','', $row["rdate"]).preg_replace('/[^0-9]/','', $row["rtime"]);

	$loan_contract_id = "";
	$goods_id="";
	$contract_id="";

	if ($target_apiNO == "4.2.5" or $target_apiNO == "4.2.8") {
		$tmp = explode("/" , $row["toUrl"]);
		$loan_contract_id = $tmp[count($tmp)-1];

	} else if ($target_apiNO == "4.3.3" or $target_apiNO == "4.3.4") {
		$tmp = explode("/" , $row["toUrl"]);
		$goods_id = $tmp[count($tmp)-1];

	} else if ($target_apiNO == "4.4.5") {
		$tmp = explode("/" , $row["toUrl"]);
		$contract_id = $tmp[count($tmp)-1];

	} else if ($target_apiNO == "4.4.6") {
		$tmp = explode("/" , $row["toUrl"]);
		$contract_id = $tmp[count($tmp)-1];

	}


	if ($row["apiNo"]=="4.2.2") {   // 대출신청 기록


	} else if ($row["apiNo"]=="4.2.4") {   // 대출계약 기록

		if ($resD["rsp_code"]=="A0000") {

			$prd_sql = "SELECT idx FROM cf_product WHERE loan_register_id='".$reqD["loan_register_id"]."'";
			$prd_res = sql_query($prd_sql);
			$prd_row = sql_fetch_array($prd_res);	

			$up1_sql = "UPDATE p2pctr_request_log SET product_idx='".$prd_row["idx"]."' WHERE idx='".$row["idx"]."' AND product_idx=''";
			//sql_query($up1_sql);

			echo "$i ".$reqD["loan_register_id"]." $prd_row[idx] $up1_sql<br/>";

		}

		continue;


	} else if ($row["apiNo"]=="4.2.5") {   // 대출계약 조회

		if ($loan_contract_id) {
			
			$prd_sql = "SELECT idx FROM cf_product WHERE loan_contract_id='".$loan_contract_id."'";
			$prd_res = sql_query($prd_sql);
			$prd_row = sql_fetch_array($prd_res);	

			$up1_sql = "UPDATE p2pctr_request_log SET product_idx='".$prd_row["idx"]."' WHERE idx='".$row["idx"]."' AND product_idx=''";
			//sql_query($up1_sql);

			echo "$i $loan_contract_id $prd_row[idx] $up1_sql<br/>";
			continue;

		}

	} else if ($row["apiNo"]=="4.2.7") {   // 대출상환 기록


		if ($resD["rsp_code"]=="A0000" ) {
			$prd_sql = "SELECT idx FROM cf_product WHERE loan_contract_id='".$reqD["loan_contract_id"]."'";
			$prd_res = sql_query($prd_sql);
			$prd_row = sql_fetch_array($prd_res);
			$product_idx = $prd_row["idx"];	

			$up1_sql = "UPDATE p2pctr_request_log SET product_idx='".$prd_row["idx"]."' WHERE idx='".$row["idx"]."' AND product_idx=''";
			//sql_query($up1_sql);

			echo "$i ".$reqD["loan_contract_id"]." $product_idx $up1_sql<br/>";
			continue;

		}
		/*
		if ($resD["rsp_code"]=="A0000") {
			$prd_sql = "SELECT idx FROM cf_product WHERE loan_contract_id='".$reqD["loan_contract_id"]."'";
			$prd_res = sql_query($prd_sql);
			$prd_row = sql_fetch_array($prd_res);
			$product_idx = $prd_row["idx"];

			echo "상품번호 $product_idx<br/>";
			
			for ($j=0 ; $j<count($reqD["loan_repayment_list"]) ; $j++) {
				$tn =  $reqD["loan_repayment_list"][$j]["repayment_n_count"];
				$suc_sql = "SELECT * FROM cf_product_success WHERE product_idx='$product_idx' AND turn='$tn'";
				$suc_res = sql_query($suc_sql);
				$suc_cnt = sql_num_rows($suc_res);
				unset($suc_row);
				if ($suc_cnt) {
					$suc_row = sql_fetch_array($suc_res);
					
					if (!$suc_row["loan_contract_id"]) {
						$ups_sql1 = "UPDATE cf_product_success SET loan_contract_id='".$reqD["loan_contract_id"]."' WHERE idx = '$suc_row[idx]'";
						echo "ups_sql1 $ups_sql1 <br/>";
						//sql_query($ups_sql1);
					}
					if (!$suc_row["p2pCtr_date"]) {

						$ups_sql2 = "UPDATE cf_product_success SET p2pCtr_date='".$rdate_rtime."' WHERE idx = '$suc_row[idx]'";
						echo "ups_sql2 $ups_sql2 <br/>";
						//sql_query($ups_sql2);
					}

				}

			}

			$uplog_sql = "UPDATE p2pctr_request_log SET qna_log='Y' WHERE idx='$row[idx]'";
			echo "uplog_sql $uplog_sql<br/>";
			//sql_query($uplog_sql);

		}
		*/

	} else if ($row["apiNo"]=="4.2.8") {   // 대출상환 예정정보 갱신


		if ($resD["rsp_code"]=="A0000") {

			if ($loan_contract_id) {
				
				$prd_sql = "SELECT idx FROM cf_product WHERE loan_contract_id='".$loan_contract_id."'";
				$prd_res = sql_query($prd_sql);
				$prd_row = sql_fetch_array($prd_res);	

				$up1_sql = "UPDATE p2pctr_request_log SET product_idx='".$prd_row["idx"]."' WHERE idx='".$row["idx"]."' AND product_idx=''";
				//sql_query($up1_sql);

				echo "$i $loan_contract_id $prd_row[idx] $up1_sql<br/>";

			}


		}
		continue;

	} else if ($row["apiNo"]=="4.3.3") {   // 투자계약 갱시
		
		if ($goods_id) {
			
			$prd_sql = "SELECT idx FROM cf_product WHERE goods_id='".$goods_id."'";
			$prd_res = sql_query($prd_sql);
			$prd_row = sql_fetch_array($prd_res);		

			$up1_sql = "UPDATE p2pctr_request_log SET product_idx='".$prd_row["idx"]."' WHERE idx='".$row["idx"]."' AND product_idx=''";

			$up2_sql = "UPDATE p2pctr_product SET goods_id='$goods_id' WHERE product_idx='".$prd_row["idx"]."' AND goods_id=''";

			$up3_sql = "UPDATE p2pctr_product SET goods_status='".$reqD["status"]."' WHERE product_idx='".$prd_row["idx"]."' AND goods_status=''";

			//sql_query($up1_sql);
			//sql_query($up2_sql);
			//sql_query($up3_sql);

			echo $i." - ".$goods_id." ".$prd_row["idx"]." ".$reqD["status"]."<br/>".$up1_sql."<br/>".$up2_sql."<br/>$up3_sql<br/><br/>";

		}

	} else if ($row["apiNo"]=="4.3.4") {   // 상품 조회
		
		if ($goods_id) {
			
			$prd_sql = "SELECT idx FROM cf_product WHERE goods_id='".$goods_id."'";
			$prd_res = sql_query($prd_sql);
			$prd_row = sql_fetch_array($prd_res);

			$up1_sql = "UPDATE p2pctr_request_log SET product_idx='".$prd_row["idx"]."' WHERE idx='".$row["idx"]."' AND product_idx=''";
			//sql_query($up1_sql);

			echo $i." - ".$goods_id." ".$prd_row["idx"]." <br/>".$up1_sql."<br/><br/>";
			continue;

		}


	} else if ($row["apiNo"]=="4.4.2") {   // 투자신청 기록

		if ($resD["rsp_code"]=="A0000" or 2>1) {

			/*
			$inv_sql = "SELECT idx,product_idx,member_idx FROM cf_product_invest WHERE investment_register_id='".$reqD["investment_register_info"]["investment_register_id"]."'";
			$inv_res = sql_query($inv_sql);
			$inv_row = sql_fetch_array($inv_res);	
			*/

			$prd_sql = "SELECT idx FROM cf_product WHERE goods_id='".$reqD["goods_info"]["goods_id"]."'";
			$prd_res = sql_query($prd_sql);
			$prd_row = sql_fetch_array($prd_res);


			$up1_sql = "UPDATE p2pctr_request_log SET product_idx='".$prd_row["idx"]."' WHERE idx='".$row["idx"]."' AND product_idx=''";
			//sql_query($up1_sql);

			echo "$i ".$reqD["goods_info"]["goods_id"]." $prd_row[idx] $inv_row[member_idx] $up1_sql<br/>";
			continue;
		}

	} else if ($row["apiNo"]=="4.4.6") {   // 대출계약 조회


		if ($contract_id) {
			
			$inv_sql = "SELECT idx,product_idx,member_idx FROM cf_product_invest WHERE contract_id='".$contract_id."'";
			$inv_res = sql_query($inv_sql);
			$inv_row = sql_fetch_array($inv_res);	

			$up1_sql = "UPDATE p2pctr_request_log SET product_idx='".$inv_row["product_idx"]."', member_idx='".$inv_row["member_idx"]."' WHERE idx='".$row["idx"]."' AND product_idx=''";
			//sql_query($up1_sql);

			echo "$i $contract_id $inv_row[product_idx] $inv_row[member_idx]<br/>$up1_sql<br/><br/>";
			continue;

		}


	} else if ($row["apiNo"]=="4.4.7") {   // 원리금지급 기록

		if ($resD["rsp_code"]=="A0020") {

			for ($j=0 ; $j<count($resD["result_list"]) ; $j++) {

				echo "<pre>"; print_r($resD["result_list"][$j]); echo "</pre>";

					$inv_sql = "SELECT product_idx, member_idx FROM cf_product_invest WHERE contract_id='".$resD["result_list"][$j]["investment_contract_id"]."'";
					$inv_res = sql_query($inv_sql);
					$inv_row = sql_fetch_array($inv_res);
					
					$pidx = $inv_row["product_idx"];
					$midx = $inv_row["member_idx"];
					$trn  = $reqD["pni_payment_common_info"]["securities_n_count"];

					echo $pidx." ".$midx." ".$trn."회차 --<br/>";

				if ($resD["result_list"][$j]["pni_payment_rsp_code"]=="A0000") {



					if ($pidx and $midx and $trn) {
						$giv_sql = "SELECT idx, p2pCtr_contract_id, p2pCtr_date FROM cf_product_give WHERE product_idx='$pidx' AND member_idx='$midx' AND turn='$trn'";
						$giv_res = sql_query($giv_sql);
						$giv_cnt = sql_num_rows($giv_res);
						$giv_row = sql_fetch_array($giv_res);
						echo "$giv_cnt $giv_row[idx]<br/>";

						if ($giv_row["idx"]) {
							if ($giv_row["p2pCtr_date"]=="") {
								$rdate_rtime = preg_replace('/[^0-9]/','', $row["rdate"]).preg_replace('/[^0-9]/','', $row["rtime"]);
								$upg_sql1 = "UPDATE cf_product_give SET p2pCtr_date='$rdate_rtime' WHERE idx='$giv_row[idx]'";
								echo "upg_sql1 $upg_sql1<br/>";
								//sql_query($upg_sql1);
							}
							if ($giv_row["p2pCtr_contract_id"]=="") {
								$upg_sql2 = "UPDATE cf_product_give SET p2pCtr_contract_id='".$resD["result_list"][$j]["investment_contract_id"]."' WHERE idx='$giv_row[idx]'";
								echo "upg_sql2 $upg_sql2<br/>";
								//sql_query($upg_sql2);
							}
						}

						echo "OK<br/>";

					} else {

						echo "NOT FOUND $pidx and $midx and $trn";
					}

				} else if ($resD["result_list"][$j]["pni_payment_rsp_code"]=="A5020") {
					echo "중복기록요청<br/>";
				} else if ($resD["result_list"][$j]["pni_payment_rsp_code"]=="A8752") {
					echo "회차번호가 순차적이지 않음<br/>";
				} else {
					echo "OOPS<br/>";
					$cfm = "fail";
				}

			}

			echo "cfm = $cfm<br/>";

			$uplog_sql = "UPDATE p2pctr_request_log SET qna_log='Y' WHERE idx='$row[idx]'";
			echo "uplog_sql $uplog_sql<br/>";
			//sql_query($uplog_sql);

		} else if ($resD["rsp_code"]=="A0000") {

			if (!count($resD["result_list"])) {
				$upsql = "UPDATE p2pctr_request_log SET qna_log='Y' WHERE idx='$row[idx]'";
				echo $upsql."<br/>";
				sql_query($upsql);
			} else {
	
				//$prd_sql = "SELECT idx FROM cf_product WHERE goods_id='".$reqD["pni_payment_common_info"]["goods_id"]."'";
				//$prd_res = sql_query($prd_sql);
				//$prd_row = sql_fetch_array($prd_res);


				for ($j=0 ; $j<count($resD["result_list"]) ; $j++) {
					echo "<pre>num = $j "; print_r($resD["result_list"][$j]); echo "</pre>";
					echo "OK<br/>";

					$inv_sql = "SELECT product_idx, member_idx FROM cf_product_invest WHERE contract_id='".$resD["result_list"][$j]["investment_contract_id"]."'";
					$inv_res = sql_query($inv_sql);
					$inv_row = sql_fetch_array($inv_res);
					
					$pidx = $inv_row["product_idx"];
					$midx = $inv_row["member_idx"];
					$trn  = $reqD["pni_payment_common_info"]["securities_n_count"];

					echo $pidx." ".$midx." ".$trn."회차 --<br/>";
					if ($pidx and $midx and $trn) {
						$giv_sql = "SELECT idx, p2pCtr_contract_id, p2pCtr_date FROM cf_product_give WHERE product_idx='$pidx' AND member_idx='$midx' AND turn='$trn'";
						$giv_res = sql_query($giv_sql);
						$giv_cnt = sql_num_rows($giv_res);
						$giv_row = sql_fetch_array($giv_res);
						echo "$giv_cnt $giv_row[idx]<br/>";

						if ($giv_row["idx"]) {
							if ($giv_row["p2pCtr_date"]=="") {
								$rdate_rtime = preg_replace('/[^0-9]/','', $row["rdate"]).preg_replace('/[^0-9]/','', $row["rtime"]);
								$upg_sql1 = "UPDATE cf_product_give SET p2pCtr_date='$rdate_rtime' WHERE idx='$giv_row[idx]'";
								echo "upg_sql1 $upg_sql<br/>";
							}
							if ($giv_row["p2pCtr_contract_id"]=="") {
								$upg_sql2 = "UPDATE cf_product_give SET p2pCtr_contract_id='".$resD["result_list"][$j]["investment_contract_id"]."' WHERE idx='$giv_row[idx]'";
								echo "upg_sql2 $upg_sql2<br/>";
								//sql_query($upg_sql2);
							}
						}

						
					} else {
						$cfm = "fail";
					}
				}

				echo "cfm = $cfm<br/>";

				if ($cfm=="succ") {
					$uplog_sql = "UPDATE p2pctr_request_log SET qna_log='Y' WHERE idx='$row[idx]'";
					echo "uplog_sql $uplog_sql<br/>";
					//sql_query($uplog_sql);

				}


			}
		}

	} else if ($row["apiNo"]=="4.4.5") {   // 투자계약 갱신

		$psql = "";
		$upp_sql = "";

		if ($resD["rsp_code"]=="A0000" ) {

			if ($row["qna_log"]<>"Y") {
			
				$psql = "SELECT * FROM cf_product_invest WHERE product_idx='".$row["product_idx"]."' AND contract_id='".$contract_id."'";
				$prow = sql_fetch($psql);
				if (!$prow["contract_status"]) {
					$upp_sql = "UPDATE cf_product_invest SET contract_status='".$reqD["status"]."' WHERE idx='$prow[idx]'";
					//sql_query($upp_sql);
					$uplog_sql = "UPDATE p2pctr_request_log SET qna_log='Y' WHERE idx='$row[idx]'";
					//sql_query($uplog_sql);
				}
			}

		} else {
			continue;
		}

	}
	?>
	<tr>
		<td><?=$num2++?></td>
		<td><?=$row["idx"]?></td>
		<td><?=$row["product_idx"]?></td>
		<td><?=$contract_id?></td>
		<td><?=$resD["rsp_code"]?></td>
		<td><pre><?=print_r($reqD)?></pre></td>
		<td><pre><?=print_r($resD)?></pre></td>
		<td><?=$prow["idx"]?> <?=$prow["loan_status"]?></td>
		<td><?=$upp_sql?><br/><?=$uplog_sql?></td>
	</tr>
	<?
}
?>
</table>
<?
/*
function p2pctr_end_check_imsi($product_idx) {
	$all_sql = "SELECT COUNT(idx) all_cnt FROM cf_product_invest WHERE product_idx='$product_idx' AND invest_state='Y'";
	$all_row = sql_fetch($all_sql);
	$all_cnt = $all_row["all_cnt"];

	$end_sql = "SELECT COUNT(idx) end_cnt FROM cf_product_invest 
				 WHERE product_idx='$product_idx' AND invest_state='Y'
			       AND contract_status IN ('S301','S302', 'S303', 'S304', 'S311', 'S312')";
	$end_row = sql_fetch($end_sql);
	$end_cnt = $end_row["end_cnt"];

	if ($end_cnt==$all_cnt) {

		$main_sql = "SELECT * FROM p2pctr_product WHERE product_idx='$product_idx'";
		$main_res = sql_query($main_sql);
		$main_cnt = $main_res->num_rows;

		if ($main_cnt) {
			$main_row = sql_fetch_array($main_res);
			$upd_sql = "UPDATE p2pctr_product SET p2pctr_end='Y' WHERE product_idx='$product_idx'";
			sql_query($upd_sql);
		} else {
			$main_ins_sql = "INSERT INTO p2pctr_product 
									 SET product_idx='$product_idx', 
										 p2pctr_end='Y'";
			sql_query($main_ins_sql);
		}


	}
}
*/
?>