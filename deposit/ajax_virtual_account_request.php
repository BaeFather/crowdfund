<?
################################################################################
# 가상계좌 발급 처리
# http://hellofunding.co.kr/deposit/deposit.php 에서 ajax로 호출
# 가상계좌 발급 및 예치금 충전은 http://hellofunding.co.kr/example_vacs.php 를
# 참고하세요.
################################################################################

$mode = $_REQUEST['mode'];

// 자사 도메인이 아닌곳에서 호출된 경우 exit;
if($mode != 'debug') {
	$allow_domain = "hellofunding.co.kr";
	if(isset($_SERVER['HTTP_REFERER'])) {
		if(!preg_match("/$allow_domain/i", $_SERVER['HTTP_REFERER'])) {
			header('HTTP/1.1 404 Not Found');
		}
	}
}


include_once("_common.php");

// 로그인 체크
if(!$_SESSION['ss_mb_id']) {
	header('HTTP/1.1 404 Not Found');
	exit;
}

include_once(G5_PATH.'/lib/vacs.lib.php');
include_once(G5_PATH.'/lib/sms.lib.php');

while(list($key, $value) = each($_POST)) { ${$key} = trim($value); }

if($mode=='new' || $mode=='debug') {

	if($mode=='debug') $bank_cd = '003';

	if(!$bank_cd) {
		echo "0:은행이 선택되지 않았습니다.";
		exit;
	}

	if($mode!='debug') {
		$result = vans_edit('ready', $bank_cd);  //****** 가상계좌 생성 ********//
		// 환급계좌정보가 없는 상태면 원리금 수취방식을 예치금(가상계좌) 방식으로 셋팅한다.
		if( empty($member['bank_name']) || empty($member['bank_code']) || empty($member['account_num']) || empty($member['bank_private_name']) ) {
			if(sql_query("UPDATE g5_member SET receive_method='2' WHERE mb_no='".$member['mb_no']."'")) {
				member_edit_log($member['mb_no']);	// 회원정보변경기록
			}
		}
	}

	if($result['res_cd']=='1' || $mode=='debug') {

		echo $result['res_cd'] . ':' . $result['res_msg'] . ':' . $result['bank_cd'] . ':' . $result['acct_no'] . ':' . $result['cmf_nm'];

		$MB = get_member($_SESSION['ss_mb_id']);
		//print_r($MB); exit;

		// 가상계좌 코드 및 번호 $VBANK = array('003'=>'기업은행', '023'=>'SC제일은행', '031'=>'대구은행');
		$va_bank = $VBANK[$MB['va_bank_code']];

		$sms_result = query("SELECT * FROM `g5_sms_userinfo` WHERE use_yn='1' AND idx='15'");
		$sms_cnt = mysqli_num_rows($sms_result);
		if($sms_cnt > 0) {
			$sms_row = mysqli_fetch_array($sms_result);
			if($sms_row["msg"]){
				$sms_msg = str_replace("{USER_NAME}", $MB['mb_name'], $sms_row["msg"]);      // 성명변경
				$sms_msg = str_replace("{BANK}", $va_bank, $sms_msg);                        // 은행명 변경
				$sms_msg = str_replace("{ACCOUNT_NAME}", $MB['va_private_name'], $sms_msg);  // 예금주명 변경
				$sms_msg = str_replace("{ACCOUNT}", $MB['virtual_account'], $sms_msg);       // 계좌번호 변경
				$rst = unit_sms_send($_admin_sms_number, $MB['mb_hp'], $sms_msg);            //** 문자발송 실행 **//
			}
		}


		// 추천인에게 문자 발송 =======================================================================
		$rec_exec_date = "2016-12-09";
		$rec_sdate = "2016-11-28";
		$rec_edate = "2016-12-09";
		if( date(Ymd) <= preg_replace("/-/", "", $rec_exec_date) ) {
			if( $mode=='new' && $member['rec_mb_id'] && $member['virtual_account']=='' ) {

				$sql = "
					SELECT
						A.mb_hp,
						(
							SELECT
								COUNT(mb_no)
							FROM
								g5_member
							WHERE 1=1
								AND rec_mb_no=A.mb_no
								AND rec_mb_id=A.mb_id
								AND (va_bank_code!='' AND virtual_account!='')
								AND LEFT(mb_datetime, 10) BETWEEN '$rec_sdate' AND '$rec_edate'
						) AS recommend_count
					FROM
						g5_member A
					WHERE
						A.mb_no='".$MB['rec_mb_no']."' AND A.mb_id='".$MB['rec_mb_id']."'";

				$REC_MB = sql_fetch($sql);  //추천받은 회원정보 가져오기
				$REC_MB['mb_hp'] = masterDecrypt($REC_MB['mb_hp'], false);

				$sql2 = sql_query("UPDATE g5_member SET rec_date=NOW() WHERE mb_no='".$MB['mb_no']."'");  // 가상계좌 생성시 추천일자 등록

				//print_r($REC_MB);

				$sms_msg2 = "[헬로펀딩 추천인 이벤트 픽미픽미 헬로업 수익 안내]\n";
				$sms_msg2.= "당신의 설레는 내일 헬로펀딩입니다.\n\n";
				$sms_msg2.= "[{MB_ID}]님이 [{REC_MB_ID}]님을 추천하셨습니다.\n\n";
				$sms_msg2.= "◆ 현재 이벤트 수익 안내 ◆\n";
				$sms_msg2.= "1. 회원님의 총 추천인 : {REC_COUNT}명\n";
				$sms_msg2.= "2. 입금예정 이벤트 수익 : {REC_AMOUNT}원\n";
				$sms_msg2.= "3. 12월 12일 예치금으로 일괄 지급예정\n\n";
				$sms_msg2.= "(단,추천하신 회원이 탈퇴한 경우 입금예정 이벤트 수익에서 제외됩니다.)\n\n";
				$sms_msg2.= "◆ 참 쉬운~ 이벤트 참여 방법 ◆\n";
				$sms_msg2.= "1. 친구에게 헬로펀딩을 소개합니다.\n";
				$sms_msg2.= "2. 회원가입한 친구 전원에게 영화티켓을 드립니다.\n";
				$sms_msg2.= "(추가비용 없이 전국에서 영화 관람 가능)\n";
				$sms_msg2.= "3. 친구가 회원가입할 때마다 내 예치금은 1,000원씩 늘어납니다.\n\n";
				$sms_msg2.= "◆ 이벤트 관련 설명 ◆\n";
				$sms_msg2.= "1. 이벤트보기: https://goo.gl/kUBIrh\n";
				$sms_msg2.= "2. 기간 : 2016년 12월 09일 까지\n";
				$sms_msg2.= "3. 영화티켓과 예치금은 12월 12일 일괄지급\n\n";
				$sms_msg2.= "[헬로펀딩]\n";
				$sms_msg2.= "고객센터 : 1588-6760\n";
				$sms_msg2.= "홈페이지 : www.hellofunding.co.kr\n\n";


				$sms_msg2 = str_replace("{MB_ID}", $MB['mb_id'], $sms_msg2);
				$sms_msg2 = str_replace("{REC_MB_ID}", $MB['rec_mb_id'], $sms_msg2);
				$sms_msg2 = str_replace("{REC_COUNT}", number_format($REC_MB['recommend_count']), $sms_msg2);
				$sms_msg2 = str_replace("{REC_AMOUNT}", number_format($REC_MB['recommend_count']*1000), $sms_msg2);

				$rst2 = unit_sms_send($_admin_sms_number, $REC_MB['mb_hp'], $sms_msg2);            //** 문자발송 실행 **//
			}
			// 추천인에게 문자 발송 =======================================================================
		}


		if($mode=='debug') echo $sms_msg;

	}
	else {
		echo $result['res_cd'] . ':' . $result['res_msg'];
	}

}


@sql_close();
exit;

?>