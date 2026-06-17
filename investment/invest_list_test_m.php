<!-- 본문내용 START -->
<div id="content">
	<div class="location"><span><a href="<?=G5_URL?>/investment/invest_list.php">투자상품보기</a></span><b class="blue"><?=$subtitle?></b></div>
	<div class="content invest_list2" style="min-height:500px">

<?
if(false) {
	if($tmp_special_user) {
?>
		<div class="list_info" style="color:#284893;">
			<ul>
				<li>본 상품은 펀딩심사 진행중입니다. 안전성 확보등 추가내용은 업데이트 되며, 내부 심사평가에 따라 펀딩진행이 불가 할 수 있습니다.</li>
				<li>본 상품은 헬로펀딩 VIP투자자분만 확인 가능합니다.<br>
					<span style="color:red">[주의] 정식 투자시작 전 상품의 정보가 외부로 유출되지 않도록 주의 부탁드립니다.</span>
				<li>
				<li style="border-bottom:1px solid red;"><b>투자시작일은 대출자와의 일정협의를 통해 조정될 수 있습니다.</b></li>
			</ul>
		</div>
<?
	}
}
?>

<? if( in_array($member['mb_id'], array('test1111','test2222')) ) { ?>
		<div class="list_info">
			본 상품은 헬로펀딩 VIP투자자분만 확인 가능합니다.<br>
			<span>[주의] 정식 투자시작 전 상품의 정보가 외부로 유출되지 않도록 주의 부탁드립니다.</span>
		</div>
<? } ?>

		<!-- 탭메뉴 //-->
		<ul class="tab_type03">
			<li onClick="location.href='<?=$_SERVER['PHP_SELF']?>'" <?=($category=='')?'class="on"':'';?>>전체</li>
			<li onClick="location.href='?CA=A'" <?=($CA=='A')?'class="on"':'';?>>부동산</li>
			<li onClick="location.href='?CA=A2'" <?=($CA=='A2')?'class="on"':'';?>>주택담보</li>
			<li onClick="location.href='?CA=B'" <?=($CA=='B')?'class="on"':'';?>>동산</li>
			<li onClick="location.href='?CA=C'" <?=($CA=='C')?'class="on"':'';?>>확정매출채권</li>
		</ul>
		<!-- 탭메뉴 //-->

<?
switch($CA) {
	case 'A'  : $CATEGORY = array('id'=>'CAA',   'title'=>'부동산',       'guide_image_a'=>'/images/investment/ca_ban_tit02_m.jpg', 'guide_image_b'=>'/images/investment/ca_ban02_m.jpg'); break;
	case 'A2' : $CATEGORY = array('id'=>'CAA2',  'title'=>'주택담보',     'guide_image_a'=>'/images/investment/ca_ban_tit03_m.jpg', 'guide_image_b'=>'/images/investment/ca_ban03_m.jpg'); break;
	case 'B'  : $CATEGORY = array('id'=>'CAB',   'title'=>'동산',         'guide_image_a'=>'/images/investment/ca_ban_tit04_m.jpg', 'guide_image_b'=>'/images/investment/ca_ban04_m.jpg'); break;
	case 'C'  : $CATEGORY = array('id'=>'CAC',   'title'=>'확정매출채권', 'guide_image_a'=>'/images/investment/ca_ban_tit05_m.jpg', 'guide_image_b'=>'/images/investment/ca_ban05_m.jpg'); break;
	default   : $CATEGORY = array('id'=>'CAALL', 'title'=>'전체',         'guide_image_a'=>'/images/investment/ca_ban_tit01_m.jpg', 'guide_image_b'=>'/images/investment/ca_ban01_m.jpg'); break;
}
?>
		<!-- 카테고리 타이틀/설명 -->
		<div class="clearfix">
			<div id="<?=$CATEGORY['id']?>" title="<?=$CATEGORY['title']?>">
				<span class="unfold"></span>
				<img src="<?=$CATEGORY['guide_image_a']?>" width="100%">
				<p class="hide"><img src="<?=$CATEGORY['guide_image_b']?>" width="100%"></p>
			</div>
		</div>
		<!-- 카테고리 타이틀/설명 -->

		<!-- 펼치기메뉴 시작 //-->
		<script type="text/javascript">
		$(document).ready(function() {
			$('.unfold').click(function() {
				if($('.unfold').hasClass('unfold')) {
					$('.unfold').addClass('fold').removeClass('unfold');
					$('.hide').slideUp();
				}
				else if($('.fold').hasClass('fold')) {
					$('.fold').addClass('unfold').removeClass('fold');
					$('.hide').slideDown();
				}
			});
		});
		</script>
		<!-- 펼치기메뉴 끝 //-->

