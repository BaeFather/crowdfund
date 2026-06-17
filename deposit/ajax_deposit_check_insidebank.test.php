<?
##########################################################################################################
## 가상계좌 실시간 입금 알림 (신한 인사이드뱅크 예치금 입금 통지내역 정의 테이블 내용 기준으로 적용시)
## /theme/blueman1/tail.sub.php 에서 팝업(/popup/inc_deposit_check_insidebank.php)으로 출력
##########################################################################################################
include_once("_common.php");

if(!$member['mb_id']) {
	$ARR = array('result'=>'LOGIN_PLEASE', 'message'=>'');
	echo json_encode($ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);
	exit;
}

if(!$office_connect) {
	if($_SERVER["REQUEST_METHOD"] != "POST") {
		$ARR = array('result'=>'FAIL', 'message'=>'데이터 전송방식 오류');
		echo json_encode($ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);
		exit;
	}
}

include_once(G5_LIB_PATH."/sms.lib.php");


$date = date('Ymd');

$datetime_s = $date . '000000';
$datetime_e = $date . '235959';

$ARR = array('result'=>'NONE', 'message'=>'');


// 가상계좌(모계좌:헬로핀테크 마킹) 입금내역 가져오기 (인사이드뱅크 데이터 기준)
// TR_AMT_GBN - 자금성격(10:예치금 20:대출금 30:상환금 40:원리금)
$sql = "
	SELECT
		FB_SEQ, TR_AMT, REMITTER_NM
	FROM
		IB_FB_P2P_IP
	WHERE 1
		AND CUST_ID = '".$member['mb_no']."'
		AND REMITTER_NM != '보정입금'
		AND TR_AMT_GBN = '10'
		AND ERP_TRANS_DT BETWEEN '".$datetime_s."' AND '".$datetime_e."'
	ORDER BY
		ERP_TRANS_DT DESC
	LIMIT 1";

$TRADE = sql_fetch($sql);

