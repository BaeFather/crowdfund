<?
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

//while(list($key, $value) = each($_REQUEST)) { ${$key} = trim($value); }


if($k_no == '1') {  // 완납확인서
	$sql = "
		INSERT INTO
			cf_paper
		SET 
			k_idx='".$k_no."', 
			creditor_no='".$creditor_no."', 
			loan_name='".$loan_name."', 
			p_category='".$category."', 
			reg_date='".$reg_date."', 
			writer='".$member['mb_name']."'
	";
	$res = sql_query($sql);

	if($res) {
		$last_id = sql_insert_id();

		if($last_id) {
			$sql = "
				INSERT INTO
					cf_paper_t1
				SET 
					p_no='".$last_id."', 
					k_no='".$k_no."', 
					loan_name='".$loan_name."', 
					loan_birth='".$loan_birth."', 
					loan_addr='".$loan_addr."', 
					loan_f_no='".$loan_f_no."', 
					reg_date='".$reg_date."', 
					writer='".$member['mb_name']."'
			";
			$res = sql_query($sql);

			if($res) {
				$last_id = sql_insert_id();
				
				for($i=0; $i<count($recruit_amount); $i++) {
					$sql = "
						INSERT INTO
							cf_paper_t1_detail
						SET 
							type_idx='".$last_id."', 
							p_idx='".$prdidx[$i]."', 
							loan_amount='".replace_integer($recruit_amount[$i])."', 
							loan_sdate='".$loan_start_date[$i]."', 
							loan_edate='".$loan_end_date[$i]."', 
							bank_name='".$bank_name[$i]."', 
							bank_acc='".$bank_acc[$i]."'
					";
					$final_res = sql_query($sql);
				}

				if($final_res) {
					msg_replace("등록되었습니다.", "./confirm_list.php");
				} else {
					msg_replace("등록실패했습니다. 다시 시도해주세요.", "../../repayment/invest_status_list.php");
				}
			}
		}
	}

} else if($k_no == '2') {  // 금융거래확인서


	$sql = "
		INSERT INTO
			cf_paper
		SET 
			k_idx='".$k_no."', 
			creditor_no='".$creditor_no."', 
			loan_name='".$loan_name."', 
			p_category='".$category."', 
			reg_date='".$reg_date."', 
			writer='".$member['mb_name']."'
	";
	$res = sql_query($sql);
	
	if($res) {
		$last_id = sql_insert_id();

		$sql = "
			INSERT INTO
				cf_paper_t2
			SET
				p_no='".$last_id."',
				k_no='".$k_no."',
				loan_name='".$loan_name."',
				loan_birth='".$loan_birth."', 
				loan_addr='".$loan_addr."',
				loan_f_no='".$loan_f_no."',
				reg_date='".$reg_date."',
				writer='".$member['mb_name']."',
				dambo_kinds='".$dambo_kinds."',
				dambo_price='".replace_integer($dambo_price)."',
				dambo_date='".$dambo_date."',
				dambo_note='".$dambo_note."',
				is_overdue='".$is_overdue."'
		";
		$res = sql_query($sql);

		if($res) {
			$last_id = sql_insert_id();

			for($i=0; $i<count($loan_kinds); $i++) {
				$sql = "
					INSERT INTO
						cf_paper_t2_detail
					SET
						type_idx='".$last_id."',
						p_idx='".$prdidx[$i]."',
						loan_kinds='".$loan_kinds[$i]."',
						loan_sdate='".$loan_sdate[$i]."',
						loan_edate='".$loan_edate[$i]."',
						loan_price='".replace_integer($loan_price[$i])."',
						loan_remain='".replace_integer($loan_remain[$i])."',
						loan_note='".$loan_note[$i]."'
				";
				$final_res = sql_query($sql);
			}
			if($final_res) {
				msg_replace("등록되었습니다.", "./confirm_list.php");
			} else {
				msg_replace("등록실패했습니다. 다시 시도해주세요.", "../../repayment/invest_status_list.php");
			}
		}
	}


} else if($k_no == '3') {  // 이자납입내역서

	$sql = "
		INSERT INTO
			cf_paper
		SET 
			k_idx='".$k_no."', 
			creditor_no='".$creditor_no."', 
			loan_name='".$loan_name."', 
			p_category='".$category."', 
			reg_date='".$reg_date."', 
			writer='".$member['mb_name']."'
	";
	$res = sql_query($sql);
	
	if($res) {
		$last_id = sql_insert_id();

		$sql = "
			INSERT INTO
				cf_paper_t3
			SET
				p_no='".$last_id."',
				k_no='".$k_no."',
				loan_no='".$loan_no."',
				p_idx='".$p_idx."',
				loan_name='".$loan_name."',
				loan_addr='".$loan_addr."',
				loan_birth='".$loan_birth."', 
				loan_kinds='".$loan_kinds."',
				loan_sdate='".$loan_sdate."',
				loan_edate='".$loan_edate."',
				loan_price='".replace_integer($loan_price)."',
				loan_remain='".replace_integer($loan_remain)."',
				basic_date='".$basic_date."',
				reg_date='".$reg_date."',
				writer='".$member['mb_name']."',
				use_text='".$use_text."',
				is_overdue='".$is_overdue."',
				price_field1='".$price_field1."',
				price_field2='".$price_field2."'
		";
		$res = sql_query($sql);

		if($res) {
			$last_id = sql_insert_id();

			for($i=0; $i<count($ins_date); $i++) {
				
				$sql = "
					INSERT INTO
						cf_paper_t3_detail
					SET
						type_idx='".$last_id."',
						ins_date='".$ins_date[$i]."',
						ins_principal='".replace_integer($ins_principal[$i])."',
						ins_eja='".replace_integer($ins_eja[$i])."',
						field1_price='".replace_integer($field1_price[$i])."',
						field2_price='".replace_integer($field2_price[$i])."'
				";
				$final_res = sql_query($sql);
			}
			if($final_res) {
				msg_replace("등록되었습니다.", "./confirm_list.php");
			} else {
				msg_replace("등록실패했습니다. 다시 시도해주세요.", "../../repayment/invest_status_list.php");
			}
		}
	}


} else if($k_no == '4') {  // 이자내역서
	
	$sql = "
		INSERT INTO
			cf_paper
		SET 
			k_idx='".$k_no."', 
			creditor_no='".$creditor_no."', 
			loan_name='".$loan_name."', 
			p_category='".$category."', 
			reg_date='".$reg_date."', 
			writer='".$member['mb_name']."'
	";
	$res = sql_query($sql);

	if($res) {
		$last_id = sql_insert_id();

		$sql = "
			INSERT INTO
				cf_paper_t4
			SET
				p_no='".$last_id."',
				k_no='".$k_no."',
				loan_name='".$loan_name."',
				loan_addr='".$loan_addr."',
				loan_f_no='".$loan_f_no."',
				loan_kinds='".$loan_kinds."',
				loan_sdate='".$loan_sdate."',
				loan_edate='".$loan_edate."',
				loan_price='".replace_integer($loan_price)."',
				loan_eja_perc='".$loan_eja_perc."',
				reg_date='".$reg_date."',
				writer='".$member['mb_name']."'
		";
		$res = sql_query($sql);

		if($res) {
			$last_id = sql_insert_id();

			for($i=0; $i<count($price); $i++) {
				$sql = "
					INSERT INTO
						cf_paper_t4_detail
					SET
						type_idx='".$last_id."',
						p_idx='".$prdidx[$i]."',
						price='".replace_integer($price[$i])."',
						bank_name='".$bank_name[$i]."',
						bank_acc='".$bank_acc[$i]."',
						note='".$note[$i]."'
				";
				$final_res = sql_query($sql);
			}
			if($final_res) {
				msg_replace("등록되었습니다.", "./confirm_list.php");
			} else {
				msg_replace("등록실패했습니다. 다시 시도해주세요.", "../../repayment/invest_status_list.php");
			}
		}
	}

}


?>