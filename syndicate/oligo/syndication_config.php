<?

//header("HTTP/1.0 404 Not Found");
//die();


@set_time_limit(0);

define('SERVER_TIME', time());
define('DATE_YMDHIS', date('Y-m-d H:i:s', SERVER_TIME));
define('DATE_YMD',    substr(DATE_YMDHIS, 0, 10));
define('DATE_HIS',    substr(DATE_YMDHIS, 11, 8));

$base_path = "/home/crowdfund/public_html";
$syndi_base_path = $base_path . "/syndicate/oligo";

if( preg_match("/\/web\//", $_SERVER['PHP_SELF']) ) {
	$common_file = $base_path . "/common.php";
}
else {
	$common_file = $base_path . "/common.cli.php";
}

include_once($common_file);
include_once($syndi_base_path . "/lib/local_common.lib.php");
include_once($syndi_base_path . "/lib/oligo_crypt.lib.php");			// 올리고용 암복호화(AES256) 모듈 호출
include_once($base_path . "/lib/crypt.lib.php");

$crypto = new CryptoCBC();

$_CONF['host_domain']   = "https://www.hellofunding.co.kr";
$_CONF['domain_for_syndi'] = "https://oligo.hellofunding.co.kr";
$_CONF['SYNDI_TITLE']   = "올리고";
$_CONF['SYNDI_ID']      = "oligo";
$_CONF['SYNDI_ID_AS']   = "apro";
$_CONF['SYNDI_PW']      = "oligo!@#";
$_CONF['comp_cd']       = "CP-2da586e964b4472e8ec65a7cf6f1b5df";		// 헬로펀딩 업체코드 (아프로에서 발급함)
$_CONF['crypt_key']     = "AproSystem123$%^";												// 암호화 방식 : AES256

$_CONF['syndi_url']     = "https://m.mycereal.co.kr:8443";					// 상용서버
//$_CONF['syndi_url']     = "https://dm.mycereal.co.kr:8443";					// 테스트서버
$CONF['oligo_report_url'] = $_CONF['syndi_url'];


$_CONF['overdue_rate']    = 24;			// 기본 연체이자(연리 %)
$_CONF['fee']             = 1.2;		// 기본 플랫폼이용료(연간 %)
$_CONF['syndication_fee'] = 0.8;		// 신디케이션 수수료(투자금액대비)

$_CONF['min_invest_limit']    = $CONF['min_invest_limit'];


if( preg_match("/(\/api\/|\/scheduler\/|\/report\/)/i", $_SERVER['PHP_SELF']) ) {

	$LOG['table']  = "oligo_request_data_log_" . date('Ym');		// 일반데이터 요청 로그
	$LOG['table2'] = "oligo_send_report_log_" . date('Ym');

	$res = mysqli_query($g5['connect_db'], "SHOW TABLES LIKE '{$LOG['table']}'");
	$row = mysqli_fetch_row($res);
	if(!$row[0]) {
		$create_sql1 = "
			CREATE TABLE `{$LOG['table']}` (
				`idx` INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ip` VARCHAR(20) NOT NULL DEFAULT '',
				`title` VARCHAR(50) NOT NULL DEFAULT '',
				`path` VARCHAR(150) NOT NULL DEFAULT '',
				`input` TEXT NULL,
				`output` TEXT NULL,
				`rdate` DATETIME NULL DEFAULT NULL,
				`edate` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`idx`),
				INDEX `ip` (`ip`),
				INDEX `path` (`path`),
				INDEX `rdate` (`rdate`)
			)
			COMMENT='올리고 TO 헬로 자료요청 로그'
			COLLATE='utf8_general_ci'
			ENGINE=InnoDB
			ROW_FORMAT=DYNAMIC";
		mysqli_query($g5['connect_db'], $create_sql1);
	}
	$res = $row = NULL;

	$res = mysqli_query($g5['connect_db'], "SHOW tables LIKE '{$LOG['table2']}'");
	$row = mysqli_fetch_row($res);
	if(!$row[0]) {
		$create_sql2 = "
			CREATE TABLE `{$LOG['table2']}` (
				`idx` INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ip` VARCHAR(20) NOT NULL DEFAULT '',
				`title` VARCHAR(50) NOT NULL DEFAULT '',
				`path` VARCHAR(150) NOT NULL DEFAULT '',
				`input` TEXT NULL,
				`output` TEXT NULL,
				`rdate` DATETIME NULL DEFAULT NULL,
				`edate` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`idx`),
				INDEX `ip` (`ip`),
				INDEX `path` (`path`),
				INDEX `rdate` (`rdate`)
			)
			COMMENT='헬로 TO 올리고 자료발송 로그'
			COLLATE='utf8_general_ci'
			ENGINE=InnoDB
			ROW_FORMAT=DYNAMIC";
		mysqli_query($g5['connect_db'], $create_sql2);
	}
	$res = $row = NULL;


	if( preg_match("/checkJoin\.do/i", $_SERVER['REQUEST_URI']) )                $LOG['title'] = "가입여부체크";
	else if( preg_match("/checkJuminNo\.do/i", $_SERVER['REQUEST_URI']) )        $LOG['title'] = "주민번호체크";
	else if( preg_match("/termsList\.do/i", $_SERVER['REQUEST_URI']) )           $LOG['title'] = "약관보기";
	else if( preg_match("/join\.do/i", $_SERVER['REQUEST_URI']) )                $LOG['title'] = "회원가입";
	else if( preg_match("/invest\.do/i", $_SERVER['REQUEST_URI']) )              $LOG['title'] = "투자하기";
	else if( preg_match("/investEnd\.do/i", $_SERVER['REQUEST_URI']) )           $LOG['title'] = "투자요청완료";
	else if( preg_match("/investStat\.do/i", $_SERVER['REQUEST_URI']) )          $LOG['title'] = "투자현황";
	else if( preg_match("/withdrawRequest\.do/i", $_SERVER['REQUEST_URI']) )     $LOG['title'] = "출금요청";
	else if( preg_match("/amountHistory\.do/i", $_SERVER['REQUEST_URI']) )       $LOG['title'] = "투자금거래내역";
	else if( preg_match("/repayHistory\.do/i", $_SERVER['REQUEST_URI']) )        $LOG['title'] = "상환내역";
	else if( preg_match("/spotImg\.do/i", $_SERVER['REQUEST_URI']) )             $LOG['title'] = "현장실사";
	else if( preg_match("/investCancel\.do/i", $_SERVER['REQUEST_URI']) )        $LOG['title'] = "투자취소";
	else if( preg_match("/balanceCheck\.do/i", $_SERVER['REQUEST_URI']) )        $LOG['title'] = "예치금잔액조회";
	else if( preg_match("/autoInveCheckHello\.do/i", $_SERVER['REQUEST_URI']) )  $LOG['title'] = "자동투자조회";
	else if( preg_match("/autoInveReqHello\.do/i", $_SERVER['REQUEST_URI']) )    $LOG['title'] = "자동투자신청";


	header("Content-Type:application/json; charset=UTF-8");

}

?>