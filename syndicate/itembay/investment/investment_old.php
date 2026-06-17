<?
include_once('./_common.php');


$g5['title'] = '투자상품 상세보기';
$g5['top_bn'] = "/images/investment/sub_investment.jpg";
$g5['top_bn_alt'] = "투자하기 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');



$special_user = ($is_admin=='super' || in_array($member['mb_id'], array('yr4msp','hellosiesta','sori9th','judero831','hellofunding','test070','test999','master'))) ? true : false;
$tmp_special_user = ( in_array($member['mb_id'], array('samo','samo001','samo002')) ) ? true : false;

$prd_idx = trim($_REQUEST['prd_idx']);

if($prd_idx=='') { goto_url('/'); exit; }
if(!preg_match('/^[0-9]{0,10}$/', $prd_idx)) { header('Location: /', true, 302); exit; }

if($prd_idx=='164') {
	if(!$special_user) { header('Location: /investment/investment.php?prd_idx=168', true, 302); exit; }
}


// 지정투자상품 설정
if( in_array($prd_idx, array('148','157','171','175','176')) ) {
	if($prd_idx=='148') {
		if( !$is_admin && !in_array($member['mb_id'], array('moreamc','uildnm2012','yr4msp','sori9th')) ) {
			echo "
			<script>
			alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 사전에 협의완료된 대출자와 투자자가 제3자에 의한 체계적 담보권리확보 및 자금관리를 목적으로 헬로펀딩을 통해 펀딩을 진행합니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');
			location.replace('/investment/invest_list.php');
			</script>";
			exit;
		}
	}
	else if($prd_idx=='157') {
		if( !$is_admin && !in_array($member['mb_id'], array('fintech05','yr4msp','sori9th')) ) {
			echo "
			<script>
			alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 투자자와 사전에 협의가 완료된 지정투자상품입니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');
			location.replace('/investment/invest_list.php');
			</script>";
			exit;
		}
	}
	else if($prd_idx=='171') {
		if( !$is_admin && !in_array($member['mb_id'], array('KJHInvest1019','GraceInvest1102','master')) ) {
			echo "
			<script>
			alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 투자자와 사전에 협의가 완료된 지정투자상품입니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');
			location.replace('/investment/invest_list.php');
			</script>";
			exit;
		}
	}
	else if( in_array($prd_idx, array('175','176')) ) {
		if( !$is_admin && $member['mb_id']!='apollon' ) {
			echo "
			<script>
			alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 투자자와 사전에 협의가 완료된 지정투자상품입니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');
			location.replace('/investment/invest_list.php');
			</script>";
			exit;
		}
	}

}


$sql = "SELECT a.* FROM cf_product a WHERE a.idx='".$prd_idx."'";
if($special_user || $tmp_special_user) {
	$sql.= "";	  //관리자는 모조리 출력
}
else {
	//if(!preg_match('/wowstar/i', $_COOKIE['PHPSESSID'])) {
		$sql.= " AND a.display='Y'";
	//}
}


$PRDT = sql_fetch($sql);

//if($is_admin=='super') { echo "(".$product_cnt.") ". $product_query; exit; }

