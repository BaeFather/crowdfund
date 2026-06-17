#!/usr/local/php/bin/php -q
<?
## /usr/local/php/bin/php -q /home/crowdfund/public_html/shinhan/test.php

set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', false);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/data/dbconfig.php');
include_once($path . '/data/sms_dbconfig.php');
include_once($path . '/lib/common.lib.php');
include_once($path . '/lib/sms.lib.php');

// config.php 를 로드하지 않으므로 고정설정항목을 임시로 지정한다.
$CONF['admin_sms_number'] = '15886760';

// 서버비상상황 일때 문자알림 수신번호 설정
$CONF['event_receive_phone'] = array(
	'01064063972',
	//'01086246176',
	//'01088944740',
	//'01067241409',
	//'01043380580'
);

	//---------------------------------------------------------------------------
	$link = @sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD, G5_MYSQL_DB);
	@sql_set_charset("UTF8", $link);
	//---------------------------------------------------------------------------


$i = 0;

$LIST[$i]['mb_id']       = 'test';
$LIST[$i]['mb_name']     = '김테스트';
$LIST[$i]['REMITTER_NM'] = '차명차명';
$LIST[$i]['TR_AMT']      = '1';


	//---------------------------------------------------------------------------
	$link3 = @sql_connect(G5_MYSQL_HOST3, G5_MYSQL_USER3, G5_MYSQL_PASSWORD3, G5_MYSQL_DB3);
	@sql_set_charset('UTF8', $link3);
	//---------------------------------------------------------------------------

					$sms_contents = $LIST[$i]['mb_id'] . "(" . $LIST[$i]['mb_name'] . ") 가상계좌\n" .
													"입금:" . $LIST[$i]['REMITTER_NM'] . "\n" .
													"금액:" . number_format((int)$LIST[$i]['TR_AMT']) . "원";

					// 문자발송
					$sms_send_count = 0;
					for($k=0; $k<count($CONF['event_receive_phone']); $k++) {
						unit_sms_send_smtnt($CONF['admin_sms_number'], $CONF['event_receive_phone'][$k], $sms_contents);
						$sms_send_count += 1;
					}

sql_close($link3);



sql_close($link);
exit;

?>