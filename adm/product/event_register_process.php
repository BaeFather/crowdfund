<?php
$sub_menu = "600200";
include_once('./_common.php');

//echo "<pre>"; print_r($_POST); echo "</pre>"; exit;

if ($w == 'u')
    check_demo();

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

//check_admin_token();

switch ($_REQUEST['action']) {

	////////////////////////////
	// 대출이자 수급완료 처리
	////////////////////////////
	case 'loan_interest_success' :
		$sql = "
			INSERT INTO
				cf_event_product_success
			SET
				loan_interest_state = 'Y',
				product_idx = '".$_POST['idx']."',
				date = '".$_POST['date']."',
				turn = '".$_POST['turn']."'
			ON duplicate key update loan_interest_state = 'Y'";
		sql_query($sql);
	break;

	////////////////////////////
	// 지급처리 완료
	////////////////////////////
	case 'invest_give_success' :
		$sql = "
			INSERT INTO
				cf_event_product_success
			SET
				invest_give_state = 'Y',
				product_idx = '".$_POST['idx']."',
				date = '".$_POST['date']."'
			ON duplicate key update invest_give_state = 'Y'";
		sql_query($sql);
	break;

	case 'product_image_upload' :
		$image = '';
		$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
		if (is_uploaded_file($_FILES['image_upload']['tmp_name'])) {
			@mkdir(G5_DATA_PATH.'/product_special', G5_DIR_PERMISSION);
			@chmod(G5_DATA_PATH.'/product_special', G5_DIR_PERMISSION);

			shuffle($chars_array);
			$shuffle = $image = substr(implode('', $chars_array), 0, 10);
			$dest_path = G5_DATA_PATH.'/product_special/'.$shuffle;

			move_uploaded_file($_FILES['image_upload']['tmp_name'], $dest_path);
			chmod($dest_path, G5_FILE_PERMISSION);
		}

		echo $image;
	break;

	case 'product_image_delete' :
		@unlink(G5_DATA_PATH.'/product_special/'.$_POST['file']);
	break;


	case 'calculate_state_update' :

		// $state : 진행현황(1:이자상환중|2:상품마감|3:투자금모집실패|4:부실|5:중도일시상환)

		if ($_POST['state'] == '1') {
			$sql = "SELECT * FROM cf_event_product WHERE idx = '".$_POST['idx']."'";
			$product = sql_fetch($sql);

			$start_date    = new DateTime($product['loan_start_date']);
			$end_date      = new DateTime(date('Y-m-d', strtotime($_POST['date'].' +'.$product['invest_period'].' month')));
			$loan_end_date = $end_date->format('Y-m-d');
			$total_date    = date_diff($start_date, $end_date);
			$total_day     = $total_date->days - 1;

			$sql = "UPDATE cf_event_product SET state = '".$_POST['state']."' WHERE idx = '".$_POST['idx']."'";

		}
		else if ($_POST['state'] == '3') {

			$sql = "
				SELECT
					a.*, b.mb_id, c.title
				FROM
					cf_event_product_invest a, g5_member b, cf_event_product c
				WHERE
					a.member_idx = b.mb_no
					AND a.product_idx = c.idx
					AND a.product_idx = '".$_POST['idx']."'";
			$result = sql_query($sql);

			while ($row=sql_fetch_array($result)) {
				insert_point($row['mb_id'], $row['amount'], $row['title'].' - 예치금 반환', '@return', $row['mb_id'], $member['mb_id'].'-'.uniqid(''), $config['cf_point_term']);

				$param = array();
				$param['apiGbn']    = 'pendingCancel';
				$param['mb_id']     = $row['mb_id'];
				$param['parentTid'] = $row['tr_no'];

				$result = payGateCurl($param);

			}

			$sql = "UPDATE cf_event_product SET state = '".$_POST['state']."' WHERE idx = '".$_POST['idx']."'";
		}
		else if ($_POST['state'] == '5') {
			$sql = "UPDATE cf_event_product SET state = '".$_POST['state']."' WHERE idx = '".$_POST['idx']."'";
			//$sql = "UPDATE cf_event_product SET state = '".$_POST['state']."', loan_end_date = '".$_POST['loan_end_date']."' WHERE idx = '".$_POST['idx']."'";
		}
		else {
			$sql = "UPDATE cf_event_product SET state = '".$_POST['state']."' WHERE idx = '".$_POST['idx']."'";
		}

		sql_query($sql);
		alert('정상적으로 처리되었습니다.', G5_ADMIN_URL.'/event_product_calculate.php?idx='.$_POST['idx']);

	break;


	////////////////////////////
	// 지급처리
	////////////////////////////
	case 'loan_interest_give' :
		if ($_POST['give_state'] == 'Y') {
			$sql = "
				INSERT INTO
					cf_event_product_give
				SET
					date = '".$_POST['date']."',
					invest_amount = '".$_POST['invest_amount']."',
					invest_idx = '".$_POST['invest_idx']."',
					product_idx = '".$_POST['product_idx']."'";
		}
		else if ($_POST['give_state'] == 'N') {
			$sql = "
				DELETE FROM
					cf_event_product_give
				WHERE
					date = '".$_POST['date']."'
					AND invest_idx = '".$_POST['invest_idx']."'
					AND product_idx = '".$_POST['product_idx']."'";
		}

		sql_query($sql);
	break;

	case 'product_insert' :
	case 'product_update' :
		$open_datetime  = $_POST['open_date'].' '.sprintf("%02d", $_POST['open_hour']).':'.sprintf("%02d", $_POST['open_minute']).':'.sprintf("%02d", $_POST['open_second']);
		$start_datetime = $_POST['start_date'].' '.sprintf("%02d", $_POST['start_hour']).':'.sprintf("%02d", $_POST['start_minute']).':'.sprintf("%02d", $_POST['start_second']);
		$end_datetime   = $_POST['end_date'].' '.sprintf("%02d", $_POST['end_hour']).':'.sprintf("%02d", $_POST['end_minute']).':'.sprintf("%02d", $_POST['end_second']);


		$sql_common = "
			category = '".$_POST['category']."',
			title = '".$_POST['title']."',
			invest_amount = '".$_POST['invest_amount']."',
			invest_profit = '".$_POST['invest_profit']."',
			total_return_amount = '".$_POST['total_return_amount']."',
			invest_return = '".$_POST['invest_return']."',
			withhold_tax_rate = '".$_POST['withhold_tax_rate']."',
			invest_usefee = '".$_POST['invest_usefee']."',
			invest_period = '".$_POST['invest_period']."',
			recruit_period_start = '".$_POST['recruit_period_start']."',
			recruit_period_end = '".$_POST['recruit_period_end']."',
			recruit_amount = '".$_POST['recruit_amount']."',
			repay_type = '".$_POST['repay_type']."',
			repay_day = '".$_POST['repay_day']."',
			detail_image = '".@join('|', $_POST['detail_image'])."',
			comment = '".$_POST['comment']."',
			invest_summary = '".$_POST['invest_summary']."',
			open_datetime = '".$open_datetime."',
			open_date = '".$_POST['open_date']."',
			open_hour = '".$_POST['open_hour']."',
			open_minute = '".$_POST['open_minute']."',
			open_second = '".$_POST['open_second']."',
			start_datetime = '".$start_datetime."',
			start_date = '".$_POST['start_date']."',
			start_hour = '".$_POST['start_hour']."',
			start_minute = '".$_POST['start_minute']."',
			start_second = '".$_POST['start_second']."',
			end_datetime = '".$end_datetime."',
			end_date = '".$_POST['end_date']."',
			end_hour = '".$_POST['end_hour']."',
			end_minute = '".$_POST['end_minute']."',
			end_second = '".$_POST['end_second']."',
			display = '".$_POST['display']."',
			evaluate_score1 = '".$_POST['evaluate_score1']."',
			evaluate_score2 = '".$_POST['evaluate_score2']."',
			evaluate_score3 = '".$_POST['evaluate_score3']."',
			evaluate_score4 = '".$_POST['evaluate_score4']."',
			judge = '".$_POST['judge']."',
			screening = '".$_POST['screening']."',
			lat = '".$_POST['lat']."',
			lng = '".$_POST['lng']."',
			faq = '".$_POST['faq']."'";

		$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
		if (is_uploaded_file($_FILES['main_image']['tmp_name'])) {
			@mkdir(G5_DATA_PATH.'/product_special', G5_DIR_PERMISSION);
			@chmod(G5_DATA_PATH.'/product_special', G5_DIR_PERMISSION);

			shuffle($chars_array);
			$shuffle = substr(implode('', $chars_array), 0, 10);
			$dest_path = G5_DATA_PATH.'/product_special/'.$shuffle;

			move_uploaded_file($_FILES['main_image']['tmp_name'], $dest_path);
			chmod($dest_path, G5_FILE_PERMISSION);
			$sql_common .= ", main_image = '".$shuffle."'";
		}

		if (is_uploaded_file($_FILES['main_image_m']['tmp_name'])) {
			@mkdir(G5_DATA_PATH.'/product_special', G5_DIR_PERMISSION);
			@chmod(G5_DATA_PATH.'/product_special', G5_DIR_PERMISSION);

			shuffle($chars_array);
			$shuffle = substr(implode('', $chars_array), 0, 10);
			$dest_path = G5_DATA_PATH.'/product_special/'.$shuffle;

			move_uploaded_file($_FILES['main_image_m']['tmp_name'], $dest_path);
			chmod($dest_path, G5_FILE_PERMISSION);
			$sql_common .= ", main_image_m = '".$shuffle."'";
		}

		if ($_POST['action'] == 'product_insert') {
			$sql_common .= ", insert_date = '".date('Y-m-d H:i:s')."'";
			$sql = "INSERT INTO cf_event_product set {$sql_common}";

			sql_query($sql);
			$idx = sql_insert_id();

		}
		else if ($_POST['action'] == 'product_update') {
			$idx = $_POST['idx'];
			$sql = "UPDATE cf_event_product SET {$sql_common} WHERE idx = '".$idx."'";
			sql_query($sql);
		}

		//echo $sql;

		echo "<script>alert('정상적으로 처리되었습니다.');location.replace('event_product_form.php?idx=".$idx."');</script>";
	break;

	case 'product_delete' :
		$sql = "INSERT INTO cf_event_product_delete SELECT * FROM cf_event_product WHERE idx = '".$_GET['idx']."'";
		sql_query($sql);

		$sql = "DELETE FROM cf_event_product WHERE idx = '".$_GET['idx']."'";
		sql_query($sql);

		alert('정상적으로 처리되었습니다.');
	break;

/*
	case 'inquiry_update' :
		$sql = "
			UPDATE
				cf_inquiry
			SET
				receive_email = '".$_POST['receive_email']."',
				sms_user_content = '".$_POST['sms_user_content']."',
				sms_user_use = '".$_POST['sms_user_use']."',
				sms_admin_content = '".$_POST['sms_admin_content']."',
				sms_admin_use = '".$_POST['sms_admin_use']."',
				privacy_content = '".$_POST['privacy_content']."'";
		sql_query($sql);
		alert('정상적으로 처리되었습니다.');
	break;
*/

	case 'invest_update' :
		$sql = "
			UPDATE
				cf_event_invest
			SET
				min_invest_limit = '".$_POST['min_invest_limit']."',
				min_invest_nolimit = '".$_POST['min_invest_nolimit']."',
				max_invest_limit = '".$_POST['max_invest_limit']."',
				max_invest_nolimit = '".$_POST['max_invest_nolimit']."',
				average_return = '".$_POST['average_return']."',
				total_invest = '".$_POST['total_invest']."',
				total_repay = '".$_POST['total_repay']."',
				bankruptcy = '".$_POST['bankruptcy']."',
				display = '".$_POST['display']."'";
		sql_query($sql);
		alert('정상적으로 처리되었습니다.');
	break;

	case 'balance_update' :
		$sql_search = " WHERE (1) ";
		switch ($_POST['member_select']) {
			case '2' :
				if ($_POST['total_count'] == 0) {
					alert('검색된 회원이 없습니다.');
				}

				if ($_GET['keyword']) {
					$sql_search .= " and ( ";
					switch ($_POST['field']) {
						case 'mb_name' :
							$sql_search .= " ({$_POST['field']} = '%{$_POST['keyword']}%') ";
						break;
						case 'mb_tel' :
						case 'mb_hp' :
							$sql_search .= " ({$_POST['field']} LIKE '%{$_POST['keyword']}') ";
						break;
						default :
							$sql_search .= " ({$_POST['field']} LIKE '{$_POST['keyword']}%') ";
						break;
					}

					$sql_search .= " ) ";
				}

				$search_field = array('mb_1', 'mb_mailing', 'mb_sms');
				foreach ($_POST as $key => $val) {
					if (in_array($key, $search_field) && $val) {
						$sql_search .= " AND {$key} = '{$val}' ";
					}
				}

				if ($_POST['mb_datetime_start'] && $_POST['mb_datetime_end']) {
					$sql_search .= " AND date_format(mb_datetime, '%Y-%m-%d') >= '".$_POST['mb_datetime_start']."' AND date_format(mb_datetime, '%Y-%m-%d') <= '".$_POST['mb_datetime_end']."' ";
				}

				if ($_POST['mb_point_start'] && $_POST['mb_point_end']) {
					$sql_search .= " AND mb_point >= '".$_POST['mb_point_start']."' AND mb_point <= '".$_POST['mb_point_end']."' ";
				}
			break;
			case '3' :
				if (count($_POST['chk']) == 0) {
					break;
				}

				$chk = array();
				foreach ($_POST['chk'] as $key => $val) {
					array_push($chk, $val);
				}

				$sql_search .= " AND (mb_no IN(".join(',', $chk).")) ";
			break;
		}

		if ($_POST['balance']) {
			$sql = "SELECT * FROM g5_member {$sql_search}";
			$result = sql_query($sql);
			while ($row=sql_fetch_array($result)) {
				if ($_POST['balance_select'] == '1') {
					insert_point($row['mb_id'], $_POST['balance'], "예치금 지급");
				} else if ($_POST['balance_select'] == '2') {
					insert_point($row['mb_id'], $_POST['balance'] * (-1), "예치금 차감");
				}
			}
		}

		alert('정상적으로 처리되었습니다.', G5_ADMIN_URL.'/balance_list.php');

	break;

}
?>