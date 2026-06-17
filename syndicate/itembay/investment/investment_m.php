<?
###############################################################################
## 투자상품 상세보기
###############################################################################

include_once('./_common.php');


$g5['title'] = '투자상품 상세보기';
$g5['top_bn'] = "/images/investment/sub_investment.jpg";
$g5['top_bn_alt'] = "투자하기 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";


$special_user = ($is_admin=='super' || in_array($member['mb_id'], array('hellosiesta','sori9th','judero831','hellofunding','test070','test999','master','romrom'))) ? true : false;
$tmp_special_user = ( in_array($member['mb_id'], array('samo','samo001','samo002')) ) ? true : false;

$prd_idx = trim($_REQUEST['prd_idx']);

if($prd_idx=='') { goto_url('/'); exit; }
if(!preg_match('/^[0-9]{0,10}$/', $prd_idx)) { header('Location: /', true, 302); exit; }

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
$sql.= ($special_user || $tmp_special_user) ? "" : " AND A.display='Y'";
$PRDT = sql_fetch($sql);

if($PRDT) {

	$PRDT['extend_7']            = preg_replace("/(https:\/\/hellofunding\.co\.kr)/i", "http://www.hellofunding.kr", $PRDT['extend_7']);
	$PRDT['extend_8']            = preg_replace("/(https:\/\/hellofunding\.co\.kr)/i", "http://www.hellofunding.kr", $PRDT['extend_8']);
	$PRDT['extend_9']            = preg_replace("/(https:\/\/hellofunding\.co\.kr)/i", "http://www.hellofunding.kr", $PRDT['extend_9']);
	$PRDT['extend_10']           = preg_replace("/(https:\/\/hellofunding\.co\.kr)/i", "http://www.hellofunding.kr", $PRDT['extend_10']);
	$PRDT['invest_summary']      = preg_replace("/(https:\/\/hellofunding\.co\.kr)/i", "http://www.hellofunding.kr", $PRDT['invest_summary']);
	$PRDT['invest_summary_m']    = preg_replace("/(https:\/\/hellofunding\.co\.kr)/i", "http://www.hellofunding.kr", $PRDT['invest_summary_m']);
	$PRDT['core_invest_point']   = preg_replace("/(https:\/\/hellofunding\.co\.kr)/i", "http://www.hellofunding.kr", $PRDT['core_invest_point']);
	$PRDT['product_summary']     = preg_replace("/(https:\/\/hellofunding\.co\.kr)/i", "http://www.hellofunding.kr", $PRDT['product_summary']);
	$PRDT['product_description'] = preg_replace("/(https:\/\/hellofunding\.co\.kr)/i", "http://www.hellofunding.kr", $PRDT['product_description']);


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
			$invest_button = '<a href="javascript:;" onClick="alert(\'본 서비스는 로그인이 필요합니다.\');fn_login_check();" class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';

			$advance_invest_button = '<a id="btn_advance_invest" href="javascript:;" onClick="alert(\'본 서비스는 로그인이 필요합니다.\');fn_login_check();" class="btn_big_maple">사전투자하기</a>';
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
			//$invest_button = '<a href="/member/login.php?url='.urlencode($_SERVER['REQUEST_URI']).'" class="'.$button_class.'">투자하기</a>';

			$invest_button = '<a href="#none" onClick="fn_login_check();" class="'.$button_class.'" >투자하기</a>';
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
$invest_summary = (G5_IS_MOBILE) ? $PRDT['invest_summary_m'] : $PRDT['invest_summary'];


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

if($co['co_include_head']) @include_once($co['co_include_head']);
else include_once(HF_PATH.'/hf_head.php');

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

<script src="<?=HF_URL?>/js/jquery.blink.js"></script>

<link rel="stylesheet" type="text/css" href="/css/<?=(G5_IS_MOBILE?'investment_info_old_m':'investment_info_old')?>.css">
<input type="hidden" name="prd_idx" value="<?=$prd_idx;?>">
<input type="hidden" name="invest_finished" id="invest_finished" value="<?=($invest_finished) ? 'true' : 'false'; ?>">

<!-- 본문내용 START -->
<div id="content">

	<div class="content">
<style>
/*** 상품슬라이드 ***/
.product_cont {position: relative;width:99%;height:539px;min-width: 980px;}
.product_cont .main_image {position: absolute;width:100%;height:528px;min-width: 980px;z-index: -2;top: 0; left: 0; right: 0; bottom: 0;}
.product_cont .main_image img {
	min-width: 980px;
	position: absolute; left: 50%; top: 50%; height: auto; width: auto; -webkit-transform: translate(-50%,-50%); -ms-transform: translate(-50%,-50%); transform: translate(-50%,-50%);
			image-rendering: -moz-auto; /*Firefox*/
			image-rendering: -o-auto; /*Opera*/
			image-rendering: -webkit-optimize-contrast; /*Webkit*/
			image-rendering: auto;
			-ms-interpolation-mode: bicubic; /*IE*/
			-webkit-perspective: 1;
}

@media screen and (max-width:1366px){
.product_cont {position: relative;width:99%;height:539px;}
.product_cont .main_image {position: absolute;width:100%;height:528px;z-index: -2;top: 0; left: 0; right: 0; bottom: 0;overflow:hidden;}
.product_cont .main_image img {
		position: absolute; left: 50%; top: 50%; height: auto; min-width: 1366px; -webkit-transform: translate(-50%,-50%); -ms-transform: translate(-50%,-50%); transform: translate(-50%,-50%);
		image-rendering: -moz-auto; /*Firefox*/
		image-rendering: -o-auto; /*Opera*/
		image-rendering: -webkit-optimize-contrast; /*Webkit*/
		image-rendering: auto;
		-ms-interpolation-mode: bicubic; /*IE*/
		-webkit-perspective: 1;}
}

.product_cont .product_wrapper {width:980px; margin:0 auto;color:#000;text-align:left;}
.product_cont .product_wrapper .product_info {float:left;}
.product_cont .product_wrapper .product_info .numb {font-size:22px;font-family:'spoqahansans';font-weight:600;display:block;color:#073190;float:left;padding-top:60px;letter-spacing:-1px;}
.product_cont .product_wrapper .product_info .titles {color:#000;font-size:22px;font-family:'spoqahansans';font-weight:400;letter-spacing:-2px;display:block;clear:both;}
.product_cont .product_wrapper .product_info .date {color:#000;font-size:18px;font-family:'spoqahansans';font-weight:400;display:block;clear:both;}

.product_cont .product_wrapper .product_info .p_flags {position: relative;width:100%;padding: 0;}
.product_cont .product_wrapper .product_info .p_flags ul {overflow: hidden;}
.product_cont .product_wrapper .product_info .p_flags ul li {list-style: none;padding:2px 10px;text-align:center;border-radius:3px;color:#FFF;font-size:14px;line-height:22px;font-family:'spoqahansans';font-weight:400;display:block;float:left;margin:65px 8px 0 0;letter-spacing:1px;cursor: pointer;}
.product_cont .product_wrapper .product_info .p_flags ul li:after {content: "";display: block;clear: both;}

.product_cont .product_wrapper .product_info .percent {color:#073190;font-size:50px;font-family:'spoqahansans';font-weight:300;display:inline-block;clear:both;padding-right:30px;line-height:15px;}
.product_cont .product_wrapper .product_info .percent strong {color:#000;font-size:15px;font-family:'spoqahansans';font-weight:400;margin-top:0;line-height:60px;}
.product_cont .product_wrapper .product_info .percent b {color:#073190;font-size:30px;font-family:'spoqahansans';font-weight:300;}

.product_cont .product_wrapper .product_info .during {color:#073190;font-size:65px;font-family:'spoqahansans';font-weight:300;display:inline-block;}
.product_cont .product_wrapper .product_info .during strong {color:#000;font-size:16px;font-family:'spoqahansans';font-weight:400;}
.product_cont .product_wrapper .product_info .during b {color:#073190;font-size:30px;font-family:'spoqahansans';font-weight:300;}

.product_cont .product_wrapper .product_info .total {color:#073190;font-size:65px;font-family:'spoqahansans';font-weight:300;display:inline-block;}
.product_cont .product_wrapper .product_info .total strong {color:#000;font-size:16px;font-family:'spoqahansans';font-weight:400;}
.product_cont .product_wrapper .product_info .total b {color:#073190;font-size:30px;font-family:'spoqahansans';font-weight:300;}

/* 소셜미디어 URL 버튼 */
.product_cont .product_wrapper .sns_share {margin-left:940px;padding-top:15px;z-index:60;}
.product_cont .product_wrapper .sns_share li {list-style: none;float:left;padding-left:12px;}
.product_cont .product_wrapper .sns_share li img {width: 29px;}

/* 투자하기 진행율 */
.product_cont .product_wrapper .process_wrap {clear:both;width:980px;display:inline-block;margin-top:40px;}
.product_cont .product_wrapper .process_wrap .process {position: relative;background-color:#fff;width:100%;height:10px;margin-top:90px;}
.product_cont .product_wrapper .process_wrap .process .process_bar {position:relative;min-width: 0;max-width: 100%;height:10px;background: linear-gradient(to right, #00a0e9, #073190);}
.product_cont .product_wrapper .process_wrap .process .process_top_bar {position:relative;min-width: 0;max-width: 100%;height:10px;}
.product_cont .product_wrapper .process_wrap .process .process_tag {position:absolute;display: block;background: url('https://www.hellofunding.co.kr/theme/2018/img/sub/invest_percent_info.png') center no-repeat;width:272px;height:74px;text-align:center;font-size:14px;font-weight:400;top: -70px;left: 0;}
.product_cont .product_wrapper .process_wrap .process .process_tag span {margin-top:5px;display:block;font-family:'spoqahansans';}
.product_cont .product_wrapper .process_wrap .process .process_tag .p_t_n {margin-top:2px;display:inline-block;font-family:'spoqahansans';font-size:20px;font-weight:400;line-height:18px;color:#e91d1d;}
.product_cont .product_wrapper .process_wrap .process .process_tag .p_t_t {margin-top:2px;display:inline-block;font-family:'spoqahansans';font-size:20px;font-weight:400;line-height:18px;color:#073190;}
.product_cont .product_wrapper .process_wrap .process .process_total {text-align:right;margin-top:5px;font-family:'spoqahansans';font-size:18px;font-weight:400;color:#000;}

/* 투자하기 버튼 */
.product_cont .product_wrapper .process_btn {text-align:center;margin-top:0;height:55px;}
.product_cont .product_wrapper .process_btn ul {float: none;text-align: center;clear:both;overflow: inherit;}
.product_cont .product_wrapper .process_btn ul:after {content: "";display: block;clear: both;}
.product_cont .product_wrapper .process_btn ul li {list-style: none;min-width: 180px;display: inline-block;overflow: inherit !important;height:53px;}
.product_cont .product_wrapper .process_btn ul li a {display: block;color:#FFF;font-size:16px;line-height:53px;border:0px;}
.product_cont .product_wrapper .process_btn ul li:hover,
.product_cont .product_wrapper .process_btn ul li:focus {background: transparent;}
/* .product_cont .product_wrapper .process_btn ul li.invest {background-color:#006ADF;border-radius:3px;border:0px;border:1px solid #006ADF;} */

.product_cont .product_wrapper .process_btn ul li.simulation {background-color:#FFF;border-radius:3px;border:1px solid #c4c4c4;}
.product_cont .product_wrapper .process_btn ul li.simulation a {color: #000;}
.product_cont .product_wrapper .process_btn ul li.auto_invest {background-color:#EC8203;border-radius:3px;}
.product_cont .product_wrapper .process_btn ul li.reser_invest {background-color:#16B911;border-radius:3px;}


/*** 예상수익금***/
.pre_earn {width:1150px;margin:0 auto ;background-color:#f7f8fa;margin-top:90px;}
.pre_earn .pre_earn_tit {font-size:26px;font-weight:500;font-family:'spoqahansans';color:#000;border-bottom:1px solid #143fa8;line-height:56px;background-color:#fff;}
.pre_earn .pre_earn_c {padding:70px 0 20px 0;text-align:center;}
.pre_earn .pre_earn_c li {list-style: none;display:inline-block;vertical-align:top;font-size:24px;font-weight:300;font-family:'spoqahansans';line-height:50px;}
.pre_earn .pre_earn_c li:first-child {display:inline-block;vertical-align:top;font-size:24px;font-weight:300;font-family:'spoqahansans';line-height:50px;padding-right:10px;}
.pre_earn .pre_earn_c li input {width:350px;height:48px;border:1px solid #073190;border-radius:5px;background-color:#fff;color:#000;vertical-align:top;padding: 0 10px;font-size:18px;text-align:right;}
.pre_earn .pre_earn_c li input::-webkit-input-placeholder {background-color:#fff;color:#96a7d1;font-size:18px;}
.pre_earn .pre_earn_c li input::-moz-placeholder {background-color:#fff;color:#96a7d1;font-size:18px;}
.pre_earn .pre_earn_c li input:-ms-input-placeholder {background-color:#fff;color:#96a7d1;font-size:18px;}
.pre_earn .pre_earn_c .equal {background-color:#1E89EC;width:120px;height:50px;border-radius:5px;margin-left:3px;}
.pre_earn .pre_earn_c .equal:hover,
.pre_earn .pre_earn_c .equal:focus {background-color:#073190;}

.pre_earn .pre_earn_c .equal a {display: block;color:#fff;border-radius:5px;}
.pre_earn .earn_btn {padding-top:10px;font-size:22px;font-weight:300;font-family:'spoqahansans';text-align:center;}
.pre_earn .earn_btn strong {color:#073190;font-size:28px;font-weight:400;font-family:'spoqahansans';}
.pre_earn .earn_info {text-align:center;width:100%;}
.pre_earn .earn_info p {display:inline-block;width:32%;line-height:38px;margin:0 auto;}
.pre_earn .earn_info .claim-mark {display:inline-block;vertical-align:top;margin-top:9px;line-height:18px; border-radius:100px; background-color:#bfbfbf; width:18px; height:18px;font-size:15px;color:#fff; font-family:'spoqahansans'; font-weight:400; text-align:center; cursor:pointer; }
.pre_earn .simulation_detail_btn {clear:both;padding:50px 0 50px 0;text-align:center;color:#1E89EC;font-size:20px;cursor:pointer;}
.pre_earn .blind {display:none;}
/*.pre_earn .simulation_detail_btn {clear:both;margin:0 auto;width:26%;text-align:center;border-radius:5px;padding:20px 20px;background-color:#cce0ec;color:#000;font-size:18px;}*/

</style>

	<div class="product_cont">
		<div class="main_image">
		<? if($PRDTIMG[0]) { ?><img src="http://www.hellofunding.co.kr<?=$PRDTIMG[0]?>" alt="<?=$print_sdate;?>" style="width:100%;height:100%;"><? } ?>
		</div>
		<div class="product_wrapper">
			<div class="product_info">
				<!-- <span class="numb"><? echo ($productNum) ? '['.$productNum.']' : '';?></span> //-->
				<div class="p_flags">
					<ul>
						<?=$cFlag?><?=$aiFlag?><?=$conFlag?><?=$newFlag?><?=$srmFlag?><?=$adiFlag?><?=$pgFlag?><?=$adpFlag?>
					</ul>
				</div>
				<span class="titles"><?=$PRDT['title']?></span>
				<span class="date">모집기간 : <?=$print_sdate?> ~</span>

				<!-- 상품 현황 -->
				<span class="percent">
					<strong>예상 투자수익률(연)</strong><br>
					<?=$invest_return?><b>%</b>
				</span>
				<span class="percent">
					<strong>투자기간</strong><br>
					<?=$invest_period?><b><?=$invest_period_unit?></b>
				</span>
				<span class="percent">
					<strong>모집금액</strong><br>
					<?=$print_recruit_amount?><b>원</b>
				</span>
			</div>
			<ul class="sns_share">
				<li>
					<a href="#" data-toggle="sns_share" data-service="facebook" data-title="페이스북 SNS공유">
						<img src="<?=G5_THEME_IMG_URL?>/sub/sns_f_btn01.png" alt="facebook">
					</a>
				</li>
				<li>
					<a href="#" data-toggle="sns_share" data-service="naver" data-title="네이버 SNS공유">
						<img src="<?=G5_THEME_IMG_URL?>/sub/sns_b_btn01.png" alt="naver">
					</a>
				</li>
				<li>
					<a href="#" data-toggle="sns_share" data-service="kakaostory" data-title="카카오스토리 SNS공유">
						<img src="<?=G5_THEME_IMG_URL?>/sub/sns_k_btn01.png" alt="kakao">
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
						<img src="<?=G5_THEME_IMG_URL?>/investment/url_icon.png" alt="url_copy">
					</a>
				</li>
			</ul>
			<div class="process_wrap">
				<div class="process">
					<div id="progressLayer" class="process_tag" style="left:<?=($product_invest_percent <= 100) ? $product_invest_percent - 11.7 : 88.2;?>%">
						<span>투자모집률 / 모집된 금액</span>
						<strong class="p_t_n" id="progressData"><?=$product_invest_percent;?>%</strong> / <strong class="p_t_t" id="totalRecruitValue"><?=price_cutting($PRDT["total_invest_amount"]+0);?>원</strong>
<? if(false) { ?>
<!--
						<span>투자모집률 / 남은 모집금액</span>
						<strong class="p_t_n" id="progressData"><?=$product_invest_percent;?>%</strong> / <strong class="p_t_t" id="totalRecruitValue"><?=price_cutting($PRDT["recruit_amount"]-$PRDT["total_invest_amount"]+0);?>원</strong>
-->
<? } ?>
					</div>
					<div id="progressBar" class="process_bar" style="width:<?=($product_invest_percent <= 100) ? $product_invest_percent : 100;?>%"></div>
					<div class="process_total">모집금액 : <?=($PRDT["recruit_amount"]) ? number_format($PRDT["recruit_amount"]) : 0;?>원</div>
				</div>
			</div>
			<div class="process_btn">
				<ul id="processBtn">

					<? if($invest_finished == false) { ?>
					<!--<li class="simulation"><a href="/investment/simulation.php?prd_idx=<?=$PRDT['idx']?>" class="btn_big_link">투자시뮬레이션</a></li>-->
					<? } ?>

					<? if($invest_button) { ?>
					<li class="invest"><?=$invest_button?></li>
					<? } ?>

					<? if($PRDT['ai_grp_idx']) { ?>
					<li class="auto_invest"><a href="/deposit/deposit.php?tab=5" class="btn_big_orange">자동투자설정</a></li>
					<? } ?>

					<? if($advance_invest_button) { ?>
					<li class="reser_invest"><?=$advance_invest_button?></li>
					<? } ?>

					<? if(!$is_member && $invest_finished) { ?>
					<li><a id="reqsms_btn2" class="btn_big_blue">다음 상품 알림받기</a></li>
					<? } ?>

				</ul>
			</div>

			<? if($PRDT['product_summary']) { echo $PRDT['product_summary']; } ?>
		</div>
	</div>

	<!-- 예상 수익금-->
	<div class="pre_earn" style="margin-bottom:-100px;">
		<!--p class="pre_earn_tit">예상 수익금</p-->
		<ul class="pre_earn_c">
			<li>지금 이 상품에</li>
			<li><input type="text" name="principal_value" value="<?=number_format(5000000)?>" maxlength="11" placeholder="투자금액을 입력하세요. 예) 1,000,000원" onkeyup="formatNumber(this);simulation();"></li>
			<li>원을 투자하시면</li>
			<!--<li class="equal"><a href="javascript:;" onclick="simulation();">계산하기</a></li>-->
		</ul>
		<div class="earn_info">
			<p class="earn_btn">
				예상 총 실수익금(세후) <span id="earninfo1-claim-mark" class="claim-mark">?</span><br/>
				<strong id="ajxTotalInterestPrice">0</strong>원
			</p>
<? if($PRDT['invest_period'] > 1) { /* 2018-12-07 이정환 차장 요청으로 블락처리 */ ?>
			<p class="earn_btn <?=($PRDT['open_datetime'] < '2018-08-31 09:00:00')?'blind':'';?>" style="width:1px;height:1px;overflow:hidden;">
				월 평균 예상 수익금 지급액 <span id="earninfo2-claim-mark" class="claim-mark">?</span><br/>
				<strong id="ajxInvestMonth">0</strong>개월 동안 매월 <strong id="ajxMonthAvrPrice">0</strong>원
			</p>
<? } ?>
			<p class="earn_btn <?=($PRDT['open_datetime'] < '2018-08-31 09:00:00')?'blind':'';?>">
				은행예금 대비 수익<span id="earninfo3-claim-mark" class="claim-mark">?</span><br/>
				<strong id="ajaDiffEarning">0</strong>배
			</p>
		</div>
		<div class="simulation_detail_btn" onClick="location.href='simulation.php?prd_idx=<?=$prd_idx?>';">투자시뮬레이션 자세히보기 > </div>
	</div>
	<script type="text/javascript">
		var msg = "본 상품의 투자금액에 따른 수익금에서 세금과 플랫폼 이용료를 제외한 금액이며, 조기상환 등 투자기간 변동에 의해 실제와 다를 수 있습니다.";
		$('#earninfo1-claim-mark').webuiPopover({ title: "예상 총 실수익금(세후)", content: msg, closeable: true, width: 330, height: 70, trigger: "click", placement: 'bottom', backdrop: false});
		var msg = "투자기간 중 헬로펀딩이 매월 지급해 드리는 세후 수익금으로, 이자산정일에 따라 변동될 수 있습니다.";
		$('#earninfo2-claim-mark').webuiPopover({ title: "월 평균 지급수익금 ", content: msg, closeable: true, width: 330, height: 50, trigger: "click", placement: 'bottom', backdrop: false});
		var msg = "1금융권 정기예금 평균 금리 1.7% 대비 본 투자상품의 수익률입니다. (각 세후 실수익 기준)";
		$('#earninfo3-claim-mark').webuiPopover({ title: "은행에 예금시보다 ", content: msg, closeable: true, width: 330, height: 50, trigger: "click", placement: 'bottom', backdrop: false});
	</script>




		<!-- 상품 슬라이드 시작 -->
		<?php IF(G5_IS_MOBILE) { ?>
		<!-- 모바일 //-->
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
					<ul id="processBtn" class="btn_all">

						<? if($invest_finished == false) { ?>
						<!--<li class="simulation"><a href="/investment/simulation.php?prd_idx=<?=$PRDT['idx']?>" class="btn_big_link">투자시뮬레이션</a></li>-->
						<? } ?>

						<? if($invest_button) {?>
						<li class="invest"><?=$invest_button?></li>
						<? } ?>

						<? if($advance_invest_button) { ?>
						<li class="reser_invest"><?=$advance_invest_button?></li>
						<? } ?>

						<? if($PRDT['ai_grp_idx']) { ?>
						<!--<li class="auto_invest"><a href="/deposit/deposit.php?tab=5" class="btn_big_orange">자동투자설정</a></li>//-->
						<? } ?>

						<? if(!$is_member && $invest_finished) { ?>
						<li><a id="reqsms_btn2" class="btn_big_blue">다음 상품 알림받기</a></li>
						<? } ?>

					</ul>


					<? if($PRDT['product_summary']) echo $PRDT['product_summary']; ?>

					<div class="sns_share">
						<ul>
							<li>
								<a href="#" data-toggle="sns_share" data-service="facebook" data-title="페이스북 SNS공유">
									<img src="<?=G5_THEME_IMG_URL?>/sub/sns_f_btn01.png" alt="facebook" width="30">
								</a>
							</li>
							<li>
								<a href="#" data-toggle="sns_share" data-service="naver" data-title="네이버 SNS공유">
									<img src="<?=G5_THEME_IMG_URL?>/sub/sns_b_btn01.png" alt="naver" width="30">
								</a>
							</li>
							<li>
								<a href="#" data-toggle="sns_share" data-service="kakaostory" data-title="카카오스토리 SNS공유">
									<img src="<?=G5_THEME_IMG_URL?>/sub/sns_k_btn01.png" alt="kakao" width="30">
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
									<img src="<?=G5_THEME_IMG_URL?>/investment/url_icon.png" alt="url_copy" width="30">
								</a>
							</li>
						</ul>
					</div>
				</div><!-- p_info_bb -->
			</div><!-- p_info_b -->
		</div><!-- p_info -->
		<?php } ?>
		<!-- 모바일 종료//-->

		<!--투자포인트//-->
		<? if($PRDT['core_invest_point']) { echo $PRDT['core_invest_point']; } ?>


		<!-- 상품개요 -->
		<div class="product_description">
			<p class="product_info_tit">상품 개요</p>
			<p class="product_info_cont">
			<span>
				투자모집액
				<strong><?=price_cutting($PRDT['recruit_amount']);?>원</strong>
			</span>
			<span>
				예상 투자 수익률
				<strong>연 <?=$invest_return?>%</strong>
			</span>
			<span>
				투자기간
				<strong><?=$invest_period.$invest_period_unit;?></strong>
			</span>
			<span>
				상환방법
				<strong><?=$repay_pay_title;?></strong>
			</span>
			</p>

			<? if($description = nl2br($PRDT['product_description'])) { // 상품설명 ?>
			<p class="product_info_cont_c">
				<?=$description; ?>
			</p>
			<? } ?>

		</div>

		<!-- 실시간 현장 라이브 -->
		<? if($live_link) { ?>
		<div class="hello_live">
			<img src="/img/sub_m/live_banner.jpg" alt="실시간 현장 방송" onClick="<?=$live_link?>">
		</div>
		<? } ?>


	<!-- 안전장치 업데이트 -->
	<? if(trim($PRDT['extend_8']) && $PRDT['extend_8']) { ?>
		<?=$PRDT['extend_8']?>
	<? } ?>


		<!-- 신한은행 배너 -->
		<? if(G5_IS_MOBILE) { ?>
		<div class="shinhan_ban"><img src="/img/sub_m/shinhan_ban01_m.jpg" width="100%"></div>
		<? } else { ?>
		<div class="shinhan_ban"><img src="/img/sub_m/shinhan_ban01_m.jpg"></div>
		<? } ?>

		<div id="detail_box" class="detail_box">

			<?=$invest_summary?>

			<!-- 증빙서류 -->
			<?
			if($PRDT['extend_9']) $PRDT['extend_9'] = str_replace("/theme/2018/img" , "/img", $PRDT['extend_9']);
			echo $PRDT['extend_9'];
			?>

			<br/><br/>

			<? if($PRDT['extend_7']) { ?>
				<?=$PRDT['extend_7']?>
			<? } ?>

		</div>

<?
if ($PRDT['loadview_url'] and preg_match('/\bkakao\b/i', $PRDT['loadview_url'] ,$matches)) {
	$tmp1 = explode("?",$PRDT['loadview_url']);
	parse_str($tmp1[1]);
	//echo "panoid (고유값) = > $panoid<br/>";
	//echo "pan (수평각) => $pan<br/>";
	//echo "tile (수직각) => $tilt<br/>";
	//echo "zoom (확대) => $zoom<br/>";
	?>
	<script>
	function isFlashEnabled()
	{
		var hasFlash = false;
		try
		{
			var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
			if(fo) hasFlash = true;
		}
		catch(e)
		{
			if(navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) hasFlash = true;
		}
		return hasFlash;
	}
	var flash_yn = isFlashEnabled();
	</script>
	<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=a1a12feb2e53aac7f2424691b4532110"></script>
	<script>
		$(".prdt_summ").append("<div id='kakao_roadview' style='width:90%; height:500px;border:1px solid black;margin:0 auto 5px;'></div><div style='width:100%;margin:5px auto 30px;text-align:center;'>화면을 클릭한 후 상하좌우로 움직여서 현장을 확인하세요 !</div>");
		//로드뷰를 표시할 div
		var roadviewContainer = document.getElementById('kakao_roadview');

		if (flash_yn) {
			//로드뷰 객체를 생성한다
			var roadview = new kakao.maps.Roadview(roadviewContainer, {
				panoId : <?=$panoid?>, // 로드뷰 시작 지역의 고유 아이디 값
				pan: <?=$pan?>, // 로드뷰 처음 실행시에 바라봐야 할 수평 각
				tilt: <?=$tilt?>, // 로드뷰 처음 실행시에 바라봐야 할 수직 각
				zoom: <?=$zoom?> // 로드뷰 줌 초기값
			});
		} else {
			$("#kakao_roadview").css('background-image','url("/images/bg_pattern.jpg")');
			$("#kakao_roadview").html("<div style='text-align:center;width:290px;height:86px;margin:20% auto;'><b>로드뷰 서비스를 이용하시려면<br/>Adobe Flash Player 설치 및 허용이 필요합니다.<br/><br/><a href='http://get.adobe.com/flashplayer/' target='_blank'>[최신버전 다운로드]</a></b></div>");
		}
	</script>
	<?
} else if ($PRDT['loadview_url'] and preg_match('/\bnaver\b/i', $PRDT['loadview_url'] ,$matches)) {
	// 네이버 파노라마(로드뷰) 2018-07-18 전승찬 추가
	/*
	$tmp1 = explode("?",$PRDT['loadview_url']);
	parse_str($tmp1[1]);
	?>
	<script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?clientId=wgdMUaKHdFdJ8M6hMFJ_&submodules=panorama"></script>
	<script type="text/javascript">
		$(".prdt_summ").append("<div id='pano' style='width:100%; height:600px;border:1px solid black;margin:0 auto 5px;'></div><div style='width:888px;margin:5px auto 30px;text-align:center;'>화면을 클릭한 후 상하좌우로 움직여서 현장을 확인하세요! &nbsp;&nbsp;&nbsp;<a onclick='load_naver_map();' style='cursor:pointer;font-weight:bold;'>[로드뷰 초기화는 <span style='text-decoration:underline;'>여기</span>를 클릭해 주세요.]</a></div>");

		function load_naver_map() {
			var pano = new naver.maps.Panorama(document.getElementById("pano"), {
				size               : new naver.maps.Size(888, 600),
				panoId             : "<?=$vrpanoid?>",
				pov                : {pan : <?=$vrpanopan?>, tilt : <?=$vrpanotilt?>, fov : <?=$vrpanofov?> },
				aroundControl      : true,
				MapDataControl     : true,
				zoomControl        : true,
				zoomControlOptions : {position: naver.maps.Position.TOP_RIGHT}
			});
		}
		load_naver_map();
	</script>
	<?
	*/
}
?>


	</div><!-- .content -->

</div><!-- #content -->

<script type="text/javascript">
//simulation(5000000);

// faq
$(".faq a").click(function() {
	$(".faq p").css("display","none");
	$(this).next().slideToggle("fast");
	//$(this).next().slideToggle("fast").parent().siblings().children("dd").show();
	return false;
});

// 라이브 티비
function popupOpen() {
	var popUrl = "live.html"; //팝업창에 출력될 페이지 URL
	var popOption = "width=640, height=494, top=250, left=600, resizable=no, scrollbars=no, status=no;"; //팝업창 옵션(optoin)
	window.open(popUrl,"",popOption);
}

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

	setInterval(function() {
		if($('#invest_finished').val() == 'false') {
			$.ajax({
				type: "GET",
				url: "/root_investment/ajax_investment_r114.php",
				dataType: "json",
				data: {prd_idx:<?=$prd_idx;?>},
				success: function(json) {
					// 3초간 데이터 조회
					// 바뀌는 값들 모집금액, 투자모집률, 남은 모집금액, 버튼들
					$('#invest_finished').val(json.data.invest_finished); // 현재진행상태
					$('#progressBar').attr('style', "width:" + json.data.progress_width); // 진행률 표시
					$('#progressBtn').html("<li>"+json.data.button_data1.replace("/investment","<?=BSC_URL?>/investment")+"</li>");

					// progressData, totalRecruitValue
					$('#progressData').text(json.data.progress); // 진행률
					$('#totalRecruitValue').text(json.data.total_invest_amount_k); // 현재 모집금액
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

	if(sns_type == 'facebook')        { loc = '//www.facebook.com/sharer/sharer.php?u='+href+'&t='+title; }
	else if(sns_type == 'twitter')    { loc = '//twitter.com/home?status='+encodeURIComponent(title)+' '+href; }
	else if(sns_type == 'google')     { loc = '//plus.google.com/share?url='+href; }
	else if(sns_type == 'pinterest')  { loc = '//www.pinterest.com/pin/create/button/?url='+href+'&media='+img+'&description='+encodeURIComponent(title); }
	else if(sns_type == 'kakaostory') { loc = 'https://story.kakao.com/share?url='+encodeURIComponent(href); }
	else if(sns_type == 'band')       { loc = 'http://www.band.us/plugin/share?body='+encodeURIComponent(title)+'%0A'+encodeURIComponent(href); }
	else if(sns_type == 'naver')      { loc = "http://share.naver.com/web/shareView.nhn?url="+encodeURIComponent(href)+"&title="+encodeURIComponent(title); }
	else if(sns_type == 'url_copy')   { copy_trackback(href); }
	else if(sns_type == 'instagram')  { alert("현재 지원하지 않는 기능입니다."); loc = ""; return false; }
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

/*
	if(principal_value < min_invest_limit) {
		alert("최소 금액은 " + number_format(min_invest_limit) + "원 이상 입니다.");
		$("input:text[name='principal_value']").focus();
		return;
	}
*/

	if(principal_value >= <?=$CONF['min_invest_limit']?>) {
		$.ajax({
			url : g5_url + "/root_investment/ajax_simulation.php",
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
						$("#ajxTotalInterestPrice").text(data.totalInterestPrice);
						$("#ajxInvestMonth").text(data.investMonth);
						$("#ajxMonthAvrPrice").text(data.monthAvrPrice);
						$("#ajaDiffEarning").text(data.diffEarning);
					}
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {

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