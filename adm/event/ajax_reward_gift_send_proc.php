<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

include_once("_common.php");
include_once(G5_LIB_PATH . '/sms.lib.php');

auth_check($auth[$sub_menu], 'w');

if ($is_admin!= 'super') exit;

while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

$ROW = sql_fetch("SELECT COUNT(idx) AS cnt_idx FROM event_reward_coupon WHERE event_no='$event_no' AND mb_no='$mb_no'");

if($ROW['cnt_idx']) {
	echo "ERROR:DUP_REQUEST";		// 이미 지급 처리된 요청 입니다.
}
else {

	$MEM = sql_fetch("SELECT mb_hp FROM g5_member WHERE mb_no='$mb_no'");
	$MEM['mb_hp'] = masterDecrypt($MEM['mb_hp'], false);

	$ROW2 = sql_fetch("SELECT idx, coupon_no FROM event_reward_coupon WHERE event_no='$event_no' AND mb_no=''");
	if($ROW2['idx']) {
		$sql = "
			UPDATE
				event_reward_coupon
			SET
				mb_no='$mb_no',
				give_date=NOW()
			WHERE
				idx='".$ROW2['idx']."'";
		if($result = sql_query($sql)) {
			$sms_msg = "[헬로펀딩 추천인 이벤트 영화티켓 지급안내]\n" .
			           "당신의 설레는 내일, 헬로펀딩입니다.\n" .
			           "\n" .
			           "헬로펀딩 추천인 이벤트에 참여해 주셔서 감사합니다.\n" .
			           "\n" .
			           "영화티켓 : ".$ROW2['coupon_no']."\n" .
			           "\n" .
			           "◆ 영화티켓 사용방법 ◆\n" .
			           "1. www.ecomovie.co.kr에서 영화티켓 등록 후 사용 가능합니다.\n" .
			           "(영화티켓 등록은 2017년 1월 22일까지 가능하며, 등록 후 30일 이내 사용 가능합니다.)\n" .
			           "2. 예매 시 전국 CGV, 메가박스, 롯데시네마, 기타 지역 영화관 선택이 가능합니다.\n" .
			           "3. 일부 특수관의 경우 관람이 제한됩니다.\n" .
			           "(아이맥스, 디지털3D, M2관, 샤롯데 등)\n" .
			           "4. 당일 예매 시 영화 시작 3시간 전에는 예매를 해주세요.\n\n" .
			           "[헬로펀딩]\n" .
			           "고객센터 : 1588-6760\n" .
			           "홈페이지 : www.hellofunding.co.kr";
			$rst = unit_sms_send($_admin_sms_number, $MEM['mb_hp'], $sms_msg);
			echo "SUCCESS";
		}
		else {
			echo "ERROR:SYSTEM";		// 시스템에러 입니다. 개발팀에게 문의하십시요.
		}
	}
	else {
		echo "ERROR:TYPE1";		// 지급 가능한 쿠폰이 부족합니다.
	}

}

exit;

?>