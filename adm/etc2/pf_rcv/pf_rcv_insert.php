<?
include_once('./_common.php');

while(list($key, $value) = each($_REQUEST)) { ${$key} = trim($value); }


// 약정금액, 대출금액, 미집행금액 콤마 제거 후 저장 
$contract_amount = replace_integer($contract_amount);
$loan_amount = replace_integer($loan_amount);
$yet_amount = replace_integer($yet_amount);


############## 파일 처리 ##############

// 기존 저장된 데이터 select 후 원본 파일, 임시 파일명의 필드 값 변수 지정
$sql = "SELECT * FROM cf_pf_accounts_rcv WHERE idx='$idx'";
$res = sql_query($sql);
$old_data = sql_fetch_array($res);
$ori_file_names = $old_data["origin_file"];
$tmp_file_names = $old_data["temp_file"];

// 파일 업로드
if($_FILES['origin_file']['name'][0]) {

	// 파일명 초기화
	$ori_file_names = "";  
	$tmp_file_names = "";


	// hcseq 값이 있으면 원본 파일명, 임시 파일명의 값이 select 된 필드 값
	if($idx) {
		$row = sql_fetch("SELECT origin_file, temp_file FROM cf_pf_accounts_rcv WHERE idx = '".$idx."'");

		$ori_file_names = $row['origin_file'];
		$tmp_file_names = $row['temp_file'];

	}

	// 파일 업로드 관련 변수 지정
	$uploads_dir  = "./uploads/";
	$allowed_ext = array('jpg','jpeg','png','pdf','doc','docx','xlsx','xls','hwp','zip','JPG','JPEG','PNG','PDF','DOC','DOCX','XLSX','XLS','HWP','ZIP');

	$error = $_FILES['origin_file']['error'];
	$name = $_FILES['origin_file']['name'];

	$sw = true;


	// 원본 파일명을 가진 파일의 갯수만큼 루프
	for($i=0; $i<count($_FILES['origin_file']['name']); $i++) {

		$ext = substr($name[$i], strrpos($name[$i],'.') + 1);  // 확장자만 담는 변수 ex) jpg
		$ext = strtoupper($ext);
		//echo $ext; die();
		$uploadFile = $uploads_dir.basename($_FILES['origin_file']['tmp_name'][$i]);  // uploads/임시파일명의 basename 


		// 확장자 체크
		if(in_array($ext, $allowed_ext)) {  

			// 서버로 전송된 파일을 저장할 때 - move_uploaded_file(파일, 옮겨질 곳)
			if(move_uploaded_file($_FILES['origin_file']['tmp_name'][$i], $uploadFile)) {
				
				$ori_file_names .= $_FILES['origin_file']['name'][$i]. ";";
				$tmp_file_names .= basename($_FILES['origin_file']['tmp_name'][$i]). ";";
			
			} else {
				$sw = false;
			}

		
		} else {
			echo "<script>alert('등록할 수 없는 확장자입니다.\\n\\n등록 가능 확장자 : jpg, jpeg, png, pdf, doc, docx, xlsx, xls, hwp, zip'); history.back();</script>"; 
			$sw = false;
		}
	}

	if (!$sw) {
		echo "<script>alert('파일 업로드 실패했습니다. 다시 시도해주세요.'); history.back();</script>"; 
		EXIT;
	}

}
############## 파일 처리 end ##############

// 연대보증인 정보 암호화
$guarantor_uniqno1 = masterEncrypt($guarantor_uniqno1);
$guarantor_phone1 = masterEncrypt($guarantor_phone1);
$guarantor_uniqno2 = masterEncrypt($guarantor_uniqno2);
$guarantor_phone2 = masterEncrypt($guarantor_phone2);
$guarantor_uniqno3 = masterEncrypt($guarantor_uniqno3);
$guarantor_phone3 = masterEncrypt($guarantor_phone3);
$guarantor_uniqno4 = masterEncrypt($guarantor_uniqno4);
$guarantor_phone4 = masterEncrypt($guarantor_phone4);
$guarantor_uniqno5 = masterEncrypt($guarantor_uniqno5);
$guarantor_phone5 = masterEncrypt($guarantor_phone5);


