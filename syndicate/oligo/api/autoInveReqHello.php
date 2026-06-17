<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/autoInveReqHello.do
## 19. 헬로펀딩_자동투자신청
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST['ci']
$REQUEST['comp_cd']
$REQUEST['autoinve_yn']
$REQUEST['autoInve_list'] = array();
$REQUEST['autoInve_list'][$i]['prod_cate']
$REQUEST['autoInve_list'][$i]['min_amt']
$REQUEST['autoInve_list'][$i]['max_amt']
*/

/*
[카테고리]
부동산-주택담보 : mortgage
부동산-건축자금 : pf
매출채권-면세점 : dutyfree
매출채권-소상공인 : smalltrade
동산 : movable
*/

$ARR = array("code"=>"9999", "msg"=>"자동투자서비스가 종료되었습니다."); echo printJson($ARR); exit;

function auto_inv_log($idx, $edate) {
	// 1. 수정일 업데이트
	$update_sql = "UPDATE cf_auto_invest_config_user SET edate='".$edate."' WHERE idx='".$idx."'";
	sql_query($update_sql);

	// 2.로그 기록
	$log_sql = "INSERT INTO cf_auto_invest_config_user_log SELECT * FROM cf_auto_invest_config_user WHERE idx='".$idx."'";
	$res = sql_query($log_sql);		// 로그기록

	return $res;
}


$REQUEST['ci'] = urldecode($REQUEST['ci']);
//$REQUEST['ci'] = "INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==";

/*
$REQUEST['autoinve_yn'] = 'Y';
$REQUEST['autoInve_list'] = array(
	array('prod_cate'=>'mortgage',   'min_amt'=>'10000', 'max_amt'=>'100000'),
	array('prod_cate'=>'pf',         'min_amt'=>'20000', 'max_amt'=>'200000'),
	array('prod_cate'=>'dutyfree',   'min_amt'=>'30000', 'max_amt'=>'300000'),
	array('prod_cate'=>'smalltrade', 'min_amt'=>'40000', 'max_amt'=>'400000'),
	array('prod_cate'=>'movable',    'min_amt'=>'50000', 'max_amt'=>'500000')
);
*/


$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }
if($REQUEST['comp_cd'] != $_CONF['comp_cd']) { $ARR = array('code'=>'9999', 'msg'=>'업체코드오류'); echo printJson($ARR); exit; }

$MB = get_member($mb_id);


$request_count = count($REQUEST['autoInve_list']);

if($request_count) {

	// 금액 오류 설정 체크
	for($i=0; $i<$request_count; $i++) {

		if($REQUEST['autoInve_list'][$i]['min_amt'] > $REQUEST['autoInve_list'][$i]['max_amt']) {
			$ARR = array("code"=>'9999', "msg"=>"설정 최대금액을 최소금액 이상으로 설정하여야 합니다.");
			echo printJson($ARR); exit;
		}

		if(($REQUEST['autoInve_list'][$i]['min_amt']%10000) > 0 || ($REQUEST['autoInve_list'][$i]['max_amt']%10000) > 0) {
			$ARR = array("code"=>'9999', "msg"=>"설정 금액은 만원 단위로 설정하여야 합니다.");
			echo printJson($ARR); exit;
		}

	}

	for($i=0; $i<$request_count; $i++) {

		switch($REQUEST['autoInve_list'][$i]['prod_cate']) {
			case 'mortgage'   : $where = "AND B.category='2' AND B.grp_title='주택담보'";              break;
			case 'pf'         : $where = "AND B.category='2' AND B.grp_title='부동산'";                break;
			case 'dutyfree'   : $where = "AND B.category='3' AND B.grp_title='헬로페이 (면세점 등)'";  break;
			case 'smalltrade' : $where = "AND B.category='3' AND B.grp_title='헬로페이 (소상공인)'";   break;
			case 'movable'    :
			default           : $where = "AND B.category='1' AND B.grp_title='동산담보'";              break;
		}


		$sql = "
			SELECT
				A.idx, A.ai_grp_idx, A.member_idx, A.setup_amount, A.setup_amount2
			FROM
				cf_auto_invest_config_user A
			LEFT JOIN
				cf_auto_invest_config B  ON A.ai_grp_idx=B.idx
			WHERE 1
				AND A.member_idx = '".$MB['mb_no']."'
				$where";
		$DATA = sql_fetch($sql);
		//print_r($sql); print_r($DATA); exit;

		$edit_datetime = date('Y-m-d H:i:s');

		if( $DATA['idx'] ) {

			// 기존데이터 업데이트시

			if($REQUEST['autoinve_yn']=='N') {

				// 1.수정일 업데이트, 2.로그 기록 (기존내용 기록)
				auto_inv_log($DATA['idx'], $edit_datetime);

				$sqlx = "DELETE FROM cf_auto_invest_config_user WHERE idx = '".$DATA['idx']."'";

			}
			else {

				// 기 설정된 레코드가 있더라도 최소금액 또는 최대금액을 다르게 설정했다면 업데이트
				if($DATA['setup_amount']<>$REQUEST['autoInve_list'][$i]['min_amt'] || $DATA['setup_amount2']<>$REQUEST['autoInve_list'][$i]['max_amt']) {

					// 1.수정일 업데이트, 2.로그 기록 (기존내용 기록)
					auto_inv_log($DATA['idx'], $edit_datetime);

					// 3. 정보 업데이트
					$sqlx = "
						UPDATE
							cf_auto_invest_config_user
						SET
							setup_amount = '".$REQUEST['autoInve_list'][$i]['min_amt']."',
							setup_amount2 = '".$REQUEST['autoInve_list'][$i]['max_amt']."',
							edate = '".$edit_datetime."',
							syndi_id = '".$_CONF['SYNDI_ID']."'
						WHERE
							idx='".$DATA['idx']."'";

				}

			}

		}
		else {

			// 최초등록시

			$AIGRP = sql_fetch("SELECT idx FROM cf_auto_invest_config B WHERE 1 $where");			// 자동투자 그룹 IDX 가져오기

			$sqlx = "
				INSERT INTO
					cf_auto_invest_config_user
				SET
					ai_grp_idx = '".$AIGRP['idx']."',
					member_idx = '".$MB['mb_no']."',
					setup_amount = '".$REQUEST['autoInve_list'][$i]['min_amt']."',
					setup_amount2 = '".$REQUEST['autoInve_list'][$i]['max_amt']."',
					invest_warning_agree = '1',
					rdate = NOW(),
					syndi_id = '".$_CONF['SYNDI_ID']."'";

		}

		if($sqlx) {

			sql_query($sqlx);

			// 상환금환급방식 강제 변경 (예치금충전 방식으로)
			if( $member['receive_method']=='1' ) {

				$rcv_sql = "
					UPDATE
						g5_member
					SET
						receive_method = '2',
						edit_datetime = '".$edit_datetime."'
					WHERE
						mb_no='".$MB['mb_no']."'";
				sql_query($rcv_sql);

			}

			$sqlx = NULL;

		}

	}		// end for($i=0; $i<$request_count; $i++)

}
else {
	$ARR = array("code"=>'9999', "msg"=>"설정 요청 항목이 없습니다.");
	echo printJson($ARR); exit;
}


$ARR = array('code'=>'0000', 'msg'=>'정상처리되었습니다.');


##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>