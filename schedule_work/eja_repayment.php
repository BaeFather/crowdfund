<?
###############################################################################
##  상품-회차별 총금액 기준 지급스케쥴 (생성된 스케쥴의 업데이트용)
###############################################################################

include_once('/home/crowdfund/public_html/common.cli.php');

?>
<?
$ymd = date("Ymd");

$sql1 = "
	SELECT
		idx, title, loan_mb_no
	FROM
		cf_product A
	WHERE 1
		AND A.state IN('1','8')
		AND A.category = '2'
		AND A.mortgage_guarantees = '1'
		AND A.recruit_amount > 10000
		AND A.loan_start_date <> '0000-00-00'
		AND A.idx <> '5933'
AND A.idx='7703'
	ORDER BY
		A.loan_start_date, A.start_num, A.idx";
// AND A.idx = '4945'

$res1 = sql_query($sql1);
$cnt1 = $res1->num_rows;

$ret_arr = array();  // 출력용 배열 변수
$in_tot  = 0;         // 총 입금건수
$idx     = 0;         // 출력용 LIST 인덱스

for ($i=0 ; $i<$cnt1 ; $i++) {

	$row1 = sql_fetch_array($res1);


	$sql2 = "SELECT * FROM cf_product_turn WHERE product_idx='$row1[idx]' AND eja_in_date='' ORDER BY turn";
//$sql2 = "SELECT * FROM cf_product_turn WHERE product_idx='$row1[idx]' ORDER BY turn";
	$res2 = sql_query($sql2);
	$cnt2 = $res2->num_rows;


	if (!$cnt2) continue;

	//echo "$sql2\n";

	for ($j=0 ; $j<$cnt2 ; $j++) {

		$row2 = sql_fetch_array($res2);

		//echo "$row1[idx] $row1[title] $row2[turn]회차\n";



		// 이전 회차의 잔액을 가져온다 -------------------------------------------------------------------------------------------------------
		$sql_chk = "SELECT * FROM cf_product_turn WHERE product_idx='$row1[idx]' AND turn<'$row2[turn]' ORDER BY turn DESC limit 1";
		$res_chk = sql_query($sql_chk);
		$cnt_chk = sql_num_rows($res_chk);

		$bf_amt = 0;

		if ($cnt_chk) {
			$row_chk = sql_fetch_array($res_chk);
			$bf_SR_DATE = $row_chk["eja_in_date"];
			$bf_FB_SEQ  = $row_chk["FB_SEQ"];
			$bf_ERP_TRANS_DT = $row_chk["ERP_TRANS_DT"];
			$bf_amt = $row_chk["remain_amt"];
			//$wh1 = " AND (SR_DATE>='$bf_SR_DATE' AND FB_SEQ>'$bf_FB_SEQ)' ";
			$wh1 = " AND (ERP_TRANS_DT>'$bf_ERP_TRANS_DT') ";
		} else {
			$wh1 = "";
			$bf_amt = 0;
		}


		if ($bf_amt>=$row2["eja"]) {
			$in_date = $bf_SR_DATE;
			$ERP_TRANS_DT = $bf_ERP_TRANS_DT;
			$FB_SEQ = $bf_FB_SEQ;
			$tot_in_amt = 0;
			$cnt3 = 0;
		} else {


			//$sql3 = "SELECT * FROM IB_FB_P2P_IP WHERE cust_id='$row1[loan_mb_no]' AND SR_DATE='$ymd'";
			$sql3 = "SELECT * FROM IB_FB_P2P_IP WHERE cust_id='$row1[loan_mb_no]' $wh1 AND TR_AMT<10000000 ORDER BY SR_DATE, FB_SEQ";
			$res3 = sql_query($sql3);
			$cnt3 = sql_num_rows($res3);
			//echo $sql3." $cnt3\n";

			$tot_in_amt = 0;
			$in_date = "";
			$ERP_TRANS_DT = "";
			$FB_SEQ = "";
			for ($k=0 ; $k<$cnt3 ; $k++) {
				$row3 = sql_fetch_array($res3);
				$tot_in_amt = $tot_in_amt + $row3["TR_AMT"];
				//echo "$k $tot_in_amt = $tot_in_amt + $row3[TR_AMT] $row2[eja]\n";
				if ($tot_in_amt>=$row2["eja"]) {
					$in_date = $row3["SR_DATE"];
					$ERP_TRANS_DT = $row3["ERP_TRANS_DT"];
					$FB_SEQ = $row3["FB_SEQ"];
					break;
				}
			}
			//echo $tot_in_amt."\n";

		}

		if ($bf_amt>=$row2["eja"] or $cnt3) {

			$in_tot = $in_tot + 1 ;

			//echo $row1["idx"]."\n";
			//$row3 = sql_fetch_array($res3);

			//echo "이자 $row2[eja] - 잔액 $row3[TR_AMT] - 이전잔액 $bf_amt\n";

			//if ($row2["eja"] <= $row3["TR_AMT"]+$bf_amt) {
			if ($row2["eja"] <= $tot_in_amt + $bf_amt) {

				$idx = $idx+1;


				//$remain_amt = number_format($row3["TR_AMT"] - $row2["eja"], 2);
				//$remain_amt = bcsub($row3["TR_AMT"] , $row2["eja"], 2);
				//$remain_amt = $row3["TR_AMT"] - $row2["eja"] + $bf_amt;
				$remain_amt = $tot_in_amt - $row2["eja"] + $bf_amt;

				//echo "이자납입 $row3[SR_DATE] 입금액 $row3[TR_AMT] $row2[eja] 보정 $bf_amt 잔액 $remain_amt\n";

				$ret_arr["LIST"][$idx]["prd_idx"] = $row1["idx"];

				$up_sql = "
					UPDATE
						cf_product_turn
					SET
						eja_in_date = '$in_date',
						remain_amt = '$remain_amt',
						FB_SEQ = '$FB_SEQ',
						ERP_TRANS_DT = '$ERP_TRANS_DT'
					WHERE
						idx = '$row2[idx]'";
				sql_query($up_sql);
				//echo "$up_sql\n";

			} else {
				//echo "\nBREAK 1\n\n";
				break;
			}

		} else {
			//echo "입금내역 없음 \n";
			break;
		}

	}
	//echo "\n";

}

$ret_arr["in_tot"] = $in_tot;


echo json_encode($ret_arr , JSON_UNESCAPED_UNICODE);
//echo "\n";
?>