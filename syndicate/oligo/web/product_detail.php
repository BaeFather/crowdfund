<?
include_once("../syndication_config.php");

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }

$sql ="
	SELECT
		A.*,
		B.*
	FROM
		cf_product A
	LEFT JOIN
		cf_product_container B  ON A.idx=B.product_idx
	WHERE (1)
		AND A.idx='".$prd_idx."'";
$PRDT = sql_fetch($sql);

if($PRDT['idx']) {
	$sql2 = "
		SELECT
			COUNT(product_idx) AS total_invest_count,
			IFNULL(SUM(amount), 0) AS total_invest_amount
		FROM
			cf_product_invest
		WHERE (1)
			AND product_idx='".$PRDT['idx']."'";
	$sql2.= ($PRDT['state']=='6') ? " AND invest_state='R'" : " AND invest_state='Y'";  //투자취소 상품의 경우 반환 처리된 투자금 내역을 가져온다.
	$row = sql_fetch($sql2);

	$PRDT['total_invest_count']  = (int)$row['total_invest_count'];
	$PRDT['total_invest_amount'] = (int)$row['total_invest_amount'];

	//print_rr($PRDT, 'font-size:12px;line-height:13px;');

	// 투자모집진행률
	$product_invest_percent = 0;
	if($PRDT['total_invest_amount']) {
		$product_invest_percent = ($PRDT['total_invest_amount'] / $PRDT['recruit_amount']) * 100;
		$product_invest_percent = floatCutting($product_invest_percent, 2);
	}

	if($product_invest_percent < 100) {
		@header('Cache-Control: no-cache, no-store, must-revalidate');
		@header('Pragma: no-cache');
		@header('Expires: -1');
	}


	switch($PRDT['category']) {
		case '1' : $cFlag = '<li class="p_ca-B">동산</li>'; break;
		case '2' : $cFlag = ($PRDT['mortgage_guarantees']=='1') ? '<li class="p_ca-A2">주택담보</li>' : '<li class="p_ca-A">부동산</li>'; break;
		case '3' : $cFlag = '<li class="p_ca-C">헬로페이</li>'; break;
		default  : $cFlag = ''; break;
	}

	$aiFlag  = ($PRDT['ai_grp_idx']>0) ? '<li class="p_ai">자동투자</li>' : '';
//$newFlag = ($PRDT['new_flag']=='Y') ? '<li class="p_new">N</li>' : '';
	$srmFlag = ($PRDT["stream_url1"] OR $PRDT["stream_url2"]) ? '<li class="p_live_tv"  onClick="'.$live_link.'"><i class="fa fa-tv"></i> LIVE TV</li>' : '';
	$adiFlag = ($PRDT['advance_invest']=='Y') ? '<li class="p_adir">사전투자 ' . floatRtrim($PRDT['advance_invest_ratio']).'% <i class="fa fa-question-circle" id="question_1"></i></li>' : '';
	$pgFlag  = ($PRDT['purchase_guarantees']=='Y' && preg_match("/dev\.hello/", G5_URL)) ? '<li class="p_pg">채권매입계약</li>' : '';
	$adpFlag = ($PRDT['advanced_payment']=='Y') ? '<li class="p_adpy">이자 선지급</li>' : '';
	$conFlag = ($PRDT['isConsor']=='1') ? '<li class="p_con">컨소시엄</li>' : '';

	// 투자모집진행률
	$product_invest_percent = 0;
	if($PRDT['total_invest_amount']) {
		$product_invest_percent = ($PRDT['total_invest_amount'] / $PRDT['recruit_amount']) * 100;
		$product_invest_percent = floatCutting($product_invest_percent, 2);
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
	if($PRDT_STATE['code']=='A02') {
		$invest_finished = true;
		$button_class   = 'btn_big_gray';

		$msg = "본 상품의 투자가 종료 되었습니다.";
		$msg.= (!$member["mb_id"]) ? "\\n`투자상품 알림받기`로 헬로펀딩의\\n신규상품 정보를 가장 먼저 받아보세요." : "";
		$invest_button   = '<a href="javascript:;" onClick="alert(\''.$msg.'\');" class="'.$button_class.'">투자상환완료</a>';
	}

	else if(preg_match('/(B00|B01)/', $PRDT_STATE['code'])) {
		$button_class  = 'btn_big_green';
		if($PRDT['open_datetime'] > G5_TIME_YMDHIS) {
			$msg = "투자 가능 시간이 아닙니다.";
		}
		else {
			if($PRDT['start_datetime'] > G5_TIME_YMDHIS) {
				$print_day = date("Y.m.d", strtotime($PRDT['start_date']))." ".get_yoil($PRDT['start_date'])."요일";
				$print_time = ($PRDT['start_hour']<=12) ? '오전' : '오후';
				$print_time.= date("g:i", strtotime($PRDT['start_datetime'])); //출력표기 시간
				$msg = $print_day." ".$print_time." 부터 투자가 가능합니다.";
			}
		}

		$invest_button = '<a href="javascript:;" onClick="alert(\''.$msg.'\');" class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';
		$advance_invest_button = '<a id="btn_advance_invest" href="javascript:;" onClick="alert(\'본 상품은 올리고에서 투자 가능한 상품입니다.\');" class="btn_big_maple">사전투자하기</a>';

		if($PRDT['advance_invest']!='Y') {
			$advance_invest_button = "";
		}
	}

	else if($PRDT_STATE['code']=='B02') {
		$button_class  = 'btn_big_blue';
		$invest_button = '<a href="javascript:;" onClick="alert(\'본 상품은 올리고에서 투자 가능한 상품입니다.\');" class="'.$button_class.'">투자하기</a>';
	}

	else {
		$invest_finished = true;
		$button_class	   = 'btn_big_gray';

		$msg = "본 상품의 투자가 종료 되었습니다.";
		$invest_button   = '<a href="javascript:;" onClick="alert(\''.$msg.'\');" class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';
	}

	$invest_button2 = $invest_button;  // 하단 투자하기 버튼


	//투자요약
	$invest_summary = $PRDT['invest_summary'];
	if(G5_IS_MOBILE) {
		$invest_summary = ($PRDT['invest_summary_m']) ? $PRDT['invest_summary_m'] : $PRDT['invest_summary'];
	}
	else {
		$invest_summary = $PRDT['invest_summary'];
	}

	// 슬라이드 구현을 위한 상품이미지 배열화
	$DTLIMG_ARR  = explode("|", $PRDT["detail_image"]);
	for($i=0; $i<count($DTLIMG_ARR); $i++) {
		$DTLIMG_ARR[$i] = trim($DTLIMG_ARR[$i]);
		if(is_file(G5_DATA_PATH."/product/".$DTLIMG_ARR[$i])) {
			$PRDTIMG[] = G5_DATA_URL."/product/".$DTLIMG_ARR[$i];
		}
	}

	// 대표 이미지
	if(!count($PRDTIMG) && count($PRDTIMG) <= 0) {
		if(!G5_IS_MOBILE) {
			if($PRDT["main_image"]!="" && is_file(G5_DATA_PATH."/product/".$PRDT["main_image"])) {
				$PRDTIMG[] = G5_DATA_URL."/product/".$PRDT["main_image"];
				$title_image_size = fileSize(G5_DATA_PATH."/product/".$PRDT["main_image"]);
			}
		}
		else {
			if($PRDT["main_image_m"] != "" && is_file(G5_DATA_PATH . "/product/" . $PRDT["main_image_m"])) {
				$PRDTIMG[] = G5_DATA_URL . "/product/" . $PRDT["main_image_m"];
				$title_image_size = fileSize(G5_DATA_PATH . "/product/" . $PRDT["main_image_m"]);
			}
		}
	}

	$product_image_count = count($PRDTIMG);

	if($PRDT['main_image']) {
		if( file_exists(G5_DATA_PATH . "/product/" . $PRDT['main_image']) && filesize(G5_DATA_PATH . "/product/" . $PRDT['main_image']) ) {
			$PRDT['title_image_url'] = G5_DATA_URL."/product/" . $PRDT['main_image'];
		}
	}

	if($PRDT['main_image_m']) {
		if( file_exists(G5_DATA_PATH . "/product/" . $PRDT['main_image_m']) && filesize(G5_DATA_PATH . "/product/" . $PRDT['main_image_m']) ) {
			$PRDT['title_image_url_m'] = G5_DATA_URL."/product/" . $PRDT['main_image_m'];
		}
	}

	// 모집시작시간
	$start_timestamp  = strtotime($PRDT["start_datetime"]);
	$print_sdate = date('Y년 m월 d일', $start_timestamp);
	$print_sdate.= ' '.get_yoil($PRDT["start_datetime"]).'요일 ';
	$print_sdate.= (date(H, $start_timestamp) < 12) ? ' 오전 ' : ' 오후 ';
	$print_sdate.= date('h:i', $start_timestamp);

	// 최신 새상품여부
	$new_flag = (G5_TIME_YMD <= date('Y-m-d', strtotime('+5day', strtotime($PRDT['open_datetime']))) && ($PRDT['recruit_amount'] > $PRDT['total_invest_amount'])) ? 'Y' : 'N';

	// 몇호 상품인지 제목 구분
	$titleAndSubject = ($PRDT['title']) ? extractText($PRDT['title']) : $PRDT['title'];
	$productNum = ($titleAndSubject[0]) ? $titleAndSubject[0] : $PRDT['title'];
	$productTitle = ($titleAndSubject[1]) ? $titleAndSubject[1] : $PRDT['title'];

	// 투자수익률
	$invest_return = floatRtrim($PRDT['invest_return']);


	// 상환방식
	$repay_pay_title = "";
	if($PRDT['repay_type'] == 1)	    $repay_pay_title = "만기일시상환";
	else if($PRDT['repay_type'] == 2) $repay_pay_title = "원리금균등상환";
	else if($PRDT['repay_type'] == 3) $repay_pay_title = "원금균등상환";
	else if($PRDT['repay_type'] == 4) $repay_pay_title = "원리금 만기일시상환";


	// 실시간 카메라 스트림
	if($PRDT['stream_url1']) {
		if($PRDT['stream_url1']=='ready') {
			$live_link = "openStreamReady();";  // /popup/inc_stream_ready.php 에 함수 정의
		}
		else {
			$play_url = "http://hellolivetv.co.kr/onair.php?prd_idx=".$prd_idx;
			if(G5_IS_MOBILE) {
				$live_link = "window.open('".$play_url."','stream_win','toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
			}
			else {
				$live_link = "window.open('".$play_url."','stream_win','width=730,height=500,toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
			}
		}
	}

	// 상단 출력용 모집금액 형식 설정
	$print_recruit_amount = price_cutting($PRDT['recruit_amount']);
	$print_recruit_amount = preg_replace("/억/", "<b>억</b>", $print_recruit_amount);
	$print_recruit_amount = preg_replace("/천/", "<b>천</b>", $print_recruit_amount);
	$print_recruit_amount = preg_replace("/만/", "<b>만</b>", $print_recruit_amount);


	// 투자기간 표기
	if($PRDT['invest_period']==1 && $PRDT['invest_days'] > 0) {
		$invest_period = $PRDT['invest_days'];
		$invest_period_unit = '일';
	}
	else {
		$invest_period = $PRDT['invest_period'];
		$invest_period_unit = '개월';
	}

}
else {
	alert("올바른 경로가 아닙니다.","/");
	exit;
}


include_once("header.php");


include_once('./product_detail_m.php');
return;



?>