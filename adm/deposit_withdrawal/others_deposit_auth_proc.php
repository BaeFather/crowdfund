<?

include_once('./_common.php');
include_once(G5_LIB_PATH . "/sms.lib.php");

if(!$is_admin) alert('관리자만 접근 가능합니다.');

while(list($k, $v)=each($_POST)) { ${$k} = trim($v); }

//print_r($_POST); exit;

if($action=='deposit_trans') {

	$data = preg_replace("/chk\[\]=/", "", urldecode($data));
	$LIST = explode('&', $data);

	$list_count = count($LIST);
	if(!$list_count) { echo "전송된 데이터가 없습니다."; exit; }


	$COUNT = array("succ" => 0, "already_succ" => 0, "fail" => 0);


	for($i=0; $i<$list_count; $i++) {

		list($fb_seq, $erp_trans_dt) = explode("^", $LIST[$i]);

		$sql = "
			SELECT
				A.FB_SEQ, A.ERP_TRANS_DT, A.TR_AMT, A.REMITTER_NM, manual_auth,
				B.mb_no, B.mb_id, B.mb_name, B.mb_hp
			FROM
				IB_FB_P2P_IP A
			INNER JOIN
				g5_member B  ON A.CUST_ID=B.mb_no
			WHERE (1)
				AND FB_SEQ = '".$fb_seq."'
				AND ERP_TRANS_DT = '".$erp_trans_dt."'";
		if( $ROW = sql_fetch($sql) ) {
			$ROW['mb_hp'] = masterDecrypt($ROW['mb_hp'], false);

			if($ROW['manual_auth']=='1') {

				$COUNT['already_succ']+=1;

			}
			else {

				//print_r($ROW);

				$sql2 = "
					UPDATE
						IB_FB_P2P_IP
					SET
						trans_to_point = 'OK',
						trans_date = '".date('Y-m-d H:i:s')."',
						manual_auth = '1',
						auth_admin  = '".$member['mb_id']."',
						auth_date  = '".date('Y-m-d H:i:s')."'
					WHERE
						FB_SEQ = '".$fb_seq."' AND ERP_TRANS_DT = '".$erp_trans_dt."'";
				// echo $sql2."\n\n";
				$res2 = sql_query($sql2);
				// echo $ROW['mb_id'] . "(".$ROW['mb_name'].") 회원 차명입금승인 ::: 입금자명: " . $ROW['REMITTER_NM'] . ", 입금액: " . number_format($ROW['TR_AMT']) . "원\n";

				if( insert_point($ROW['mb_id'], (int)$ROW['TR_AMT'], "예치금 충전 (TYPE B)", '@deposit', $member['mb_id'], $member['mb_id'].'-'.uniqid('')) ) {
					$COUNT['succ']+=1;
				}
				else {
					$COUNT['fail']+=1;
				}

			}

		}
		else {
			$COUNT['fail']+=1;
		}

	}

	echo "전환처리 결과 :\n" .
			"  요청건수 : " . $list_count . "\n" .
			"  정상 처리 : " . $COUNT['succ'] . "건\n" .
			"  처리 실패 : " . $COUNT['fail'] . "건\n" .
			"  이미 정상 처리된 건 : " . $COUNT['already_succ'] ."건";

}

else if($action=='allow_remitter') {

	$allow_remitter_name = sql_real_escape_string($allow_remitter_name);

	$row = sql_fetch("SELECT COUNT(mb_no) cnt FROM IB_auth_deposit_to_amount WHERE mb_no='".$member_idx."' AND allow_remitter_name = '".$allow_remitter_name."'");

	if($row['cnt']) {
		echo "DUPLICATE ORDER";
	}
	else {
		$sql = "
			INSERT INTO
				IB_auth_deposit_to_amount
			SET
				mb_no = '".$member_idx."',
				allow_remitter_name = '".$allow_remitter_name."',
				auth_admin = '".$_SESSION['ss_mb_id']."',
				rdate = NOW()";
		if( sql_query($sql) ) {
			echo "OK";
		}
		else {
			echo "SYSTEM ERROR";
		}
	}

}

sql_close();
exit;

?>