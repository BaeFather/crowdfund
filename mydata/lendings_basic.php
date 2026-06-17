<?
include "dbconfig.php";
include "mydata_common.lib.php";

// 마이데이터 
// P2P-002 P2P 대출 기본정보 조회
// POST /lendings/basic
?>
<?
$lending_id = $_REQUEST["lending_id"];  // 정보제공자가 부여한 대출계약 식별값
?>
<?
$sql = "SELECT loan_end_date, loan_interest_rate, repay_type, ltv FROM cf_product WHERE idx='$lending_id' ";
$res = mysqli_query($con, $sql);
$row = mysqli_fetch_array($res);
echo $lending_id."<br/>";

echo "<pre>"; print_r($row); echo "</pre>";

if ($row["repay_type"]=="1") $repay_method="01";

$ret = array();
$ret["Header"]["x-api-tran-id"] = get_tran_id();
$ret["Body"]["rsp_code"] = "00000";
$ret["Body"]["rsp_msg"] = "성공";
$ret["Body"]["search_timestamp"] = "0";
$ret["Body"]["exp_date"] = $row["loan_end_date"];
$ret["Body"]["offered_rate"] = $row["loan_interest_rate"];
$ret["Body"]["repay_method"] = $repay_method;
$ret["Body"]["ltv_rate"] = $row["ltv"];

echo "<pre>"; print_r($ret); echo "</pre>";
?>