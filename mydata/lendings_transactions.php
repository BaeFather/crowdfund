<?
include "dbconfig.php";
include "mydata_common.lib.php";

error_reporting(E_ALL & ~E_NOTICE)

// 마이데이터 
// P2P-002 P2P 대출 거래내역 조회
// POST /lendings/transactions
?>
<?
$from_date = $_REQUEST["from_date"];    // 조회 시작일자
$to_date = $_REQUEST["to_date"];        // 조회 시작일자
$next_page = $_REQUEST["next_page"];    // 다음 페이지 요청을 위한 기준개체
$limit = $_REQUEST["limit"];            // 기준개체 이후 반환될 개체의 개수
$lending_id = $_REQUEST["lending_id"];  // 정보제공자가 부여한 대출계약 식별값

$product_idx = $lending_id;

$sql = "SELECT turn, turn_sno,  banking_date, SUM(principal) sum_prin, SUM(interest+interest_tax+local_tax+fee) sum_int FROM cf_product_give WHERE product_idx='$product_idx'
		 GROUP BY turn, turn_sno";
$res = mysqli_query($con, $sql);
$cnt = mysqli_num_rows($res);

$LIST = array();
for ($i=0 ; $i<$cnt ; $i++) {
	$row = mysqli_fetch_array($res);

	$LIST[$i]["trans_dtime"] = $row["banking_date"] ;
	$LIST[$i]["repay_cnt"] = $row["turn"] ;
	$LIST[$i]["repay_type"] = "01" ;
	$LIST[$i]["trans_amt"] = $row["sum_prin"] + $row["sum_int"];
	$LIST[$i]["principal_amt"] = $row["sum_prin"];
	$LIST[$i]["int_amt"] = $row["sum_int"];

}

$ret = array();
$ret["Header"]["x-api-tran-id"] = get_tran_id();
$ret["Body"]["rsp_code"] = "00000";
$ret["Body"]["rsp_msg"] = "성공";
$ret["Body"]["next_page"] = "";  // 다음 페이지 요청을 위한 기준개체 , 다음페이지 없으면 미회신
$ret["Body"]["trans_cnt"] = 0;
$ret["body"]["trans_list"] = $LIST;

echo "<pre>"; print_r($ret); echo "</pre>";
?>