<?
include_once('_common.php');

$answer = $_POST["answer"];
$ymd = $_POST["ymd"];

//$db_port = 3308; // test 서버
$db_port = 3307; // live 서버


$conn = new mysqli(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD, G5_MYSQL_DB, $db_port);

if ($conn->connect_error) {
	$res["result"] = "fail";
	json_encode($res);
	die();
}

//$ymd = date("Ymd",strtotime("+1 day", time() ));


$res["res"] = "FAIL";
$res["answer"] =$answer;
$res["mb_id"] = $member["mb_id"];

$sql = "insert into cf_event_10bS set 
			mb_no=?,
			mb_id=?,
			ymd=?,
			answer=?,
			insert_datetime=now()";
$stmt = $conn->prepare($sql);
$stmt -> bind_param("issi", $member['mb_no'] , $member['mb_id'] , $ymd , $answer);
if ($stmt -> execute()) $res["res"] = "SUCCESS";

mysqli_close($conn);
?>
<?
echo json_encode($res);
?>