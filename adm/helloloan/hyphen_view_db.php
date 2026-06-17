<?
include_once('./_common.php');
?>
<?
if (!$uniqNo) die();

$sql = "SELECT * FROM hloan_content_pdf WHERE uniqNo='$uniqNo' AND pdf_hex<>'' ORDER BY input_datetime DESC LIMIT 1";
$res = sql_query($sql);

if ($res->num_rows) {
	$row = sql_fetch_array($res);

	$filename = date("YmdHis").".pdf";

	header('Content-type: application/pdf');
	header("Content-Disposition: attachment; filename=$filename"); 

	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	$pack = pack("H*", $row["pdf_hex"]);

	echo $pack;
}
?>