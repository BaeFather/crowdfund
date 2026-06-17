<?

$sub_menu = "200200";
include_once("./_common.php");
check_demo();

auth_check($auth[$sub_menu], "d");

// post로 받은 데이터를 변수화
foreach($_POST as $k=>$v) {
	$$_POST[$k] = $v;
}


if($mode=='list_state_update') {

	$idx_count = count($_POST['chk']);
	$succ_count = 0;
	$fail_count = 0;

	if($idx_count) {

		$IDX = $_POST['chk'];

		for($i=0, $j=1; $i<$idx_count; $i++,$j++) {

			$ODATA = sql_fetch("SELECT idx, mb_id, req_price, state, LEFT(regdate, 16) AS regdate FROM g5_withdrawal WHERE idx='".$IDX[$i]."'");

			$sql = "
				UPDATE
					g5_withdrawal
				SET
					state='2',
					admin_memo='요청 목록에서 일괄 지급 처리',
					admin_editdate=NOW()
				WHERE (1)
					AND idx='".$IDX[$i]."'
					AND state='1'";
			if( sql_query($sql) ) {
				if(sql_affected_rows()) $succ_count+=1;

				$o_req_price = (int)$ODATA['req_price'] * -1;  // 포인트로그 테이블상의 차감요인(po_content)은 po_point 필드에 마이너스값으로 등록되어있으므로 처리할 출금액을 마이너스값으로 전환
				$sql2 = "
					UPDATE
						g5_point
					SET
						po_content='예치금 출금'
					WHERE 1=1
						AND mb_id = '".$ODATA['mb_id']."'
						AND po_datetime LIKE '".$ODATA['regdate']."%'
						AND po_content = '예치금 출금 대기'
						AND po_point = '".$o_req_price."'";
				$res = sql_query($sql2);

			}
			else {
				$fail_count+=1;
			}

		}

	}
	else {
		echo "출금 요청 인덱스 전송불량!!";
	}

	$msg = "출금 요청 : ". $succ_count . "건 처리 완료";
	if($fail_count) $msg.= " / " . $fail_count . "건 처리 불가";
	echo $msg;

	exit;

}
else {

	if(!$_POST['idx'] || $_POST['idx'] == '') { alert("잘못된 접근입니다.","./withdrawal_list.php"); exit; }

	$qstr = "idx=".$idx."&".$qstr;

	$ODATA  = sql_fetch("SELECT idx, mb_id, req_price, state, LEFT(regdate, 16) AS regdate FROM g5_withdrawal WHERE idx='$idx'");
	if(!$ODATA) { alert("등록된 데이터가 없습니다.","./withdrawal_list.php"); exit; }

	if($state || $admin_memo) {

		if( ($state != $ODATA['state']) || ($admin_memo != $ODATA['admin_memo']) ) {

			$sql = "UPDATE g5_withdrawal SET ";
			$sql.= ($state) ? "  state = $state, " : "";
			$sql.= ($admin_memo) ? "  admin_memo = '$admin_memo', " : "";
			$sql.= "  admin_editdate = NOW() ";
			$sql.= "WHERE ";
			$sql.= " idx = '$idx'";
			//echo $sql;
			if( sql_query($sql) ) {

				// 예치금 출금완료 처리에 대한 포인트로그 출납사유 정보 업데이트
				if($state=='2') {
					if($ODATA['state']=='1') {		//** 출금신청중일 경우에만 완료 처리 가능하도록..
						$o_req_price = (int)$ODATA['req_price'] * -1;  // 포인트로그 테이블상의 차감요인(po_content)은 po_point 필드에 마이너스값으로 등록되어있으므로 처리할 출금액을 마이너스값으로 전환
						$sql = "
							UPDATE
								g5_point
							SET
								po_content = '예치금 출금'
							WHERE 1=1
								AND mb_id = '".$ODATA['mb_id']."'
								AND po_datetime LIKE '".$ODATA['regdate']."%'
								AND po_content = '예치금 출금 대기'
								AND po_point = '".$o_req_price."'";
						$res = sql_query($sql);
					}
				}


				//출금처리 보류건 추출
				if($state=='3') {
					if($ODATA['state']=='1') {		//** 출금신청중일에 한해서 취소 처리 가능하도록..
						$sql = "SELECT * FROM g5_withdrawal WHERE idx='$idx' AND state='3'";
						if($row = sql_fetch($sql)) {

							$mb_id = $row['mb_id'];
							$point = (int)$row['req_price'];
							$content = "예치금 지급";

							//예치금 출금
							insert_point($mb_id, $point, $content, $rel_table='', $rel_id='', $rel_action='', $expire=0);
						}
					}
				}

				alert("수정되었습니다.","./withdrawal_form.php?$qstr");
			}
		}
		else {
			alert("변경된 데이터가 없습니다.","./withdrawal_form.php?$qstr");
		}
	}
	else {
		header("Location:./withdrawal_form.php?$qstr");
	}

}

?>