if($PRDT) {

	//while(list($row_key, $row_value)=each($PRDT)) { $PRDT[$row_key] = trim($row_value); }

	$sql2 = "
		SELECT
			COUNT(product_idx) AS total_invest_count,
			IFNULL(SUM(amount), 0) AS total_invest_amount
		FROM
			cf_product_invest
		WHERE
				idx > 0
				AND product_idx='".$PRDT['idx']."'";
	if($PRDT['state']=='6') {
		$sql2.= " AND invest_state='R'";  //투자취소 상품의 경우 반환 처리된 투자금 내역을 가져온다.
	}
	else {
		$sql2.= " AND invest_state='Y'";
	}
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
				$print_day = date("Y년 m월 d일", strtotime($PRDT['start_date']))." ".get_yoil($PRDT['start_date'])."요일";
				$print_hour = ($PRDT['start_hour']<=12) ? '오전' : '오후';
				$print_hour.= date("g", strtotime($PRDT['start_datetime']))."시"; //출력표기 시간
				$msg = $print_day." ".$print_hour." 부터 투자가 가능합니다.";
			}
		}

		if($is_member) {
			if($member['invest_warning_agree']=='Y') {
				$invest_button = '<a href="javascript:;" onClick="alert(\''.$msg.'\');" class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';
				$advance_invest_button = '<a id="btn_advance_invest" href="/investment/detail.php?prd_idx='.$PRDT['idx'].'&advance=1" class="btn_big_maple">사전투자하기</a>';
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
						$invest_button = '<a href="javascript:;" onClick="invest_warning_agree_open();" class="'.$button_class.'">투자하기</a>';  //투자위험고지 팝업 : /popup/inc_invest_warning_agree_form.php
					}
				}
			}
			else {
				$invest_button = '<a href="javascript:;" onClick="if(confirm(\'발급된 가상계좌 정보가 없습니다.\\n가상계좌 신청 페이지로 이동하시겠습니까?\')){ location.href=\'/deposit/deposit.php?tab=3\'; }" class="'.$button_class.'">투자하기</a>';
			}
		}
		else {
			$invest_button = '<a href="/bbs/login.php?url='.urlencode($_SERVER['REQUEST_URI']).'" class="'.$button_class.'">투자하기</a>';
		}
	}
	else {
		$invest_finished = true;
		$button_class    = 'btn_big_gray';

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
for($i=0; $i<count($DTLIMG_ARR);$i++){
	if(is_file(G5_DATA_PATH."/product/".$DTLIMG_ARR[$i])) {
		$PRDTIMG[] = G5_DATA_URL."/product/".$DTLIMG_ARR[$i];
	}
}
if(!count($PRDTIMG)) {
	if($PRDT["main_image"]!="" && is_file(G5_DATA_PATH."/product/".$PRDT["main_image"])) {
		$PRDTIMG[] = G5_DATA_URL."/product/".$PRDT["main_image"];
		$title_image_size = fileSize(G5_DATA_PATH."/product/".$PRDT["main_image"]);
	}
}
$product_image_count = count($PRDTIMG);

$start_timestamp  = strtotime($PRDT["start_datetime"]);
$print_sdate = date('Y년 m월 d일', $start_timestamp);
$print_sdate.= ' ' . get_yoil($PRDT["start_datetime"]).'요일 ';
$print_sdate.= (date(H, $start_timestamp) < 12) ? ' 오전' : ' 오후';
$print_sdate.= date('H시', $start_timestamp);

// 헬로라이브 클릭시 오픈 경로
if($PRDT['stream_url1']) {
	if($PRDT['stream_url1']=='ready') {
		$live_link = "openStreamReady()";  // /popup/inc_stream_ready.php 에 함수 정의
	}
	else {
		$play_url = "http://hellolivetv.co.kr/onair.php?prd_idx=".$prd_idx;
		$play_url.= (preg_match("/dev.hellofunding/", $_SERVER['HTTP_HOST'])) ? "&mode=test" : "";
		if(G5_IS_MOBILE){
			$live_link = "window.open('".$play_url."','stream_win','toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
		}
		else {
			$live_link = "window.open('".$play_url."','stream_win','width=730,height=500,toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
		}
	}
}

// 모바일 분기
if(G5_IS_MOBILE){
	include_once('./investment_m.php');
	return;
}

?>

<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/popup<?=( G5_IS_MOBILE )?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/flexslider<?=( G5_IS_MOBILE )?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<script src="<?=G5_URL?>/js/jquery.blink.js"></script>
<script src="<?=G5_URL?>/Highcharts-5.0.0/js/highcharts.js"></script>
<script src="<?=G5_URL?>/Highcharts-5.0.0/js/highcharts-more.js"></script>
<script src="<?=G5_URL?>/Highcharts-5.0.0/js/modules/solid-gauge.js"></script>
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/popup<?=( G5_IS_MOBILE )?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/flexslider<?=( G5_IS_MOBILE )?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="/investment/css/investment_info_old.css">
<script type="text/javascript" src="../js/jquery.bxslider.min.js"></script>

<!-- 본문내용 START -->
<div id="content">
	<div class="location"><span><a href="<?=G5_URL?>/investment/invest_list.php">투자상품보기</a></span><b class="blue"><?=$PRDT["title"]?></b></div>
	<div class="content investment">

<!-- view_head 수정 2017.01.04 -->
		<div class="boxArea">
			<style>
			.bx-wrapper {width:100%; height:100%;}
			.bx-wrapper .bx-controls-direction a {position:absolute; top:50%; margin-top:-16px; outline:0; width:32px; height:32px; text-indent:-9999px; z-index:9999}
			.bx-wrapper .bx-controls-direction .bx-next{right:10px; z-index:2; background:url(/investment/controls.png) no-repeat -43px -32px;}
			.bx-wrapper .bx-controls-direction .bx-prev{left:10px; z-index:2; background:url(/investment/controls.png) no-repeat 0 -32px;}
			</style>
			<div class="box">
				<div class="con" id="flexslider1" style="width:569px;height:442px;overflow:hidden;position:relative;">
					<div class="open_tit"><span style="padding:0 8px 3px 0;"><img src="/images/investment/timer_icon01.png" /></span>투자 시작일 : <?=$print_sdate?></div>
					<ul class="<?=(count($PRDTIMG)>1)?'slides':''?>">
<?
for($i=0; $i<$product_image_count; $i++) {
	echo '	<li><img src="'.$PRDTIMG[$i].'" style="width:auto;height:442px;margin-left:0;"></li>'.PHP_EOL;
}
?>
					</ul>
				</div>
			</div>
			<script type="text/javascript">
			$(document).ready(function(){
				$('#flexslider1 .slides').bxSlider({
					speed: 800,
					auto: true,
					pause: 5000,
					pager: false,
					controls: true,
					autoHover:true
				});
			});
			</script>

			<div class="detail_info">
				<div class="detail_cont" style="width:540px;height:387px;background-color:#FDFDFD">

					<input type="hidden" id="invest_finished" value="<?=($invest_finished)?'true':'false';?>">
					<div class="detail_tit"><?=$PRDT['title']?></div>
					<div class="flag_area">
						<? if($PRDT['advance_invest']=='Y') { ?><div class="flag_green">사전투자 <?=(int)$PRDT['advance_invest_ratio']?>% <span id="advance_invest_help" class="help">?</span></div><? echo "\n"; }?>
						<? if($PRDT['purchase_guarantees']=='Y') { ?><!--<div class="flag_red">채권매입계약</div>--><? echo "\n"; }?>
						<? if($PRDT['advanced_payment']=='Y') { ?><div class="flag_orange">이자 선지급</div><? echo "\n"; } ?>
						<? if($live_link){ ?><a href="javascript:;" onClick="<?=$live_link?>"><img src="/images/investment/live_icon01.gif"></a><? echo "\n"; } ?>
					</div>

					<div id="advance_invest_guide" style="display:none; position:absolute; z-index:10; left:50px; top:94px; width:300px; padding:12px 8px; border:1px solid #aaa; border-radius:5px; background-color:#FFFF99; font-size:12px; line-height:18px;">
						<span style="font-size:14px; line-height:24px;font-weight:bold;">사전 투자 서비스란?</span><br>
						펀딩오픈 시간에 투자참여가 어려운 회원분들을 위하여 사전에 투자할 수 있는 서비스입니다.<br><br>

						<span style="font-size:14px; line-height:24px;font-weight:bold;">사전 투자 유의사항</span><br>
						본 상품은 사전 투자가 가능한 상품으로 목표금액의 <b><?=(int)$PRDT['advance_invest_ratio']?>%</b> 까지 사전 투자가 진행됩니다.<br>
						<div style="font-size:12px; line-height:18px; margin-top:8px;">
							1. 사전 투자는 가상계좌의 예치금으로 투자 가능합니다.<br>
							2. 사전 투자는 신청순으로 적용됩니다.
							<!--3.사전 투자 취소는 펀딩 완료전까지 가능합니다.-->
						</div>
					</div>

					<ul class="detail_table">
						<li>
							<p>투자자 수익률(연)</p>
							<p><?=$PRDT['invest_return']?>%</p>
						</li>
						<li>
							<p>투자기간</p>
							<p><?=$PRDT['invest_period']?>개월</p>
						</li>
					</ul>
					<ul class="detail_table">
<?
if( in_array($PRDT_STATE['code'], array('A01','A02','A05')) ) {
?>
						<li>
							<p id="area3_title">지급회차</p>
							<p id="area3_data"><span style="color:<?=($repay_count)?'#FF6633':'#AAA'?>"><?=$repay_count?></span> / <?=$total_repay_count?></p>
						</li>
<?
}
else {
?>
						<li>
							<p id="area3_title">목표금액</p>
							<p id="area3_data"><?=price_cutting($PRDT['recruit_amount'])?>원</p>
						</li>
<?
}
?>
						<li>
							<p>모집금액</p>
							<p id="area4_data"><?=price_cutting($PRDT['total_invest_amount'])?>원</p>
						</li>
					</ul>

					<div class="detail_progress">
						<p class="prog_tit1">진행률</p>
						<p class="prog_tit2" id="progress_data"><?=$product_invest_percent?>%</p>
						<ul class="progress">
							<li class="rate"><img id="progress_bar" src="/images/investment/rate_blue.gif" alt="진행률" style="width:<?=$product_invest_percent?>%;"></li>
						</ul>
					</div>
					<div class="detail_btn" id="button_area1">
						<? if($invest_finished==false) { ?><a href="/investment/simulation.php?prd_idx=<?=$PRDT['idx']?>" class="btn_big_link">투자시뮬레이션</a><? } ?>
						<?=$invest_button?>
						<?=$advance_invest_button?>
						<? if(!$is_member && $invest_finished) { ?><a id="reqsms_btn2" class="btn_big_blue">다음 상품 알림받기</a><? } ?>
					</div>

				</div>
				<div class="detail_guide">
					<p class="guide1" onClick="location.href='<?=G5_URL?>/investment/guide.php';"><span style="padding-left:85px;">투자가 처음이신가요?</span></p>
					<p class="guide2" onClick="location.href='<?=G5_URL?>/company.php#d2';"><span style="padding-left:95px;">헬로펀딩의 안전성</span></p>
				</div>
			</div>
<!-- view_head 수정 2017.01.04 -->
		</div>

		<script type="text/javascript">
		$(document).ready(function() {
			$('#advance_invest_help').on('mouseover', function() {
				$('#advance_invest_guide').fadeIn();
			}).on('mouseout', function() {
				$('#advance_invest_guide').fadeOut();
			});
		});
		</script>

		<script type="text/javascript">
		$(document).ready(function() {
			setInterval(function() {
				if( $('#invest_finished').val()=='false' ) {
					$.ajax({
						type: "GET",
						url: "/investment/ajax_investment.php",
						dataType: "json",
						data: {prd_idx:<?=$prd_idx?>},
						success: function(json) {
							$('#ajax_return_txt').val(
								'version: ' + json.data.version + '\n' +
								'referer: ' + json.data.referer + '\n' +
								'invest_finished: ' + json.data.invest_finished + '\n' +
								'area3_title: ' + json.data.area3_title + '\n' +
								'area3_data: ' + json.data.area3_data + '\n' +
								'area4_data: ' + json.data.area4_data + '\n' +
								'progress: ' + json.data.progress + '\n' +
								'progress_width: ' + json.data.progress_width + '\n' +
								'button_data1: ' + json.data.button_data1 + '\n' +
								'advance_invest_button_data: ' + json.data.advance_invest_button_data + '\n' +
								'button_data2: ' + json.data.button_data2
							);

							$('#invest_finished').val(json.data.invest_finished);
							$('#area3_title').html(json.data.area3_title);
							$('#area3_data').html(json.data.area3_data);
							$('#area4_data').html(json.data.area4_data);
							$('#progress_data').html(json.data.progress);
							$('#progress_bar').attr('style', "width:" + json.data.progress_width);
							$('#button_area1').html(json.data.button_data1);
							$('#button_area2').html(json.data.button_data2);
						},
						error: function(e) { }
					});
				}
			}, 3 * 1000);
		});
		</script>

		<div style='height:20px;'></div>
		<!--동영상 팝업영역-->
		<script type="text/javascript">
		<!--

		function popupOpen(){
			var popUrl = "live.html";    //팝업창에 출력될 페이지 URL
			var popOption = "width=640, height=494, top=250, left=600, resizable=no, scrollbars=no, status=no;";    //팝업창 옵션(optoin)
				window.open(popUrl,"",popOption);
			}

		//-->
		</script>

		<ul class="tab_type03">
			<li data-gubun="tab1" class="on">투자상품 기본정보</li>
			<li data-gubun="tab3">증빙자료</li>
			<li data-gubun="tab2">안전장치 업데이트 <?=($PRDT["extend_8"])?" <font style='line-height:8px;font-size:8pt;color:red'>new</font>" : "";?></li>
		</ul>
		<script>
		//탭 기능
		$(document).ready(function(){
			$('.tabArea:eq(0)').slideToggle('slow');
		//$('.tabArea:eq(0)').show();
			$('.tab_type03 li').click(function(){
				$(this).addClass('on').siblings().removeClass('on');
				var cur = $(this).index();
				$('.tabArea').hide();
				$('.tabArea:eq('+cur+')').slideToggle('slow');
			//$('.tabArea:eq('+cur+')').show();
			});
		});
		</script>

		<div class="tabArea">

<? if($PRDT["extend_6"]!=""){ ?>
			<dl class="profit_title">
				<dt>채권매입계약</dt>
				<dd>
<?
		if($PRDT["purchase_guarantees"]=='Y' ) {
			echo "<div style='margin:0 0 20px 0;'><img src='/images/investment/guarantee_system.jpg' width='100%'></div>";
		}
?>
					<?=$PRDT["extend_6"]?>
				</dd>
			</dl>
			<div style="height:20px"></div>
<? } ?>

<?
if($invest_summary){
	$invest_summary = @preg_replace("/script/i", "script.", $invest_summary);
?>
			<div style="height:20px"></div>
			<h3 style="padding-left:10px;">투자설명</h3>
			<div class="point">
<?
	//동산일때
	if($PRDT["category"]=='1' && $PRDT['portfolio']=='Y') {
		echo "<div style='margin:10px auto 20px;'><center><img src='/images/investment/guarantee_port.jpg' width='1120'></center></div>";
	}

    // 주택담보대출일때 2018-01-10
    if($PRDT['mortgage_guarantees']=='1') {
        echo "<div style='margin:10px auto 20px;'><center><img src='/images/investment/morgage_guarantees.jpg' width='1120'></center></div>";
    }

?>
				<?=$invest_summary?>
			</div>
<?
}
?>

<? if($PRDT["core_invest_point"]!=""){ ?>
			<h3 style="padding-left:10px;"><?=($PRDT['category']=='1') ? '대출자 정보' : '핵심 투자포인트';?></h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["core_invest_point"]);?></div>
<? } ?>

