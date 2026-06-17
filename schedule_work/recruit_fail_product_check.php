#!/usr/local/php/bin/php -c /etc/php.ini -q
<?
###############################################################################
## 투자모집실패상품 상태값 변경 및 투자금 반환 처리
##
## * * * * * /usr/local/php/bin/php -q /home/crowdfund/schedule_work/recruit_fail_product_check.php yes
###############################################################################

set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', true);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/common.cli.php');
//include_once($path . '/data/dbconfig.php');
//include_once($path . '/lib/common.lib.php');

$action = (@$_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : 'debug';

$target_end_datetime = date('Y-m-d H:i');
//$target_end_datetime = "2018-06-17 18:00";

$sql = "
	SELECT
		A.idx, A.state, A.title, A.recruit_amount, A.end_datetime, A.invest_end_date,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS total_invest_count,
		(SELECT IFNULL(SUM(amount), 0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS total_invest_amount
	FROM
		cf_product A
	WHERE 1
		AND display='Y'
		AND state=''
		AND LEFT(end_datetime, 16)='".$target_end_datetime."'
	ORDER BY
		A.end_datetime";
$res = sql_query($sql);
$rows = sql_num_rows($res);

if($rows) {
	for($i=0; $i<$rows; $i++) {
		$PLIST[$i] = sql_fetch_array($res);


		if($PLIST[$i]['recruit_amount'] > $PLIST[$i]['total_invest_amount']) {

			$sql2 = "
				SELECT
					idx, amount, member_idx, product_idx
				FROM
					cf_product_invest
				WHERE 1
					AND product_idx='".$PLIST[$i]['idx']."'
					AND invest_state='Y'
				ORDER BY
					idx";
			$res2 = sql_query($sql2);
			while( $INVEST = sql_fetch_array($res2) ) {

				//print_r($INVEST);

				if($INVEST['idx']) {
					// 투자 취소 처리
					$sql3 = "UPDATE cf_product_invest SET invest_state='N' WHERE idx='".$INVEST['idx']."'";
					if($action=='debug') { echo "sql3 : " . $sql3 . "\n"; }

					$sql4 = "UPDATE cf_product_invest_detail SET invest_state='N' WHERE invest_idx='".$INVEST['idx']."'";
					if($action=='debug') { echo "sql4 : " . $sql4 . "\n"; }

					if($action=='yes') {
						sql_query($sql3);
						sql_query($sql4);
					}


					// 예치금 반환 처리
					$po_content = $PLIST[$i]['title']. '-투자 취소';

					//echo "SELECT mb_id FROM g5_member WHERE mb_no='".$INVEST['member_idx']."'\n";
					$MB = sql_fetch("SELECT mb_id FROM g5_member WHERE mb_no='".$INVEST['member_idx']."'");
					if($MB['mb_id']) {
						if($action=='yes') {
							insert_point($MB['mb_id'], $INVEST['amount'], $po_content, '@cancel', $MB['mb_id'], $MB['mb_id'].'-'.uniqid(''), 0);
						}
						else {
							echo "포인트반환 실행 : insert_point(".$MB['mb_id'].", ".$INVEST['amount'].", ".$po_content.", @cancel, ".$MB['mb_id'].", ".$MB['mb_id'].'-'.uniqid('').", 0);\n";
						}
					}

				}

				if($action=='debug') echo "----------------------------------------------------------------------------------------------\n";

			}		// end while

			// 상품 상태값 변경
			$sql5 = "UPDATE cf_product SET state='3' WHERE idx='".$PLIST[$i]['idx']."'";
			if($action=='yes') {
				sql_query($sql5);
			}
			else {
				echo "sql5 : " . $sql5 . "\n\n";
			}

		}

	}		// end for
}

if($action=='debug') echo "finish\n";

exit;

?>