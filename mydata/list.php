<?
include_once("dbconfig.php");
include_once("mydata_common.lib.php");
include_once("mydata_header.php");
?>
<?
$user_ci = $rdata["Header"]["x-user-ci"];  // 정보제공자가 부여한 대출계약 식별값
$next_page = $rdata["Parameter"]["next_page"];  // 
$limit   = $rdata["Parameter"]["limit"];  // 
$user_ci = "0ZwVRG/UpkxFzg6StddadYpi9R8Oohb28ysvnCG8M3pzXRls/ofaVIFqCfDdTd/2Ang9S8hjyD6467DBSXV1Uw==";

$next_page = "6162";
$limit = 10;
?>
<?
$sqlm = "SELECT mb_no FROM g5_member WHERE mb_ci='".$user_ci."'";
$resm = mysqli_query($con, $sqlm);
$rowm = mysqli_fetch_array($resm);



$sql1 = "SELECT gr_idx FROM cf_product WHERE loan_mb_no='".$rowm["mb_no"]."'";
$res1 = mysqli_query($con, $sql1);
$row1 = mysqli_fetch_array($res1);

if ($next_page) $wh_nxt = " AND idx < '$next_page' ";

$sql = "SELECT idx, gr_idx, category, category2, recruit_amount, loan_start_date from cf_product 
		 WHERE gr_idx='".$row1["gr_idx"]."' 
		 $wh_nxt
		 ORDER BY idx DESC
		 LIMIT $limit";
$res = mysqli_query($con, $sql);
$cnt = mysqli_num_rows($res);


$ret = array();
$ret["Header"]["x-api-tran-id"] = get_tran_id();
$ret["Body"]["rsp_code"] = "00000";
$ret["Body"]["rsp_msg"] = "성공";
$ret["Body"]["search_timestamp"] = 0;    // Timestamp 로직 미제공
$ret["Body"]["lending_cnt"] = $cnt;
$ret["Body"]["lending_list"] = array();

for ($i=0 ; $i<$cnt ; $i++) {
	$row = mysqli_fetch_array($res);

	$LIST = array();
	$LIST["lending_id"] = $row["idx"];
	$LIST["type"] = get_prd_type($row["category"] , $row["category2"]);
	$LIST["lending_amt"] = $row["recruit_amount"];
	$LIST["issue_date"] = $row["loan_start_date"];
	

	$ret["Body"]["lending_list"][$i] = $LIST;
}


// 이후 데이터가 더 있으면 리턴값에 next_page 를 세팅한다.
$chk_sql = "SELECT COUNT(idx) chk_cnt
			  FROM cf_product
			 WHERE gr_idx='".$row1["gr_idx"]."' 
			   AND idx < '".$LIST["lending_id"]."'
			 ORDER BY idx DESC";
$chk_res = mysqli_query($con, $chk_sql);
$chk_row = mysqli_fetch_array($chk_res);
if ($chk_row["chk_cnt"]) $ret["Body"]["next_page"] = $LIST["lending_id"];



echo "<pre>"; print_r($ret); echo "</pre>";
?>