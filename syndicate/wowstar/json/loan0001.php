<?
################################################################################
## DESC:	펀딩 상품 리스트
## MESSAGE_ID:	LOAN0001
## REQUEST_URL: http://www.hellofunding.co.kr/syndicate/wowstar/json/loan0001.php
################################################################################
/*
request 예시 : &APPLICATION_ID=AP001&CORP_KEY=c9b0e0b2395deaab7140074f40da34bb&USER_UNIQUE_KEY=와우스타아이디&SESS_KEY=헬로펀딩발행세션아이디&MESSAGE_ID=LOAN0001&PAGE_NO=1&REC_ROW=20&debugmode=1
response 예시 :
{
	"header": {
		"APPLICATION_ID": "AP001",
		"MESSAGE_ID": "LOAN0001",
		"CORP_KEY": "corp_key",
		"USER_UNIQUE_KEY": "user_unque_key",
		"SESS_KEY": "session_key",
		"RTN_VAL": 0,
		"RTN_MSG": "success"
	},
	"body": {
		"TOT_ROW": "160",
		"PAGE_NO": "1",
		"REC_ROW": "20",
		"list": [{
			"OPEN_TYPE": "2",
			"LOAN_NO": "201607010000001",
			"LOAN_TYPE": "B",
			"FUND_NO_NAME": "펀딩 1호",
			"ITEM_NAME": "펀딩이름",
			"BRIEF_INTRO": "간략 설명",
			"LOAN_PERIOD": "6",
			"LOAN_INTE_RATE": "16.0",
			"INTE_RATE_ADD_VIEW": "0.0",
			"COLL_INFO": "담보정보",
			"LOAN_AMT_MAX": "20,000",
			"FUND_JOIN_CNT": "1",
			"FUND_PROG_RATE": "0.5",
			"FUND_START_TM": "2016-07-14 10:00",
			"FUND_START_WEEK": "목",
			"FUND_END_INFO": "2일남음",
			"SPECIAL_PATH": "스페셜 이미지(360*230) 경로",
			"THUMBNAIL_PATH": "썸내일 이미지(360*165) 경로",
			"LOGO_PATH": "로고 이미지(150*60) 경로"
		}]
	}
}
*/

include_once("../syndication_config.php");
include_once("inc_request_check.php");
include_once("inc_login_check.php");


$debugmode = $_REQUEST['debugmode'];

$datetime = date("Y-m-d H:i:s");


$where = "1=1";
$where.= " AND A.display='Y' AND A.scrap_out='' AND A.isTest='' AND A.only_vip=''";
$where.= " AND A.platform LIKE '%".$_CONF['SYNDI_ID']."%'";
$where.= " AND (A.category IN(1,2) OR (A.category='3' AND category2='2'))";

switch($gubun) {

	## 투자 대기중
	case 'recruit_wait' :
		$where.= " AND A.state='' AND A.invest_end_date=''";
		$where.= " AND A.open_datetime<='$datetime' AND A.start_datetime>='$datetime'";
	break;

	## 진행중
	case 'recruit_started' :
		$where.= " AND A.state='' AND A.invest_end_date=''";
		$where.= " AND A.start_datetime<='$datetime' AND A.end_datetime >= '$datetime' AND (SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') < A.recruit_amount ";
	break;

	## 투자 모집완료
	case 'recruit_finished' :
		$where.= " AND A.state=''";
		$where.= " AND (A.invest_end_date!='' OR (SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') >= A.recruit_amount)";
	break;

	## 이자상환중
	case 'repay_started' :
		$where.= " AND A.state='1'";
	break;

	## 투자상환완료
	case 'repay_finished' :
		$where.= " AND A.state IN('2','5')";
	break;

	## 전체
	case 'all' :
	default :
	break;

}

$where.= ($mode=="success") ? " AND A.success_example='Y'" : "";

$sql = "
	SELECT
		COUNT(idx) AS product_count
	FROM
		cf_product A
	WHERE
		$where";
//if($debugmode=='1') { print_rr($sql,"font-size:12px") . "\n\n"; }
$row = sql_fetch($sql);
$affect_num = $row['product_count'];
$page = ($PAGE_NO) ? $PAGE_NO : 1;
$size = ($REC_ROW) ? $REC_ROW : 9;
$total_page = ceil($affect_num / $size);

$_RESULT['BODY'] = array(
		'TOT_ROW' => $affect_num,
		'PAGE_NO' => $page,
		'REC_ROW' => $size
);
$_RESULT['BODY']['list'] = array();

