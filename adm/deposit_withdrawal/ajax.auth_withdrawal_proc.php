<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

include_once("./_common.php");

while(list($k, $v)=each($_POST)) { ${$k} = trim($v); }

if(!$is_admin) { echo "ERROR-LOGIN"; exit; }

if($action=='auth_regist') {

	$mb = sql_fetch("SELECT account_num FROM g5_member WHERE mb_no = '".$mb_no."'");
	$mb['account_num'] = masterDecrypt($mb['account_num'], false);

	$AUTH_DATA = sql_fetch("SELECT mb_no, account_num, rdate FROM IB_auth_withdrawal WHERE mb_no = '".$mb_no."'");

	if($AUTH_DATA['mb_no'] && $AUTH_DATA['account_num']) {

		if($checkval=='Y') {

			if($mb['account_num']==$AUTH_DATA['account_num']) {
				echo "ALREADY_SET_VALUE"; exit;
			}
			else {

				$sql = "
					UPDATE
						IB_auth_withdrawal
					SET
						account_num = '".$account_num."',
						auth_admin  = '".$member['mb_id']."',
						rdate   = '".G5_TIME_YMDHIS."'
					WHERE
						mb_no = '".$mb_no."'";
				if(sql_query($sql)) {
					echo "UPDATE_SUCCESS"; exit;
				}

			}

		}
		else {

			$sql = "DELETE FROM IB_auth_withdrawal WHERE mb_no = '".$mb_no."'";
			if(sql_query($sql)) {
				echo "DELETE_SUCCESS"; exit;
			}

		}

	}
	else {

		if($checkval=='Y') {

			$sql = "
				INSERT INTO
					IB_auth_withdrawal
				SET
					mb_no       = '".$mb_no."',
					account_num = '".$mb['account_num']."',
					auth_admin  = '".$member['mb_id']."',
					rdate       = '".G5_TIME_YMDHIS."'";
			if(sql_query($sql)) {
				echo "INSERT_SUCCESS"; exit;
			}

		}
		else {
			echo "ALREADY_SET_VALUE"; exit;
		}

	}

}

else if($action=='delete') {

	//print_r(urldecode($_REQUEST['data']));

	$COUNT = array("succ" => 0, "fail" => 0);

	$data = preg_replace("/chk\[\]=/", "", urldecode($data));
	$LIST = explode('&', $data);

	$list_count = count($LIST);
	if(!$list_count) { echo "전송된 데이터가 없습니다."; exit; }


	$COUNT = array("succ" => 0, "already_succ" => 0, "fail" => 0);


	for($i=0; $i<$list_count; $i++) {

		list($mb_no, $account_num) = explode("^", $LIST[$i]);

		$sql = "DELETE FROM IB_auth_withdrawal WHERE mb_no='".$mb_no."' AND account_num='".$account_num."'";
		//echo $sql."\n";
		if( sql_query($sql, true) ) {
			$COUNT['succ']+=1;
		}
		else {
			$COUNT['fail']+=1;
		}

	}

	echo "삭제처리 결과 :\n" .
			"  요청건수 : " . $list_count . "\n" .
			"  정상 처리 : " . $COUNT['succ'] . "건\n" .
			"  처리 실패 : " . $COUNT['fail'] . "건";

}

?>