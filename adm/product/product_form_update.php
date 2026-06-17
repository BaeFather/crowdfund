<?php

$sub_menu = "600000";
include_once('./_common.php');

//print_rr($_REQUEST); exit;

if($w == 'u') check_demo();
auth_check($auth[$sub_menu], 'w');

if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


$g5['title'] = "상품등록처리";
if($member['mb_level'] == '9') include_once(G5_ADMIN_PATH."/inc_sub_admin_access_check.php");		// 부관리자 접속로그 등록


//상품정보제공 외부플랫폼 (scrap_out이 빈값인 것에서만 효과가 있도록 설정할것)
$platform_count = count($_POST['PLATFORM']);
$platform = "";
for($i=0,$j=1; $i<$platform_count; $i++,$j++) {
	$platform.= $_POST['PLATFORM'][$i];
	if($j < $platform_count) $platform.= "|";
}


switch($_REQUEST['action']) {

	case 'product_copy' :

		if ($_POST['main_image_ori']) {
			$main_image_new = tempnam(G5_DATA_PATH.'/product/' , 'CP_');
			unlink($main_image_new);
			copy(G5_DATA_PATH.'/product/'.$_POST['main_image_ori'] , $main_image_new);
		}
		if ($_POST['main_image_m_ori']) {
			$main_image_m_new = tempnam(G5_DATA_PATH.'/product/' , 'CP_');
			unlink($main_image_m_new);
			copy(G5_DATA_PATH.'/product/'.$_POST['main_image_m_ori'] , $main_image_m_new);
		}
		if ($_POST['detail_image_ori']) {
			$detail_image_ori_new = tempnam(G5_DATA_PATH.'/product/' , 'CP_');
			unlink($detail_image_ori_new);
			copy(G5_DATA_PATH.'/product/'.$_POST['detail_image_ori'] , $detail_image_ori_new);
		}

	case 'product_insert' :
	case 'product_update' :

		if($_POST['invest_period']=='under1month') {
			$invest_period = 1;
			$invest_days   = trim($_POST['invest_days']);
			if($_POST['invest_days'] < 1) { alert('투자기간을 1개월 미만으로 설정시, 일수를 1일 이상 설정하여 주십시요.'); }
		}
		else {
			$invest_period = $_POST['invest_period'];
			$invest_days = 0;
		}

		$open_datetime  = $_POST['open_date'].' '.$_POST['open_hour'].':'.$_POST['open_minute'].':'.$_POST['open_second'];
		$start_datetime = $_POST['start_date'].' '.$_POST['start_hour'].':'.$_POST['start_minute'].':'.$_POST['start_second'];
		$end_datetime   = $_POST['end_date'].' '.$_POST['end_hour'].':'.$_POST['end_minute'].':'.$_POST['end_second'];

		$purchase_guarantees = ($_POST['purchase_guarantees']) ? $_POST['purchase_guarantees'] : 'N';		// 채권매입보증 구분
		$advanced_payment	   = ($_POST['advanced_payment'])	? $_POST['advanced_payment'] : 'N';					// 이자선지급 구분
		$portfolio           = ($_POST['portfolio']) ? $_POST['portfolio'] : 'N';												// 포트폴리오상품 구분
		$mortgage_guarantees = ($_POST['mortgage_guarantees'] && $_POST['mortgage_guarantees']!='none') ? $_POST['mortgage_guarantees'] : '';		// 주택담보대출 구분
		$success_example     = ($_POST['success_example']) ? $_POST['success_example'] : 'N';						// 성공사례리스트출력 구분
		$popular_goods       = ($_POST['popular_goods']) ? $_POST['popular_goods'] : 'N';								// 인기상품 구분
		$loan_advanced_count = ($_POST['loan_advanced_count']) ? $_POST['loan_advanced_count'] : 0;			// 부분이자선택시 지급회차 (부분이자선택시에만 개월수 기록)
		$ib_trust            = ($_POST['ib_trust']) ? $_POST['ib_trust'] : 'N';													// 금융기관 연계 여부

		if($_REQUEST['action']=='product_insert') {
			$gr_idx = ($_POST['gr_idx']) ? $_POST['gr_idx'] : '';
		}
		else {
			$gr_idx = ($_POST['gr_idx']) ? $_POST['gr_idx'] : $_POST['idx'];
		}

		$start_num = ( preg_match("/\[제/", $_POST['title']) && preg_match("/호/", $_POST['title']) ) ? @str_f6($_POST['title'], "[제", "호") : "";

		if($_POST['loan_usefee_type']=='A') {
			$loan_usefee_repay_count =  $_POST['loan_usefee_repay_count'];
			if($loan_usefee_repay_count < 1) { alert('대출자 플랫폼수수료 후취방식 적용시 분납횟수를 1회 이상 입력하십시요.'); }
		}
		else {
			$loan_usefee_repay_count = 0;
		}

		$withhold_tax_rate = ($CONF['indi']['interest_tax_ratio']*100) + ($CONF['indi']['local_tax_ratio']*10);


		$sql_common = " category='".$_POST['category']."'";
		$sql_common.= ", category2='".$_POST['m_category2']."'";
		$sql_common.= ", gr_idx='".$gr_idx."'";														// 동일차주상품번호
		$sql_common.= ", ai_grp_idx='".$_POST['ai_grp_idx']."'";					// 자동투자그룹번호
		if($start_num) $sql_common.= ", start_num='".$start_num."'";
		$sql_common.= ", title='".$_POST['title']."'";
		$sql_common.= ", recruit_amount='".$_POST['recruit_amount']."'";
		$sql_common.= ", invest_period='".$invest_period."'";
		$sql_common.= ", invest_days='".$invest_days."'";
		$sql_common.= ", calc_type='".$calc_type."'";
		$sql_common.= ", invest_return='".$_POST['invest_return']."'";
		$sql_common.= ", loan_interest_rate='".$_POST['loan_interest_rate']."'";
		$sql_common.= ", overdue_rate='".$_POST['overdue_rate']."'";
		$sql_common.= ", withhold_tax_rate='".$withhold_tax_rate."'";
		$sql_common.= ", loan_interest_type='".$_POST['loan_interest_type']."'";
		$sql_common.= ", loan_advanced_count='".$loan_advanced_count."'";
		$sql_common.= ", loan_usefee='".$_POST['loan_usefee']."'";
		$sql_common.= ", loan_usefee_type='".$_POST['loan_usefee_type']."'";
		$sql_common.= ", loan_usefee_repay_count='".$loan_usefee_repay_count."'";
		$sql_common.= ", invest_usefee='".$_POST['invest_usefee']."'";
		$sql_common.= ", invest_usefee_type='".$_POST['invest_usefee_type']."'";
		$sql_common.= ", recruit_period_start='".$_POST['recruit_period_start']."'";
		$sql_common.= ", recruit_period_end='".$_POST['recruit_period_end']."'";
		$sql_common.= ", middle_withdraw_state='".$_POST['middle_withdraw_state']."'";
		$sql_common.= ", middle_withdraw_charge='".$_POST['middle_withdraw_charge']."'";
		$sql_common.= ", repay_type='".$_POST['repay_type']."'";
		//$sql_common.= ", detail_image='".@implode('|', trim($_POST['detail_image']))."'";
		if ($_REQUEST['action']<>"product_copy") $sql_common.= ", detail_image='".@implode('|', array_map('trim',$_POST['detail_image']))."'";
		$sql_common.= ", open_datetime='".$open_datetime."'";
		$sql_common.= ", open_date='".$_POST['open_date']."'";
		$sql_common.= ", open_hour='".$_POST['open_hour']."'";
		$sql_common.= ", open_minute='".$_POST['open_minute']."'";
		$sql_common.= ", open_second='".$_POST['open_second']."'";
		$sql_common.= ", start_datetime='".$start_datetime."'";
		$sql_common.= ", start_date='".$_POST['start_date']."'";
		$sql_common.= ", start_hour='".$_POST['start_hour']."'";
		$sql_common.= ", start_minute='".$_POST['start_minute']."'";
		$sql_common.= ", start_second='".$_POST['start_second']."'";
		$sql_common.= ", end_datetime='".$end_datetime."'";
		$sql_common.= ", end_date='".$_POST['end_date']."'";
		$sql_common.= ", end_hour='".$_POST['end_hour']."'";
		$sql_common.= ", end_minute='".$_POST['end_minute']."'";
		$sql_common.= ", end_second='".$_POST['end_second']."'";
		$sql_common.= ", loan_contact='".$_POST['loan_contact']."'";
		$sql_common.= ", loan_address='".$_POST['loan_address']."'";
		$sql_common.= ($_POST['isEtcCost']=='1') ? ", display='N'" : ", display='".$_POST['display']."'";			// 기타비용처리상품 강제 비출력설정
		$sql_common.= ", isEtcCost='".$_POST['isEtcCost']."'";
		$sql_common.= ", isRefPrdt='".$_POST['isRefPrdt']."'";		// 상환참조상품 플래그
		$sql_common.= ($_POST['isEtcCost']=='1') ? ", scrap_out='1'" : ", scrap_out='".$_POST['scrap_out']."'";		// 기타비용처리상품 강제 외부스크랩불가처리
		$sql_common.= ", platform='".$platform."'";
		$sql_common.= ", isTest='".$isTest."'";
		$sql_common.= ", purchase_guarantees='".$purchase_guarantees."'";
		$sql_common.= ", advanced_payment='".$advanced_payment."'";
		$sql_common.= ", portfolio='".$portfolio."'";
		$sql_common.= ", mortgage_guarantees='".$mortgage_guarantees."'";
		$sql_common.= ", success_example='".$success_example."'";
		$sql_common.= ", popular_goods='".$popular_goods."'";
		$sql_common.= ", advance_invest='".$_POST['advance_invest']."'";
		$sql_common.= ", advance_invest_ratio='".$_POST['advance_invest_ratio']."'";
		$sql_common.= ", only_vip='".$_POST['only_vip']."'";
		$sql_common.= ", vip_mb_no='".preg_replace("/ /", "", trim($_POST['vip_mb_no']))."'";
		$sql_common.= ", isConsor='".$_POST['isConsor']."'";
		$sql_common.= ", consor_co='".$_POST['consor_co']."'";
		$sql_common.= ", repay_acct_no='".$_POST['repay_acct_no']."'";

		//-- 상환금 참조용 상품번호 및 계좌번호 등록/수정 -----------------------------------
		if($_POST['ref_prdt_idx']) {
			$REF_PRDT = sql_fetch("SELECT idx, repay_acct_no FROM cf_product WHERE idx='".$_POST['ref_prdt_idx']."'");
		}
		$sql_common.= ", ref_prdt_idx='".$REF_PRDT['idx']."'";
		$sql_common.= ", ref_prdt_repay_acct_no='".$REF_PRDT['repay_acct_no']."'";
		//-- 상환금 참조용 상품번호 및 계좌번호 등록/수정 -----------------------------------

		$sql_common.= ", ib_trust ='".$ib_trust."'";

		// 대출자 패밀리 No 값 추가  --------------------------------------------------
		if($_POST['loan_mb_no']) {
			$sql_common.= ", loan_mb_no='".$_POST['loan_mb_no']."'";

			$MEM = sql_fetch("SELECT mb_f_no FROM g5_member WHERE mb_no='".$_POST['loan_mb_no']."'");
			$sql_common.= ", loan_mb_f_no='".$MEM['mb_f_no']."'";
		}

		$sql_common.= ", loan_dep_bank_cd1='".$_POST['loan_dep_bank_cd1']."'";
		$sql_common.= ", loan_dep_acct_nb1='".$_POST['loan_dep_acct_nb1']."'";
		$sql_common.= ", loan_dep_amt1='".$_POST['loan_dep_amt1']."'";
		$sql_common.= ", loan_dep_acct_memo1='".$_POST['loan_dep_acct_memo1']."'";
		$sql_common.= ", loan_dep_bank_cd2='".$_POST['loan_dep_bank_cd2']."'";
		$sql_common.= ", loan_dep_acct_nb2='".$_POST['loan_dep_acct_nb2']."'";
		$sql_common.= ", loan_dep_amt2='".$_POST['loan_dep_amt2']."'";
		$sql_common.= ", loan_dep_acct_memo2='".$_POST['loan_dep_acct_memo2']."'";
		$sql_common.= ", loan_dep_bank_cd3='".$_POST['loan_dep_bank_cd3']."'";
		$sql_common.= ", loan_dep_acct_nb3='".$_POST['loan_dep_acct_nb3']."'";
		$sql_common.= ", loan_dep_amt3='".$_POST['loan_dep_amt3']."'";
		$sql_common.= ", loan_dep_acct_memo3='".$_POST['loan_dep_acct_memo3']."'";
		$sql_common.= ", loan_dep_bank_cd4='".$_POST['loan_dep_bank_cd4']."'";
		$sql_common.= ", loan_dep_acct_nb4='".$_POST['loan_dep_acct_nb4']."'";
		$sql_common.= ", loan_dep_amt4='".$_POST['loan_dep_amt4']."'";
		$sql_common.= ", loan_dep_acct_memo4='".$_POST['loan_dep_acct_memo4']."'";
		$sql_common.= ", loan_dep_bank_cd5='".$_POST['loan_dep_bank_cd5']."'";
		$sql_common.= ", loan_dep_acct_nb5='".$_POST['loan_dep_acct_nb5']."'";
		$sql_common.= ", loan_dep_amt5='".$_POST['loan_dep_amt5']."'";
		$sql_common.= ", loan_dep_acct_memo5='".$_POST['loan_dep_acct_memo5']."'";

		$sql_common.= ", loadview_url='".$_POST['loadview_url']."'";
		$sql_common.= ", stream_url1='".$_POST['stream_url1']."'";
		$sql_common.= ", stream_url2='".$_POST['stream_url2']."'";

		$sql_common.= ", zipcode='".$_POST['zipcode']."'";
		$sql_common.= ", address='".$_POST['address']."'";
		$sql_common.= ", address_detail='".$_POST['address_detail']."'";
		$sql_common.= ", lat='".$_POST['lat']."'";
		$sql_common.= ", lng='".$_POST['lng']."'";
		$sql_common.= ", ltv='".$_POST['ltv']."'";


		$sql_common2 = " evaluate_score1='".$_POST['evaluate_score1']."'";
		$sql_common2.= ", evaluate_score2='".$_POST['evaluate_score2']."'";
		$sql_common2.= ", evaluate_score3='".$_POST['evaluate_score3']."'";
		$sql_common2.= ", evaluate_score4='".$_POST['evaluate_score4']."'";
		$sql_common2.= ", evaluate_star1='".$_POST['evaluate_star1']."'";
		$sql_common2.= ", evaluate_star2='".$_POST['evaluate_star2']."'";
		$sql_common2.= ", evaluate_star3='".$_POST['evaluate_star3']."'";
		$sql_common2.= ", evaluate_star4='".$_POST['evaluate_star4']."'";
		$sql_common2.= ", evaluate_grade1='".$_POST['evaluate_grade1']."'";
		$sql_common2.= ", evaluate_grade2='".$_POST['evaluate_grade2']."'";
		$sql_common2.= ", evaluate_grade3='".$_POST['evaluate_grade3']."'";
		$sql_common2.= ", evaluate_grade4='".$_POST['evaluate_grade4']."'";

		$sql_common2.= ", extend_1='".$_POST['extend_1']."'";
		$sql_common2.= ", extend_2='".$_POST['extend_2']."'";
		$sql_common2.= ", extend_3='".$_POST['extend_3']."'";
		$sql_common2.= ", extend_4='".$_POST['extend_4']."'";
		$sql_common2.= ", extend_5='".$_POST['extend_5']."'";
		$sql_common2.= ", extend_6='".$_POST['extend_6']."'";
		$sql_common2.= ", extend_7='".$_POST['extend_7']."'";
		$sql_common2.= ", extend_8='".$_POST['extend_8']."'";
		$sql_common2.= ", extend_9='".$_POST['extend_9']."'";

		$sql_common2.= ", invest_summary='".$_POST['invest_summary']."'";
		$sql_common2.= ", invest_summary_m='".$_POST['invest_summary_m']."'";
		$sql_common2.= ", core_invest_point='".$_POST['core_invest_point']."'";
		$sql_common2.= ", product_summary='".$_POST['product_summary']."'"; // 상품요약
		$sql_common2.= ", product_description='".$_POST['product_description']."'"; // 상품설명
		$sql_common2.= ", evidence='".@join('|', $_POST['evidence'])."'";
		$sql_common2.= ", security_loan='".$_POST['security_loan']."'";
		$sql_common2.= ", special_info='".$_POST['special_info']."'";
		$sql_common2.= ", judge='".$_POST['judge']."'";
		$sql_common2.= ", screening='".$_POST['screening']."'";
		$sql_common2.= ", comment='".$_POST['comment']."'";
		$sql_common2.= ", receiver='".$_POST['receiver']."'";
		$sql_common2.= ", broker='".$_POST['broker']."'";
		$sql_common2.= ", commission_fee='".$_POST['commission_fee']."'";

		//echo $sql_common; exit;


		$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
		if (is_uploaded_file($_FILES['main_image']['tmp_name'])) {
			@mkdir(G5_DATA_PATH.'/product', G5_DIR_PERMISSION);
			@chmod(G5_DATA_PATH.'/product', G5_DIR_PERMISSION);

			shuffle($chars_array);
			$shuffle = substr(implode('', $chars_array), 0, 10);
			$dest_path = G5_DATA_PATH.'/product/'.$shuffle;

			move_uploaded_file($_FILES['main_image']['tmp_name'], $dest_path);
			chmod($dest_path, G5_FILE_PERMISSION);
			$sql_common.= ", main_image = '".$shuffle."'";
		}
		if ($_REQUEST['action']=="product_copy") {
			$sql_common.= ", main_image = '".basename($main_image_new)."'";
			$sql_common.= ", main_image_m = '".basename($main_image_m_new)."'";
			$sql_common.= ", detail_image = '".basename($detail_image_ori_new)."'";
		}

		if (is_uploaded_file($_FILES['main_image_m']['tmp_name'])) {
			@mkdir(G5_DATA_PATH.'/product', G5_DIR_PERMISSION);
			@chmod(G5_DATA_PATH.'/product', G5_DIR_PERMISSION);

			shuffle($chars_array);
			$shuffle = substr(implode('', $chars_array), 0, 10);
			$dest_path = G5_DATA_PATH.'/product/'.$shuffle;

			move_uploaded_file($_FILES['main_image_m']['tmp_name'], $dest_path);
			chmod($dest_path, G5_FILE_PERMISSION);
			$sql_common.= ", main_image_m = '".$shuffle."'";
		}

		if($_POST['action']=='product_insert') {
			$sql_common.= ", writer_id='".$member['mb_id']."'";
			$sql_common.= ", insert_date=NOW()";
			$sql = "INSERT INTO cf_product SET {$sql_common}";
		}
		else if($_POST['action']=='product_update') {
			$FIRST_DATA = sql_fetch("SELECT writer_id, insert_date FROM cf_product WHERE idx='".$_POST['idx']."'");
			$sql = "UPDATE cf_product SET {$sql_common} WHERE idx='".$_POST['idx']."'";
		}
		else if($_POST['action']=='product_copy') {
			$sql_common.= ", writer_id='".$member['mb_id']."'";
			$sql_common.= ", insert_date=NOW()";
			$sql = "INSERT INTO cf_product SET {$sql_common}";
		}

		//print_rr($_POST, 'font-size:12px');
		//print_rr($sql, 'font-size:12px');
		//exit;

		if(sql_query($sql, true)) {

			if ( preg_match("/(product_insert|product_update)/", $_POST['action']) ) {
				@exec("/usr/local/php/bin/php -q ".G5_PATH."/xml/make_active_product_list.php");						// 활성상품리스트 XML 업데이트
				@exec("/usr/local/php/bin/php -q ".G5_PATH."/xml/make_success_popular_product_list.php");				// 인기상품리스트 XML 업데이트

				// 2018-04-05 추가
				// 상품 수정시 캐시파일 새로생성
				require_once(G5_LIB_PATH."/Cache/Lite.php");
				$cacheOption = array(
					'cacheDir' => G5_DATA_PATH."/cache/pages/",
					'hashedDirectoryLevel' => 0
				);
				$cacheLite = new Cache_Lite($cacheOption);
				$cacheId = "/investment/view.php?prd_idx={$_POST['idx']}"; // 상품상세
				$cacheLite->remove($cacheId);
				$cacheId = "/"; // 메인
				$cacheLite->remove($cacheId);
				$cacheId = "/investment/list.php"; // 상품리스트
				$cacheLite->remove($cacheId);

				@unlink(G5_DATA_PATH."/cache/productList-active.php");
			}

			if($_REQUEST['action']=='product_insert' || $_REQUEST['action']=='product_copy') {

				$insert_idx = sql_insert_id();

				if($insert_idx) {
					$sqlx = "
						INSERT INTO
							cf_product_container
						SET
							product_idx='".$insert_idx."',
							{$sql_common2}";
					sql_query($sqlx);
				}

				if(!$_POST['gr_idx']) {
					sql_query("UPDATE cf_product SET gr_idx='".$insert_idx."' WHERE idx='".$insert_idx."'");
				}
				msg_replace("상품정보 등록완료", "/adm/product/product_form.php?idx=".$insert_idx);

			}
			else {

				$sqlx = "
					UPDATE
						cf_product_container
					SET
						{$sql_common2}
					WHERE
						product_idx='".$_POST['idx']."'";
				sql_query($sqlx);


				// 변경로그 등록 (상품 수정시에만 등록)
				// 상품개요
				sql_query("
					INSERT INTO
						cf_product_history
					SET
						edit_datetime = '".G5_TIME_YMDHIS."'
						, editor_id = '".$member['mb_id']."'
						, product_idx = '".$_POST['idx']."'
						, {$sql_common}
						, writer_id = '".$FIRST_DATA['writer_id']."'
			      , insert_date = '".$FIRST_DATA['insert_date']."'");

				// 상세내용
				sql_query("
					INSERT INTO
						cf_product_container_history
					SET
						edit_datetime = '".G5_TIME_YMDHIS."'
						, editor_id = '".$member['mb_id']."'
						, product_idx = '".$_POST['idx']."'
						, {$sql_common2}");

				msg_replace("상품정보 수정완료", "/adm/product/product_form.php?idx=".$_POST['idx']);

			}

		}
		else {
			alert('SQL CHECK PLEASE');
		}
	break;


	///////////////////////////////////////////////////////////////////////////////
	// 상품 정보 삭제
	///////////////////////////////////////////////////////////////////////////////
	case 'product_delete' :
		$sql = "INSERT INTO cf_product_delete SELECT * FROM cf_product WHERE idx = '".$_GET['idx']."'";
		if(sql_query($sql)) {
			sql_query("DELETE FROM cf_product WHERE idx = '".$_GET['idx']."'");
			sql_query("DELETE FROM cf_product_container WHERE product_idx = '".$_GET['idx']."'");
			alert('정상적으로 처리되었습니다.');
		}

	break;

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

	case 'product_image_delete' :

		$file_name = trim($_POST['fname']);
		$file_path = G5_DATA_PATH.'/product/' . $file_name;

		if( filesize($file_path) ) {
			unlink($file_path);
		}

	break;

}


sql_close();
exit;

?>