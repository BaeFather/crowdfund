<?
include "dbconfig.php";
include "mydata_common.lib.php";
include_once("mydata_header.php");
?>
<?
$adata = array();
$adata["Header"]["x-api-tran-id"] = get_tran_id();
$adata["Body"]["rsp_code"] = "00000";
$adata["Body"]["rsp_msg"] = "성공";

//echo "<pre>"; print_r($adata); echo "</pre>";
?>
<?
$rsp_json = json_encode($adata , JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE );

$uplog_sql = "UPDATE mydata_log 
				 SET snd_tran_id = '".$adata["Header"]["x-api-tran-id"]."',
					 rsp_code='".$adata["Body"]["rsp_code"]."',
					 rsp_msg='".$adata["Body"]["rsp_msg"]."',
				     rsp_json = '".addslashes(trim($rsp_json))."' WHERE idx='$log_id'";
mysqli_query($con, $uplog_sql);
?>
<?
echo $rsp_json;
?>