<?
include "dbconfig.php";
include "mydata_common.lib.php";
?>
<?
header('Content-Type: application/json; charset=utf-8');
?>
<?

$sql = "SELECT idx, category, category2, mortgage_guarantees, recruit_amount, loan_start_date  FROM cf_product ORDER BY idx DESC LIMIT 10";
$res = mysqli_query($con, $sql);
$cnt = mysqli_num_rows($res);

$data = array();

$data["Header"]["x-api-tran-id"] = get_tran_id();

$data["Body"]["rsp_code"] = "00000";
$data["Body"]["rsp_msg"] = "성공";

for ($i=0 ; $i<$cnt ; $i++) {

	$row = mysqli_fetch_array($res);


	$type = get_prd_type($row["category"], $row["mortgage_guarantees"]);


	$data["Body"]["lending_list"][$i]["lending_id"] = $row["idx"];
	$data["Body"]["lending_list"][$i]["type"] = $type;
	$data["Body"]["lending_list"][$i]["lending_amt"] = $row["recruit_amount"];
	$data["Body"]["lending_list"][$i]["issue_date"] = $row["loan_start_date"];

}


?>
<?
echo "<pre>";
print_r($data);
echo "</pre>";
//echo json_encode($data);
?>

<?

?>