<?
###############################################################################
## 투자상품 상세보기
###############################################################################

include_once('./_common.php');


$g5['title'] = '투자상품 상세보기';
$g5['top_bn'] = "/images/investment/sub_investment.jpg";
$g5['top_bn_alt'] = "투자하기 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

//include_once(HF_LIB_PATH.'/review.lib.php');




$special_user = ($is_admin=='super' || in_array($member['mb_id'], array('hellosiesta','sori9th','judero831','hellofunding','test070','test999','master','romrom'))) ? true : false;
$tmp_special_user = ( in_array($member['mb_id'], array('samo','samo001','samo002')) ) ? true : false;

$prd_idx = trim($_REQUEST['prd_idx']);

if($prd_idx=='') { goto_url('/'); exit; }
if(!preg_match('/^[0-9]{0,10}$/', $prd_idx)) { header('Location: /', true, 302); exit; }

if($prd_idx=='164') {
	if(!$special_user) { header('Location: /investment/investment.php?prd_idx=168', true, 302); exit; }
}


// 지정투자상품 설정 ------------------------------------------------------------------
$defeat_msg = "[본 투자상품 관련 공지]\\n\\n본 투자상품은 투자자와 사전에 협의가 완료된 지정투자상품입니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.";
if(!$is_admin) {
	if($prd_idx=='148' && !in_array($member['mb_id'], array('moreamc','uildnm2012','yr4msp','sori9th')) ) {
		msg_replace("[본 투자상품 관련 공지]\\n\\n본 투자상품은 사전에 협의완료된 대출자와 투자자가 제3자에 의한 체계적 담보권리확보 및 자금관리를 목적으로 헬로펀딩을 통해 펀딩을 진행합니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.", "/investment/invest_list.php");
	}
	if($prd_idx=='157' && !in_array($member['mb_id'], array('fintech05','yr4msp','sori9th')) ) {
		msg_replace($defeat_msg, "/investment/invest_list.php");
	}
	if($prd_idx=='171' && !in_array($member['mb_id'], array('KJHInvest1019','GraceInvest1102','master')) ) {
		msg_replace($defeat_msg, "/investment/invest_list.php");
	}
	if( in_array($prd_idx, array('175','176')) && $member['mb_id']!='apollon' ) {
		msg_replace($defeat_msg, "/investment/invest_list.php");
	}
	if($prd_idx=='225' && $member['mb_id']!='dividend01' ) {
		msg_replace($defeat_msg, "/investment/invest_list.php");
	}
	if($prd_idx=='231' && $member['mb_id']!='directlending' ) {
		msg_replace($defeat_msg, "/investment/invest_list.php");
	}
	if($prd_idx=='238' && $member['mb_id']!='akorea' ) {
		msg_replace($defeat_msg, "/investment/invest_list.php");
	}
}
// 지정투자상품 설정 ------------------------------------------------------------------


$sql = "
	SELECT
		A.*,
		B.*
	FROM
		cf_product A
	LEFT JOIN
		cf_product_container B  ON A.idx=B.product_idx
	WHERE
		A.idx='".$prd_idx."'";
$sql.= ($_REQUEST['syndi_id']=='finnq' || $special_user || $tmp_special_user) ? "" : " AND A.display='Y'";
$PRDT = sql_fetch($sql);

