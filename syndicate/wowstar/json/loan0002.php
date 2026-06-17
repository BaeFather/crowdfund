<?
################################################################################
## DESC:	펀딩 상세
## MESSAGE_ID:	LOAN0002
## REQUEST_URL: https://www.hellofunding.co.kr/syndicate/wowstar/json/loan0002.php
################################################################################
/*
request 예시 : http://www.hellofunding.co.kr/syndicate/wowstar/json/loan0002.php?APPLICATION_ID=AP001&CORP_KEY=c9b0e0b2395deaab7140074f40da34bb&USER_UNIQUE_KEY=user_unque_key&SESS_KEY=session_key&MESSAGE_ID=LOAN0002&LOAN_NO=127&debugmode=1
response 예시 :
{
        "header": {
                "APPLICATION_ID": "AP001",
                "MESSAGE_ID": "LOAN0002",
                "CORP_KEY": "e08c01aaa2-05bf818664-3fbf6bf0e1-4f3fbf6",
                "USER_UNIQUE_KEY": "11111111111115",
                "SESS_KEY": "a7af0852fa228dc4958b121a4052a5cc",
                "RTN_VAL": 0,
                "RTN_MSG": "success"
        },
        "body": {
                "LOAN_NO": "201607180003907",
                "FUND_NO_NAME": "대출채권134호",
                "ITEM_NAME": "바디프랜드",
                "THUMBNAIL_PATH": "607180003907/thumbnail_1468813664.png",
                "LOAN_INTE_RATE": "10.0",
                "LOAN_AMT_MAX": "7,300,000",
                "COLL_LOAN_RATE": "65",
                "COLL_INFO": null,
                "LOAN_NAME": "대출채권",
                "LOAN_PERIOD": "6",
                "RDMPT_NAME": "만기일시상환",
                "GIVE_DAY": "매월 10일",
                "INTE_AMT_MONTH": "62,000",
                "LOAN_ETC": "",
                "FUND_JOIN_AMT": "7,300,000",
                "FUND_JOIN_RATE": "100.0",
                "FUND_JOIN_CNT": "16",
                "INVEST_BUTM_YN": "N",
                "INVEST_BUTM_NM": "투자종료",
                "FUND_START_TM": "2016-07-18 10:00:00",
                "FUND_END_TM": "2016-08-02 23:59:00",
                "LOAN_DTL_URL": "IFrame"
        }
}
*/

include_once("../syndication_config.php");
include_once("inc_request_check.php");
include_once("inc_login_check.php");

$debugmode = $_REQUEST['debugmode'];

$prd_idx = $LOAN_NO;

$sql = "SELECT a.* FROM cf_product a WHERE a.idx = '".$prd_idx."'";
//$sql.= " AND a.display='Y'";

$PRDT = sql_fetch($sql);

