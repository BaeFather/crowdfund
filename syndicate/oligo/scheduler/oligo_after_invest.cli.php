#!/usr/local/php/bin/php -q
<?
################################################################
## 올리고 대기투자건 제한시간 이후 무효처리
## /usr/local/php/bin/php -q /home/crowdfund/public_html/syndicate/oligo/scheduler/oligo_after_invest.cli.php yes
################################################################

exit;			// 2022-03-05 exit 처리

set_time_limit(0);


define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', true);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/data/dbconfig.php');
include_once($path . '/lib/common.lib.php');

$action = (@$_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : 'debug';

$syndi_id = "oligo";
$limit_second = 600;		// 10분내
$syndi_base_path = $path . "/syndicate/oligo";


$x = true;

while($x > 0) {

	if($action=='debug') { echo ("[" . date('Y-m-d H:i:s') . "]\n"); }

	//---------------------------------------------------------------------------
	$connect_db = sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD) or die('MySQL Connect Error!!!');
	$select_db  = sql_select_db(G5_MYSQL_DB, $connect_db) or die('MySQL DB Error!!!');
	sql_set_charset('utf8', $connect_db);
	$g5['connect_db'] = $connect_db;
	//---------------------------------------------------------------------------


	$range_sdate  = date("Y-m-d H:i:s", time()-$limit_second);
	$range_edate  = date("Y-m-d H:i:s");

	$CDT = explode(" ", $range_sdate);
	$cdt = $CDT[0];
	$ctm = $CDT[1];

	// ▼ 제한 시간범위 이전에 투자된 내역 중 대기중인(미입금인) 투자내역 취소 처리 ▼ //
	$sql0 = "
		SELECT
			idx, product_idx
		FROM
			cf_product_invest
		WHERE 1
			AND invest_state = 'W'
			AND insert_date = '".$cdt."' AND LEFT(insert_time,5)<'".substr($ctm,0,5)."'
			AND syndi_id='".$syndi_id."'
		ORDER BY
			idx";

	if($action=='debug') { echo ($sql0.";\n"); }
	$res0 = sql_query($sql0);

	while( $DRAW_INVEST = sql_fetch_array($res0) ) {

		$sqlA = "
			UPDATE
				cf_product_invest
			SET
				 invest_state = 'N'
				,cancel_by = 'system'
				,cancel_date = NOW()
				,memo = '투자 예치금 미입금'
			WHERE 1
				AND idx = '".$DRAW_INVEST['idx']."'";

		$sqlB = "
			UPDATE
				cf_product_invest_detail
			SET
				 invest_state = 'N'
				,cancel_date = NOW()
			WHERE 1
				AND invest_idx = '".$DRAW_INVEST['idx']."'";

		if($action=='yes') {
			sql_query($sqlA);
			sql_query($sqlB);

			// 올리고에 결과 전송 --------------------
			@shell_exec("/usr/local/php/bin/php -q " . $syndi_base_path . "/report/investResultReport.php " . $DRAW_INVEST['idx']);
			// ---------------------------------------
		}
		else {
			echo $sqlA . "\n";
			echo $sqlB . "\n";
		}

	}
	// ▲ 제한 시간범위 이전에 투자된 내역 중 대기중인(미입금인) 투자내역 취소 처리 ▲ //


	// 제한시간내의 투자내역중 투자금입금확인이 되지 않은 투자건 추출
	$sql = "
		SELECT
			A.idx, A.amount, A.member_idx, A.product_idx, A.insert_datetime,
			(SELECT mb_id FROM g5_member WHERE mb_no=A.member_idx) AS mb_id
		FROM
			cf_product_invest A
		WHERE 1
			AND A.invest_state = 'W'
			AND A.syndi_id = '".$syndi_id."'
			AND LEFT(A.insert_datetime,16) >= '".substr($range_sdate,0,16)."'
		ORDER BY
			idx ASC";
	if($action=='debug') { echo ($sql.";\n"); }
	$res  = sql_query($sql);
	$rows = $res->num_rows;
	for($i=0; $i<$rows; $i++) {

		if( $INVEST = sql_fetch_array($res) ) {

			// 현재포인트 호출
			$POINT = sql_fetch("SELECT po_mb_point FROM g5_point WHERE mb_no='".$INVEST['member_idx']."' ORDER BY po_datetime DESC, po_id DESC LIMIT 1");
			$INVEST['mb_point'] = $POINT['po_mb_point'];

			if($action=='debug') { print_r($INVEST); }

			$prdt_sql = "
				SELECT
					A.idx, A.state, A.category, A.mortgage_guarantees, A.title, A.recruit_amount, A.open_datetime, A.start_datetime, A.end_datetime, A.invest_end_date,
					A.advance_invest, A.advance_invest_ratio,
					A.live_invest_amount AS total_invest_amt
				FROM
					cf_product A
				WHERE 1
					AND A.idx = '".$INVEST['product_idx']."'";
			$PRDT = sql_fetch($prdt_sql);
			if($action=='debug') { print_r($PRDT); }

			if($PRDT['state'] || $PRDT['invest_end_date'] || $PRDT['end_datetime'] < date('Y-m-d H:i:s') || $PRDT['recruit_amount']<=$PRDT['total_invest_amt']) {
				$memo = "취소사유:모집완료된 투자상품임";

				$sqlA  = "
					UPDATE
						cf_product_invest
					SET
						 invest_state = 'N'
						,cancel_by = 'system'
						,cancel_datetime = NOW()
						,memo = '".$memo."'
					WHERE
						idx = '".$INVEST['idx']."'";

				$sqlB = "
					UPDATE
						cf_product_invest_detail
					SET
						 invest_state = 'N'
						,cancel_datetime = NOW()
					WHERE
						invest_idx = '".$INVEST['idx']."'";

				if($action=='yes') {
					sql_query($sqlA);
					sql_query($sqlB);

					// 올리고에 결과 전송 --------------------
					@shell_exec("/usr/local/php/bin/php -q " . $syndi_base_path . "/report/investResultReport.php " . $INVEST['idx']);
					// ---------------------------------------
				}
				else {
					echo $sqlA . "\n";
					echo $sqlB . "\n";
				}

			}
			else {

				if($INVEST['mb_point'] >= $INVEST['amount']) {

					///////////////////////////////////
					// 투자내역 등록
					///////////////////////////////////
					$input_datetime = date('Y-m-d H:i:s');
					$IDT = explode(" ", $input_datetime);
					$idt = $IDT[0];
					$itm = $IDT[1];

					// 헬로펀딩 예치금이 충분하면 투자내역 직접 등록
					$sqlA = "UPDATE cf_product_invest SET invest_state='Y' WHERE idx='".$INVEST['idx']."'";
					$sqlB = "UPDATE cf_product_invest_detail SET invest_state='Y' WHERE invest_idx='".$INVEST['idx']."'";
					if($action=='yes') {
						sql_query($sqlA);
						sql_query($sqlB);

						// 올리고에 결과 전송 --------------------
						@shell_exec("/usr/local/php/bin/php -q " . $syndi_base_path . "/report/investResultReport.php " . $INVEST['idx']);
						@shell_exec("/usr/local/php/bin/php -q " . $syndi_base_path . "/report/productStateReport.php " . $INVEST['product_idx']);
						// ---------------------------------------
					}
					else {
						echo $sqlA . "\n";
						echo $sqlB . "\n";
					}

					// 포인트 차감
					$po_content = $PRDT['title']. "-투자(".$syndi_id.")";
					if($action=='yes') {

						insert_point($INVEST['mb_id'], $INVEST['amount'] * (-1), $po_content, '@invest', $INVEST['mb_id'], $INVEST['mb_id'].'-'.uniqid(''), 0);

						// 상품관리테이블에 실시간 모집금액 반영하기 :: 2021-02-15 추가
						sql_query("UPDATE cf_product SET live_invest_amount = live_invest_amount + {$INVEST['amount']} WHERE idx = '".$PRDT['idx']."'");
					}
					else {
						echo "insert_point({$INVEST['mb_id']}, {$INVEST['amount']} * (-1), {$po_content}, '@invest', {$INVEST['mb_id']}, {$INVEST['mb_id']}-".uniqid('').", 0);\n";
					}

					// 투자금 모집완료시 투자종료일 표기. 투자마무리
					if( $PRDT['recruit_amount'] <= ($PRDT['total_invest_amt'] + $INVEST['amount']) ) {
						$sqlx = "UPDATE cf_product SET invest_end_date='".$input_dt."' WHERE idx='".$INVEST['product_idx']."'";
						if($action=='yes') {
							sql_query($sqlx);
						}
						else {
							echo $sqlx . "\n";
						}
					}

				}

			}

		}

	}		// end for

	//if($action!='yes') { echo date('YmdHis') . " - g5: "; echo "(proxy:".$use_proxy.")"; print_r($g5); echo "\n"; }

	sql_free_result($res);

	sql_close($connect_db);

	sleep(10);

}

?>