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
<?
//$headers = apache_request_headers_uf();

//echo "<pre>"; print_r($rdata); echo "</pre>";
?>
<?
//phpinfo();
//if( !function_exists('apache_request_headers') ) {

	function apache_request_headers_uf() {
		$arh = array();
		$rx_http = '/\AHTTP_/';
		foreach($_SERVER as $key => $val) {
			if( preg_match($rx_http, $key) ) {
				$arh_key = preg_replace($rx_http, '', $key);
				$rx_matches = array();
				// do some nasty string manipulations to restore the original letter case
				// this should work in most cases
				$rx_matches = explode('_', $arh_key);
				if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
					foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
					$arh_key = implode('-', $rx_matches);
				}
				$arh[$arh_key] = $val;
			}
		}
		return( $arh );
	}

//}
?>
<?
echo "<br/> ======================================= <br/>";
echo "<pre>"; print_r($_SERVER); echo "</pre>";
?>