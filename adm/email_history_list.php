<?

include_once('./_common.php');

// post로 받은 데이터를 변수화
foreach($_POST as $k=>$v) {
	$$_POST[$k] = $v;
}


// 과거메일 리스트
$sql2 = "SELECT email_title, email_contents FROM g5_mailling_list WHERE idx = {$idx}";
$res = sql_fetch($sql2);

echo $res['email_contents'];

//echo str_replace("\r\n","<br>",$res['email_contents']);


?>