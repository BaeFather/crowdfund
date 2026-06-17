<?

// json 출력 및 로그 기록
function printJson($array) {

	global $inputJSON, $REQUEST, $LOG;

	$json  = json_encode($array, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

	/*
	if( preg_match("/(\/api\/product\/detail|\/api\/invest-info\/detail)/i", $_SERVER['REQUEST_URI']) ) {
		sql_query("DELETE FROM {$LOG['table']} WHERE rdate <= '".date("Y-m-d H:i:s", strtotime("-2 day"))."'");
	}
	*/

	if($LOG['idx']) {

		$update_sql = "
			UPDATE
				{$LOG['table']}
			SET
				output = '".sql_real_escape_string($json)."',
				edate = SYSDATE()
			WHERE
				idx = '".$LOG['idx']."'";

		sql_query($update_sql);

	}

	return $json;

}


// 사이닝(SHA256) ::: $array['data'] 내용의 해시값을 이용
function resultSignature($array) {

	global $_CONF;

	$json = json_encode($array, JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);
	$data = str_f6($json, "\"data\":", ",\"hello\":");
	//echo "\n\n".$data."\n\n";
	$signature = base64_encode(hash("SHA256", $data, true));					// 암호화
	openssl_sign($data, $signature, $_CONF['rstPriKey'], "SHA256");		// 사이닝

	return base64_encode($signature);

}


// 정수형 밀리초 가져오기
function milliseconds() {
	$mt = explode(' ', microtime());
	return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
}


// 투자자유형별 상품 투자가능금액
function getInvestAbleAmountOligo($product_idx, $member_idx) {

	global $_CONF;
	global $INDI_INVESTOR;

	if(!$member_idx) return 0;

	$member = sql_fetch("SELECT mb_no, mb_id, mb_level, member_group, member_type, member_investor_type FROM g5_member WHERE mb_no='".$member_idx."'");
	if(!$member['mb_no'] || $member['mb_level']!='1' || $member['member_group']!='F') return 0;

	//$VALUE[] = (int)get_point_sum($member['mb_id']);		// 비교인자1 : 본인소유예치금

	$PRDT = sql_fetch("
		SELECT
			A.idx, A.gr_idx, A.ai_grp_idx, A.state, A.category,
			( A.recruit_amount - (SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') ) AS recruit_need_amount
		FROM
			cf_product A
		WHERE
			A.idx='".$product_idx."'");

	$VALUE[] = (int)$PRDT['recruit_need_amount'];				// 비교인자2 : 상품모집잔액

	// 개인회원(일반투자자,소득적격투자자) 조건별 투자가능금액 가져오기
	if( $member['member_type']=='1' && in_array($member['member_investor_type'], array('1','2')) ) {

		// A: 전체 투자금액 추출
		$INVESTING = sql_fetch("
			SELECT
				IFNULL(SUM(amount),0) AS amount
			FROM
				cf_product_invest A
			LEFT JOIN
				cf_product B  ON A.product_idx=B.idx
			WHERE (1)
				AND A.member_idx='".$member_idx."' AND A.invest_state='Y'
				AND B.state='1'");

		$BALANCE[] = $INDI_INVESTOR[$member['member_investor_type']]['site_limit'] - $INVESTING['amount'];								// 사이트 투자한도 잔여금액 = 사이트투자한도금액 - 현재상환중인투자금액

		// B: 동일차주상품 투자금액 추출
		$GRP_INVEST = sql_fetch("
			SELECT
				IFNULL(SUM(amount),0) AS amount
			FROM
				cf_product_invest A
			LEFT JOIN
				cf_product B  ON A.product_idx=B.idx
			WHERE (1)
				AND A.member_idx='".$member_idx."' AND A.invest_state='Y'
				AND B.state='1' AND B.gr_idx='".$PRDT['gr_idx']."'");

		$BALANCE[] = $INDI_INVESTOR[$member['member_investor_type']]['group_product_limit'] - $GRP_INVEST['amount'];			// 동일차주상품 투자한도 잔여금액 = 동일차주 상품투자한도금액 - 동일차주상품에 투자중인 금액

		// C: 부동산상품이며, 개인-일반투자자인 경우 부동산 투자가능 금액 추출
		if($member['member_investor_type']=='1' && $PRDT['category']=='2') {
			$CA_INVEST = sql_fetch("
				SELECT
					IFNULL(SUM(amount),0) AS amount
				FROM
					cf_product_invest A
				LEFT JOIN
					cf_product B  ON A.product_idx=B.idx
				WHERE (1)
					AND A.member_idx='".$member_idx."' AND A.invest_state='Y'
					AND B.state='1' AND B.category='2'");

			$BALANCE[] = $INDI_INVESTOR[$member['member_investor_type']]['prpt_limit'] - $CA_INVEST['amount'];							// 부동산 투자한도 잔여금액
		}

		$VALUE[] = (int)min($BALANCE);				// 비교인자3 : A, B, C중 최소액

	}

	/*
	echo "<pre style='font-size:12px'>";
	echo "\nVALUE : "; print_r($VALUE);
	echo "\n\nBALANCE : "; print_r($BALANCE);
	echo "</pre>";
	*/

	$min_value = floor(min($VALUE) / 10000) * 10000;		// 만원미만절사

	return $min_value;

}


//function getProductStatusCode($prd_idx) {
function getProductStatOligo($prd_idx) {

	/*
		올리고 상태값 규칙
			01:모집예정
			02:모집중
			03:모집취소
			04:모집완료
			05:상환중
			06:상환완료
			07:상환지연
			08:연체중(단기)
			09:연체중(장기)
			10:부도
			11:상환완료(연체)
	*/

	if(!$prd_idx) return;

	$sql = "
		SELECT
			A.state, A.title, A.recruit_amount,
			A.recruit_period_start, A.recruit_period_end, A.open_datetime, A.start_datetime, A.end_datetime, A.invest_end_date,
			A.loan_end_date,
			A.advance_invest, A.advance_invest_ratio
		FROM
			cf_product A
		WHERE
			idx='".$prd_idx."'";
	if( $PRDT = sql_fetch($sql) ) {

		$RESULT = array(
			'code' => '',
			'code_str' => '',
			'advence_invest_ing' => '',
			'state' => '',
			'title' => ''
		);

		$nowdate = date('Y-m-d H:i:s');

		if($PRDT['state']) {
			if($PRDT['state']=='1') {
				$RESULT['code'] = '05';			// 이자상환중
			}
			else if($PRDT['state']=='2') {
				$RESULT['code'] = '06';			// 상환완료
			}
			else if($PRDT['state']=='3') {
				$RESULT['code'] = '03';			// 모집취소 (모집실패)
			}
			else if($PRDT['state']=='4') {
				$RESULT['code'] = '10';			// 부도(부실)
			}
			else if($PRDT['state']=='5') {
				$RESULT['code'] = '06';			// 상환완료 (중도상환완료)
			}
			else if($PRDT['state']=='6') {
				$RESULT['code'] = '03';			// 모집취소 (기표전대출취소)
			}
			else if($PRDT['state']=='7') {
				$RESULT['code'] = '03';			// 모집취소 (기표후대출취소)
			}
			else if($PRDT['state']=='8') {
				if( date('Y-m-d') > strtotime("first day of ".$PRDT['loan_end_date']." +1 months") ) {
					$RESULT['code'] = '09';			// 장기연체 (대출종료일 이후 1개월을 초과한 연체건)
				}
				else {
					$RESULT['code'] = '08';			// 단기연체
				}
			}
			else if($PRDT['state']=='9') {
				$RESULT['code'] = '10';			// 부도
			}
		}
		else {

			$INVEST = sql_fetch("SELECT IFNULL(SUM(amount),0) AS total_amount FROM cf_product_invest WHERE product_idx='".$prd_idx."' AND invest_state='Y'");		// 투자금 합계

			/////////////////////////////////////////////////////
			// 투자종료플래그(invest_end_date) 가 없을 경우
			// 투자기간, 투자만료기록일 시점의 상태를 반환한다.
			/////////////////////////////////////////////////////
			if($PRDT['invest_end_date']=='') {

				/////////////////
				// 모집기간 전
				/////////////////
				if($PRDT['start_datetime'] > $nowdate) {

					$RESULT['code'] = '01';			// 모집예정 (투자대기중)

					// 사전투자상품 -> 사전투자금이 다 모이지 않았을 경우
					if($PRDT['advance_invest']=='Y' && $PRDT['star_datetime'] <= $nowdate) {

						$advance_invest_amount = $PRDT['recruit_amount'] * $PRDT['advance_invest_ratio'] / 100;

						if($advance_invest_amount > $INVEST['total_amount']) {
							$RESULT['advence_invest_ing'] = 'Y';
						}

					}

				}

				/////////////////
				// 모집기간 중
				/////////////////
				else if($PRDT['start_datetime'] <= $nowdate && $PRDT['end_datetime'] >= $nowdate) {

					if($PRDT['recruit_amount'] > $INVEST['total_amount']) {
						$RESULT['code'] = '02';			// 투자모집중
					}
					else {
						$RESULT['code'] = '04';			// 투자모집완료
					}

				}

				/////////////////
				// 모집기간 후
				/////////////////
				else if($PRDT['end_datetime'] < $nowdate) {

					if($PRDT['recruit_amount'] <= $INVEST['total_amount']) {
						$RESULT['code'] = '04';			// 투자모집완료
					}
					else {
						$RESULT['code'] = '03';			// 투자모집실패
					}

				}

				/////////////////////////////////////////
				// 그 외, 모집기간이 설정되지 않은 경우
				/////////////////////////////////////////
				else {

					$RESULT['code'] = '';

				}

			}

			/////////////////////////////////////////////////////
			// 투자종료플래그(invest_end_date) 가 있을 경우
			/////////////////////////////////////////////////////
			else {

				$INVEST = sql_fetch("SELECT IFNULL(SUM(amount),0) AS total_amount FROM cf_product_invest WHERE product_idx='".$prd_idx."' AND invest_state='Y'");		// 투자금 합계 다시 호출

				if($PRDT['recruit_amount'] <= $INVEST['total_amount']) {
					$RESULT['code'] = '04';			// 투자모집완료
				}
				else {
					$RESULT['code'] = '03';			// 투자모집실패
				}

			}

		}

		return $RESULT['code'];

	}
	else {
		return 0;
	}

}

function memberCheck($ci) {

	global $_CONF;

	$row = sql_fetch("
		SELECT
			mb_id
		FROM
			g5_member
		WHERE 1
			AND member_group='F'
			AND mb_level='1'
			AND oligo_userid='".$_CONF['SYNDI_ID']."'
			AND mb_ci='".$ci."'");

	$value = $row['mb_id'];

	return $value;

}


?>