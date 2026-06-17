<?
include_once('_common.php');

if (!$member["mb_id"]) {
	$res["res"] = "FAIL";
	$res["msg"] = "로그인후에 응모가능합니다.";
	echo json_encode($res);
	die();
}

$answer = $_POST["answer"];
$ymd = $_POST["ymd"];

//$db_port = 3308; // test 서버
$db_port = 3307; // live 서버


$this_tm = date("Hi");
if ($this_tm>="1028" and $this_tm<="2302") {
} else {
	$res["res"] = "FAIL";
	$res["msg"] = "응모 가능시간이 아닙니다.";
	echo json_encode($res);
	die();
}

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

$chk_sql = "select count(*) ct from cf_event_10bS where ymd='$ymd' and mb_no='$member[mb_no]'";
$chk_res = sql_query($chk_sql);
$chk_row = sql_fetch_array($chk_res);
$chk_yn  = $chk_row["ct"];

if ($chk_yn) {
	$res["res"] = "FAIL";
	$res["msg"] = "이미 응모하셨습니다.";
	echo json_encode($res);
	die();
}

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