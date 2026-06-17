<style>
.text2 { width:200px;height:38px;line-height:36px; font-size:14pt; padding:0 5px; border:1px solid #AAA; border-radius:3px; vertical-align:middle; }
</style>

<!-- 본문내용 START -->
<div id="content">
	<div class="location"><span><a href="<?=G5_URL?>/investment/invest_list.php">투자상품보기</a></span><b class="blue"><?=$subtitle?></b></div>
	<div id="list_start" class="content invest_list2">

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
				<span id="fold_button" class="<?=($_COOKIE['tImgHide'])?'fold':'unfold';?>"></span>
				<img src="<?=$CATEGORY['guide_image_a']?>" width="100%">
				<p class="hide" <?=($_COOKIE['tImgHide']) ? 'style="display:none;"':'';?>><img src="<?=$CATEGORY['guide_image_b']?>" width="100%"></p>
			</div>
		</div>
		<!-- 카테고리 타이틀/설명 -->


		<!-- 펼치기메뉴 시작 //-->
		<script type="text/javascript">
		$('#<?=$CATEGORY['id']?>').click(function() {
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
		<!-- 펼치기메뉴 끝 //-->


		<div style="width:97%; margin:20px 1.5% 0 1.5%; padding:0;">
			<form method="get">
				<input type="hidden" name="CA" value="<?=$CA?>">
				<ul style="width:100%; margin:0 0 -5px 0;">
					<li style="float:left;width:29%;margin-right:1%;">
						<select name="search_div" style="height:38px;width:99%;">
							<option value="">전체</option>
							<option <?=$_REQUEST['search_div']=="9"?"selected":""?>   value="9">모집중</option>
							<option <?=$_REQUEST['search_div']=="1"?"selected":""?> value="1">이자상환중</option>
							<option <?=$_REQUEST['search_div']=="2"?"selected":""?>  value="2">상환완료</option>
						</select>
					</li>
					<li style="float:left;width:49%;margin-right:1%;"><input type="text" name="search_title" value="<?=$search_title?>" class="text2" style="width:99%" placeholder="상품명 검색"></li>
					<li style="float:left;width:20%;"><button type="submit" class="btn_blue" style="width:99%">검색</button></li>
				</ul>
			</form>
		</div>

<?
if(!$list_count) {
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
	for($i=0; $i<$elist_count; ++$i) {
		//print_rr($ELIST[$i], 'font-size:12px');
?>

				<li <?=($ELIST[$i]['display']=='N' && ($goods_officer || $tmp_special_user)) ? 'style="opacity:0.5;"' : '';?>>
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
			$main_image_tag = '<img src="/data/product/'.$PLIST[$i]['main_image_url'].'" alt="'.$PLIST[$i]['title'].'" width="100%" height="230px">';
		}
		else {
			$main_image_tag = '<img src="/shop/img/no_image.gif" alt="'.$PLIST[$i]['title'].'" width="100%" height="230px">';
		}


		$coverCaption = $buttonCaption = NULL;
		$coverCaptionBgClass = "s_cover";

		// 모집중일 경우(사전투자포함) 모집중 블링블링 이미지로 출력
		$coverCaption = '<b>'.$PLIST[$i]['buttonAndCover']['coverCaption'].'</b>';
		if($PLIST[$i]['buttonAndCover']['code']=='B01') {
			$coverCaption = '<img src="/theme/2018/img/main/pro_ready_m.gif" style="width:100%;height:100%;">';
			$coverCaptionBgClass = "s_cover2";
		}
		else if($PLIST[$i]['buttonAndCover']['code']=='B02') {
			$coverCaption = '<img src="/theme/2018/img/main_m/img_cover02.gif" height="40%">';
		}

		$buttonCaption = $PLIST[$i]['buttonAndCover']['buttonCaption'];
		if($PLIST[$i]['buttonAndCover']['code']=='B01') {
			// 대기중일때
			$buttonCaption.= ($PLIST[$i]['total_invest_amount'] > 0) ? ' <span style="font-size:12px">( 모집된 금액 :  '.price_cutting($PLIST[$i]['total_invest_amount']).'원 )</span>' : '';
		}
		else if($PLIST[$i]['buttonAndCover']['code']=='B02') {
			// 모집중일때
			$buttonCaption.= ($PLIST[$i]['total_invest_amount'] > 0) ? ' <span style="font-size:12px">( 모집된 금액 :  '.price_cutting($PLIST[$i]['total_invest_amount']).'원 )</span>' : '';
		}
		else if($PLIST[$i]['buttonAndCover']['code']=='A01') {
			// 이자상환중일때
			$buttonCaption.= ($PLIST[$i]['repay_count']) ? ' <span style="font-size:12px">( 지급회차 '.$PLIST[$i]['repay_count'].' / '.$PLIST[$i]['total_repay_count'].' )</span>' : '';
		}

?>

				<li <?=($PLIST[$i]['display']=='N' && ($goods_officer || $tmp_special_user)) ? 'style="opacity:0.5;"' : '';?>>
					<div class="p_flags">
						<ul>
							<?=$cFlag?><?=$aiFlag?><?=$newFlag?><?=$srmFlag?><?=$adiFlag?><?=$pgFlag?><?=$adpFlag?>
						</ul>
					</div>
					<p class="p_img_cover" onClick="<?=$PLIST[$i]['detail_url_script']?>"></p>
					<p class="<?=$coverCaptionBgClass?>"><?=$coverCaption?></p>
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
						<a href="<?=$PLIST[$i]['detail_url']?>"><?=$buttonCaption?></a>
					</div>
				</li>

<?
	}
?>
			</ul>
		</div>

		<div id="paging_start" style="display:inline-block; width:100%;height:45px;">
			<div id="paging_span" style="width:100%:border:1px solid blue">
				<? paging($product_count, $page, $size); ?>
			</div>
		</div>

<? if($total_page > 1) { ?>
		<style>
		#debug_pannel {position:fixed; z-index:1002; top:200px;left:30px; width:250px; border:1px solid #bbb; padding:4px;background-color:#FFFF99;}
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
				var	lep = $(document).height() - $('#ft').offset().top - $('#bottom_guide').height();

				$('#print_wst').val(wst);
				$('#print_wsb').val(wsb);
				$('#print_lsp').val(lsp);
				$('#print_lep').val(lep);

				$(window).scroll(function() {

					wst = $(window).scrollTop();
					wsb = $(document).height() - $(window).height() - $(window).scrollTop();

					if(wst >= lsp && wsb >= lep) {
						if(fixed_flag == false) {
							$('#paging_span').css({'position':'fixed', 'opacity':'0.95', 'background-color':'#fff', 'border-top':'1px dotted #AAA', 'z-index':'20', 'left':'0', 'width':'100%', 'bottom':'50px', 'padding':'10px 0'});
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

<? if($isFirstPageM) { ?>
	<style>
	.more_zone {display:inline-block;width:92%;margin:0 4% 20px;text-align:center;}
	.addstyle {width:100%;color:#3366FF;border:1px solid #000;border-raduis:3px;font-size:1.2em;font-weight:bold;}
	</style>
	<div id="more" class="more_zone">
		<button id="more_button" type="button" class="btn_link addstyle">더보기</button>
	</div>
	<script>
	$('#more_button').click(function() {
		location.replace('<?=$_SERVER['PHP_SELF']?>');
	});
	</script>
<? } ?>

</div>

<!-- 본문내용 E N D -->
<?
if($co['co_include_tail']){
	@include_once($co['co_include_tail']);
} else {
	include_once('./_tail.php');
}
?>