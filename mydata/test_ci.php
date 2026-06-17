<?
include "dbconfig.php";
include "mydata_common.lib.php";
include_once("mydata_header.php");

$adata = array();
$adata["Header"]["x-api-tran-id"] = get_tran_id();
$adata["Body"]["rsp_code"] = "00000";
$adata["Body"]["rsp_msg"] = "성공";

echo json_encode($adata , JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE );
?>