if($TRADE) {

	$LOG = sql_fetch("SELECT COUNT(FB_SEQ) AS cnt FROM IB_deposit_notify_daylog WHERE FB_SEQ='".$TRADE['FB_SEQ']."' AND mb_no='".$member['mb_no']."'");

	if(!$LOG['cnt']) {

		// 알림 로그 남김
		$sql3 = "
			INSERT INTO
				IB_deposit_notify_daylog
			SET
				FB_SEQ = '".$TRADE['FB_SEQ']."',
				mb_no  = '".$member['mb_no']."',
				mb_id  = '".$member['mb_id']."',
				amount = '".(int)$TRADE['TR_AMT']."',
				rdate  = NOW()";
		if( sql_query($sql3) ) {

			// 한달전 알림 로그 삭제
			$limit_date = date('Y-m-d H:i:s', strtotime('-30 days'));

			$old_log_res = sql_query("SELECT FB_SEQ FROM IB_deposit_notify_daylog WHERE rdate < '".$limit_date."'");
			if( $old_log_res->num_rows > 0 ) {

				$del_fb_seq = '';
				for($i=0,$j=1; $i<$old_log_res->num_rows; $i++,$j++) {
					if( $OLD_LOG = sql_fetch_array($old_log_res) ) {
						$del_fb_seq.= "'".$OLD_LOG['FB_SEQ']."'";
						$del_fb_seq.= ($j < $old_log_res->num_rows) ? "," : "";
					}
				}

				sql_query("DELETE FROM IB_deposit_notify_daylog WHERE FB_SEQ IN(".$del_fb_seq.") AND rdate < '".$limit_date."'");

			}


			// 차명입금 확인
			$name_matched = false;

			if($member['member_type']=='2') {
				$name_matched = true;				// 법인 회원인 경우 차명입금 알림 패스
			}
			else {

				$remitter_nm = preg_replace("/( )/", "", trim($TRADE['REMITTER_NM']));		// 입금자명
				$mb_name     = preg_replace("/( )/", "", trim($member['mb_name']));

				if( preg_match("/$mb_name/", $remitter_nm) )        $name_matched = true;		// 입금자명에 회원명이 포함된 경우
				else if($remitter_nm=='럭키박스')                   $name_matched = true;		// 럭키박스 이벤트 당첨금 지급
				else if(preg_match("/3주년/", $remitter_nm))        $name_matched = true;		// 3주년 고객감사 이벤트 당첨금 지급
				else if($remitter_nm=="NH이벤트")                   $name_matched = true;		// NH투자증권 CMA 계좌개설 보상금";
				else if($remitter_nm=="2천억돌파상품투자")          $name_matched = true;		// 2천억 돌파 기념 상품투자 이벤트 보상금
				else if($remitter_nm=="2천억돌파친구초대")          $name_matched = true;		// 2천억 돌파 기념 친구초대 이벤트 보상금
				else if(preg_match("/초대이벤트/", $remitter_nm))   $name_matched = true;		// 친구초대이벤트 보상금
				else if(preg_match("/투자지원금/", $remitter_nm))   $name_matched = true;		// 투자지원금 이벤트 보상금
				else if($remitter_nm=="NH이벤트")                   $name_matched = true;		// NH투자증권 CMA 계좌개설 보상금
				else if($remitter_nm=="네이버페이")                 $name_matched = true;		// 네이버페이 제휴 이벤트 첫투자 지원금
				else if(preg_match("/오케이캐쉬백/", $remitter_nm)) $name_matched = true;		// 오케이캐쉬백 제휴 이벤트 첫투자 지원금
				else if($remitter_nm=="보정입금")                   $name_matched = true;		// 보정입금
				else if(preg_match("/HELLO첫투자/", $remitter_nm))  $name_matched = true;		// 헬로첫투자11,12......

				$reward_deposit = false;

				$tmpRes = sql_query("SELECT name, match_type, print_title FROM cf_remitter_config WHERE is_usable='1' ORDER BY idx");
				while( $REMITTER_CONF = sql_fetch_array($tmpRes) ) {
					if( in_array($REMITTER_CONF['match_type'], array('1','2')) ) {
						if($REMITTER_CONF['match_type']=='1') {
							if($remitter_nm == $REMITTER_CONF['name']) {
								$name_matched = $reward_deposit = true;
								$reward_title = $REMITTER_CONF['print_title'];
								break;
							}
						}
						else if($REMITTER_CONF['match_type']=='2') {
							if(preg_match("/".$REMITTER_CONF['name']."/", $remitter_nm)) {
								$name_matched = $reward_deposit = true;

								if( preg_match("/\([0-9]{1,2}\)/", $remitter_nm) ) {
									$month_str = str_f6($remitter_nm, "(", ")") . "월";																					// 괄호 안의 문자열 추출 (괄호안에 월이 숫자로만 표기되어 있는 경우)
									$reward_title = preg_replace("/\{month\}/", $month_str, $REMITTER_CONF['print_title']);			// 문자열 치환 및 가공 ex) {month} HELLO첫투자 이벤트 보상금 ===> "예치금 충전: 12월 HELLO첫투자 이벤트 보상금"
								}
								else if( preg_match("/\([0-9]{1,2}월\)/", $remitter_nm) ) {																		// 괄호 안의 문자열 추출 (괄호안에 숫자+월로 표기된 경우.)
									$reward_title = preg_replace("/\{month\}/", $month_str, $REMITTER_CONF['print_title']);			// 문자열 치환 및 가공 ex) {month} HELLO첫투자 이벤트 보상금 ===> "예치금 충전: 12월 HELLO첫투자 이벤트 보상금"
								}
								else {
									$reward_title = $REMITTER_CONF['print_title'];
								}

								break;
							}
						}
					}
				}
				sql_free_result($tmpRes);



				// 차명입금자중 3회이상 투자 이력이 있는 회원인지 확인
				if(!$name_matched) {
					$INVEST = sql_fetch("SELECT COUNT(idx) AS cnt FROM cf_product_invest WHERE member_idx='".$member['mb_no']."' AND invest_state='Y'");
					if($INVEST['cnt'] >= 3) {
						$name_matched = true;
					}
				}

			}


			/*카카오 모듈로 교체 */
			$tcode = "hello003";
			$KaKao_Message_Send = new KaKao_Message_Send();
			$KaKao_Message_Send->DEPOSIT_MONEY = (int)$TRADE['TR_AMT'];
			$KaKao_Message_Send->MEMBER = $member;	// common.lib member 환경변수
			$KaKao_Message_Send->kakao_insert($tcode);
			/*카카오 모듈로 교체 */

			if($name_matched) {

				$ARR['result']  = 'OK';

				if($reward_deposit) {
					$ARR['message'] = $reward_title . " <font color='red'>".number_format((int)$TRADE['TR_AMT'])."원</font>이 입금되었습니다.<br>\n입금하신 예치금으로 투자 가능합니다.";
				}
				else {
					$ARR['message'] = "예치금 <font color='red'>".number_format((int)$TRADE['TR_AMT'])."원</font>이 입금되었습니다.<br>\n입금하신 예치금으로 투자 가능합니다.";
				}

			}
			else {

				$ARR['result']   = 'OK';
				$ARR['message'] = "<div style='font-size:16px;'>회원님의 가상계좌에 차명 입금된 내역이 확인되어 예치금 반영이 보류되었습니다.<br/>\n고객센터로 문의주시면 빠른 상담 도와드리겠습니다.</div>";
				$ARR['message'].= "<div style='margin-top:20px'>";
				$ARR['message'].= "  <a href='//pf.kakao.com/_xgAdWu' target='_blank'><button type='button' style='margin-top:8px;' class='btn_big_blue'>문의하기</button></a> <a href='/bbs/faq.php?fm_id=4'><button type='button' style='margin-top:8px;' class='btn_big_blue'>도움말보기</button></a>";
				$ARR['message'].= "</div>";
			//$ARR['message'] = "<font style='color:#FF2222;font-size:11pt;line-height:18px'>회원님의 가상계좌로 차명 입금내역이 확인 되었으며, 해당 입금자가 보이스피싱 등의 범죄에 연루되지 아니함을 확인 한 후 예치금으로 지급해드릴 예정입니다.<br>\n자세한 사항은 당사 고객센터로 문의하시기 바랍니다.</font>";

			}

		}

	}

}

echo json_encode($ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);



sql_close();
exit;

?>