<? if($PRDT["extend_4"]!=""){ ?>
			<h3 style="padding-left:10px;"><?=($PRDT['category']=='1') ? '담보물 정보' : '투자자 보호장치';?></h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_4"]);?></div>
<? } ?>

<? if($PRDT["extend_1"]!=""){ ?>
			<h3 style="padding-left:10px;"><?=($PRDT['category']=='1') ? '투자자보호장치' : '담보 분석 및 평가';?></h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_1"]);?></div>
<? } ?>

<? if($grade) { ?>
			<h3 style="padding-left:10px;">평가등급</h3>
	<? if($grade_type=="v1") { ?>
			<div class="level">
				<img src="/images/investment/level_site.png" alt="position" class="site" style="left:<?=((16-$level_score)*6.65)?>%;" />
				<div class="A1" <?=($grade=="A1")?'style="color:#fff;font-weight:bold"':''?>>A1</div>
				<div class="A2" <?=($grade=="A2")?'style="color:#fff;font-weight:bold"':''?>>A2</div>
				<div class="A3" <?=($grade=="A3")?'style="color:#fff;font-weight:bold"':''?>>A3</div>
				<div class="B1" <?=($grade=="B1")?'style="color:#fff;font-weight:bold"':''?>>B1</div>
				<div class="B2" <?=($grade=="B2")?'style="color:#fff;font-weight:bold"':''?>>B2</div>
				<div class="B3" <?=($grade=="B3")?'style="color:#fff;font-weight:bold"':''?>>B3</div>
				<div class="C1" <?=($grade=="C1")?'style="color:#fff;font-weight:bold"':''?>>C1</div>
				<div class="C2" <?=($grade=="C2")?'style="color:#fff;font-weight:bold"':''?>>C2</div>
				<div class="C3" <?=($grade=="C3")?'style="color:#fff;font-weight:bold"':''?>>C3</div>
				<div class="D1" <?=($grade=="D1")?'style="color:#fff;font-weight:bold"':''?>>D1</div>
				<div class="D2" <?=($grade=="D2")?'style="color:#fff;font-weight:bold"':''?>>D2</div>
				<div class="D3" <?=($grade=="D3")?'style="color:#fff;font-weight:bold"':''?>>D3</div>
				<div class="E1" <?=($grade=="E1")?'style="color:#fff;font-weight:bold"':''?>>E1</div>
				<div class="E2" <?=($grade=="E2")?'style="color:#fff;font-weight:bold"':''?>>E2</div>
				<div class="E3" <?=($grade=="E3")?'style="color:#fff;font-weight:bold"':''?>>E3</div>
			</div>
			<div class="level_info">
				<div class="label"><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>" /></div>
				<ul class="info" style="width:60%;">
					<li>안전성 <span class="star<?=$PRDT["evaluate_star1"]?>"></span> <?=$PRDT["evaluate_score1"]?>/100</li>
					<li>수익성 <span class="star<?=$PRDT["evaluate_star2"]?>"></span> <?=$PRDT["evaluate_score3"]?>/100</li>
					<li>환금성 <span class="star<?=$PRDT["evaluate_star3"]?>"></span> <?=$PRDT["evaluate_score2"]?>/100</li>
					<!--<li>합계 <b class="green"><?=$_evaluation_grade_array[$total_evaluate_star]?></b></li>-->
				</ul>
			</div>
	<? } else if($grade_type=="v2") { ?>
			<div style="width:100%; text-align:right; font-size:1.0em;color:brown">헬로펀딩은 안전투자를 위해 <span style="color:#FF2222"><b>A등급</b></span> 이상의 상품만 취급합니다.</div>
			<div class="_level">
				<div style="height:20px; background:#146CE9"></div>
				<div style="height:20px; background:#03A9F5"></div>
				<div style="height:20px; background:#009788"></div>
				<div style="height:20px; background:#8CC34B"></div>
				<div style="height:20px; background:#FEC107"></div>
				<div class="_S<?=($grade=='S')?' selected':''?>">S</div>
				<div class="_A<?=($grade=='A')?' selected':''?>">A</div>
				<div class="_B<?=($grade=='B')?' selected':''?>">B</div>
				<div class="_C<?=($grade=='C')?' selected':''?>">C</div>
				<div class="_D<?=($grade=='D')?' selected':''?>">D</div>
			</div>
			<div class="level_info">
				<div class="label" bgcolor="#EDEDED"><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>" /></div>
				<div style="float:left; height:175px; overflow-y:hidden; border:0px solid #000;">
					<div id="Gauge1" style="width:245px; height:190px; float:left" alt="안전성"></div>
					<div id="Gauge2" style="width:245px; height:190px; float:left" alt="상환성"></div>
					<div id="Gauge3" style="width:245px; height:190px; float:left" alt="수익성"></div>
					<div id="Gauge4" style="width:245px; height:190px; float:left" alt="환금성"></div>
				</div>
			</div>
	<? } else if($grade_type=="v3") { ?>
			<div style="width:100%; text-align:right; font-size:1.0em;color:brown">헬로펀딩은 안전투자를 위해 <span style="color:#FF2222"><b>A등급</b></span> 이상의 상품만 취급합니다.</div>
			<div class="_level">
				<div style="height:20px; background:#146CE9"></div>
				<div style="height:20px; background:#03A9F5"></div>
				<div style="height:20px; background:#009788"></div>
				<div style="height:20px; background:#8CC34B"></div>
				<div style="height:20px; background:#FEC107"></div>
				<div class="_S<?=($grade=='S')?' selected':''?>">S</div>
				<div class="_A<?=($grade=='A')?' selected':''?>">A</div>
				<div class="_B<?=($grade=='B')?' selected':''?>">B</div>
				<div class="_C<?=($grade=='C')?' selected':''?>">C</div>
				<div class="_D<?=($grade=='D')?' selected':''?>">D</div>
			</div>
			<div class="level_info">
				<div class="label" bgcolor="#EDEDED"><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>" /></div>
				<div style="float:left; height:175px; overflow-y:hidden; border:0px solid #000;">
					<div id="Gauge1" style="margin-left:80px;width:260px; height:190px; float:left" alt="안전성"></div>
					<div id="Gauge2" style="margin-left:50px;width:260px; height:190px; float:left" alt="상환성"></div>
					<!--<div id="Gauge3" style="margin-left:30px;width:260px; height:190px; float:left" alt="수익성"></div>-->
					<div id="Gauge4" style="margin-left:50px;width:260px; height:190px; float:left" alt="환금성"></div>
				</div>
			</div>
	<? } ?>
<? } ?>

