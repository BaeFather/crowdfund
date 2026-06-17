<?
include_once("_common.php");

while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

function getProductStats($prd_idx) {

	if(!$prd_idx) return;

	$sql = "
		SELECT
			A.state, A.title, A.recruit_period_start, A.recruit_period_end, A.recruit_amount, A.open_datetime, A.start_datetime, A.end_datetime, A.invest_end_date,
			(SELECT COUNT(idx)  FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_count,
			(SELECT SUM(amount) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_amount
		FROM
			cf_product A
		WHERE
			idx='$prd_idx'";

	//$RESULT = array();

	if( $PRDT = sql_fetch($sql) ) {
		echo "<pre style='font-size:9pt'>"; print_r($PRDT); echo "</pre>";

		$nowdate = date('Y-m-d H:i:s');

		if($PRDT['state']) {

			if($PRDT['state']=='1')      $RESULT = array('code'=>'A01', 'state_str'=>'이자상환중');
			else if($PRDT['state']=='2') $RESULT = array('code'=>'A02', 'state_str'=>'상품마감');
			else if($PRDT['state']=='3') $RESULT = array('code'=>'A03', 'state_str'=>'투자모집실패');
			else if($PRDT['state']=='4') $RESULT = array('code'=>'A04', 'state_str'=>'부실');
			else if($PRDT['state']=='5') $RESULT = array('code'=>'A05', 'state_str'=>'중도일시상환');
			else if($PRDT['state']=='6') $RESULT = array('code'=>'A06', 'state_str'=>'상환완료');
		}
		else {
			// 확정된 투자진행상태값이 없을 경우 투자만료기록일 시점의 상태를 반환한다.
			if($PRDT['invest_end_date']!='') {
				if($PRDT['recruit_amount'] > $PRDT['invest_amount']) {
					$RESULT = array('code'=>'B04', 'state_str'=>'투자모집실패');
				}
				else {
					$RESULT = array('code'=>'B03', 'state_str'=>'투자모집완료');
				}
			}
			else {
				if($PRDT['start_datetime'] > $nowdate) {
					$RESULT = array('code'=>'B01', 'state_str'=>'투자대기중');
				}
				else if($PRDT['start_datetime'] <= $nowdate && $PRDT['end_datetime'] >= $nowdate) {
					if($PRDT['recruit_amount'] > $PRDT['invest_amount']) {
						$RESULT = array('code'=>'B02', 'state_str'=>'투자모집중');
					}
					else {
						$RESULT = array('code'=>'B03', 'state_str'=>'투자모집완료');
					}
				}
				else if($PRDT['end_datetime'] < $nowdate) {
					if($PRDT['recruit_amount'] > $PRDT['invest_amount']) {
						$RESULT = array('code'=>'B04', 'state_str'=>'투자모집실패');
					}
					else {
						$RESULT = array('code'=>'B03', 'state_str'=>'투자모집완료');
					}
				}
				else {
					$RESULT = array('code'=>'B00', 'state_str'=>'준비중');
				}
			}

		}

		$RESULT['title']          = $PRDT['title'];
		$RESULT['recruit_amount'] = $PRDT['recruit_amount'];
		$RESULT['invest_count']   = $PRDT['invest_count'];
		$RESULT['invest_amount']  = $PRDT['invest_amount'];

		return $RESULT;

	}
	else {
		return 0;
	}

}

$PRDRES = getProductStats($prd_idx);

echo "<pre style='font-size:9pt'>"; print_r($PRDRES); echo "</pre>";

?>