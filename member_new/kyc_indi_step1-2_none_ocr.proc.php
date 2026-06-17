<?

include_once("_common.php");

while(list($k, $v) = each($_POST)) { ${$k} = trim($v); }


$juminNo = $privateNo1 . $privateNo2;

$foriegner = ($koreanYN=='Y') ? false : true;

if( checkJumin($juminNo, $foriegner) ) {

	$juminNo_enc = masterEncrypt($juminNo, false);		// AES256 암호화

	$ARR = array('result'  => 'success', 'private_num' => $juminNo_enc);

}
else {

	$ARR = array('result'  => 'fail', 'message' => '정상적인 개인번호가 아닙니다.');

}

echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);


sql_close();
exit;

?>