if($PRDT) {

	// chosun.hellofunding.kr 은 https 를 사용하지 않는다.
	$PRDT['extend_7']            = preg_replace("/(https:\/\/hellofunding\.|https:\/\/www\.hellofunding\.)/i", "http://www.hellofunding.", $PRDT['extend_7']);
	$PRDT['extend_8']            = preg_replace("/(https:\/\/hellofunding\.|https:\/\/www\.hellofunding\.)/i", "http://www.hellofunding.", $PRDT['extend_8']);
	$PRDT['extend_9']            = preg_replace("/(https:\/\/hellofunding\.|https:\/\/www\.hellofunding\.)/i", "http://www.hellofunding.", $PRDT['extend_9']);
	$PRDT['extend_10']           = preg_replace("/(https:\/\/hellofunding\.|https:\/\/www\.hellofunding\.)/i", "http://www.hellofunding.", $PRDT['extend_10']);
	$PRDT['invest_summary']      = preg_replace("/(https:\/\/hellofunding\.|https:\/\/www\.hellofunding\.)/i", "http://www.hellofunding.", $PRDT['invest_summary']);
	$PRDT['invest_summary_m']    = preg_replace("/(https:\/\/hellofunding\.|https:\/\/www\.hellofunding\.)/i", "http://www.hellofunding.", $PRDT['invest_summary_m']);
	$PRDT['core_invest_point']   = preg_replace("/(https:\/\/hellofunding\.|https:\/\/www\.hellofunding\.)/i", "http://www.hellofunding.", $PRDT['core_invest_point']);
	$PRDT['product_summary']     = preg_replace("/(https:\/\/hellofunding\.|https:\/\/www\.hellofunding\.)/i", "http://www.hellofunding.", $PRDT['product_summary']);
	$PRDT['product_description'] = preg_replace("/(https:\/\/hellofunding\.|https:\/\/www\.hellofunding\.)/i", "http://www.hellofunding.", $PRDT['product_description']);


	$sql2 = "
		SELECT
			COUNT(product_idx) AS total_invest_count,
			IFNULL(SUM(amount), 0) AS total_invest_amount
		FROM
			cf_product_invest
		WHERE
			idx > 0
			AND product_idx='".$PRDT['idx']."'";
	$sql2.= ($PRDT['state']=='6') ? " AND invest_state='R'" : " AND invest_state='Y'";  //투자취소 상품의 경우 반환 처리된 투자금 내역을 가져온다.

	$tmpres = sql_fetch($sql2);
	//print_rr($tmpres);

	$PRDT['total_invest_count']  = $tmpres['total_invest_count'];
	$PRDT['total_invest_amount'] = $tmpres['total_invest_amount'];
	unset($sql2);


	/*
	// 관리자가 아닐 경우 사전 투자상품의 진행률 나오지 않도록 모집금액 0처리 (중요)
	if($is_advance_invest != 'Y') {
		if(!$is_admin) {
			if($PRDT['advance_invest']=='Y' && $PRDT['start_datetime'] > G5_TIME_YMDHIS) {
				$total_invest_amount = $PRDT["total_invest_amount"];
				$PRDT["total_invest_amount"] = 0;
			}
		}
	}
	*/


	// 투자모집진행률
	$product_invest_percent = 0;
	if($PRDT['total_invest_amount']) {
		$product_invest_percent = ($PRDT['total_invest_amount'] / $PRDT['recruit_amount']) * 100;
		$product_invest_percent = floatCutting($product_invest_percent, 2);
	}


	if($member['mb_id']) {
		$shinhan_vacct = ( trim($member['va_bank_code2']) && trim($member['virtual_account2']) ) ? true : false;
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
				$print_day  = date("Y년 m월 d일", strtotime($PRDT['start_date']))." ".get_yoil($PRDT['start_date'])."요일";
				$print_time = ($PRDT['start_hour']<=12) ? '오전' : '오후';
				$print_time.= date("g시 i분", strtotime($PRDT['start_datetime'])); //출력표기 시간
				$msg = $print_day." ".$print_time." 부터 투자가 가능합니다.";
			}
		}

		if($is_member) {
			if($member['invest_warning_agree']=='Y') {
				$invest_button = '<a href="javascript:;" onClick="alert(\''.$msg.'\');" class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';
				$advance_invest_button = '<a id="btn_advance_invest" href="<?=BSC_URL?>/investment/detail.php?prd_idx='.$PRDT['idx'].'&advance=1" class="btn_big_maple">사전투자하기</a>';
			}
			else {
				$invest_button = '<a href="javascript:;" onClick="invest_warning_agree_open();"  class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';  //투자위험고지 팝업 : /popup/inc_invest_warning_agree_form.php
				$advance_invest_button = '<a id="btn_advance_invest" href="javascript:;" onClick="invest_warning_agree_open();"  class="btn_big_maple">사전투자하기</a>';
			}
		}
		else {
			$invest_button = '<a href="javascript:;" onClick="alert(\'본 서비스는 로그인이 필요합니다.\');" class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';
			$advance_invest_button = '<a id="btn_advance_invest" href="javascript:;" onClick="alert(\'본 서비스는 로그인이 필요합니다.\');" class="btn_big_maple">사전투자하기</a>';
		}

		if($PRDT['advance_invest']!='Y') {
			$advance_invest_button = "";
		}

	}

	else if($PRDT_STATE['code']=='B02') {
		$button_class  = 'btn_big_blue';
		if($member["mb_id"]) {
			if($shinhan_vacct) {
				if($member['insidebank_after_trans_target']=='1') {
					// 기존계좌의 금액이전이 완료되지 않은 경우
					$tmp_msg = "신한은행 가상계좌 발급이 완료되어 현재 보유하신 예치금이 신한은행으로 이관중입니다.\n\n이관에 소요되는 시간은 가상계좌 발급 후 영업일 기준 최장 48시간 이내이며,\n\n이관이 완료된 후 투자가 가능한 점 양해부탁드립니다.";
					$invest_button = '<a href="javascript:;" onClick="alert(\''.$tmp_msg.'\');" class="'.$button_class.'">투자하기</span>';
				}
				else {
					if($member['invest_warning_agree']=='Y') {
						$invest_button = '<a href="/investment/detail.php?prd_idx='.$PRDT['idx'].'" class="'.$button_class.'">투자하기</a>';
					}
					else {
						$invest_button = '<a href="javascript:;" onClick="invest_warning_agree_open();" class="'.$button_class.'" >투자하기</a>';  //투자위험고지 팝업 : /popup/inc_invest_warning_agree_form.php
					}
				}
			}
			else {
				$invest_button = '<a href="javascript:;" onClick="if(confirm(\'발급된 가상계좌 정보가 없습니다.\\n가상계좌 신청 페이지로 이동하시겠습니까?\')) { location.href=\'/deposit/deposit.php?tab=3\'; }" class="'.$button_class.'">투자하기</a>';
			}
		}
		else {
			$invest_button = '<a href="/member/login.php?url='.urlencode($_SERVER['REQUEST_URI']).'" class="'.$button_class.'">투자하기</a>';
		}
	}
	else {
		$invest_finished = true;
		$button_class	   = 'btn_big_gray';

		$msg = "본 상품의 투자가 종료 되었습니다.";
		$msg.= (!$member["mb_id"]) ? "\\n`투자상품 알림받기`로 헬로펀딩의\\n신규상품 정보를 가장 먼저 받아보세요." : "";
		$invest_button   = '<a href="javascript:;" onClick="alert(\''.$msg.'\');" class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';
	}

	$invest_button2 = $invest_button;  // 하단 투자하기 버튼

}
else {
	alert("올바른 경로가 아닙니다.","/");
	exit;
}


