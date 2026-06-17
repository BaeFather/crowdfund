<?php

///////////////////////////////////////////////////////////
// 페이지로딩타임 로그 생성
//  로그등록 :  LoadingLogRegist() => $log_idx 리턴
//  로그마감 :  LoadingLogRegist($log_idx, $thrSec) => 소요시간 기록 및 로그 종료
///////////////////////////////////////////////////////////
function LoadingLogRegist($log_idx='', $ip ='', $path = '', $thrSec='') {

	global $g5;

	$logTable = "cf_loading_time_log";

	$tres = sql_query("SHOW TABLES LIKE '$logTable'", true);
	if( $tres->num_rows == 0 ) {
		$tresx = sql_query("
			CREATE TABLE `{$logTable}` (
				`idx` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ip` VARCHAR(15) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`path` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`sdt` DATETIME NULL DEFAULT NULL,
				`edt` DATETIME NULL DEFAULT NULL,
				`thrSec` FLOAT(8,4) UNSIGNED NOT NULL DEFAULT '0.0000',
				PRIMARY KEY (`idx`) USING HASH,
				INDEX `ip` (`ip`) USING HASH
			)
			COLLATE='utf8_general_ci'
			ENGINE=MEMORY
			ROW_FORMAT=DYNAMIC");
	}

	if($log_idx=='') {

		$sql = "
			INSERT INTO
				{$logTable}
			SET
				sdt = NOW(),
				ip = '".$ip."',
				path = '".$path."'";

		if( $res = sql_query($sql, true) ) {
			$log_idx = sql_insert_id();
			return $log_idx;
		}
		else {
			return '';
		}

	}
	else {

		$sql = "
			UPDATE
				{$logTable}
			SET
				edt = NOW(),
				thrSec = '".$thrSec."'
			WHERE
				idx = '".$log_idx."'";

		if( $res = sql_query($sql, true) ) {
			return 1;
		}
		else {
			return 0;
		}

	}

}

?>