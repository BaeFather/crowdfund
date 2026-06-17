<?
###############################################################################
## 파트너체크 페이지
## - 외부 온라인 광고 활용시 랜딩페이지에 본페이지 링크를 통하여 pid를 검증 후
##   회원가입페이지로 전환
###############################################################################
## 네이버페이 : https://www.hellofunding.co.kr/member/mcheck.php
## 오케이캐쉬백 : https://www.hellofunding.co.kr/mcheck/okcashback
##
## pid 적용우선순위 : $_REQUEST['pid'] > $ck_pid(쿠키)
## $_REQUEST['pid'] 가 존재하더라도 referer가 일치하지 않는 경우 pid 반려
###############################################################################

include_once('./_common.php');
include_once(G5_PATH . '/pid_check.inc.php');


// $pid 는 pid_check.inc.php 에서 상속받음!!!!

if($pid) {

	// pid 에 설정된 레퍼러와 실제방문시 레퍼러의 비교 검사
	if($CONF['PARTNER'][$pid]['referer']) {

		$check_str = @preg_replace("/(\/)/", "\/", $CONF['PARTNER'][$pid]['referer']);
		$check_str = @preg_replace("/(\.)/", "\.", $check_str);
		$check_str = @preg_replace("/(\=)/", "\=", $check_str);

		if( @preg_match("/$check_str/", $pid_referer) ) {
			set_cookie("ck_pid", $pid, $pid_cookie_time);
		}
		else {
			set_cookie("ck_pid", "", -1);				// 직접 접속또는 pid의 referer 값과 일치하지 않는 경로로 접속시 쿠키 제거
			unset($pid);
		}

	}

}

$join_url = "/member/join_info.php?tab=p";

msg_replace("", $join_url);

sql_close();
exit;

?>