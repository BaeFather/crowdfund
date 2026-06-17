<?
###############################################################################
## 대출상품리스트
###############################################################################
##	2018-04-05 개편
###############################################################################

if (!preg_match("/220\.117\.134/", $_SERVER["REMOTE_ADDR"])) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include_once('./_common.php');


$g5['title']      = '투자상품목록';
$g5['top_bn']     = "/images/investment/sub_investment.jpg";
$g5['top_bn_alt'] = "투자하기 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if ($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

// while(list($k, $v)=each($_REQUEST)) { ${$k} = trim($v); }

$mode = trim($_REQUEST['mode']);
$CA   = trim($_REQUEST['CA']);
switch($CA) {
	case "A" : $category = '2'; $subtitle.= '부동산';					break;		//부동산
	case "A2": $category = '2'; $subtitle.= '주택담보';				break;		//주택담보
	case "B" : $category = '1'; $subtitle.= '동산';						break;		//동산
	case "C" : $category = '3'; $subtitle.= '확정매출채권';		break;		//확정매출채권
	default  : $category = '';  $subtitle.= '전체';						break;		//전체
}

$search_div   = trim($_REQUEST['search_div']);
$search_title = trim($_REQUEST['search_title']);

$developer        = ( in_array($member['mb_id'], $CONF['DEVELOPER']) ) ? true : false;
$goods_officer    = ( in_array($member['mb_id'], $CONF['GOODS_OFFICER']) ) ? true : false;
$tmp_special_user = ( in_array($member['mb_id'], array('samo','samo001','samo002')) ) ? true : false;

$YmdHis = preg_replace("/(-|:| )/", "", G5_TIME_YMDHIS);