$DATA['query_string'] = $_SERVER['QUERY_STRING'];
$DATA['gubun']      = ($gubun) ? $gubun : 'all';
$DATA['page']       = $page;
if($total_page) {
	$DATA['total_page'] = $total_page;
	$DATA['next_page']  = ($page < $total_page) ? $page + 1 : '';
}
else {
	$DATA['total_page'] = 0;
	$DATA['next_page']  = 0;
}

if($affect_num > 0) {

	if($page > ceil($affect_num / $size)) {
		$page = ceil($affect_num / $size);
	}
	$start_num = ($page - 1) * $size;

	// 일반 투자 상품 정보 추출

	$sql = "
		SELECT
			A.idx, A.start_num, A.advance_invest, A.advance_invest_ratio, A.loan_start_date, A.open_datetime, A.start_datetime, A.end_datetime, A.state, A.recruit_amount,
			A.category, A.title, A.invest_return, A.withhold_tax_rate, A.invest_usefee, A.invest_usefee_type,
			A.invest_period, A.recruit_period_start, A.recruit_period_end, A.repay_type,
			A.main_image, A.display, A.purchase_guarantees, A.advanced_payment, A.success_example, A.popular_goods,
			(SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state IN('Y','R')) AS total_invest_count,
			(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state IN('Y','R')) AS total_invest_amount
		FROM
			cf_product A
		WHERE
			$where
		ORDER BY
			A.cancel_date ASC,
			(total_invest_amount/A.recruit_amount) ASC,
			A.open_datetime DESC,
			A.start_num DESC,
			A.idx DESC
		LIMIT
			$start_num, $size";

	//if($debugmode=='1') { print_rr($sql,"font-size:12px") . "\n\n"; }

	$res  = sql_query($sql);
	$rows = sql_num_rows($res);
	for($i=0; $i<$rows; $i++) {
		$PROW = sql_fetch_array($res);

		$r = sql_fetch("SELECT invest_summary FROM cf_product_container WHERE product_idx='".$PROW['idx']."'");
		$PROW['invest_summary'] = $r['invest_summary'];
		unset($r);

		$title_arr = explode("호]", $PROW['title']);
		$P['ITEM_NAME'] = trim($title_arr[1]);

		if($PROW['main_image']) {
			$P['main_image'] = (is_file(G5_DATA_PATH."/product/".$PROW['main_image'])) ? G5_URL."/data/product/".$PROW['main_image'] : "";
		}
		else {
			$P['main_image'] = "";
		}

		if($PROW["recruit_amount"] > 0) {
			$P['invest_percent'] = ($PROW["total_invest_amount"] > 0) ? round((($PROW["total_invest_amount"]/$PROW["recruit_amount"])*100), 2) : 0;
		}
		else{
			$P['invest_percent'] = 0;
		}

		$PRDT_STATE = getProductStat($PROW['idx']);
		$P['invest_finished'] = (preg_match('/(B00|B01|B02)/', $PRDT_STATE['code']))  ? false : true;


		// $P['OPEN_TYPE'] ==> 1:대기중, 2:진행중, 3:종료
		switch($PRDT_STATE['code']) {
			case "A01" : $P['OPEN_TYPE'] = 3; break;		// 이자상환중
			case "A02" : $P['OPEN_TYPE'] = 3; break;		// 투자상환완료
			case "A03" : $P['OPEN_TYPE'] = 3; break;		// 투자모집실패
			case "A04" : $P['OPEN_TYPE'] = 3; break;		// 부실
			case "A05" : $P['OPEN_TYPE'] = 3; break;		// 중도상환완료
			case "A06" : $P['OPEN_TYPE'] = 3; break;		// 투자금반환완료
			case "B00" : $P['OPEN_TYPE'] = 1; break;		// 상품준비중
			case "B01" : $P['OPEN_TYPE'] = 1; break;		// 투자대기중
			case "B02" : $P['OPEN_TYPE'] = 2; break;		// 투자모집중
			case "B03" : $P['OPEN_TYPE'] = 3; break;		// 투자모집완료
			case "B04" : $P['OPEN_TYPE'] = 3; break;		// 투자모집실패
			default    : $P['OPEN_TYPE'] = 1; break;
		}

		$P['period_month'] = $PROW['invest_period'].'개월';

		// 대출실행 완료건에 대하여 이자지급 차수 표시
		/*
		if($PROW['loan_start_date'] && $PROW['loan_start_date']!='0000-00-00') {
			$loan_start_date_day = (int)substr($PROW['loan_start_date'], -2);
			$total_repay_count = ((int)$loan_start_date_day < 5) ? $PROW['invest_period'] : $PROW['invest_period'] + 1; //총 지급횟수
			$PAIED = sql_fetch("SELECT MAX(turn) as max_turn FROM cf_product_success WHERE product_idx='".$PROW['idx']."' AND invest_give_state='Y'");
			$repay_count = ($PAIED['max_turn']) ? $PAIED['max_turn'] : 0;
			$repay_count_fcolor = ($repay_count) ? '#ff6633' : '#aaaaaa';

			$P['repay_count_tag'] = "<span style='color:$repay_count_fcolor'>$repay_count</span> / $total_repay_count";

		}
		*/

		$P['FUND_NO_NAME']    = "제".$PROW['start_num']."호";
		$P['FUND_START_WEEK'] = preg_replace("/요일/", "", get_yoil($PROW['start_datetime']));
		$P['invest_summary']  = preg_replace("/\&nbsp;/", " ", strip_tags($PROW['invest_summary']));
		$P['invest_summary']  = preg_replace("/script/i", "script.", $P['invest_summary']);

		$fund_end_info = ceil((strtotime($PROW['end_datetime'])-time())/86400);
		if($fund_end_info < 0) $fund_end_info = 0;
		$P['FUND_END_INFO']   = $fund_end_info . "일 남음";

		// 사전투자 가능여부
		$advance_invest	= "N";
		if($PROW['advance_invest']=='Y') {
			$recruit_amount_advance = round($PROW['recruit_amount'] * ($PROW['advance_invest_ratio']/100));		// 사전투자비율에 따른 사전투자전체한도액
			if($PROW['total_invest_amount'] < $recruit_amount_advance) {
				$advance_invest	= "Y";
			}
		}

		$logo_path = G5_URL.'/theme/blueman1/img/logo.png';

		$LIST[$i] = array(
			'OPEN_TYPE'          => $P['OPEN_TYPE'],
			'LOAN_NO'            => $PROW['idx'],
			'LOAN_TYPE'          => 'N',															// N:일반, S:스페셜, K:k-컬쳐, E:이벤트, B:블라인드 (P2P업체에서 정의)
			'FUND_NO_NAME'       => $P['FUND_NO_NAME'],
			'ITEM_NAME'          => $P['ITEM_NAME'],
			'BRIEF_INTRO'        => $P['invest_summary'],
			'LOAN_PERIOD'        => $PROW['invest_period'],
			'LOAN_INTE_RATE'     => $PROW["invest_return"],
			'INTE_RATE_ADD_VIEW' => '0.0',  // 추가 금리
			'COLL_INFO'          => '담보정보',
			'LOAN_AMT_MAX'       => $PROW['recruit_amount'],					//목표금액
			'FUND_JOIN_AMT'      => $PROW["total_invest_amount"],			//참여금액
			'FUND_JOIN_CNT'      => 0,																//투자자수 $PROW["total_invest_count"]
			'FUND_PROG_RATE'     => $P['invest_percent'],							//펀딩 진행율
			'FUND_START_TM'      => $PROW['start_datetime'],
			'FUND_START_WEEK'    => $P['FUND_START_WEEK'],
			'FUND_END_INFO'      => $P['FUND_END_INFO'],
			'SPECIAL_PATH'       => $P['main_image'],									//스페셜 이미지(360*230) 경로
			'THUMBNAIL_PATH'     => $P['main_image'],									//썸내일 이미지(360*165) 경로
			'LOGO_PATH'          => $logo_path,												//로고 이미지(150*60) 경로
			'ADVANCE_INVEST'     => $advance_invest,									//사전투자가능여부
			'ADVANCE_INVEST_RATIO' => $PROW['advance_invest_ratio']		//목표금액대비 사전투자가능비율
		);

		array_push($_RESULT['BODY']['list'], $LIST[$i]);

		unset($P);

	}

}

if($debugmode=='1') {
	//echo "GET:"; print_rr($_GET, 'font-size:11px;');
	//echo "POST:"; print_rr($_POST, 'font-size:11px;');
	//echo "SESSION:"; print_rr($_SESSION, 'font-size:11px;');
	//echo "COOKIE:"; print_rr($_COOKIE, 'font-size:11px;');
	print_rr($_RESULT, 'font-size:12px;');

	$str = json_encode($_RESULT, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);
	print_rr($str, "font-size:12px");

	exit;
}
else {

	header("Content-Type:application/json");
	$str = json_encode($_RESULT);
	echo $str;

}

exit;
?>