<?
if(!$count_sum) {
	echo '
			<div class="box product_count" style="padding:150px 0;background:#FAFAFA;text-align:center;">
				<p>등록된 상품이 없습니다.</p>
			</div>' . PHP_EOL;
}
else {
?>
		<div class="p_list">
			<ul>
<?
	//-- 이벤트 투자리스트 시작 ------------------------
	for($i=0; $i<$elist_count; $i++) {
		//print_rr($ELIST[$i], 'font-size:12px');
?>

				<li <?=($ELIST[$i]['display']=='N' && ($special_user || $tmp_special_user)) ? 'style="opacity:0.5;"' : '';?>>
					<p class="p_img_cover" onClick="<?=$ELIST[$i]['detail_url_script']?>"></p>
					<p class="s_cover"><b><?=$ELIST[$i]['cover_caption']?></b></p>
					<div class="main_image" onClick="<?=$ELIST[$i]['detail_url_script']?>"><?=$ELIST[$i]['main_image_tag']?></div>
					<div class="p_info">
						<p class="p_title"><?=$ELIST[$i]['title']?></p>
						<p class="p_date">투자시작일 : <?=$ELIST[$i]['startDateTime']?></p>
						<ul class="p_total">
							<li><span><b>(연)</b><?=floatRtrim($ELIST[$i]["invest_return"], 2)?></span> <b>%</b></li>
							<li><span><?=$ELIST[$i]['event_period_days']?></span> <b>일</b></li>
							<li><span><?=number_format($ELIST[$i]['recruit_amount'])?></span> <b>원</b></li>
						</ul>
					</div>
					<div class="percent">
						<div class="title">
							<div class="pull-left">펀딩 진행율</div>
							<div class="pull-right blue"><?=$ELIST[$i]['invest_percent']?>%</div>
						</div>
						<div class="progressbar" style="width:<?=$ELIST[$i]['invest_percent']?>%">
							<div class="progress"></div>
						</div>
					</div>
					<div class="p_btn">
						<a href="<?=$ELIST[$i]['detail_url']?>"><?=($ELIST[$i]['button_caption']) ? $ELIST[$i]['button_caption'] : "상품상세보기";?></a>
					</div>
					<div class="p_repay">
						<? if($ELIST[$i]['repay_count'] > 0){ ?>
							<strong>지급회차</strong>
							<span class="repay_count"><?=$ELIST[$i]['repay_count']?></span> / <span class="total_repay_count"><?=$ELIST[$i]['total_repay_count']?></span>
						<? } else { ?>
							<? if($ELIST[$i]['invest_percent'] >= 100) { ?>
								<span>모집완료</span>
							<? }else{ ?>
								<strong class="invest_percent"><?=$ELIST[$i]['invest_percent']?>%</strong>
								<span>모집중</span>
							<? } ?>
						<? } ?>
					</div>
				</li>

<?
	}
	//-- 이벤트 투자리스트 끝 ------------------------

	//-- 투자상품리스트 시작 -------------------------
	for($i=0; $i<$plist_count; $i++) {

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
			$main_image_tag = '<img src="/data/product/'.$PLIST[$i]['main_image_url'].'" alt="'.$PLIST[$i]['title'].'" width="100%" height="230px">';
		}
		else {
			$main_image_tag = '<img src="/shop/img/no_image.gif" alt="'.$PLIST[$i]['title'].'" width="100%" height="230px">';
		}

?>

				<li <?=($PLIST[$i]['display']=='N' && ($special_user || $tmp_special_user)) ? 'style="opacity:0.5;"' : '';?>>
					<div class="p_flags">
						<ul>
							<?=$cFlag?><?=$aiFlag?><?=$newFlag?><?=$srmFlag?><?=$adiFlag?><?=$pgFlag?><?=$adpFlag?>
						</ul>
					</div>
					<p class="p_img_cover" onClick="<?=$PLIST[$i]['detail_url_script']?>"></p>
					<p class="s_cover"><b><?=$PLIST[$i]['buttonAndCover']['coverCaption']?></b></p>
					<div class="main_image" onClick="<?=$PLIST[$i]['detail_url_script']?>"><?=$main_image_tag?></div>
					<div class="p_info">
						<p class="p_title"><?=$PLIST[$i]['title']?></p>
						<p class="p_date">투자시작일 : <?=$PLIST[$i]['startDateTime']?></p>
						<ul class="p_total">
							<li><span><b>(연)</b><?=$PLIST[$i]['invest_return']?></span> <b>%</b></li>
							<li><span><?=$PLIST[$i]['print_invest_period']?></span> <b><?=$PLIST[$i]['print_invest_period_unit']?></b></li>
							<li><span><?=$PLIST[$i]['print_recruit_amount']?></span> <b><?=$PLIST[$i]['print_recruit_amount_unit']?>원</b></li>
						</ul>
					</div>
					<div class="percent">
						<div class="title">
							<div class="pull-left">펀딩 진행율</div>
							<div class="pull-right blue"><?=$PLIST[$i]['invest_percent']?>%</div>
						</div>
						<div class="progressbar" style="width:<?=$PLIST[$i]['invest_percent']?>%">
							<div class="progress"></div>
						</div>
					</div>
					<div class="p_btn">
						<a href="<?=$PLIST[$i]['detail_url']?>"><?=($PLIST[$i]['buttonAndCover']['buttonCaption']) ? $PLIST[$i]['buttonAndCover']['buttonCaption'] : "상품상세보기";?></a>
					</div>
					<div class="p_repay">
						<? if($PLIST[$i]['repay_count'] > 0){ ?>
							<strong>지급회차</strong>
							<span class="repay_count"><?=$PLIST[$i]['repay_count']?></span> / <span class="total_repay_count"><?=$PLIST[$i]['total_repay_count']?></span>
						<? } else { ?>
							<? if($PLIST[$i]['invest_percent'] >= 100) { ?>
								<span>모집완료</span>
							<? }else{ ?>
								<strong class="invest_percent"><?=$PLIST[$i]['invest_percent']?>%</strong>
								<span>모집중</span>
							<? } ?>
						<? } ?>
					</div>
				</li>

<?
	}
?>
			</ul>
		</div>
<?
}
?>
	</div>
</div>

<!-- 본문내용 E N D -->
<?
if($co['co_include_tail']){
	@include_once($co['co_include_tail']);
} else {
	include_once('./_tail.php');
}
?>