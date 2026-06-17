<?
include_once('_common.php');

if (!$member["mb_id"]){ $err['err_msg']="로그인후 이용해 주세요.";echo json_encode($err); exit; }

$member_idx = $member['mb_no'];

$data['cnt'] = count($auto_inv);

for ($i=0 ; $i<$data['cnt'] ; $i++) {
	$data['auto_set'][$i]['ai_grp_idx'] = $auto_inv[$i]['grp'];
	$auto_inv[$i]['amt'] = $auto_inv[$i]['amt']*10000;

	$ai_grp_idx = $auto_inv[$i];

	$pre_sql = "select * from cf_auto_invest_config_user where member_idx = '$member_idx' and ai_grp_idx='".$auto_inv[$i]['grp']."' ";
	$pre_res = sql_query($pre_sql);
	$pre_cnt = $pre_res->num_rows;
	$data['auto_set'][$i]['chk_res'] = $pre_cnt;
	$data['auto_set'][$i]['checked'] = $auto_inv[$i]['yn'];
	$data['auto_set'][$i]['amt'] = $auto_inv[$i]['amt'];

	$data['auto_set'][$i]['res_msg'] = "변동없음";

	if ($auto_inv[$i]['yn']=="Y" and $auto_inv[$i]['amt']) {

		if ($pre_cnt) {

			$pre_row = sql_fetch_array($pre_res);

			if ($auto_inv[$i]['amt']<>$pre_row['setup_amount']) {

				$data['auto_set'][$i]['res_msg'] = "수정";

				// 1. 수정일 업데이트
				$edate = date('Y-m-d H:i:s');
				$up_e_sql = "UPDATE cf_auto_invest_config_user SET edate='".$edate."' WHERE idx='".$pre_row['idx']."'";
				//sql_query($up_e_sql);

				// 2.로그 기록
				$log_sql = "INSERT INTO cf_auto_invest_config_user_log SELECT * FROM cf_auto_invest_config_user WHERE idx='".$pre_row['idx']."'";
				$data['auto_set'][$i]['sql'] = $log_sql;
				//sql_query($log_sql);		// 로그기록

				// 3. 기존자료 삭제
				$del_sql = "DELETE FROM cf_auto_invest_config_user WHERE idx='".$pre_row['idx']."'";
				//sql_query("DELETE FROM cf_auto_invest_config_user WHERE idx='".$CFGS['idx']."'");

				// 4. 데이타 입력
				$lsql = "
					INSERT INTO
						cf_auto_invest_config_user
					SET
						ai_grp_idx='".$pre_row['ai_grp_idx']."',
						member_idx='".$pre_row['member_idx']."',
						setup_amount='".$auto_inv[$i]['amt']."',
						invest_warning_agree='".$pre_row['invest_warning_agree']."',
						rdate='".$pre_row['rdate']."',
						edate='".$edate."'";
				//$lres = sql_query($lsql);


			}
		} else {
			$data['auto_set'][$i]['res_msg'] = "신규";
			$lsql = "
				INSERT INTO
					cf_auto_invest_config_user
				SET
					ai_grp_idx='".$auto_inv[$i]['grp']."',
					member_idx='".$member['mb_no']."',
					setup_amount='".$auto_inv[$i]['amt']."',
					invest_warning_agree='1',
					rdate=NOW()";
			$data['auto_set'][$i]['sql'] = $lsql;
			//$lres = sql_query($lsql);
		}

		// 상환금환급방식 강제 변경 (예치금충전 방식으로)
		if($receive_method_change) {
			if( $member['receive_method']=='1' && ($member['bank_code'] && $member['bank_private_name'] && $member['account_num']) && ($member['va_bank_code2'] && $member['virtual_account2'] && $member['va_private_name2']) ) {
				$rcv_sql = "UPDATE g5_member SET receive_method='2' WHERE mb_no='".$member['mb_no']."'";
				//sql_query($rcv_sql);
			}
		}

	} else {
		$data['auto_set'][$i]['res_msg'] = "삭제";

		$del_sql = "DELETE FROM cf_auto_invest_config_user WHERE member_idx = '$member_idx' and ai_grp_idx='".$auto_inv[$i]['grp']."' ";
		$data['auto_set'][$i]['sql'] = $del_sql;
		//$res = sql_query($del_sql);
	}

}

echo json_encode($data);
?>