///////////////////////////////////////
// 이벤트 상품 배열화
///////////////////////////////////////
if($CA == '' || $mode != "success") {

	$where = " 1=1 ";
	$where.= " AND A.display='Y' AND A.isTest=''";
	$where.= " AND A.end_datetime > NOW()";
	if($search_title) {
		$search_title = sql_real_escape_string($search_title);
		$where.= " AND A.title LIKE '%".$search_title."%' ";
	}

	$esql = "
		SELECT
			A.idx, A.state, A.category, A.title,
			A.invest_amount, A.invest_profit, A.invest_return, A.invest_period, A.invest_end_date,
			A.total_return_amount, A.recruit_period_start, A.recruit_period_end, A.recruit_amount, A.repay_type, repay_day,
			A.main_image, A.main_image_m, A.open_datetime, A.start_datetime, A.end_datetime, A.display
			, (SELECT COUNT(product_idx) AS total_invest_count FROM cf_event_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS total_invest_count
			, (SELECT IFNULL(SUM(amount),0) FROM cf_event_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS total_invest_amount
		FROM
			cf_event_product A
		WHERE
			$where
		ORDER BY
			A.start_date DESC,
			A.idx DESC";
	$eres  = sql_query($esql);
	$erows = $eres->num_rows;
	for($i=0; $i<$erows; ++$i) {
		$ELIST[$i] = sql_fetch_array($eres);

		$ELIST[$i]['main_image_tag'] = (is_file(G5_DATA_PATH."/product_special/".$ELIST[$i]['main_image'])) ? "<img src='".G5_DATA_URL."/product_special/".$ELIST[$i]['main_image']."' width='100%' height='100%'>" : "";


		$ELIST[$i]['invest_percent'] = 0;
		if($ELIST[$i]['recruit_amount'] > 0) {
			$ELIST[$i]['invest_percent'] = ($ELIST[$i]['total_invest_amount']>0) ? round((($ELIST[$i]['total_invest_amount']/$ELIST[$i]['recruit_amount'])*100),2) : 0;
		}


		$event_open_date	  = preg_replace("/-|:| /", "", $ELIST[$i]["open_datetime"]);		//상점오픈 (투자시작가능)
		$event_invest_sdate = preg_replace("/-|:| /", "", $ELIST[$i]["start_datetime"]);	//상품오픈 (투자시작가능)
		$event_invest_edate = preg_replace("/-|:| /", "", $ELIST[$i]["end_datetime"]);		//상품종료 (투자모집완료)
		$event_end_date	    = preg_replace("/-/", "", $ELIST[$i]["invest_end_date"]);

		$event_state = get_product_state(
			$ELIST[$i]["recruit_period_start"],
			$ELIST[$i]["recruit_period_end"],
			$event_open_date,
			$event_invest_sdate,
			$event_invest_edate,
			$ELIST[$i]["state"],
			$ELIST[$i]['recruit_amount'],
			$ELIST[$i]['total_invest_amount'],
			$event_end_date
		);

		$ELIST[$i]['detail_url'] = "/event_invest/event_invest.php?prd_idx=".$ELIST[$i]['idx'];
		$ELIST[$i]['detail_url_script'] = "location.href='".$ELIST[$i]['detail_url']."'";

		$ELIST[$i]['button_caption'] = "상품상세보기";
		if($event_invest_sdate <= $YmdHis && $event_invest_edate >= $YmdHis) {
			if($ELIST[$i]['recruit_amount'] > $ELIST[$i]['total_invest_amount']) {
				$ELIST[$i]['button_caption'] = "이벤트 상세보기";
			}
			else {
				$ELIST[$i]['cover_caption']  = "펀딩성공";
				$ELIST[$i]['button_caption'] = "이벤트투자 모집완료";
			}
		}
		else {
			if($ELIST[$i]['recruit_amount'] > $ELIST[$i]['total_invest_amount']) {
				if( preg_replace("/-/", "", $ELIST[$i]["recruit_period_start"]) > date("Ymd") ) {
					$ELIST[$i]['button_caption'] = "이벤트투자 오픈전"; //투자대기상태
				}
				else if( preg_replace("/-/", "", $ELIST[$i]["recruit_period_end"]) < date("Ymd") ) {
					$ELIST[$i]['cover_caption'] = "펀딩성공";
					$ELIST[$i]['button_caption'] = "이벤트투자 모집종료";
				}
			}
			else {
				$ELIST[$i]['cover_caption'] = "펀딩성공";
				$ELIST[$i]['button_caption'] = "이벤트투자 모집완료";
			}
		}

		$ELIST[$i]['event_period_days'] = ceil(((strtotime($ELIST[$i]["recruit_period_end"]) - strtotime($ELIST[$i]["recruit_period_start"]))+86400) / 86400);
		$ELIST[$i]['startDateTime'] = ($ELIST[$i]["start_datetime"]) ? date("Y년 m월 d일 H:i A", strtotime($ELIST[$i]["start_datetime"])) : date("Y년 m월 d일", strtotime($ELIST[$i]["recruit_period_start"]));
		$ELIST[$i]['print_sdate'] = $print_sdate;

		$event_open_date = $event_invest_sdate = $event_invest_edate = $event_end_date = $start_timestamp = $print_sdate = NULL;

	}

	//  모집중인 상품을 걸러내기 위해서 2018-7-23 일요일 전승찬 추가
	if ($search_div=="9") {
		for ($i=0 ; $i<count($ELIST) ; ++$i) {
			if ($ELIST[$i]['total_invest_amount']>=$ELIST[$i]['recruit_amount']) {
				array_splice($ELIST, $i, 1);
				$i = $i - 1; //인덱스가 하나 줄었으므로
			}
		}
	}

	$elist_count = count($ELIST);

}


///////////////////////////////////////
// 일반 상품 배열화
///////////////////////////////////////

$where = "1=1";
$where.= " AND A.idx>74 ";
if($mode=="success") {
	$where.= " AND A.success_example='Y'";
}
else {
	if($is_admin=='super' || $developer) {
		$where.= "";
	}
	else if($goods_officer) {
		$where.= " AND A.isTest=''";
	}
	else if($tmp_special_user) {
		switch($member['mb_id']) {
			case 'samo'	   : $where.= " AND A.idx IN(144)"; break;
			case 'samo001' : $where.= " AND A.idx IN(139)"; break;
			case 'samo002' : $where.= " AND A.idx IN(142)"; break;
			default		     : $where.= " AND A.display='Y'"; break;
		}
	}
	else {
		$where.= " AND A.display='Y' AND A.isTest=''";
	}
}
if($category) {
	$where.= " AND A.category='$category' ";
	$where.= ($CA=='A2') ? " AND A.mortgage_guarantees='1' " : " AND A.mortgage_guarantees='' ";
}
if($search_div) {
	if ($search_div=="9") {
		//$where .= " AND A.state='' AND A.start_datetime>=now() and end_datetime<=now() AND A.invest_end_date='' ";
		$where .= " AND A.state='' AND A.start_datetime <= now() AND A.end_datetime > now() ";
	} else if ($search_div=="1") {
		$where .= " AND A.state='1' ";
	} else if ($search_div=="2") {
		$where .= " AND A.state IN('2','5') ";
	}
}

if($search_title) {
	$search_title = sql_real_escape_string($search_title);
	$where.= " AND A.title LIKE '%".$search_title."%' ";
}
$where.= " AND A.recruit_amount > 0 ";

$sort = " A.cancel_date ASC, ";
$sort.= " invest_percent ASC, ";
$sort.= " A.open_datetime DESC, ";
$sort.= " A.idx DESC";

if(G5_IS_MOBILE && $_REQUEST['sl']=='1') {
	$isFirstPageM = 1;
}

$row = sql_fetch("SELECT COUNT(A.idx) AS cnt FROM cf_product A WHERE " . $where);
$product_count = $row['cnt'];

if(!$page) $page = 1;
if(!$size) $size = 15;

$total_page = ceil($product_count / $size);
$start_num  = ($page - 1) * $size;

$sql_limit = ($isFirstPageM) ? " LIMIT 10" : "LIMIT $start_num, $size";

$psql = "
	SELECT
		A.idx, A.gr_idx, A.ai_grp_idx, A.state, A.category, A.title,
		A.invest_return, A.invest_period, A.invest_days, A.invest_end_date, A.recruit_period_start, A.recruit_period_end, A.recruit_amount, A.repay_type,
		A.main_image, A.main_image_m, A.open_datetime, A.start_datetime, A.end_datetime, A.loan_start_date, A.loan_end_date,
		A.purchase_guarantees, A.advanced_payment, A.mortgage_guarantees, A.success_example, A.popular_goods, A.display,
		A.advance_invest, A.advance_invest_ratio, A.stream_url1, A.stream_url2
		, (SELECT COUNT(product_idx) FROM cf_product_invest WHERE product_idx = A.idx AND invest_state = IF(state = 6, 'R', 'Y')) AS total_invest_count
		, (SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx = A.idx AND invest_state = IF(state = 6, 'R', 'Y')) AS total_invest_amount
		, ((((SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y'))/A.recruit_amount)*100) AS invest_percent
		, (SELECT IFNULL(MAX(turn),0) FROM cf_product_success WHERE product_idx=A.idx AND invest_give_state='Y' AND overdue_start_date IS NULL) AS repay_count
	FROM
		cf_product A
	WHERE
		$where
	ORDER BY
		$sort $sql_limit";

if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
	//echo $psql;
}
$pres  = sql_query($psql);
$prows = $pres->num_rows;
for($i=0; $i<$prows; ++$i) {
	$PLIST[$i] = sql_fetch_array($pres);

	// 투자모집진행률
	$product_invest_percent = 0;
	if($PLIST[$i]['total_invest_amount']) {
		if($PLIST[$i]['recruit_amount'] > 0) {
			$product_invest_percent = ($PLIST[$i]['total_invest_amount'] / $PLIST[$i]['recruit_amount']) * 100;
			$product_invest_percent = floatCutting($product_invest_percent, 2);
		}
	}
	$PLIST[$i]['invest_percent'] = $product_invest_percent;


	$TMP_AMT = getNumberArr($PLIST[$i]['recruit_amount']);
	$RECRUIT_AMT = array('amount'=>$TMP_AMT[0], 'unit'=>$TMP_AMT[1]);

	$PLIST[$i]['print_recruit_amount']      = $RECRUIT_AMT['amount'];
	$PLIST[$i]['print_recruit_amount_unit'] = $RECRUIT_AMT['unit'];

	$TMP_AMT = $RECRUIT_AMT = NULL;


	$PLIST[$i]['invest_return'] = floatRtrim($PLIST[$i]["invest_return"], 2);

	if($PLIST[$i]['invest_period']==1 && $PLIST[$i]['invest_days'] > 0) {
		$PLIST[$i]['print_invest_period'] = $PLIST[$i]['invest_days'];
		$PLIST[$i]['print_invest_period_unit'] = '일';
	}
	else {
		$PLIST[$i]['print_invest_period'] = $PLIST[$i]['invest_period'];
		$PLIST[$i]['print_invest_period_unit'] = '개월';
	}


	$PLIST[$i]['buttonAndCover'] = productStatusCheck($PLIST[$i]['idx']);

	$PLIST[$i]['detail_url'] = G5_URL."/investment/investment.php?prd_idx=".$PLIST[$i]['idx'];
	$PLIST[$i]['detail_url_script'] = "location.href='".$PLIST[$i]['detail_url']."'";

	// 지정투자상품 설정
	if( in_array($PLIST[$i]['idx'], array('148','157','171')) ) {
		if($PLIST[$i]['idx']=='148') {
			if( !$is_admin && !in_array($member['mb_id'], array('moreamc','uildnm2012','yr4msp','sori9th','master')) ) {
				$PLIST[$i]['detail_url_script'] = "alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 사전에 협의완료된 대출자와 투자자가 제3자에 의한 체계적 담보권리확보 및 자금관리를 목적으로 헬로펀딩을 통해 펀딩을 진행합니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');";
			}
		}
		else if($PLIST[$i]['idx']=='157') {
			if( !$is_admin && !in_array($member['mb_id'], array('fintech05','yr4msp','sori9th','master')) ) {
				$PLIST[$i]['detail_url_script'] = "alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 투자자와 사전에 협의가 완료된 지정투자상품입니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');";
			}
		}
		else if($PLIST[$i]['idx']=='171') {
			if( !$is_admin && !in_array($member['mb_id'], array('KJHInvest1019','GraceInvest1102','master')) ) {
				$PLIST[$i]['detail_url_script'] = "alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 투자자와 사전에 협의가 완료된 지정투자상품입니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');";
			}
		}
	}

	if($PLIST[$i]['main_image']) {
		if(file_exists(G5_DATA_PATH . "/product/".$PLIST[$i]['main_image'])) {
			$target_str	 = preg_replace("/\//", "\/", G5_DATA_PATH);
			$PLIST[$i]['main_image_url'] = preg_replace('/'.$target_str.'/', G5_DATA_URL, $PLIST[$i]['main_image']);
		}
		else {
			$PLIST[$i]['main_image_url'] = "";
		}
	}

	$loan_start_date_day = ($PLIST[$i]['loan_start_date']>'0000-00-00') ? (int)substr($PLIST[$i]['loan_start_date'], -2) : (int)date(d);
	if($PLIST[$i]['invest_period']==1 && $PLIST[$i]['invest_days'] > 0) {
		$PLIST[$i]['total_repay_count'] = 1;
	}
	else {
		$PLIST[$i]['total_repay_count'] = ($loan_start_date_day < 5) ? $PLIST[$i]['invest_period'] : $PLIST[$i]['invest_period'] + 1; //총 지급횟수
	}

	$PLIST[$i]['startDateTime'] = ($PLIST[$i]["start_datetime"]) ? date("Y년 m월 d일 H:i A", strtotime($PLIST[$i]["start_datetime"])) : date("Y년 m월 d일", strtotime($PLIST[$i]["recruit_period_start"]));

	$PLIST[$i]['new_flag'] = (G5_TIME_YMD <= date('Y-m-d', strtotime('+5day', strtotime($PLIST[$i]['open_datetime']))) && ($PLIST[$i]['recruit_amount'] > $PLIST[$i]['total_invest_amount'])) ? true : false;

}

//  모집중인 상품을 걸러내기 위해서 2018-7-23 일요일 전승찬 추가
if ($search_div=="9") {
	for ($i=0 ; $i<count($PLIST) ; ++$i) {
		if ($PLIST[$i]['total_invest_amount']>=$PLIST[$i]['recruit_amount']) {
			array_splice($PLIST, $i, 1);
			$i = $i - 1; //인덱스가 하나 줄었으므로
		}
	}
}

$plist_count = count($PLIST);
$list_count = ($elist_count + $plist_count);

$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);

//print_rr($PLIST, 'text-align:left;font-size:12px');


if(G5_IS_MOBILE) {
	include_once("invest_list_m.php");
	return;
}

?>

<style>
.text2 { width:300px;height:33px;line-height:31px; padding:0 5px; border:1px solid #AAA; border-radius:3px; vertical-align:middle; }
</style>

<!-- 본문내용 START -->
<div id="content">
	<div class="location_top">
		<div class="location"><span><a href="<? echo G5_URL;?>/investment/invest_list.php">투자상품보기</a></span><b class="blue"><?=$subtitle;?></b></div>
		<div id="list_start" class="content invest_list2">

<? if( in_array($member['mb_id'], array('test1111','test2222')) ) { ?>
			<div class="list_info">
				본 상품은 헬로펀딩 VIP투자자분만 확인 가능합니다.<br>
				<span>[주의] 정식 투자시작 전 상품의 정보가 외부로 유출되지 않도록 주의 부탁드립니다.</span>
			</div>
<? } ?>

			<!-- 탭메뉴 //-->
			<ul class="tab_type03">
				<li onClick="location.href='<?=$_SERVER['PHP_SELF']?>'" <?=($category=='')?'class="on"':'';?>>전체</li> <li class="line">|</li>
				<li onClick="location.href='?CA=A'" <?=($CA=='A')?'class="on"':'';?>>부동산</li> <li class="line">|</li>
				<li onClick="location.href='?CA=A2'" <?=($CA=='A2')?'class="on"': '';?>>주택담보</li> <li class="line">|</li>
				<li onClick="location.href='?CA=B'" <?=($CA=='B')?'class="on"':'';?>>동산</li> <li class="line">|</li>
				<li onClick="location.href='?CA=C'" <?=($CA=='C')?'class="on"':'';?>>확정매출채권</li>
			</ul>
			<!-- 탭메뉴 //-->

<?
switch($CA) {
	case 'A'  : $CATEGORY = array('id'=>'CAA',   'title'=>'부동산',       'guide_image_a'=>'/images/investment/ca_ban_tit02.jpg', 'guide_image_b'=>'/images/investment/ca_ban02.jpg'); break;
	case 'A2' : $CATEGORY = array('id'=>'CAA2',  'title'=>'주택담보',     'guide_image_a'=>'/images/investment/ca_ban_tit03.jpg', 'guide_image_b'=>'/images/investment/ca_ban03.jpg'); break;
	case 'B'  : $CATEGORY = array('id'=>'CAB',   'title'=>'동산',         'guide_image_a'=>'/images/investment/ca_ban_tit04.jpg', 'guide_image_b'=>'/images/investment/ca_ban04.jpg'); break;
	case 'C'  : $CATEGORY = array('id'=>'CAC',   'title'=>'확정매출채권', 'guide_image_a'=>'/images/investment/ca_ban_tit05.jpg', 'guide_image_b'=>'/images/investment/ca_ban05.jpg'); break;
	default   : $CATEGORY = array('id'=>'CAALL', 'title'=>'전체',         'guide_image_a'=>'/images/investment/ca_ban_tit01.jpg', 'guide_image_b'=>'/images/investment/ca_ban01.jpg'); break;
}
?>
			<!-- 카테고리 타이틀/설명 -->
			<div>
				<div id="<?=$CATEGORY['id']?>" title="<?=$CATEGORY['title']?>">
					<span id="fold_button" class="<?=($_COOKIE['tImgHide'])?'fold':'unfold';?>"></span>
					<img src="<?=$CATEGORY['guide_image_a']?>">
					<p class="hide" <?=($_COOKIE['tImgHide']) ? 'style="display:none;"':'';?>><img src="<?=$CATEGORY['guide_image_b']?>"></p>
				</div>
				<div class="clearfix"></div>
			</div>
			<!-- 카테고리 타이틀/설명 -->

			<!-- 펼치기메뉴 시작 -->
			<script type="text/javascript">
			$('#fold_button').click(function() {
				if($('.unfold').hasClass('unfold')) {
					set_cookie('tImgHide', '1', 1, g5_cookie_domain);
					$('.unfold').addClass('fold').removeClass('unfold');
					$('.hide').slideUp();
				}
				else if($('.fold').hasClass('fold')) {
					set_cookie('tImgHide', '', -1, g5_cookie_domain);
					$('.fold').addClass('unfold').removeClass('fold');
					$('.hide').slideDown();
				}
			});
			</script>
			<!-- 펼치기메뉴 끝 -->

			<div style="width:97%; margin:20px 1.5% 0 1.5%; padding:0;">
				<form method="get">
					<input type="hidden" name="CA" value="<?=$CA?>">
					<ul style="float:right; margin:0 0 -5px 0;">
						<li style="float:left;margin-right:4px;">
							<select name="search_div" style="height:33px;">
								<option value="">전체</option>
								<option <?=($_REQUEST['search_div']=="9")?"selected":""?> value="9">모집중</option>
								<option <?=($_REQUEST['search_div']=="1")?"selected":""?> value="1">이자상환중</option>
								<option <?=($_REQUEST['search_div']=="2")?"selected":""?> value="2">상환완료</option>
							</select>
						</li>
						<li style="float:left;margin-right:4px;"><input type="text" name="search_title" value="<?=$search_title?>" class="text2" placeholder="상품명 검색"></li>
						<li style="float:left"><button type="submit" class="btn_blue">검색</button></li>
					</ul>
				</form>
			</div>

<?
if(!$list_count) {
	echo '
			<div class="box product_count" style="background:#FAFAFA;text-align:center;">
				<p style="margin-top:100px">등록된 상품이 없습니다.</p>
			</div>' . PHP_EOL;
}
else {
?>
			<div class="container space-zero">
				<div class="category_list">
					<ul class="product_list">
<?
	//-- 이벤트 투자리스트 시작 ------------------------
	for($i=0; $i<$elist_count; ++$i) {
		//print_rr($ELIST[$i], 'font-size:12px');
?>

						<li <?=($ELIST[$i]['display']=='N' && ($goods_officer || $tmp_special_user)) ? 'style="opacity:0.5;"' : '';?>>
							<div class="p_img" onClick="<?=$ELIST[$i]['detail_url_script']?>">
								<p class="p_img-cover"></p>
								<p class="s_cover"><b><?=$ELIST[$i]['cover_caption']?></b></p>
								<?=$ELIST[$i]['main_image_tag']?>
							</div>
							<div class="p_info">
								<p class="p_info_tit" style="height:50px"><?=$ELIST[$i]['title']?></p>
								<p class="p_info_date">모집시작일 : <?=$ELIST[$i]['startDateTime']?></p>
								<div class="p_info_earn">
									<span><?=floatRtrim($ELIST[$i]["invest_return"], 2)?><b>%</b></span>
									<span><?=$ELIST[$i]['event_period_days']?><b>일</b></span>
									<span><?=number_format($ELIST[$i]['recruit_amount'])?><b>원</b></span>
								</div>
							</div>
							<div class="percent_area">
								<div class="percent">
									<div class="title">
										<div class="pull-left">펀딩 진행율</div>
										<div class="pull-right blue"><?=$ELIST[$i]['invest_percent']?>%</div>
									</div>
									<div class="progressbar" style="width:<?=$ELIST[$i]['invest_percent']?>%">
										<div class="progress"></div>
									</div>
								</div>
							</div>
							<div class="p_btn">
								<a href="<?=$ELIST[$i]['detail_url']?>"><?=($ELIST[$i]['button_caption']) ? $ELIST[$i]['button_caption'] : "상품상세보기";?></a>
							</div>
							<div class="p_repay">
<?
		if($ELIST[$i]['repay_count']>0) {
			echo '<strong>지급회차</strong>' . PHP_EOL .
			     '<span class="repay_count">'.$ELIST[$i]['repay_count'].'</span> / <span class="total_repay_count">'.$ELIST[$i]['total_repay_count'].'</span>' . PHP_EOL;
		}
		else {
			if($ELIST[$i]['invest_percent']>=100) {
				echo '<span>모집완료</span>' . PHP_EOL;
			}
			else{
				echo '<span>모집중</span>' . PHP_EOL;
			}
		}
?>
							</div>
						</li>


<?
	}
	//-- 이벤트 투자리스트 끝 ------------------------

	//-- 투자상품리스트 시작 -------------------------
	for($i=0; $i<$plist_count; ++$i) {

		//print_rr($PLIST[$i], 'width:50%;font-size:12px');

		switch($PLIST[$i]['category']) {
			case '1' : $cFlag = '<li class="p_ca-B">동산</li>'; break;
			case '2' : $cFlag = ($PLIST[$i]['mortgage_guarantees']=='1') ? '<li class="p_ca-A2">주택담보대출</li>' : '<li class="p_ca-A">부동산</li>'; break;
			case '3' : $cFlag = '<li class="p_ca-C">확정매출채권</li>'; break;
			default  : $cFlag = ''; break;
		}

		$aiFlag  = ($PLIST[$i]['ai_grp_idx']>0) ? '<li class="p_ai">자동투자</li>' : '';
		$newFlag = ($PLIST[$i]['new_flag']=='Y') ? '<li class="p_new">N</li>' : '';
		$srmFlag = ($PLIST[$i]["stream_url1"] OR $PLIST[$i]["stream_url2"]) ? '<li class="p_live_tv"><i class="fa fa-tv"></i> LIVE TV</li>' : '';
		$adiFlag = ($PLIST[$i]['advance_invest']=='Y') ? '<li class="p_adir">사전투자 ' . floatRtrim($PLIST[$i]['advance_invest_ratio']).'% <i class="fa fa-question-circle" id="question_1"></i></li>' : '';
		$pgFlag  = ($PLIST[$i]['purchase_guarantees']=='Y' && preg_match("/dev\.hello/", G5_URL)) ? '<li class="p_pg">채권매입계약</li>' : '';
		$adpFlag = ($PLIST[$i]['advanced_payment']=='Y') ? '<li class="p_adpy">이자 선지급</li>' : '';

		if($PLIST[$i]['main_image_url']) {
			$main_image_tag = '<img src="/data/product/'.$PLIST[$i]['main_image_url'].'" alt="'.$PLIST[$i]['title'].'">';
		}
		else {
			$main_image_tag = '<img src="/shop/img/no_image.gif" alt="'.$PLIST[$i]['title'].'">';
		}


		$coverCaption = $buttonCaption = NULL;
		$coverCaptionBgClass = "s_cover";

		// 투자대기중 또는 모집중일 경우(사전투자포함) 블링블링 이미지로 출력
		$coverCaption = '<b>'.$PLIST[$i]['buttonAndCover']['coverCaption'].'</b>';
		if($PLIST[$i]['buttonAndCover']['code']=='B01') {
			$coverCaption = '<img src="/theme/2018/img/main/pro_ready.gif" style="width:100%;height:100%;">';
			$coverCaptionBgClass = "s_cover2";
		}
		else if($PLIST[$i]['buttonAndCover']['code']=='B02') {
			$coverCaption = '<img src="/theme/2018/img/main_m/img_cover02.gif" height="40">';
		}

		$buttonCaption = $PLIST[$i]['buttonAndCover']['buttonCaption'];
		if($PLIST[$i]['buttonAndCover']['code']=='B01') {
			// 대기중일때
			$buttonCaption.= ($PLIST[$i]['total_invest_amount'] > 0) ? ' <span style="font-size:13px">( 모집된 금액 :  '.price_cutting($PLIST[$i]['total_invest_amount']).'원 )</span>' : '';
		}
		else if($PLIST[$i]['buttonAndCover']['code']=='B02') {
			// 모집중일때
			$buttonCaption.= ($PLIST[$i]['total_invest_amount'] > 0) ? ' <span style="font-size:13px">( 모집된 금액 :  '.price_cutting($PLIST[$i]['total_invest_amount']).'원 )</span>' : '';
		}
		else if($PLIST[$i]['buttonAndCover']['code']=='A01') {
			// 이자상환중일때
			$buttonCaption.= ($PLIST[$i]['repay_count']) ? ' <span style="font-size:13px">( 지급회차 '.$PLIST[$i]['repay_count'].' / '.$PLIST[$i]['total_repay_count'].' )</span>' : '';
		}

?>

						<li <?=($PLIST[$i]['display']=='N' && ($goods_officer || $tmp_special_user)) ? 'style="opacity:0.5;"' : '';?>>
							<div class="p_img" onClick="<?=$PLIST[$i]['detail_url_script']?>">
								<div class="p_flags">
									<ul>
										<?=$cFlag?><?=$aiFlag?><?=$newFlag?><?=$srmFlag?><?=$adiFlag?><?=$pgFlag?><?=$adpFlag?>
									</ul>
								</div>
								<p class="p_img-cover"></p>
								<p class="<?=$coverCaptionBgClass?>"><?=$coverCaption?></p>
								<?=$main_image_tag?>
							</div>
							<div class="p_info">
								<p class="p_info_tit" style="height:50px"><?=$PLIST[$i]['title']?></p>
								<p class="p_info_date">투자시작일 : <?=$PLIST[$i]['startDateTime']?></p>
								<div class="p_info_earn">
									<span><b>(연)</b><?=$PLIST[$i]['invest_return']?><b>%</b></span>
									<span><?=$PLIST[$i]['print_invest_period']?><b><?=$PLIST[$i]['print_invest_period_unit']?></b></span>
									<span><?=$PLIST[$i]['print_recruit_amount']?><b><?=$PLIST[$i]['print_recruit_amount_unit']?>원</b></span>
								</div>
							</div>
							<div class="percent_area">
								<div class="percent">
									<div class="title">
										<div class="pull-left">펀딩 진행율</div>
										<div class="pull-right blue"><?=$PLIST[$i]['invest_percent']?>%</div>
									</div>
									<div class="progressbar" style="width:<?=$PLIST[$i]['invest_percent']?>%">
										<div class="progress"></div>
									</div>
								</div>
							</div>
							<div class="p_btn">
								<a href="<?=$PLIST[$i]['detail_url']?>"><?=$buttonCaption?></a>
							</div>
						</li>
<?
	}
?>
					</ul>
				</div>
			</div>

			<div id="paging_start" style="display:inline-block; width:100%;height:55px;">
				<div id="paging_span" style="background-color:#fff">
					<? paging($product_count, $page, $size); ?>
				</div>
			</div>

<? if($total_page > 1) { ?>
			<style>
			#debug_pannel {position:fixed; display:inline-block; z-index:1002; top:200px;left:30px; width:250px; border:1px solid #bbb; padding:4px;background-color:#FFFF99;}
			#debug_pannel ul {display:inline-block;}
			#debug_pannel ul > li {height:22px;float:left;}
			#debug_pannel input {width:80px;text-align:right;}
			</style>
			<div id="debug_pannel" style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>">
				<ul>
					<li style="width:150px">window scroll top</li>
					<li style="width:90px"><input type="text" id="print_wst"></li>
				</ul>
				<ul>
					<li style="width:150px">window scroll bottom</li>
					<li style="width:90px"><input type="text" id="print_wsb"></li>
				</ul>
				<ul>
					<li style="width:150px">layer on point</li>
					<li style="width:90px"><input type="text" id="print_lsp"></li>
				</ul>
				<ul>
					<li style="width:150px">layer off point</li>
					<li style="width:90px"><input type="text" id="print_lep"></li>
				</ul>
			</div>

			<script type="text/javascript">
			$(document).on('click', '#paging_span span.btn_paging', function() {
				var url = '<?=$_SERVER['PHP_SELF']?>?<?=$qstr?>&page=' + $(this).attr('data-page');
				$(location).attr('href', url);
			});


			$(document).ready(function(){

				var d_count = <?=$list_count?>;
				var d_unit = 360;
				var lh = d_unit * d_count;

				if(d_count > 2) {

					var fixed_flag = false;

					var	wst = $(window).scrollTop();
					var	wsb = $(document).height() - $(window).height() - $(window).scrollTop();
					var lsp = $('#list_start').offset().top;
					var	lep = $(document).height() - $('#footer').offset().top + 28;

					$('#print_wst').val(wst);
					$('#print_wsb').val(wsb);
					$('#print_lsp').val(lsp);
					$('#print_lep').val(lep);

					$(window).scroll(function() {

						wst = $(window).scrollTop();
						wsb = $(document).height() - $(window).height() - $(window).scrollTop();

						if(wst >= lsp && wsb >= lep) {
							if(fixed_flag == false) {
								$('#paging_span').css({'position':'fixed', 'opacity':'0.95', 'background-color':'#fff', 'border-top':'1px dotted #AAA', 'z-index':'20', 'left':'0', 'width':'100%', 'bottom':'0', 'padding':'10px 0'});
								fixed_flag = true;
							}
						}
						else {
							if(fixed_flag == true) {
								$('#paging_span').css({'position':'', 'opacity':'1', 'border-top':'0', 'padding':'0'});
								fixed_flag = false;
							}
						}

						$('#print_wst').val(wst);
						$('#print_wsb').val(wsb);
						$('#print_lsp').val(lsp);
						$('#print_lep').val(lep);

					});
				}

			});
			</script>
<? } ?>

<?
}
?>
		</div>
	</div>
</div>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>