if($PRDT) {

	$sql2 = "
		SELECT
			COUNT(product_idx) AS total_invest_count,
			IFNULL(SUM(amount), 0) AS total_invest_amount
		FROM
			cf_product_invest
		WHERE
				product_idx='".$PRDT['idx']."'";
	if($PRDT['state']=='6') {
		$sql2.= " AND invest_state='R'";  //투자취소 상품의 경우 반환 처리된 투자금 내역을 가져온다.
	}
	else {
		$sql2.= " AND invest_state='Y'";
	}
	$tmpres = sql_fetch($sql2);
	$PRDT['total_invest_count']  = $tmpres['total_invest_count'];
	$PRDT['total_invest_amount'] = $tmpres['total_invest_amount'];
	unset($sql2);

	if($PRDT["recruit_amount"]>0) {
		$product_invest_percent = ($PRDT["total_invest_amount"]>0) ? round((($PRDT["total_invest_amount"]/$PRDT["recruit_amount"])*100),2) : $product_invest_percent = 0;
	}
	else {
		$product_invest_percent = 0;
	}

	###################################
	## 리턴 상태코드(code) 예시 : getProductStat($prd_idx) 리턴 배열
	## A01 : 이자상환중
	## A02 : 투자상환완료 (상품마감)
	## A03 : 투자모집실패
	## A04 : 부실
	## A05 : 중도일시상환
	## B00 : 상품준비중
	## B01 : 투자대기중
	## B02 : 투자모집중
	## B03 : 투자모집완료
	## B04 : 투자모집실패
	###################################
	$PRDT_STATE = getProductStat($prd_idx);
	$invest_finished = false;

	$INVEST_BUTM = array('BUTM_YN'=>'N', 'BUTM_NM'=>'');

	if($PRDT_STATE['code']=='B02') {
		$INVEST_BUTM = array('BUTM_YN'=>'Y', 'BUTM_NM'=>'투자하기');
	}
	else {
		$invest_finished = true;
	}

	if(preg_match('/호]/', $PRDT['title'])) {
		$title_arr = explode("호]", $PRDT['title']);
		$P['ITEM_NAME']    = trim($title_arr[1]);
		$P['FUND_NO_NAME'] = str_f6($PRDT['title'], '[', ']');
	}
	else {
		$P['ITEM_NAME']    = $PRDT['title'];
	}


	if($PRDT['main_image']) {
		$P['main_image'] = (is_file(G5_DATA_PATH."/product/".$PRDT['main_image'])) ? G5_URL."/data/product/".$PRDT['main_image'] : "";
	}

	$month_invest_avr = (5000000 * ($PRDT['loan_interest_rate']/100)) / 12;		// 월별 이자 평균
	//$month_invest_avr = ($PRDT['recruit_amount'] * ($PRDT['loan_interest_rate']/100)) / 12;		// 월별 이자 평균


	// 사전투자 가능여부
	if($PRDT['advance_invest']=='Y') {
		$recruit_amount_advance = round($PRDT['recruit_amount'] * ($PRDT['advance_invest_ratio']/100));		// 사전투자비율에 따른 사전투자전체한도액
		if($PRDT['total_invest_amount'] < $recruit_amount_advance) {
			$advance_invest	= "Y";
			$advance_invest_url  = G5_URL . '/investment/detail.php?prd_idx='.$PRDT['idx'].'&advance=1';
		}
		else {
			$advance_invest	= "N";
			$advance_invest_url  = "";
		}
		$advance_invest_desc = "사전 투자 서비스는 펀딩오픈 시간에 투자참여가 어려운 회원분들을 위하여 사전에 투자할 수 있는 서비스입니다.\\n본 상품은 사전 투자가 가능한 상품으로 목표금액의 ".(int)$PRDT['advance_invest_ratio']."% 까지 사전 투자가 진행됩니다.";
	}



	$_RESULT['BODY']['LOAN_NO']					= $LOAN_NO;												//펀딩 번호
	$_RESULT['BODY']['FUND_NO_NAME']		= $P['FUND_NO_NAME'];							//펀딩 호 이름
	$_RESULT['BODY']['ITEM_NAME']				=	$P['ITEM_NAME'];								//펀딩 이름
	$_RESULT['BODY']['THUMBNAIL_PATH']	=	$P['main_image'];								//썸내일 이미지 URL
	$_RESULT['BODY']['LOAN_INTE_RATE']	= $PRDT['loan_interest_rate'];		//연금리 (대출 금리, 수익률)
	$_RESULT['BODY']['LOAN_AMT_MAX']		=	$PRDT['recruit_amount'];				//모집 금액	(대출금액	)
	$_RESULT['BODY']['COLL_LOAN_RATE']	= '0.0';													//담보 대출비율 (단위 : %)
	$_RESULT['BODY']['COLL_INFO']				= '0.0';													//담보 내용 (empty 인경우 COLL_LOAN_RATE 표시 | empty 아닌 경우 COLL_INFO 표시)
	$_RESULT['BODY']['LOAN_NAME']				= '대출채권';											//상품명
	$_RESULT['BODY']['LOAN_PERIOD']			= $PRDT['invest_period'];					//대출기간 (단위: 월)
	$_RESULT['BODY']['RDMPT_NAME']			= "만기일시상환";									//상환방식
	$_RESULT['BODY']['GIVE_DAY']				= "매월 5일";											//이자지급일

	$_RESULT['BODY']['INTE_AMT_MONTH']	= '<span style="font-size:12px;color:#9A9A9A">5백만원 투자시 월 평균</span><br>' . number_format($month_invest_avr);	//월 이자액(평균) (단위: 원)

	$_RESULT['BODY']['LOAN_ETC']				= "";															//비고
	$_RESULT['BODY']['FUND_JOIN_AMT']		=	$PRDT['total_invest_amount'];		//펀딩 참여 금액 (단위: 원)
	$_RESULT['BODY']['FUND_JOIN_RATE']	=	$product_invest_percent;				//펀딩 참여율 (단위 : %)
	$_RESULT['BODY']['FUND_JOIN_CNT']		= 0;															//펀딩 인원수 (단위 : 명)
	$_RESULT['BODY']['INVEST_BUTM_YN']	= $INVEST_BUTM['BUTM_YN'];				//투자하기 버튼 (활성화 여부		"Y : 투자하기 버튼 활성화 | N : 투자하기 버튼 비활성화)
	$_RESULT['BODY']['INVEST_BUTM_NM']	= $INVEST_BUTM['BUTM_NM'];;			  //투자하기 버튼명
	$_RESULT['BODY']['FUND_START_TM']		= $PRDT['start_datetime'];				//펀딩 시작 시각
	$_RESULT['BODY']['FUND_END_TM']			=	$PRDT['end_datetime'];					//펀딩 종료 시각
//$_RESULT['BODY']['LOAN_DTL_URL']		=	G5_URL."/syndicate/wowstar/html/product_detail.php?prd_idx=$prd_idx";				//펀딩 상세 페이지 URL
	$_RESULT['BODY']['LOAN_DTL_URL']		=	G5_URL."/syndicate/wowstar/html/product_detail.php?prd_idx=$prd_idx&SESS_KEY=".$_RESULT['header']['SESS_KEY'];				//펀딩 상세 페이지 URL

	$_RESULT['BODY']['ADVANCE_INVEST']      = $advance_invest;						//사전투자가능여부
	$_RESULT['BODY']['ADVANCE_INVEST_URL']  = $advance_invest_url;				//사전투자URL
	$_RESULT['BODY']['ADVANCE_INVEST_DESC'] = $advance_invest_desc;				//사전투자설명

}

if($debugmode==1) {
	echo "GET:"; print_rr($_GET, 'font-size:11px;');
	echo "POST:"; print_rr($_POST, 'font-size:11px;');
	print_rr($_RESULT, 'font-size:11px;');
	exit;
}
else {

	header("Content-Type:application/json");
	$str = json_encode($_RESULT);
	echo $str;

}

?>