<?


exit;				// 2021-02-08 자동투자등록 처리파일 비활성화 시작


include_once('_common.php');
include_once('../lib/sms.lib.php');

if (!$member["mb_id"]){ $err['err_msg']="로그인후 이용해 주세요.";echo json_encode($err); exit; }

$member_idx = $member['mb_no'];


$edate = date('Y-m-d H:i:s');

$data['input_data'] = $_REQUEST;
$data['cnt_modi'] = 0;

for ($i=0 ; $i<count($grp_idx) ; $i++) {

	$auto_money[$i] = $auto_money[$i]*10000;
	$auto_money2[$i] = $auto_money2[$i]*10000;

	if ($auto_money[$i] > $auto_money2[$i]){ $err['err_msg']="시작금액은 종료금액보다 클수 없습니다.";echo json_encode($err); exit; }

	$pre_sql = "SELECT * FROM cf_auto_invest_config_user WHERE member_idx='".$member['mb_no']."' AND ai_grp_idx='".$grp_idx[$i]."'";

	$data[$i] = $pre_sql;
	$pre_res = sql_query($pre_sql);
	$pre_cnt = sql_num_rows($pre_res);

	$data['auto_set'][$i]['grp_idx'] = $grp_idx[$i];
	$data['auto_set'][$i]['res_msg'] = "변동없음";

	if ($chk_item[$i]=="Y") {

		if ($pre_cnt) {

			$pre_row = sql_fetch_array($pre_res);

			if ($auto_money[$i]<>$pre_row['setup_amount'] || $auto_money2[$i]<>$pre_row['setup_amount2']) {

				$data['auto_set'][$i]['res_msg'] = "수정";

				// 1.수정일 업데이트, 2.로그 기록
				$log_yn = auto_inv_log($pre_row['idx'], $edate);

				// 3. 기존자료 변경
				$lsql = "
					UPDATE
						cf_auto_invest_config_user
					SET
						setup_amount='".$auto_money[$i]."',
						setup_amount2='".$auto_money2[$i]."'
					WHERE 1
						AND member_idx = '$member[mb_no]'
						AND ai_grp_idx='$grp_idx[$i]'";

				/*
				// 3. 기존자료 삭제
				$del_sql = "DELETE FROM cf_auto_invest_config_user WHERE idx='$pre_row[idx]'";
				sql_query($del_sql);

				// 4. 데이타 입력
				$lsql = "
					INSERT INTO
						cf_auto_invest_config_user
					SET
						ai_grp_idx='$grp_idx[$i]',
						member_idx='$member[mb_no]',
						setup_amount='$auto_money',
						invest_warning_agree='$yn_agree',
						rdate='".$pre_row['rdate']."',
						edate='".$edate."'";
				*/
				$lres = sql_query($lsql);

				if ($lres) $data['cnt_modi'] += 1;

			}


		}
		else {

			$data['auto_set'][$i]['res_msg'] = "신규";
			$lsql = "
				INSERT INTO
					cf_auto_invest_config_user
				SET
					ai_grp_idx = '".$grp_idx[$i]."',
					member_idx = '".$member['mb_no']."',
					setup_amount = '".$auto_money[$i]."',
					setup_amount2 = '".$auto_money2[$i]."',
					invest_warning_agree = '".$yn_agree."',
					rdate = '".$edate."'";
			$data['auto_set'][$i]['sql'] = $lsql;
			$lres = sql_query($lsql);
			if($lres) $data['cnt_modi'] += 1;

		}

		// 상환금환급방식 강제 변경 (예치금충전 방식으로)
		if($receive_method_change or 2>1) {
			if( $member['receive_method']=='1' && ($member['bank_code'] && $member['bank_private_name'] && $member['account_num']) && ($member['va_bank_code2'] && $member['virtual_account2'] && $member['va_private_name2']) ) {
				$rcv_sql = "UPDATE g5_member SET receive_method='2' WHERE mb_no='".$member['mb_no']."'";
				sql_query($rcv_sql);
			}
		}

	} else {
		$data['auto_set'][$i]['res_msg'] = "삭제";

		$pre_row = sql_fetch_array($pre_res);

		$log_yn = auto_inv_log($pre_row[idx], $edate);

		$del_sql = "DELETE FROM cf_auto_invest_config_user WHERE member_idx = '$member_idx' and ai_grp_idx='$grp_idx[$i]' ";
		$data['auto_set'][$i]['sql'] = $del_sql;
		$lres = sql_query($del_sql);
		if ($lres) $data['cnt_modi'] += 1;
	}

}
echo json_encode($data);

/*카카오톡 알림톡 추가*/
$tcode = "hello012";
//$tcode = "hello012";
$KaKao_Message_Send = new KaKao_Message_Send();
$KaKao_Message_Send->MEMBER = $member;	// common.lib member 환경변수
$KaKao_Message_Send->kakao_insert($tcode);
/*카카오톡 알림톡 추가*/
?>
<?
function auto_inv_log($idx, $edate) {
	// 1. 수정일 업데이트
	$up_e_sql = "UPDATE cf_auto_invest_config_user SET edate='".$edate."' WHERE idx='".$idx."'";
	sql_query($up_e_sql);

	// 2.로그 기록
	$log_sql = "INSERT INTO cf_auto_invest_config_user_log SELECT * FROM cf_auto_invest_config_user WHERE idx='".$idx."'";
	$res = sql_query($log_sql);		// 로그기록

	return $res;
}
?>
