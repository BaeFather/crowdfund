<?
/////////////////////////////////////////////////
// 예치금 보정
// 제작완료일 : 2019-12-23
/////////////////////////////////////////////////

include_once('./_common.php');


if($is_admin != 'super') { $RESULT_ARR = array("result" => "error", "message" => "최고관리자만 접근 가능합니다."); echo json_encode($RESULT_ARR); exit; }
if(!$_POST['target_range']) { $RESULT_ARR = array("result" => "error", "message" => "처리 대상을 입력 하십시요."); echo json_encode($RESULT_ARR); exit; }
if(!$_POST['amount'] || (int)$_POST['amount']<1) { $RESULT_ARR = array("result" => "error", "message" => "금액을 입력 하십시요."); echo json_encode($RESULT_ARR); exit; }
if(!$_POST['proc']) { $RESULT_ARR = array("result" => "error", "message" => "지급/차감 액션을 입력 하십시요."); echo json_encode($RESULT_ARR); exit; }



$where = "";
$where.= " AND member_group='F' AND mb_level BETWEEN 1 AND 8 ";

///////////////////////////////////////
// 선택지급
///////////////////////////////////////
if($_POST['target_range']=='3') {
	if( count($_POST['chk']) > 0 ) {

		$chk = array();
		foreach($_POST['chk'] as $key => $val) {
			array_push($chk, $val);
		}

		$where.= " AND (mb_no IN (".join(',', $chk).")) ";

	}
	else {
		$RESULT_ARR = array("result" => "error", "message" => "선택된 처리 대상자가 없습니다."); exit;
	}
}
///////////////////////////////////////
// 전체지급
///////////////////////////////////////
else if($_POST['target_range']=='1') {
	//
}



$sql = "SELECT mb_id FROM g5_member WHERE 1 $where";
if($_POST['target_range']=='1') {
	$sql.= " ORDER BY mb_no ASC";
}
$res = sql_query($sql);

$proc_count = 0;

if($_POST['proc']=='charge') {
	$proc_str = "지급";
	$amount  =  $_POST['amount'];

	while( $row = sql_fetch_array($res) ) {
		if($row['mb_id']) {
			insert_point($row['mb_id'], $amount, "예치금 지급", "@charge", $member['mb_id'], $member['mb_id'].'-'.uniqid(''), 0);
			$proc_count += 1;
		}
	}
}
else if($_POST['proc']=='discharge') {
	$proc_str = "차감";
	$amount  =  $_POST['amount'] * -1;

	while( $row = sql_fetch_array($res) ) {
		if($row['mb_id']) {
			insert_point($row['mb_id'], $amount, "예치금 차감", "@discharge", $member['mb_id'], $member['mb_id'].'-'.uniqid(''), 0);
			$proc_count += 1;
		}
	}
}


$message = number_format($proc_count)."명의 회원에게 예치금 ".number_format($_POST['amount'])."원이 " . $proc_str . " 되었습니다.";
$RESULT_ARR = array("result" => "success", "message" => $message);
echo json_encode($RESULT_ARR);

sql_close();
exit;

?>