// 등록
 if($action=='insert') {

	// 대출금액
	$TOTAL_AMT = sql_fetch("
		SELECT SUM(recruit_amount) AS total_amount FROM cf_product WHERE gr_idx='".$prdidx."' AND recruit_amount > 10000
	");
	$loan_amount = $TOTAL_AMT['total_amount'];

	// main
	$sql = "
		INSERT INTO
			cf_pf_accounts_rcv
		SET
			product_idx='".$prdidx."',
			group_idx='".$prdidx."',
			note='".$rcv_note."',
			period='".$rcv_period."',
			loan_end_date='".$loan_end_date."',
			contract_amount='".$contract_amount."',
			loan_amount='".$loan_amount."',
			yet_amount='".$yet_amount."',
			developer='".$developer."',
			constructor='".$constructor."',
			trust='".$trust."',
			company_addr='".$company_addr."',
			content_text='".$content_text."',
			guarantor_name1='".$guarantor_name1."',
			guarantor_uniqno1='".$guarantor_uniqno1."',
			guarantor_phone1='".$guarantor_phone1."',
			guarantor_name2='".$guarantor_name2."',
			guarantor_uniqno2='".$guarantor_uniqno2."',
			guarantor_phone2='".$guarantor_phone2."',
			guarantor_name3='".$guarantor_name3."',
			guarantor_uniqno3='".$guarantor_uniqno3."',
			guarantor_phone3='".$guarantor_phone3."',
			guarantor_name4='".$guarantor_name4."',
			guarantor_uniqno4='".$guarantor_uniqno4."',
			guarantor_phone4='".$guarantor_phone4."',
			guarantor_name5='".$guarantor_name5."',
			guarantor_uniqno5='".$guarantor_uniqno5."',
			guarantor_phone5='".$guarantor_phone5."',
			origin_file='".$ori_file_names."',
			temp_file='".$tmp_file_names."',
			writer_id='".$member['mb_id']."',
			reg_date=NOW()
	";

	$result = sql_query($sql);
	
	if($result) {
		$insert_idx = sql_insert_id();
		msg_replace("등록되었습니다.", "/adm/etc2/pf_rcv/pf_rcv_form.php?idx=".$insert_idx);
	} else {
		msg_replace("등록 실패했습니다.", "/adm/etc2/pf_rcv/pf_rcv_list.php");
	}
	
} 

// 수정
else if($action=='update') {
	$sql = "
		UPDATE
			cf_pf_accounts_rcv
		SET
			product_idx='".$prdidx."',
			group_idx='".$prdidx."',
			note='".$rcv_note."',
			period='".$rcv_period."',
			loan_end_date='".$loan_end_date."',
			contract_amount='".$contract_amount."',
			loan_amount='".$loan_amount."',
			yet_amount='".$yet_amount."',
			developer='".$developer."',
			constructor='".$constructor."',
			trust='".$trust."',
			company_addr='".$company_addr."',
			content_text='".$content_text."',
			guarantor_name1='".$guarantor_name1."',
			guarantor_uniqno1='".$guarantor_uniqno1."',
			guarantor_phone1='".$guarantor_phone1."',
			guarantor_name2='".$guarantor_name2."',
			guarantor_uniqno2='".$guarantor_uniqno2."',
			guarantor_phone2='".$guarantor_phone2."',
			guarantor_name3='".$guarantor_name3."',
			guarantor_uniqno3='".$guarantor_uniqno3."',
			guarantor_phone3='".$guarantor_phone3."',
			guarantor_name4='".$guarantor_name4."',
			guarantor_uniqno4='".$guarantor_uniqno4."',
			guarantor_phone4='".$guarantor_phone4."',
			guarantor_name5='".$guarantor_name5."',
			guarantor_uniqno5='".$guarantor_uniqno5."',
			guarantor_phone5='".$guarantor_phone5."',
			origin_file='".$ori_file_names."',
			temp_file='".$tmp_file_names."'
		WHERE
			idx = '$idx'
	";
	
	sql_query($sql);
	msg_replace("수정되었습니다.", "/adm/etc2/pf_rcv/pf_rcv_form.php?idx=".$idx);
	
}
// 삭제
else if($action=='delete') {
	$sql = "
		DELETE 
		FROM 
			cf_pf_accounts_rcv
		WHERE
			idx='$idx'
	";

	sql_query($sql);
	msg_replace("삭제되었습니다.", "/adm/etc2/pf_rcv/pf_rcv_list.php");

}



?>