<?

// 표준 업종코드 JSON 출력

include_once("_common.php");

while( list($k,$v) = each($_REQUEST) ) { ${$k} = trim($v); }

$sql = "SELECT P_CD AS code, P_NM AS name FROM aml_kofiu_industry_code WHERE C_CD='".$C_CD."' ORDER BY P_CD";
$res = sql_query($sql);
while( $row = sql_fetch_array($res) ) {

	$ARR[] = $row;

}

echo json_encode($ARR);
//echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

sql_close();
exit;

?>