<? if($PRDT["extend_2"]!=""){ ?>
			<h3 style="padding-left:10px;">신용 및 부채정보</h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_2"]);?></div>
<? } ?>

<? if($PRDT["extend_3"]!=""){ ?>
			<h3 style="padding-left:10px;">투자 구조도</h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_3"]);?></div>
<? } ?>

<? if($PRDT["category"]!=1 && $PRDT["extend_5"]!=""){ ?>
			<h3 style="padding-left:10px;">평가기관 의견</h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_5"]);?></div>
<? } ?>

<? if($PRDT["category"]!=1 && $PRDT["screening"]!="") { ?>
			<h3 style="padding-left:10px;">심사총평</h3>
			<div class="point">
<?
	if( $PRDT["judge"] ) {
		$judge_profile_image_name = (G5_IS_MOBILE) ? $PRDT["judge"]."_m.jpg" : $PRDT["judge"].".jpg";
		$judge_profile_image = "../images/judge/".$judge_profile_image_name;
		if( file_exists($judge_profile_image) ) { echo "<div style='margin:0 0 20px 0; width:100%;text-align:right;'><img src='$judge_profile_image'></div>"; }
	}
?>
				<div style='padding:10px;'><?=@preg_replace("/script/i", "script.", $PRDT["screening"]);?></div>
			</div>
<? } ?>

