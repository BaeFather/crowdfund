<?
include_once('_common.php');

$allow = $_POST["allow"];

$res["allow"] = $allow;

if ($allow=="true") $allow_val = "1";
else                $allow_val = "0";
$res["allow"] = $allow_val;

//$db_port = 3308; // test 서버
$db_port = 3307; // live 서버


$conn = new mysqli(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD, G5_MYSQL_DB, $db_port);

if ($conn->connect_error) {
	$res["result"] = "fail";
	echo json_encode($res);
	die();
}

$sql = "update g5_member set mb_sms=?, mb_mailling=? where mb_no = '$member[mb_no]' ";

$stmt = $conn->prepare($sql);
$stmt -> bind_param("ss", $allow_val, $allow_val);
$stmt -> execute();

//$res["sql"] = $sql;

mysqli_close($conn);
?>
<?
echo json_encode($res);
?>