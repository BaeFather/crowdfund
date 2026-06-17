<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

include_once("_common.php");
include_once(G5_LIB_PATH . '/sms.lib.php');

auth_check($auth[$sub_menu], 'w');

if ($is_admin!= 'super') exit;

while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

$MEM = sql_fetch("SELECT mb_id, mb_hp FROM g5_member WHERE mb_no='$mb_no'");
$MEM['mb_hp'] = masterDecrypt($MEM['mb_hp'], false);

$CONTENT = explode(":::", $content);
$subject = $CONTENT[0];
$recommend_count = $CONTENT[1];
$reward_point = $point;

$POINT = sql_fetch("SELECT COUNT(po_id) AS cnt FROM g5_point WHERE mb_id='".$MEM['mb_id']."' AND po_content='$subject'");

if($POINT['cnt']) {
	echo "ERROR:DUP_REQUEST";		// 이미 지급 처리된 요청 입니다.
}
else {

	$result = insert_point($MEM['mb_id'], $reward_point, $subject);

	if($result) {

		$sms_msg = "[헬로펀딩 추천인 이벤트 수익금 지급안내]\n" .
							 "당신의 설레는 내일, 헬로펀딩입니다.\n" .
							 "\n" .
							 "헬로펀딩 추천인 이벤트에 참여해 주셔서 감사합니다.\n" .
							 "\n" .
							 "◆ 이벤트 수익금 안내 ◆\n" .
							 "1. 총 추천인 : ".number_format($recommend_count)."명\n" .
							 "2. 이벤트 수익 : ".number_format($reward_point)."원\n" .
							 "3. 가상계좌 예치금으로 지급\n" .
							 "\n" .
							 "예치금 지급 내역은 '투자내역>추천인현황'에서 확인 가능합니다.\n" .
							 "\n" .
							 "[헬로펀딩]\n" .
							 "고객센터 : 1588-6760\n" .
							 "홈페이지 : www.hellofunding.co.kr";

		$rst = unit_sms_send($_admin_sms_number, $MEM['mb_hp'], addSlashes($sms_msg));
		echo "SUCCESS";
	}
	else {
		echo "ERROR:SYSTEM";		// 시스템에러 입니다. 개발팀에게 문의하십시요.
	}

}

exit;

?>