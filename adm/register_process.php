<?php
$sub_menu = "600000";
include_once('./_common.php');

//print_rr($_POST); exit;

//if ($w == 'u') check_demo();

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

//check_admin_token();

switch ($_REQUEST['action']) {

	///////////////////////////////////////////////////////////////////////////////
	// 상품이미지 업로드
	///////////////////////////////////////////////////////////////////////////////
	case 'product_image_upload' :
		$image = '';
		$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
		if (is_uploaded_file($_FILES['image_upload']['tmp_name'])) {
			@mkdir(G5_DATA_PATH.'/product', G5_DIR_PERMISSION);
			@chmod(G5_DATA_PATH.'/product', G5_DIR_PERMISSION);

			shuffle($chars_array);
			$shuffle = $image = substr(implode('', $chars_array), 0, 10);
			$dest_path = G5_DATA_PATH.'/product/'.$shuffle;

			move_uploaded_file($_FILES['image_upload']['tmp_name'], $dest_path);
			chmod($dest_path, G5_FILE_PERMISSION);
		}

		echo $image;
	break;


	///////////////////////////////////////////////////////////////////////////////
	// 상품이미지 삭제
	///////////////////////////////////////////////////////////////////////////////
	case 'product_image_delete' :

		@unlink(G5_DATA_PATH.'/product/'.$_POST['file']);

	break;


	///////////////////////////////////////////////////////////////////////////////
	// 원리금 지급처리
	///////////////////////////////////////////////////////////////////////////////
	case 'loan_interest_give' :
		if ($_POST['give_state'] == 'Y') {
			// 중복입력방지
			$_sql = "
				SELECT
					COUNT(idx) AS cnt_idx
				FROM
					cf_product_give
				WHERE
					date = '".$_POST['date']."'
					AND invest_amount = '".$_POST['invest_amount']."'
					AND invest_idx = '".$_POST['invest_idx']."'
					AND product_idx = '".$_POST['product_idx']."'
					AND turn = '".$_POST['repay_turn']."'
					AND is_creditor = '".$_POST['is_creditor']."'
					AND receive_method = '".$_POST['receive_method']."'
					AND bank_name = '".$_POST['bank_name']."'
					AND bank_private_name = '".$_POST['bank_private_name']."'
					AND account_num = '".$_POST['account_num']."'";
			$r = sql_fetch($_sql);

			if(!$r['cnt_idx']) {
				// 수익금 입금로그 등록
				$sql = "INSERT INTO " .
				       "  cf_product_give " .
				       "SET " .
				       "  date = '".$_POST['date']."', " .
				       "  invest_amount = '".$_POST['invest_amount']."', " .
				       "  invest_idx = '".$_POST['invest_idx']."', " .
				       "  product_idx = '".$_POST['product_idx']."', " .
				       "  turn = '".$_POST['repay_turn']."', " .
				       "  is_creditor = '".$_POST['is_creditor']."', " .
				       "  receive_method = '".$_POST['receive_method']."', " .
				       "  bank_name = '".$_POST['bank_name']."', " .
				       "  bank_private_name = '".$_POST['bank_private_name']."', " .
				       "  account_num = '".$_POST['account_num']."', " .
				       "  banking_date = NOW()";
				echo $sql."\n";
				sql_query($sql);
			}
		}
		else if ($_POST['give_state'] == 'N') {
			$sql = "
				DELETE FROM
					cf_product_give
				WHERE
					date = '".$_POST['date']."'
					AND invest_idx = '".$_POST['invest_idx']."'
					AND product_idx = '".$_POST['product_idx']."'";
			sql_query($sql);
		}

	break;

	///////////////////////////////////////////////////////////////////////////////
	//
	///////////////////////////////////////////////////////////////////////////////
	case 'inquiry_update' :

		$sql = "
			UPDATE
				cf_inquiry
			SET
				receive_email     = '".$_POST['receive_email']."',
				sms_user_content  = '".$_POST['sms_user_content']."',
				sms_user_use      = '".$_POST['sms_user_use']."',
				sms_admin_content = '".$_POST['sms_admin_content']."',
				sms_admin_use     = '".$_POST['sms_admin_use']."',
				privacy_content   = '".$_POST['privacy_content']."'";

		sql_query($sql);
		alert('정상적으로 처리되었습니다.');

	break;


	///////////////////////////////////////////////////////////////////////////////
	// 메인 표기용 누적 투자정보 요약 업데이트
	///////////////////////////////////////////////////////////////////////////////
	case 'invest_update' :

		$min_invest_limit = ($_POST['min_invest_limit']) ? $_POST['min_invest_limit'] : '0';
		$max_invest_limit = ($_POST['max_invest_limit']) ? $_POST['max_invest_limit'] : '0';

		$sql = "
			UPDATE
				cf_invest
			SET
				min_invest_limit     = '".$min_invest_limit."',
				min_invest_nolimit   = '".$_POST['min_invest_nolimit']."',
				max_invest_limit     = '".$max_invest_limit."',
				max_invest_nolimit   = '".$_POST['max_invest_nolimit']."',
				average_return       = '".$_POST['average_return']."',
				total_invest         = '".$_POST['total_invest']."',
				invest_success_count = '".$_POST['invest_success_count']."',
				total_repay          = '".$_POST['total_repay']."',
				bankruptcy           = '".$_POST['bankruptcy']."',
				overdue_perc         = '".$_POST['overdue_perc']."',
				invest_ing_amount    = '".$_POST['invest_ing_amount']."',
				standard_date        = '".$_POST['standard_date']."',
				display              = '".$_POST['display']."'";

		sql_query($sql);
		alert('정상적으로 처리되었습니다.');

	break;


	/////////////////////////////////////////////////////////
	// 다중차수 동일상환계좌 상품의 참조번호 셋팅
	/////////////////////////////////////////////////////////
	case "setRepayTarget" :

		$sql = "
			UPDATE
				KSNET_VR_ACCOUNT
			SET
				REF_NO='".$product_idx."'
			WHERE 1
				AND USE_FLAG='Y'
				AND VR_ACCT_NO='".$vacct."'";
		if(sql_query($sql)) {
			$RESULT_ARR = array("result" => "SUCCESS", "message" => "");
			echo json_encode($RESULT_ARR);
		}

	break;

}

?>