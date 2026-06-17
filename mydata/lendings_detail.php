<?
include "dbconfig.php";
include "mydata_common.lib.php";

// 마이데이터 
// P2P-003 P2P 대출 추가정보 조회
// POST /lendings/detail
?>
<?
$lending_id = $_REQUEST["lending_id"];  // 정보제공자가 부여한 대출계약 식별값
$product_idx = $lending_id;
?>
<?
$sql = "SELECT recruit_amount  FROM cf_product WHERE idx='$product_idx' ";
$res = mysqli_query($con, $sql);
$row = mysqli_fetch_array($res);

$balance_amt = 0;
$prin_sql = "SELECT SUM(principal) sum_principal FROM cf_product_give WHERE product_idx='$lending_id'";
$prin_res = mysqli_query($con, $prin_sql);
$prin_row = mysqli_fetch_array($prin_res);
$balance_amt = $row["recruit_amount"] - $prin_row["sum_principal"];

$this_date = date("Y-m-d");

$next_repay_cnt = 0;
$next_repay_date = "";
$principal_amt = 0;
$int_amt = 0;

$tn_sql = "SELECT total_turn, turn, turn_sno, repay_date, principal as sum_prin, total_interest
			 FROM cf_product_turn_sum
			WHERE product_idx='$product_idx' 
			  AND repay_date>='$this_date'
			ORDER BY total_turn
			LIMIT 1
			";
$tn_res = mysqli_query($con, $tn_sql);
$tn_cnt = mysqli_num_rows($tn_res);


if ($tn_cnt) {

	$tn_row = mysqli_fetch_array($tn_res);

	$next_repay_cnt = $tn_row["total_turn"];
	$next_repay_date = $tn_row["repay_date"];
	$principal_amt = $tn_row["sum_prin"];
	$int_amt = $tn_row["total_interest"];

}
/*
//echo "<pre>"; print_r($row); echo "</pre>";
$bill_tbl_name = getBillTable_simple($lending_id);

$tn_sql = "SELECT turn, turn_sno, repay_date, SUM(partial_principal) sum_prin, SUM(day_interest) sum_inter
			 FROM $bill_tbl_name
			WHERE product_idx='$product_idx'
			GROUP BY turn, turn_sno
			ORDER BY repay_date";
$tn_res = mysqli_query($con, $tn_sql);
$tn_cnt = mysqli_num_rows($tn_res);


for ($i=0 ; $i<$tn_cnt ; $i++) {

	$tn_row = mysqli_fetch_array($tn_res);

	echo $tn_row["turn"]." ".$tn_row["turn"]." ".$tn_row["repay_date"]."<br/>";

	if ($tn_row["repay_date"] >= $this_date) {
		$next_repay_cnt = $tn_row["turn"];
		$next_repay_date = $tn_row["repay_date"];
		$principal_amt = $tn_row["sum_prin"];
		$int_amt = $tn_row["sum_inter"];
		break;
	}
	
}
*/

$ret = array();
$ret["Header"]["x-api-tran-id"] = get_tran_id();
$ret["Body"]["rsp_code"] = "00000";
$ret["Body"]["rsp_msg"] = "성공";
$ret["Body"]["search_timestamp"] = "0";
$ret["Body"]["balance_amt"] = $balance_amt;  // 대출잔액
$ret["Body"]["next_repay_cnt"] = $next_repay_cnt;  // 다음 상환 거래의 회차
$ret["Body"]["next_repay_date"] = $next_repay_date;  // 다음 회차 상환 예정일
$ret["Body"]["principal_amt"] = $principal_amt;  // 다음 회차에 상환될 금액 중 원금
$ret["Body"]["int_amt"] = $int_amt;  // 다음 회차에 상환될 금액 중 이자

echo "<pre>"; print_r($ret); echo "</pre>";
?>