<? if($PRDT["lat"]>1 && $PRDT["lng"]>1 ) { ?>
			<div class="boxArea">
				<div class="box">
					<h3 style="padding-left:10px;">지도</h3>

					<div id="testMap" class="con" style="width:1148px;height:320px;"></div>

<?
	//$client_id = "wgdMUaKHdFdJ8M6hMFJ_";  // https 로 등록된 코드
	//$client_id = "JFBRTNU1_g1m81uONlva";  // http 로 등록된 코드 (미러)
	$client_id = (preg_match("/mirror.hellofunding.co.kr/i", G5_URL)) ? "JFBRTNU1_g1m81uONlva" : "wgdMUaKHdFdJ8M6hMFJ_";

	$api_type = "js";

	if($api_type=="js") {
	///////////////////////////////////
	// 지도 API(JavaScript API) 이용시
	///////////////////////////////////
?>

					<script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?clientId=<?=$client_id?>"></script>
					<script type="text/javascript">
					var mapOptions = {
						center: new naver.maps.LatLng(<?=$PRDT["lat"]?>, <?=$PRDT["lng"]?>),
						zoom: 9,
						scaleControl: false,
						logoControl: false,
						mapDataControl: false,
						zoomControl: true,
						minZoom: 2
					};
					var map = new naver.maps.Map('testMap', mapOptions);

					var marker = new naver.maps.Marker({
						position: new naver.maps.LatLng(<?=$PRDT["lat"]?>, <?=$PRDT["lng"]?>),
						map: map
					});
					</script>

<?
	}
	else {
		///////////////////////////////////
		// 지도 API(StaticMap API) 이용시
		///////////////////////////////////
		$static_api_url = "https://openapi.naver.com/v1/map/staticmap.bin" .
											"?clientId=" . $client_id .
											"&url=" . G5_URL . $_SERVER['REQUEST_URI'] .
											"&crs=EPSG:4326" .
											"&exception=inimage" .
											"&center={$PRDT['lng']},{$PRDT['lat']}" .
											"&level=9" .
											"&w=563&h=360" .
											"&baselayer=default" .
											"&format=png" .
											"&markers={$PRDT['lng']},{$PRDT['lat']}";
?>
					<script type="text/javascript">
					$(document).ready(function(){
						$("#testMap").append("<img src='<?=$static_api_url?>' width='100%' height='100%'>");
					});
					</script>
<?
	}
?>
				</div>
			</div>
<? } ?>

