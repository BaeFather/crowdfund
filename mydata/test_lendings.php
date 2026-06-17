<?
include "dbconfig.php";
include "mydata_common.lib.php";
include_once("mydata_header.php");

$adata = array();
$adata["Header"]["x-api-tran-id"] = get_tran_id();
$adata["Body"]["rsp_code"] = "00000";
$adata["Body"]["rsp_msg"] = "성공";
$adata["Body"]["search_timestamp"] = 0;
//$adata["Body"]["next_page"] = 0;
$adata["Body"]["lending_cnt"] = 1;
$adata["Body"]["lending_list"] = array();
$adata["Body"][0]["lending_id"] = "111";
$adata["Body"][0]["type"] = "02";
$adata["Body"][0]["lending_amt"] = 1000000;
$adata["Body"][0]["issue_date"] = "2021-11-10";

$rsp_json = json_encode($adata , JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE );
echo $rsp_json;

$uplog_sql = "UPDATE mydata_log 
				 SET snd_tran_id = '".$adata["Header"]["x-api-tran-id"]."',
					 rsp_code='".$adata["Body"]["rsp_code"]."',
					 rsp_msg='".$adata["Body"]["rsp_msg"]."',
				     rsp_json = '".addslashes(trim($rsp_json))."' WHERE idx='$log_id'";
mysqli_query($con, $uplog_sql);
?>