//투자요약
$invest_summary = $PRDT["invest_summary"];
if(G5_IS_MOBILE) {
	$invest_summary = ($PRDT['invest_summary_m']) ? $PRDT['invest_summary_m'] : $PRDT["invest_summary"];
}
else {
	$invest_summary = $PRDT["invest_summary"];
}


if($PRDT["evaluate_star1"] && $PRDT["evaluate_star2"] && $PRDT["evaluate_star3"]) {
	$grade_type = "v1";
	//-- 기존 등급 산정방식 v1--------------------------------------------------//
	$level_score = $PRDT["evaluate_star1"] + $PRDT["evaluate_star2"] + $PRDT["evaluate_star3"];
	$grade = $_evaluation_grade_array[$level_score];
	//-- 기존 등급 산정방식 v1--------------------------------------------------//
}
else if($PRDT["evaluate_score1"] && $PRDT["evaluate_score2"] && $PRDT["evaluate_score3"] && $PRDT['evaluate_score4']) {
	$grade_type = "v2";
	//-- 개정 등급 산정방식 v2--------------------------------------------------//
	$level_score = round(($PRDT["evaluate_score1"] + $PRDT["evaluate_score2"] + $PRDT["evaluate_score3"] + $PRDT["evaluate_score4"]) / 5);
	$grade = $_gudge_grade_array[$level_score];
	$evaluate_score1 = round($PRDT["evaluate_score1"]/48*100);
	$evaluate_score2 = round($PRDT["evaluate_score2"]/5*100);
	$evaluate_score3 = round($PRDT["evaluate_score3"]/5*100);
	$evaluate_score4 = round($PRDT["evaluate_score4"]/42*100);
	//-- 개정 등급 산정방식 v2--------------------------------------------------//
}
else if($PRDT["evaluate_score1"] && $PRDT["evaluate_score2"]==0 && $PRDT["evaluate_score3"] && $PRDT['evaluate_score4']) {
	$grade_type = "v3";
	//-- 개정 등급 산정방식 v3--------------------------------------------------//
	$level_score = round(($PRDT["evaluate_score1"] + $PRDT["evaluate_score3"] + $PRDT["evaluate_score4"]) / 5);
	$grade = $_gudge_grade_array[$level_score];
	$evaluate_score1 = round($PRDT["evaluate_score1"]/40*100);
	$evaluate_score3 = round($PRDT["evaluate_score3"]/30*100);
	$evaluate_score4 = round($PRDT["evaluate_score4"]/30*100);
	//-- 개정 등급 산정방식 v3--------------------------------------------------//
}

// 대출실행 완료건에 대하여 이자지급 차수 표시
if($PRDT['loan_start_date'] && $PRDT['loan_start_date']!='0000-00-00') {
	$loan_start_date_day = (int)substr($PRDT['loan_start_date'], -2);
	$total_repay_count = ($loan_start_date_day > 1) ? $PRDT['invest_period'] + 1 : $PRDT['invest_period'];  //총 지급횟수
	$PAIED = sql_fetch("SELECT MAX(turn) as max_turn FROM cf_product_success WHERE product_idx='".$PRDT['idx']."' AND invest_give_state='Y'");
	$repay_count = ($PAIED['max_turn']) ? $PAIED['max_turn'] : 0;
}

// 슬라이드 구현을 위한 상품이미지 배열화
$DTLIMG_ARR  = explode("|",$PRDT["detail_image"]);

