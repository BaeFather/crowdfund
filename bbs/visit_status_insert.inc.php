<?
///////////////////////////////////////////////////////////////////////////////
// 접속자 기록
// 기록조건 : 아이피별 일자_시간대별 1회 기록
//            레퍼러값이 헬로펀딩 내부 URL 일 경우 기록하지 않음.
//            동일시간대에 동일ip로 다른pid 진입한 경우 pid 업데이트, first_pid는 유지
///////////////////////////////////////////////////////////////////////////////

if(!defined('_GNUBOARD_')) exit;

$remote_addr = (@$_SERVER['HTTP_X_FORWARDED_FOR']) ? @$_SERVER['HTTP_X_FORWARDED_FOR'] : @$_SERVER['REMOTE_ADDR'];
if($remote_addr) {
	$remote_addr = @escape_trim(clean_xss_tags($remote_addr));
	$remote_addr = @sql_escape_string($remote_addr);
}

if($remote_addr) {

	set_cookie('ck_vistatus_ref', '', -1);

	$cookie_life_time = strtotime(date('Y-m-d') . " 23:59:59") - time();

	$VIARR = $VITMPARR = NULL;

	$VIARR['ip'] = $remote_addr;
	$VIARR['ip'] = @sql_escape_string($VIARR['ip']);

	$VIARR['referer']     = escape_trim(clean_xss_tags(@$_SERVER['HTTP_REFERER']));
	$VIARR['referer']     = @sql_escape_string($VIARR['referer']);

	$VIARR['user_agent']  = escape_trim(clean_xss_tags(@$_SERVER['HTTP_USER_AGENT']));
	$VIARR['user_agent']  = @sql_escape_string($VIARR['user_agent']);

	$VIARR['is_bot']      = ( preg_match("/bot/i", $VIARR['user_agent']) ) ? '1' : '';		// 봇구분
	$VIARR['device']      = ( in_array(@strtolower(getDevice()), array('mobile','tablet')) ) ? 'm' : 'p';

	$tmp_pid = trim($_REQUEST['pid']);

	if( $tmp_pid == strtoupper(MD5('A001')) )         $pid = "A001";
	else if( $tmp_pid == strtoupper(MD5('A002')) )    $pid = "A002";
	else if( $tmp_pid == strtoupper(MD5('A003')) )    $pid = "A003";
	else if( $tmp_pid == strtoupper(MD5('ppomppu')) ) $pid = "ppomppu";
	else  {
		// 지정된 pid가 아닌경우 Request pid를 최우선으로 한다.
		if($tmp_pid) {
			$pid = $tmp_pid;
		}
		else {
			if( $ck_pid = get_cookie("ck_pid") ) $pid = $ck_pid;
		}
	}

	// pid 유효성 검증
	if($pid) {
		if( !in_array($pid, array_keys($CONF['PARTNER'])) ) {
			set_cookie("ck_pid", "", -1);		// 기간종료시 쿠키 제거
			unset($pid);
		}
	}


	if( !preg_match("/hellofunding\.co\.kr/i", $VIARR['referer']) ) {

		// 2022--04-21 IP가 쿠키로 설정되지 못하는 Device 인 경우가 많아 IP로 체크하도록 변경
		$VISIT_LOG = sql_fetch("SELECT idx FROM cf_visit_status WHERE ip='".$remote_addr."' AND rdate=CURDATE() AND rhour='".date('H')."' ORDER BY idx DESC LIMIT 1");

		if($VISIT_LOG['idx']) {

			sql_query("
				UPDATE
					cf_visit_status
				SET
					pid = '".$pid."',
					last_rdt = NOW()
				WHERE
					idx='".$VISIT_LOG['idx']."'");

		}
		else {

			//(구) ===> if( get_cookie('ck_vistatus_ip') != $remote_addr ) {
			// 컴퓨터의 아이피와 쿠키에 저장된 아이피가 다르다면 테이블에 반영함

			// $IP_AREA(접속지정보배열 => common.php 에서 정의됨

			if($VIARR['is_bot']=='') {
				$VITMPARR = urlParse($VIARR['referer']);

				if( in_array($VITMPARR['site_id'], array('hellofunding','wowstar','finnq')) || preg_match("/hellofunding\.co\.kr/i", $VIARR['referer']) ) {
					$VIARR['referer'] = '';
				}

				$VIARR['site_id']  = $VITMPARR['site_id'];
				$VIARR['site_ca']  = $VITMPARR['site_ca'];
				$VIARR['keyword']  = $VITMPARR['keyword'];
				$VIARR['pkeyword'] = $VITMPARR['pkeyword'];
				$VIARR['is_paid']  = $VITMPARR['is_paid'];

				if( in_array($IP_AREA['country_code'],array('','KR')) ) {
					$sql = "
						INSERT INTO
							cf_visit_status
						SET
							ip        = '".sql_escape_string($VIARR['ip'])."',
							rdate     = '".G5_TIME_YMD."',
							rhour     = '".substr(G5_TIME_HIS, 0, 2)."',
							referer   = '".$VIARR['referer']."',
							device    = '".$VIARR['device']."',
							site_id   = '".$VIARR['site_id']."',
							site_ca   = '".$VIARR['site_ca']."',
							keyword   = '".$VIARR['keyword']."',
							pkeyword  = '".$VIARR['pkeyword']."',
							is_paid   = '".$VIARR['is_paid']."',
							pid       = '".$pid."',
							first_pid = '".$pid."',
							country   = '".$IP_AREA['country_code']."',
							region    = '".$IP_AREA['region_name']."',
							city      = '".$IP_AREA['city']."',
							last_rdt  = NOW()";

					$result = sql_query($sql);
					$vistatus_idx = sql_insert_id();

					if($vistatus_idx) {
						// 접속정보 쿠키생성
						set_cookie('ck_vistatus_idx', $vistatus_idx, $cookie_life_time);
						set_cookie('ck_vistatus_ip', $remote_addr, $cookie_life_time);
						set_cookie('ck_vistatus_agent', @$_SERVER['HTTP_USER_AGENT'], $cookie_life_time);
						//set_cookie('ck_pid', $pid, $cookie_life_time);  // pid 쿠키는 /pid_check.php 에서 설정
					}
				}
			}		// end if($VIARR['is_bot']=='')

		}

	}
	unset($VITMPARR);
	unset($VIARR);

}

?>