<!--
			<h3 style="padding-left:10px;">기존 담보대출내역</h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["security_loan"]);?></div>

			<h3 style="padding-left:10px;">전문정보</h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["special_info"]);?></div>
//-->

<?
if($is_admin=='super') {
	if($PRDT["evidence"]!=""){
?>
			<h3 style="padding-left:10px;">증빙서류</h3>
			<div class="point">
<?
		$evidence_array  = explode("|",$PRDT["evidence"]);
		for($i=0; $i<count($evidence_array);$i++){
			if(is_file(G5_DATA_PATH."/product/".$evidence_array[$i])){
				echo "<a href=\"".G5_DATA_URL."/product/".$evidence_array[$i]."\" target=\"_blank\"><img src='/images/investment/icon_file.png'></a>";
			}
		}
?>
			</div>
<?
	}
}
?>

<?
if( !preg_match("/\<div class=\"invest_cont\"\>/i", $invest_summary) ) {

	$invest_period_month = ceil($PRDT["invest_period"]/12);
	$invest_period_month = $invest_period_month*12;
?>

			<h3 style="padding-left:10px;">투자안내</h3>
				<div class="invest_info">
					<div class="table">
						<div class="title">투자금액별 총예상수익</div>
						<table>
							<tbody>
								<tr>
									<th>투자금액</th>
									<th>예상수익<br />(연 수익 기준 / 세전)</th>
								</tr>
								<tr>
									<td>100,000원</td>
									<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*100000))))?>원</td>
								</tr>
								<tr>
									<td>500,000원</td>
									<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*500000))))?>원</td>
								</tr>
								<tr>
									<td>1,000,000원</td>
									<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*1000000))))?>원</td>
								</tr>
								<tr>
									<td>10,000,000원</td>
									<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*10000000))))?>원</td>
								</tr>
								<tr>
									<td>50,000,000원</td>
									<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*50000000))))?>원</td>
								</tr>
								<tr>
									<td>100,000,000원</td>
									<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*100000000))))?>원</td>
								</tr>
							</tbody>
						</table>
						<p style="margin-top:5px;color:#777">
						* 상환일: 매월 5일 (공휴일인 경우 익일)<br />
						* 연 수익률 기준
						</p>
						<p style="margin-top:10px;text-align:center;">
							<? if($invest_finished==false) { ?><a href="./simulation.php?prd_idx=<?=$PRDT["idx"]?>" class="btn_big_blue">투자 수익 시뮬레이션</a><? } ?>
						</p>
					</div>
					<div class="notes">
						<div class="title">투자시 참고사항</div>
						<div class="text" style="position:relative;font-size:10pt; float:right;width:49%;">
							○ 투자수익 시뮬레이션<br />
							<ul>
								<li style="list-style:disc;margin-left:24px;">투자수익 시뮬레이션은 예상수익을 표기해주는 것으로 펀딩완료 후 대출실행일과의 일수차이, 조기상환 및 기타 이유로 기재된 예상수익은 변동될 수 있습니다.</li>
							</ul>
							<br/>
							○ 원금 및 이자 보장에 대한 사항<br/>
							<ul>
								<li style="list-style:disc;margin-left:24px;">헬로펀딩은 투자금에 대하여 원금 및 이자수익을 보장하지 않습니다.</li>
								<li style="list-style:disc;margin-left:24px;">채무자의 채무 불이행시 경,공매등의 절차 과정에서 원금의 일부 손실이 발생할 수 있습니다.</li>
							</ul>
							<br/>
							○ 이자소득세 원천징수<br/>
							<ul>
								<li style="list-style:disc;margin-left:24px;">일반투자자의 투자수익은 '비영업대금의 이익'으로 소득세법 제 16조 제 1항 제 11호에 의해 25%의 소득세가 발생되며, 주민세 2.5%가 추가되어 총 27.5%의 세금을 납부해야 합니다. 이러한 세금납부에 대해 헬로핀테크에서 원천징수를 하므로 일반투자자는 별도로 세금신고를 하실 필요가 없습니다.</li>
							</ul>
							<br/>
							○ 투자일과 원금상환 입금날짜가 다른 이유<br/>
							<ul>
								<li style="list-style:disc;margin-left:24px;">투자금이 100% 펀딩된 이후 대출약정을 통해 대출이 실행되며 이 기간에 수일이 소요될 수 있으며, 대출자 분이 대출금을 받은날 부터 이자가 계산되어지기 때문에 실 투자일과 상환일에 차이가 발생합니다.</li>
							</ul>
						</div>
					</div>
				</div>
