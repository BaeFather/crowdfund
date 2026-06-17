<?

if(!defined('_GNUBOARD_')) exit;

$remote_addr = (@$_SERVER['HTTP_X_FORWARDED_FOR']) ? @$_SERVER['HTTP_X_FORWARDED_FOR'] : @$_SERVER['REMOTE_ADDR'];
if($remote_addr) {
	$remote_addr = @escape_trim(clean_xss_tags($remote_addr));
	$remote_addr = @sql_escape_string($remote_addr);
}

if($remote_addr) {

	$VIARR = $VITMPARR = NULL;

	// 컴퓨터의 아이피와 쿠키에 저장된 아이피가 다르다면 테이블에 반영함
	if( get_cookie('ck_visit_ip') != $remote_addr ) {

		$cookie_life_time = strtotime(date('Y-m-d') . " 23:59:59") - time();

		// $_SERVER 배열변수 값의 변조를 이용한 SQL Injection 공격을 막는 코드입니다. 110810
		$referer     = escape_trim(clean_xss_tags(@$_SERVER['HTTP_REFERER']));
		$referer     = @sql_escape_string($referer);

		$user_agent  = escape_trim(clean_xss_tags(@$_SERVER['HTTP_USER_AGENT']));
		$user_agent  = @sql_escape_string($user_agen);

		$is_bot      = ( preg_match("/bot/i", $user_agent) ) ? '1' : '';		// 봇구분 추가 : 2018-08-20
		$vi_browser  = '';
		$vi_os       = '';

		// 장치정보 추가
		$vi_device = @strtolower(getDevice());
		$vi_device = ( in_array($vi_device, array('mobile','tablet')) ) ? 'mobile' : '';


		if(version_compare(phpversion(), '5.3.0', '>=') && defined('G5_BROWSCAP_USE') && G5_BROWSCAP_USE) {
			include_once(G5_BBS_PATH.'/visit_browscap.inc.php');
		}


	//if( $VROW = sql_fetch("SELECT vi_id FROM g5_visit WHERE vi_ip='".$remote_addr."' AND vi_date='".G5_TIME_YMD."' ORDER BY vi_id DESC LIMIT 1") ) {
	// INSERT INTO g5_visit .....
	//}


		// $IP_AREA(접속지정보배열 => common.php 에서 정의됨

		if($is_bot=='' && $IP_AREA['country_code']=='KR') {

			$VITMPARR = urlParse($referer);

			if( in_array($VITMPARR['site_id'], array('hellofunding','wowstar','finnq')) || preg_match("/hellofunding\.co\.kr/i", $referer) ) {
				$referer = "";
			}

			$tmp_row = sql_fetch("SELECT MAX(vi_id) AS max_vi_id FROM {$g5['visit_table']}");
			$vi_id = $tmp_row['max_vi_id'] + 1;


			// $IP_AREA(접속지정보배열 => common.php 에서 정의됨
			$sqlX = "
				INSERT INTO
					g5_visit
				SET
					vi_id      = '".$vi_id."',
					vi_ip      = '".$remote_addr."',
					vi_date    = '".G5_TIME_YMD."',
					vi_time    = '".G5_TIME_HIS."',
					vi_referer = '".$referer."',
					vi_agent   = '".$user_agent."',
					vi_browser = '".$vi_browser."',
					vi_os      = '".$vi_os."',
					vi_device  = '".$vi_device."',
					country    = '".$IP_AREA['country_code']."',
					region     = '".$IP_AREA['region_name']."',
					city       = '".$IP_AREA['city']."',
					is_bot     = '".$is_bot."'";

			$resultX = sql_query($sqlX);

			// 정상으로 INSERT 되었다면 방문자 합계에 반영
			if($resultX) {

					set_cookie('ck_visit_idx',   $vi_id,       $cookie_life_time);		// 접속정보 idx 저장
					set_cookie('ck_visit_ip',    $remote_addr, $cookie_life_time);
					set_cookie('ck_visit_agent', $user_agent,  $cookie_life_time);

					$sqlXX = "INSERT INTO g5_visit_sum (vs_date, vs_count) VALUES ('".G5_TIME_YMD."', 1) ON DUPLICATE KEY UPDATE vs_date='".G5_TIME_YMD."', vs_count = vs_count + 1";
					sql_query($sqlXX);

					// INSERT, UPDATE 된건이 있다면 기본환경설정 테이블에 저장
					// 방문객 접속시마다 따로 쿼리를 하지 않기 위함 (엄청난 쿼리를 줄임 ^^)

					// 오늘
					$row = sql_fetch("SELECT vs_count AS cnt FROM g5_visit_sum WHERE vs_date = '".G5_TIME_YMD."' ");
					$vi_today = $row['cnt'];

					// 어제
					$row = sql_fetch("SELECT vs_count AS cnt FROM g5_visit_sum WHERE vs_date = DATE_SUB('".G5_TIME_YMD."', INTERVAL 1 DAY)");
					$vi_yesterday = $row['cnt'];

					// 최대
					$row = sql_fetch("SELECT MAX(vs_count) AS cnt FROM g5_visit_sum");
					$vi_max = $row['cnt'];

					// 전체
					$row = sql_fetch("SELECT SUM(vs_count) AS total FROM g5_visit_sum");
					$vi_sum = $row['total'];

					$visit = '오늘:'.$vi_today.',어제:'.$vi_yesterday.',최대:'.$vi_max.',전체:'.$vi_sum;

					// 기본설정 테이블에 방문자수를 기록한 후
					// 방문자수 테이블을 읽지 않고 출력한다.
					// 쿼리의 수를 상당부분 줄임
					sql_query("UPDATE g5_config SET cf_visit = '{$visit}'");

			}

		}

	}

}

?>