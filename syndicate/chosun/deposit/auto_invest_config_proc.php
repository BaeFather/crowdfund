<?
include_once('./_common.php');

if($_SERVER["REQUEST_METHOD"]!="POST") { echo 'x'; }

//print_r($_POST);


$member_idx = $member['mb_no'];
if(!$is_member) { echo "ERROR-LOGIN"; }

while( list($k, $v) = each($_POST) ) { ${$k} = @trim($v); }

if($auto_invest_flag && $setup_amount) {

	$changed = false;

	$CFGS = sql_fetch("SELECT * FROM cf_auto_invest_config_user WHERE member_idx='$member_idx' AND ai_grp_idx='$ai_grp_idx' ORDER BY idx LIMIT 1");

	if($CFGS['idx']) {
		if($CFGS['setup_amount'] <> $setup_amount) {

			/////////////////////////////////////////////////
			// 투자예약 데이터 순서 변경을 위한 설정 재등록
			/////////////////////////////////////////////////
			$edate = date('Y-m-d H:i:s');

			sql_query("UPDATE cf_auto_invest_config_user SET edate='".$edate."' WHERE idx='".$CFGS['idx']."'");																// 로그기록을 위한 수정일 등록
			sql_query("INSERT INTO cf_auto_invest_config_user_log SELECT * FROM cf_auto_invest_config_user WHERE idx='".$CFGS['idx']."'");		// 로그기록
			sql_query("DELETE FROM cf_auto_invest_config_user WHERE idx='".$CFGS['idx']."'");

			$lsql = "
				INSERT INTO
					cf_auto_invest_config_user
				SET
					ai_grp_idx='".$CFGS['ai_grp_idx']."',
					member_idx='".$CFGS['member_idx']."',
					setup_amount='".$setup_amount."',
					invest_warning_agree='".$CFGS['invest_warning_agree']."',
					rdate='".$CFGS['rdate']."',
					edate='".$edate."'";
			$lres = sql_query($lsql);
			echo ($lres) ? "INSERT_OK" : "INSERT_FAIL";

			$changed = true;

		}
	}
	else {
		$lsql = "
			INSERT INTO
				cf_auto_invest_config_user
			SET
				ai_grp_idx='".$ai_grp_idx."',
				member_idx='".$member['mb_no']."',
				setup_amount='".$setup_amount."',
				invest_warning_agree='1',
				rdate=NOW()";
		$lres = sql_query($lsql);
		echo ($lres) ? "INSERT_OK" : "INSERT_FAIL";

		$changed = true;

	}


	// 상환금환급방식 강제 변경 (예치금충전 방식으로)
	if($receive_method_change) {
		if( $member['receive_method']=='1' && ($member['bank_code'] && $member['bank_private_name'] && $member['account_num']) && ($member['va_bank_code2'] && $member['virtual_account2'] && $member['va_private_name2']) ) {
			sql_query("UPDATE g5_member SET receive_method='2' WHERE mb_no='".$member['mb_no']."'");
		}
	}


	if(!$changed) echo "UNCHANGED";

}
else {

	$res = sql_query("DELETE FROM cf_auto_invest_config_user WHERE idx='".$setup_idx."'");
	echo (sql_affected_rows()) ? "DROP_OK" : "DROP_FAIL";

}

exit;

?>