<?
}
?>

<? if($PRDT['extend_7']) { ?>

			<h3 style="padding-left:10px;">투자관련 도움말</h3>
			<div class="point invest_info"><?=@preg_replace("/script/i", "script.", $PRDT["extend_7"]);?></div>
<? } ?>


			<div class="btnArea">
				<!-- 하단 투자하기 버튼 (Long Type) -->
				<p align="center" id="button_area2">
					<?=$invest_button2?>
				</p>
			</div>
		</div>

		<div class="tabArea" style="padding-top:20px;">
			<h3 style="padding-left:10px;">증빙자료</h3>
			<div class="point"><?=($PRDT["extend_9"])?@preg_replace("/script/i", "script.", $PRDT["extend_9"]):'<p style="min-height:100px;text-align:center;color:#aaa">내용이 없습니다.</p>';?></div>
		</div>

		<div class="tabArea" style="padding-top:20px;">
			<h3 style="padding-left:10px;">안전장치 업데이트</h3>
			<div class="point"><?=($PRDT["extend_8"])?@preg_replace("/script/i", "script.", $PRDT["extend_8"]):'<p style="min-height:100px;text-align:center;color:#aaa">내용이 없습니다.</p>';?></div>
		</div>
	</div>

</div>

<script type="text/javascript">
$(function () {

    jQuery.curCSS = function(element, prop, val) {
        return jQuery(element).css(prop, val);
    };


    var gaugeOptions = {
        chart: { type: 'solidgauge' },
        title: null,
        pane: {
            center: ['50%', '85%'],
            size: '120%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },
        tooltip: { enabled: false },
        // 구간별 게이지 칼라
        yAxis: {
            stops: [
								<? if($grade_type=='v2') {?>
								[0.20, '#FE5722'],
                [0.35, '#FEC107'],
                [0.50, '#8CC34B'],
                [0.65, '#009788'],
                [0.80, '#03A9F5'],
                [0.85, '#146CE9']
								<? } else { ?>
								[0.20, '#FFAD5B'],
                [0.35, '#FFAD5B'],
                [0.50, '#FFAD5B'],
                [0.65, '#FFAD5B'],
                [0.80, '#FFAD5B'],
                [0.85, '#FFAD5B']
								<? } ?>
						],
            lineWidth: 0,
            minorTickInterval: null,
            tickAmount: 2,
            title: { y: -70 },
            labels: { y: 16 }
        },
        plotOptions: {
            solidgauge: {
                dataLabels: { y: 5, borderWidth: 0, useHTML: true }
            }
        }
    };

    $('#Gauge1').highcharts(Highcharts.merge(gaugeOptions, {
        yAxis: { min: 0, max: 100, title: { text:'<span style="font:bold 11pt NanumGothic;">안전성</span>' } },
        credits: { enabled: false },
        series: [{
            name: 'Gauge1',
            data: [<?=($evaluate_score1>=98) ? 98 : $evaluate_score1; ?>],
            dataLabels: {
								format: '<div style="text-align:center"><span style="color:#fff"></span></div>'
								//format: '<div style="text-align:center"><span style="font-size:25px;color:' + ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span></div>'
						},
            tooltip: { valueSuffix: ' point' }
        }]
    }));
    $('#Gauge2').highcharts(Highcharts.merge(gaugeOptions, {
        yAxis: { min: 0, max: 100, title: { text: '<span style="font:bold 11pt NanumGothic;">상환성</span>' } },
        series: [{
            name: 'Gauge2',
            data: [<?=($evaluate_score4>=98) ? 98 : $evaluate_score4?>],
            dataLabels: {
								format: '<div style="text-align:center"><span style="color:#fff"></span></div>'
								//format: '<div style="text-align:center"><span style="font-size:25px;color:' + ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span></div>'
            },
            tooltip: { valueSuffix: ' point' }
        }]
    }));
    $('#Gauge3').highcharts(Highcharts.merge(gaugeOptions, {
        yAxis: { min: 0, max: 100, title: { text: '<span style="font:bold 11pt NanumGothic;">수익성</span>' } },
        series: [{
            name: 'Gauge3',
            data: [<?=($evaluate_score2>=98) ? 98 : $evaluate_score2?>],
            dataLabels: {
								format: '<div style="text-align:center"><span style="color:#fff"></span></div>'
								//format: '<div style="text-align:center"><span style="font-size:25px;color:' + ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span></div>'
            },
            tooltip: { valueSuffix: ' point' }
        }]
    }));
    $('#Gauge4').highcharts(Highcharts.merge(gaugeOptions, {
        yAxis: { min: 0, max: 100, title: { text: '<span style="font:bold 11pt NanumGothic;">환금성</span>' } },
        series: [{
            name: 'Gauge4',
            data: [<?=($evaluate_score3>=98) ? 98 : $evaluate_score3?>],
            dataLabels: {
								format: '<div style="text-align:center"><span style="color:#fff"></span></div>'
								//format: '<div style="text-align:center"><span style="font-size:25px;color:' + ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span></div>'
            },
            tooltip: { valueSuffix: ' point' }
        }]
    }));
});
</script>

<?

// 투자위험고지 팝업
include_once(G5_PATH."/popup/inc_invest_warning_agree_form.php");

if($prd_idx=='119') {
	include_once(G5_PATH.'/popup/inc_product_119_notice.php');
}

// 라이브스트림 준비중 팝업
if($PRDT['stream_url1']=='ready') {
	include_once(G5_PATH.'/popup/inc_stream_ready.php');
}

?>

<!-- 본문내용 E N D -->
<?
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>