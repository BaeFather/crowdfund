<?

if ( !preg_match("/(183\.98\.101\.|172\.17\.3\.|172\.19\.3\.)/", $_SERVER['REMOTE_ADDR']) ) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include_once("_common.php");

// 본인소유예치금기준 투자가능금액
function getInvestAbleAmount($product_idx, $member_idx) {

	global $_CONF;
	global $INDI_INVESTOR;

	if(!$member_idx) return 0;

	$member = sql_fetch("SELECT mb_no, mb_id, mb_level, member_group, member_type, member_investor_type FROM g5_member WHERE mb_no='".$member_idx."'");
	if(!$member['mb_no'] || $member['mb_level']!='1' || $member['member_group']!='F') return 0;


	$VALUE[] = (int)get_point_sum($member['mb_id']);		// 비교인자1 : 본인소유예치금

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

	echo "<pre style='font-size:12px'>";
	echo "\nVALUE : "; print_r($VALUE);
	echo "\n\nBALANCE : "; print_r($BALANCE);
	echo "</pre>";

	$min_value = floor(min($VALUE) / 10000) * 10000;		// 만원미만절사

	return $min_value;

}


echo getInvestAbleAmount($_REQUEST['product_idx'], $_REQUEST['member_idx']);


?>