for($i=0; $i<count($DTLIMG_ARR);$i++) {
	if(is_file(HF_DATA_PATH."/product/".$DTLIMG_ARR[$i])) {
		$PRDTIMG[] = HF_DATA_URL."/product/".$DTLIMG_ARR[$i];
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
	if( file_exists(HF_DATA_PATH . "/product/" . $PRDT['main_image']) && filesize(HF_DATA_PATH . "/product/" . $PRDT['main_image']) ) {
		$PRDT['title_image_url'] = HF_DATA_URL."/product/" . $PRDT['main_image'];
	}
}

if($PRDT['main_image_m']) {
	if( file_exists(HF_DATA_PATH . "/product/" . $PRDT['main_image_m']) && filesize(HF_DATA_PATH . "/product/" . $PRDT['main_image_m']) ) {
		$PRDT['title_image_url_m'] = HF_DATA_URL."/product/" . $PRDT['main_image_m'];
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

// 모집금액
/*
$recruit_amount = 0;
$recruit_amount_unit = '원';
if($PRDT["recruit_amount"] > 0) {
	$tmpRecruitData = wonFormat($PRDT["recruit_amount"]);
	$recruit_amount = ($tmpRecruitData[0]) ? $tmpRecruitData[0] : $PRDT["recruit_amount"];
	$recruit_amount_unit = ($tmpRecruitData[1]) ? $tmpRecruitData[1] : null;
}
*/

// 투자수익률
$invest_return = floatRtrim($PRDT['invest_return']);


// 상환방식
$repay_pay_title = "";
if($PRDT['repay_type'] == 1)	    $repay_pay_title = "만기일시상환";
else if($PRDT['repay_type'] == 2) $repay_pay_title = "원리금균등상환";
else if($PRDT['repay_type'] == 3) $repay_pay_title = "원금균등상환";
else if($PRDT['repay_type'] == 4) $repay_pay_title = "원리금 만기일시상환";


// 대구 이시아폴리스 메가맥스타워 유동화자금 라이브 오류로 인한 임시처리
if( in_array($prd_idx, array('205','207')) ) {
	$PRDT['stream_url1'] = 'ready';
}

// 실시간 카메라 스트림
if($PRDT['stream_url1']) {
	if($PRDT['stream_url1']=='ready') {
		$live_link = "openStreamReady();";  // /popup/inc_stream_ready.php 에 함수 정의
	}
	else {
		$play_url = "http://hellolivetv.co.kr/onair.php?prd_idx=".$prd_idx;
		$play_url.= (preg_match("/dev.hellofunding/", $_SERVER['HTTP_HOST'])) ? "&mode=test" : "";
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
//$print_invest_period = ($PRDT['invest_days'] > 0 && $PRDT['invest_days'] < 30) ? $PRDT['invest_days'] . '일' : $PRDT['invest_period'] . '개월';

// 투자기간 표기 변경 : 2018-02-19
if($PRDT['invest_period']==1 && $PRDT['invest_days'] > 0) {
	$invest_period = $PRDT['invest_days'];
	$invest_period_unit = '일';
}
else {
	$invest_period = $PRDT['invest_period'];
	$invest_period_unit = '개월';
}


///////////////////////////////
// 핀크용 스킨 적용 분기
///////////////////////////////
if($syndi_id=='finnq') {
	$inc_file = "investment_finnq";
	$inc_file.= ($prd_idx <= 244) ? "_old" : "";
	$inc_file.= ".php";
	include_once($inc_file);
	return;
}

if($co['co_include_head']) @include_once($co['co_include_head']);
else include_once(HF_PATH.'/hf_head.php');


///////////////////////////////
// 모바일 분기
///////////////////////////////
?>
<?
switch($PRDT['category']) {
	case '1' : $cFlag = '<li class="p_ca-B">동산</li>'; break;
	case '2' : $cFlag = ($PRDT['mortgage_guarantees']=='1') ? '<li class="p_ca-A2">주택담보대출</li>' : '<li class="p_ca-A">부동산</li>'; break;
	case '3' : $cFlag = '<li class="p_ca-C">확정매출채권</li>'; break;
	default  : $cFlag = ''; break;
}

$aiFlag  = ($PRDT['ai_grp_idx']>0) ? '<li class="p_ai">자동투자</li>' : '';
$newFlag = ($PRDT['new_flag']=='Y') ? '<li class="p_new">N</li>' : '';
$srmFlag = ($PRDT["stream_url1"] OR $PRDT["stream_url2"]) ? '<li class="p_live_tv"  onClick="'.$live_link.'"><i class="fa fa-tv"></i> LIVE TV</li>' : '';
$adiFlag = ($PRDT['advance_invest']=='Y') ? '<li class="p_adir">사전투자 ' . floatRtrim($PRDT['advance_invest_ratio']).'% <i class="fa fa-question-circle" id="question_1"></i></li>' : '';
$pgFlag  = ($PRDT['purchase_guarantees']=='Y' && preg_match("/dev\.hello/", G5_URL)) ? '<li class="p_pg">채권매입계약</li>' : '';
$adpFlag = ($PRDT['advanced_payment']=='Y') ? '<li class="p_adpy">이자 선지급</li>' : '';

?>

<!-- 본문내용 START -->

<script src="<?=HF_URL?>/js/jquery.blink.js"></script>

<!-- link rel="stylesheet" type="text/css" href="/css/<?=(G5_IS_MOBILE?'investment_info_m':'investment_info')?>.css" -->
<link rel="stylesheet" type="text/css" href="/css/<?=(G5_IS_MOBILE?'investment_info_old_m':'investment_info_old')?>.css">
<input type="hidden" name="prd_idx" value="<?=$prd_idx;?>">
<input type="hidden" name="invest_finished" id="invest_finished" value="<? echo ($invest_finished) ? 'true' : 'false'; ?>">

<!-- 본문내용 START -->
<div id="content" >

<!-- 상품 슬라이드 시작 -->
<div id="p_info">
	<div class="p_info_b" <? if($PRDT['title_image_url_m']) { ?> style="background:url('<?=$PRDT['title_image_url_m']?>') repeat center;" <? } ?>>
		<div class="p_info_bb">
			<div class="p_flags">
				<ul>
					<?=$cFlag?><?=$aiFlag?><?=$newFlag?><?=$srmFlag?><?=$adiFlag?><?=$pgFlag?><?=$adpFlag?>
				</ul>
			</div>

			<div class="p_tit"><?=$PRDT['title']?></div>
			<div class="p_date">모집기간 : <?=$print_sdate;?> ~</div>

			<div class="p_info_total">
				<div>
					<span>투자수익률(연)</span>
					<?=$invest_return?><b>%</b>
				</div>
				<div>
					<span>투자기간</span>
					<?=$invest_period?><b><?=$invest_period_unit?></b>
				</div>
				<div>
					<span>모집금액</span>
					<?=$print_recruit_amount?><b>원</b>
				</div>
			</div>

			<div class="process_wrap">
				<div class="process">

					<div class="process_tag">
						<div class="process_tag_c">
							<span>투자모집률 / 모집된 금액</span>
							<strong class="p_t_n" id="progressData"><?=$product_invest_percent?>%</strong> / <strong class="p_t_t" id="totalRecruitValue"><?=price_cutting($PRDT["total_invest_amount"]+0);?>원</strong>
<!--
							<span>투자모집률 / 남은 모집금액</span>
							<strong class="p_t_n" id="progressData"><?=$product_invest_percent?>%</strong> / <strong class="p_t_t" id="totalRecruitValue"><?=price_cutting($PRDT["recruit_amount"]-$PRDT["total_invest_amount"]+0);?>원</strong>
//-->
						</div>
					</div>
					<div id="progressBar" class="process_bar" style="width:<?=(($product_invest_percent <= 100)?$product_invest_percent:100).'%';?>"></div>
				</div>
			</div>
			<ul class="btn_all" id="progressBtn">

				<? if($invest_finished == false) { ?>
				<li style="display:inline-block;">
					<a href="/investment/simulation.php?prd_idx=<?=$PRDT['idx']?>" class="btn_big_link">투자시뮬레이션</a>
				</li>
				<? } ?>

				<? if($invest_button) {?>
				<li class="invest" style="display:inline-block;color:white;">
					<?=$invest_button; // 투자하기 ?>
				</li>
				<? } ?>

				<? if($advance_invest_button) { ?>
				<li class="reser_invest">
					<?=$advance_invest_button; // 사전투자하기 ?>
				</li>
				<? } ?>

				<? if(!$is_member && $invest_finished) { ?>
				<li style="display:inline-block;">
					<a id="reqsms_btn2" class="btn_big_blue">다음 상품 알림받기</a>
				</li>
				<? } ?>
			</ul>

			<? if($PRDT['ai_grp_idx']) { ?>
			<div style="margin:-10px auto 10px; ">
				<a href="<?=G5_URL?>/deposit/deposit.php?tab=5" class="btn_big_orange">자동투자설정</a>
			</div>
			<? } ?>


			<? if($PRDT['product_summary']) echo $PRDT['product_summary']; ?>

			<div class="sns_share">
				<ul>
					<li>
						<a href="#" data-toggle="sns_share" data-service="facebook" data-title="페이스북 SNS공유">
							<img src="/img/sub/sns_f_btn01.png" alt="facebook" width="30">
						</a>
					</li>
					<li>
						<a href="#" data-toggle="sns_share" data-service="naver" data-title="네이버 SNS공유">
							<img src="/img/sub/sns_b_btn01.png" alt="naver" width="30">
						</a>
					</li>
					<li>
						<a href="#" data-toggle="sns_share" data-service="kakaostory" data-title="카카오스토리 SNS공유">
							<img src="/img/sub/sns_k_btn01.png" alt="kakao" width="30">
						</a>
					</li>
					<!--
					<li>
						<a href="#" data-toggle="sns_share" data-service="instagram" data-title="인스타그램 SNS공유">
							<img src="<?=G5_THEME_IMG_URL?>/sub/sns_i_btn01.png" alt="instagram">
						</a>
					</li>
					//-->
					<li>
						<a href="#" data-toggle="sns_share" data-service="url_copy" data-title="주소복사하기">
							<img src="/img/investment/url_icon.png" alt="url_copy" width="30">
						</a>
					</li>
				</ul>
			</div><!-- sns share -->
		</div><!-- p_info_bb -->
	</div><!-- p_info_b -->
</div><!-- p_info -->


<?
// 투자포인트
if($PRDT['core_invest_point']) {
	if ($_REQUEST['prd_idx']<='133') echo "<div class='product_info' style='margin-top:80px;margin-bottom:80px;'>\n";
	echo (isset($PRDT['core_invest_point'])) ? $PRDT['core_invest_point'] : null;
	if ($_REQUEST['prd_idx']<='133') echo "</div>\n";
}
?>

<!-- 상품개요 -->
<div class="product_info">
	<div class="product_info_tit">상품 개요</div>

	<ul class="p_i_t">
		<li>
			<p>투자모집액</p>
			<p><? echo price_cutting($PRDT['recruit_amount']);?>원</p>
		</li>
		<li>
			<p>투자수익률</p>
			<p>연 <?=$invest_return?>%</p>
		</li>
		<li>
			<p>투자기간</p>
			<p><?=$invest_period?><?=$invest_period_unit?></p>
		</li>
		<li>
			<p>상환방법</p>
			<p><?=$repay_pay_title?></p>
		</li>
	</ul>

	<? if($description = nl2br($PRDT['product_description'])) { // 상품설명 ?>
		<div class="p_i_t_i">
			<?=$description?>
		</div>
	<? } ?>

</div>

<!-- 실시간 현장 라이브 -->
<? if($live_link) { ?>
<div class="hello_tv">
	<img src="/img/sub_m/live_banner.jpg" alt="실시간 현장 방송" onClick="<?=$live_link?>">
</div>
<? } ?>

<!-- 예상수익금 -->
<div class="pre_earn clearfix" style="margin-top:0;">
	<div class="pre_earn_tit">
		<p>예상수익금</p>
		<span></span>
	</div>

	<ul class="pre_earn_c">
		<li>투자금액</li>
		<li><input type="text" name="principal_value" value="<? echo number_format(5000000);?>" placeholder="투자금액을 입력하세요. 예) 1,000,000원" onkeyup="formatNumber(this);"></li>
		<li class="equal"><a href="#" onclick="simulation(); return false;">계산하기</a></li>
	</ul>
	<p class="earn_btn"><?=$invest_period.$invest_period_unit;?>동안 총 예상 투자 수익금은<br><strong>0</strong>원(세후) 입니다.</p>
</div>

<!-- 안전장치 업데이트 -->

<?
if ($PRDT['extend_8']) {

	if ($_REQUEST['prd_idx']<='133') {
		echo "<div class='product_info'>";
		echo $PRDT['extend_8'];
		echo "</div>";
	} else {
		$PRDT['extend_8'] = str_replace("/theme/2018/img" , "/img", $PRDT['extend_8']);
		//echo str_replace('p style="text-align: center;"', 'p style="text-align: center; width:640px; margin-left:200px;"' ,$PRDT['extend_8']);
		echo $PRDT['extend_8'];
		echo "<br/><br/>";
	}

}
?>


<!-- 신한은행 배너 -->
	<? if(G5_IS_MOBILE) { ?>
	<div class="shinhan_ban"><img src="/img/sub_m/shinhan_ban01_m.jpg" width="100%"></div>
	<? } else { ?>
	<div class="shinhan_ban"><img src="/img/sub_m/shinhan_ban01_m.jpg"></div>
	<? } ?>
<!-- 상품자세히보기 -->
<div id="detail_btn" class="detail_btn">
	<p>
		<a href="#" onclick="detailShow(this); return false;">
			상품상세보기
			<span><img src="/img/sub_m/up_btn01.png" alt="Up" class="upDownBtn"></span>
		</a>
	</p>
</div>

<div id="detail_box" class="detail_box" style="width:640px;overflow:hidden;margin-right:0;">
	<?
	if ($PRDT['invest_summary_m']) {
		$PRDT['invest_summary_m'] = str_replace("/investment/" , "/root_investment/", $PRDT['invest_summary_m']);
		$PRDT['invest_summary_m'] = str_replace("/theme/2018/img" , "/img", $PRDT['invest_summary_m']);
		$PRDT['invest_summary_m'] = str_replace("<img " , "<img width=580 ", $PRDT['invest_summary_m']);
		//$PRDT['invest_summary_m'] = str_replace("income __se_tbl_ext" , "dfasdf", $PRDT['invest_summary_m']);
	}
	?>

	<?
	if ($PRDT['invest_summary_m']) {
		$PRDT['invest_summary_m'] = str_replace("investment_info_m.css","",$PRDT['invest_summary_m']);
		$PRDT['invest_summary_m'] = str_replace("invest_point","invest_point2",$PRDT['invest_summary_m']);
	}
	?>
	<?=$PRDT['invest_summary_m']?$PRDT['invest_summary_m']:'';?>

	<!-- 증빙서류 -->
<?
if ($PRDT['extend_9']) {
	$PRDT['extend_9'] = str_replace("/theme/2018/img" , "/img", $PRDT['extend_9']);

}
?>

	<?=($PRDT['extend_9']) ? $PRDT['extend_9'] : '';?>

	<?=($PRDT['extend_7']) ? $PRDT['extend_7'] : '';?>

</div>

<? // 네이버 파노라마(로드뷰) 2018-07-18 전승찬 추가

if ($PRDT['loadview_url'] ) {
	$tmp1 = explode("?",$PRDT['loadview_url']);
	parse_str($tmp1[1]);
	?>


	<!--script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?clientId=BWQ7u9E9S5otYZAgvbwU&submodules=panorama"></script-->
	<script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?clientId=EOclGR9rsmKxTz5cuOIf&submodules=panorama"></script>
	<script type="text/javascript">
		$(".prdt_summ").append("<div id='pano' style='width:100%; height:600px;border:1px solid black;margin:10px auto 5px;'></div><div style='width:450px;margin:5px auto 30px;text-align:center;'>화면을 클릭한 후 상하좌우로 움직여서 현장을 확인하세요!<br/><a onclick='load_naver_map();' style='cursor:pointer;font-weight:bold;'>[로드뷰 초기화는 <span style='text-decoration:underline;'>여기</span>를 클릭해 주세요.]</a></div>");

		function load_naver_map() {
			var pano = new naver.maps.Panorama(document.getElementById("pano"), {
				size               : new naver.maps.Size(550, 400),
				panoId             : "<?=$vrpanoid?>",
				pov                : {pan : <?=$vrpanopan?>, tilt : <?=$vrpanotilt?>, fov : <?=$vrpanofov?> },
				aroundControl      : true,
				MapDataControl     : false,
				zoomControl        : true,
				zoomControlOptions : {position: naver.maps.Position.TOP_RIGHT}
			});
		}

		load_naver_map();
	</script>

	<?
}

?>

<!--
<div class="review_front">
	<div class="review_front_t">
		<p>
			헬로펀딩 투자후기
			<span></span>
		</p>
	</div>
	<? // echo review('theme/basic', 6, 70); ?>
</div>
-->
<!--div class="partner_logo">
	<p>
		제휴사<span></span>
	</p>
	<p style="height:30px;"></p>
	<ul>
		<li>
			<a href="https://www.shinhan.com/index.jsp" target="_blank"><img src="/img/main/client_logo01_m.jpg" alt="신한은행" ></a>
		</li>
		<li>
			<a href="http://p2plending.or.kr/" target="_blank"><img src="/img/main/client_logo02_m.jpg" alt="한국P2P금융협회" ></a>
		</li>
		<li>
			<a href="http://www.hanatrust.com/" target="_blank"><img src="/img/main/client_logo03_m.jpg" alt="하나자산신탁" ></a>
		</li>
	</ul>
	<ul>
		<li>
			<a href="http://www.scri.co.kr/index.jsp" target="_blank"><img src="/img/main/client_logo04_m.jpg" alt="SCR서울신용평가" ></a>
		</li>
		<li>
			<a href="http://www.karic.or.kr/" target="_blank"><img src="/img/main/client_logo05_m.jpg" alt="한국부동산리츠투자자문협회" ></a>
		</li>
		<li>
			<a href="http://hyunlaw.co.kr/renew/kor/main/main.asp" target="_blank"><img src="/img/main/client_logo06_m.jpg" alt="법무법인현" ></a>
		</li>
	</ul>
	<ul>
		<li>
			<a href="https://www.kyoborealco.co.kr/realco/indexRealco.html" target="_blank"><img src="/img/main/client_logo07_m.jpg" alt="교보리얼코" ></a>
		</li>
		<li>
			<a href="http://seinacct.co.kr/" target="_blank"><img src="/img/main/client_logo08_m.jpg" alt="세인세무법인" ></a>
		</li>
		<li>
			<a href="http://www.apexlaw.co.kr/" target="_blank"><img src="/img/main/client_logo09_m.jpg" alt="법무법인 에이펙스" ></a>
		</li>
	</ul>
	<ul>
		<li>
			<a href="http://wowtv.co.kr/" target="_blank"><img src="/img/main/client_logo10_m.jpg" alt="한국경제티브이" ></a>
		</li>
		<li>
			<a href="http://www.fidelisam.co.kr/" target="_blank"><img src="/img/main/client_logo11_m.jpg" alt="피델리스" ></a>
		</li>
		<li>
			<a href="http://www.kyungilnet.co.kr/main/main.php" target="_blank"><img src="/img/main/client_logo12_m.jpg" alt="경일감정평가법인" ></a>
		</li>
	</ul>
	<p style="height:30px;clear:both;"></p>
</div-->

</div>

<script type="text/javascript">
simulation(5000000);

// faq
$(".faq a").click(function() {
	$(this).next().slideToggle("fast").parent().siblings().children("dd").hide();
	return false;
});

// 상품자세히 보기 슬라이드
function detailShow() {
	var imgSrc = $("img.upDownBtn");
	$("#detail_box").slideToggle("fast", function(e) {
		if($(this).is(':visible')) {
			$("div#detail_btn a").css("background", "#1E88EC").css("color", "#FFF");
			imgSrc.attr("src", imgSrc.attr("src").replace("up_btn01", "down_btn01"));
		}
		else {
			$("div#detail_btn a").css("background", "#e7f3ff").css("color", "#000");
			imgSrc.attr("src", imgSrc.attr("src").replace("down_btn01", "up_btn01"));
		}
	});
}

// 라이브 티비
function popupOpen() {
	var popUrl = "live.html"; //팝업창에 출력될 페이지 URL
	var popOption = "width=640, height=494, top=250, left=600, resizable=no, scrollbars=no, status=no;"; //팝업창 옵션(optoin)
	window.open(popUrl,"",popOption);
}

var galleryTop;
var galleryThumbs;
$(document).ready(function() {
	// 사전투자 설명
	var msg = "펀딩오픈 시간에 투자참여가 어려운 회원분들을 위하여 사전에 투자할 수 있는 서비스입니다. <br><br> <strong>사전 투자 유의사항</strong> <br><br>본 상품은 사전 투자가 가능한 상품으로 목표금액의 <? echo (int)$PRDT['advance_invest_ratio']?>%까지 사전 투자가 진행됩니다. \
				<p>1. 사전 투자는 가상계좌의 예치금으로 투자 가능합니다.</p> \
				<p>2. 사전 투자는 신청순으로 적용됩니다.</p>";

	$('#question_1').webuiPopover({
		title: "사전 투자 서비스란?",
		content: msg,
		closeable: true,
		width: 330,
		trigger: "click",
		placement: 'bottom',
		backdrop: false
	});

	galleryTop = new Swiper('#gallery', {
		spaceBetween: 10,
		onSlideChangeEnd: function() {
			$(document).trigger("slide-change");
		},
		loopedSlides: $("#gallery .swiper-wrapper .swiper-slide").length,
		effect: "fade",
		observer: true,
		observeParents: true,
	});

	galleryThumbs = new Swiper('#gallery-thumbs', {
		spaceBetween: 10,
		centeredSlides: false,
		slidesPerView: 3,
		observer: true,
		observeParents: true,
	});

	// galleryTop.controller.control = galleryThumbs;
	// galleryThumbs.controller.control = galleryTop;

	$(document).on("click", "#gallery-thumbs .swiper-slide", function(e) {
		var index = $(this).index();
		galleryTop.slideTo(index);
	});

	setInterval(function() {
		if($('#invest_finished').val() == 'false') {
			$.ajax({
				type: "GET",
				url: "/root_investment/ajax_investment.php",
				dataType: "json",
				data: {prd_idx:<?=$prd_idx;?>},
				success: function(json) {
					// 3초간 데이터 조회
					// 바뀌는 값들 모집금액, 투자모집률, 남은 모집금액, 버튼들
					$('#invest_finished').val(json.data.invest_finished); // 현재진행상태
					//$('#progressLayer').attr('style', "left:" + (json.data.progress.replace('%', '') <= 100 ? (json.data.progress.replace('%', '') - 11.7) : 88.2)+'%');  // 진행률 및 잔액출력 레이어
					$('#progressBar').attr('style', "width:" + json.data.progress_width); // 진행률 표시
					$('#progressBtn').html("<li>"+json.data.button_data1.replace("/investment","<?=BSC_URL?>/investment")+"</li>");

					// progressData, totalRecruitValue
					$('#progressData').text(json.data.progress); // 진행률
					$('#totalRecruitValue').text(json.data.total_invest_amount_k); // 현재 모집금액
					//$('#totalRecruitValue').text(json.data.need_recruit_amount_k); // 남은 모집금액
				},
				error: function(e) { console.log("ajax_investment.php error"); }
			});
		}
	}, 3 * 1000);
});

$("a[data-toggle='sns_share']").click(function(e) {
	e.preventDefault();
	var current_url = window.location.href;
	var _this       = $(this);
	var sns_type    = _this.attr('data-service');
	var href        = current_url;
	var title       = _this.attr('data-title');
	var img         = $("meta[name='og:image']").attr('content');
	var loc         = "";

	if( ! sns_type || !href || !title) return;

	if(sns_type == 'facebook') { loc = '//www.facebook.com/sharer/sharer.php?u='+href+'&t='+title; }
	else if(sns_type == 'twitter') { loc = '//twitter.com/home?status='+encodeURIComponent(title)+' '+href; }
	else if(sns_type == 'google') { loc = '//plus.google.com/share?url='+href; }
	else if(sns_type == 'pinterest') { loc = '//www.pinterest.com/pin/create/button/?url='+href+'&media='+img+'&description='+encodeURIComponent(title); }
	else if(sns_type == 'kakaostory') { loc = 'https://story.kakao.com/share?url='+encodeURIComponent(href); }
	else if(sns_type == 'band') { loc = 'http://www.band.us/plugin/share?body='+encodeURIComponent(title)+'%0A'+encodeURIComponent(href); }
	else if(sns_type == 'naver') { loc = "http://share.naver.com/web/shareView.nhn?url="+encodeURIComponent(href)+"&title="+encodeURIComponent(title); }
	else if(sns_type == 'url_copy') { copy_trackback(href); }
	else if(sns_type == 'instagram') { alert("현재 지원하지 않는 기능입니다."); loc = ""; return false; }
	else { return false; }

	if(sns_type != 'url_copy') { window.open(loc); }

	return false;
});

function copy_trackback(trb) {
	var IE=(document.all)?true:false;
	if(IE) {
		if(confirm("이 글의 트랙백 주소를 클립보드에 복사하시겠습니까?"))
			window.clipboardData.setData("Text", trb);
	} else {
		temp = prompt("이 글의 트랙백 주소입니다. Ctrl+C를 눌러 클립보드로 복사하세요", trb);
	}
}

$(document).on("keyup", 'input:text[name="principal_value"]', function() {
	var earn_btn = $("p.earn_btn");
	if(earn_btn.css("display") == "block") {
		earn_btn.hide();
	}
});

// 예상수익금 계산
function simulation(price) {
	var price = (price || '0');
	var pattern = /^[0-9]+$/;
	var prd_idx = ($("input:hidden[name='prd_idx']").val() || 0);
	var principal_value = ($("input:text[name='principal_value']").val() || price).replace(/[\D\s\._\-]+/g, "");
	var min_invest_limit = (<?=$CONF['min_invest_limit']?> || 0);

	if(principal_value == "") {
		alert("투자 금액을 입력해주새요");
		$("input:text[name='principal_value']").focus();
		return;
	}
	if(!pattern.test(principal_value) ) {
		alert("투자 금액에 사용할수 없는 문자가 있습니다. 숫자만  입력해주세요.");
		$("input:text[name='principal_value']").focus();
		return;
	}

	if(principal_value < min_invest_limit) {
		alert("최소 금액은 " + number_format(min_invest_limit) + "원 이상 입니다.");
		$("input:text[name='principal_value']").focus();
		return;
	}

	if(principal_value >= <?=$CONF['min_invest_limit']?>) {
		$.ajax({
			url : "/root_investment/ajax_simulation.php",
			type: "POST",
			data : {prd_idx: prd_idx, ajax_principal_value: principal_value, onlyInterest: 'Y'},
			success: function(data, textStatus, jqXHR)
			{
				if(data == "ERROR") {
					alert("시스템 오류입니다. 관리자에 문의해주세요.");
				}
				else if(data == "ERROR-MIN-PRICE") {
					alert("최소 금액은 " + number_format(min_invest_limit) + "원 이상 입니다.");
					$("input[name='principal_value']").focus();
					return;
				}
				else{
					var data = JSON.parse(data);
					if(data.success) {
						$("p.earn_btn").show();
						$("p.earn_btn > strong").text(data.totalInterestPrice);
					}
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log("ajax_simulation.php ERROR");
			}
		});
	}
}

function formatNumber(numberString) {
	var selection = window.getSelection().toString();
	if(selection !== '') {
		return;
	}

	if( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
		return;
	}
	var input = numberString.value;
	var input = input.replace(/[\D\s\._\-]+/g, "");
	input = input ? parseInt( input, 10 ) : 0;
	numberString.value = (input === 0 ) ? "" : input.toLocaleString('ko-KR', {maximumSignificantDigits : 21});
}
</script>

<?

// 투자위험고지 팝업
include_once(HF_PATH."/popup/inc_invest_warning_agree_form.php");

if($prd_idx == '119') {
	include_once(HF_PATH.'/popup/inc_product_119_notice.php');
}

// 라이브스트림 준비중 팝업
if($PRDT['stream_url1']=='ready') {
	include_once(HF_PATH.'/popup/inc_stream_ready.php');
}

if($co['co_include_tail']) {
    @include_once($co['co_include_tail']);
}else {
    include_once('./_tail.php');
}
?>
