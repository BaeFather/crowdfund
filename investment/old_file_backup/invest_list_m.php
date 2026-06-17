<style>
#list_start select.invest-search-list {border: 1px solid #aaa; border-radius: 3px; color: #000;}
.text2 { width:200px;height:38px;line-height:36px; font-size:14pt; padding:0 5px; border:1px solid #AAA; border-radius:3px; vertical-align:middle; }
</style>

<!-- 본문내용 START -->
<div id="content">

	<div id="list_start" class="content invest_list2">

<? if( in_array($member['mb_id'], array('test1111','test2222')) ) { ?>
		<div class="list_info">
			본 상품은 헬로펀딩 VIP투자자분만 확인 가능합니다.<br>
			<span>[주의] 정식 투자시작 전 상품의 정보가 외부로 유출되지 않도록 주의 부탁드립니다.</span>
		</div>
<? } ?>

		<!-- 탭메뉴 //-->
		<ul class="tab_type03">
			<li onClick="location.href='<?=$_SERVER['SCRIPT_NAME']?>'" <?=($category=='')?'class="on"':'';?>>전체</li>
			<li onClick="location.href='<?=$_SERVER['SCRIPT_NAME']?>?CA=C'" <?=($CA=='C')?'class="on"':'';?>>SCF</li>
			<li onClick="location.href='<?=$_SERVER['SCRIPT_NAME']?>?CA=A2'" <?=($CA=='A2')?'class="on"':'';?>>주택담보</li>
			<li onClick="location.href='<?=$_SERVER['SCRIPT_NAME']?>?CA=A'" <?=($CA=='A')?'class="on"':'';?>>부동산</li>
			<!--<li onClick="location.href='<?=$_SERVER['SCRIPT_NAME']?>?CA=B'" <?=($CA=='B')?'class="on"':'';?>>동산</li>-->
		</ul>
		<!-- 탭메뉴 //-->


		<div style="width:97%; margin:20px 1.5% 0 1.5%; padding:0;">
			<form method="get">
				<input type="hidden" name="CA" value="<?=$CA?>">
				<ul style="width:100%; margin:0 0 -5px 0;">
					<li style="float:left;width:29%;margin-right:1%;">
						<select name="search_div" class="invest-search-list" style="height:38px;width:99%;">
							<option value="">전체</option>
							<option <?=$search_div=="9"?"selected":""?> value="9">모집중</option>
							<option <?=$search_div=="1"?"selected":""?> value="1">이자상환중</option>
							<option <?=$search_div=="2"?"selected":""?> value="2">상환완료</option>
							<option <?=$search_div=="3"?"selected":""?> value="3">상환지연/연체</option>
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

				<li <?=($ELIST[$i]['display']=='N' && ($is_admin=='super' || $developer || $goods_officer || $tmp_special_user)) ? 'style="opacity:0.5;"' : '';?>>
					<p class="p_img_cover" onClick="<?=$ELIST[$i]['detail_url_script']?>"></p>
					<p class="s_cover" onClick="<?=$ELIST[$i]['detail_url_script']?>"><b><?=$ELIST[$i]['cover_caption']?></b></p>
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
							<div class="pull-left">펀딩 진행률</div>
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
			case '1' : $cFlag = '<li><span class="p_ca-B">동산</span></li>'; break;
			case '2' : $cFlag = ($PLIST[$i]['mortgage_guarantees']=='1') ? '<li><span class="p_ca-A2">주택담보</span></li>' : '<li><span class="p_ca-A">부동산</span></li>'; break;
			case '3' : $cFlag = '<li><span class="p_ca-C">SCF</span></li>'; break;
			default  : $cFlag = ''; break;
		}

		$aiFlag  = ($PLIST[$i]['ai_grp_idx']>0) ? '<li><span class="p_ai">자동투자</span></li>' : '';
		$newFlag = ($PLIST[$i]['new_flag']=='Y') ? '<li><span class="p_new">N</span></li>' : '';
		$srmFlag = ($PLIST[$i]["stream_url1"] OR $PLIST[$i]["stream_url2"]) ? '<li><span class="p_live_tv"><i class="fa fa-tv"></i> LIVE TV</span></li>' : '';
		$adiFlag = ($PLIST[$i]['advance_invest']=='Y') ? '<li><span class="p_adir">사전투자 ' . floatRtrim($PLIST[$i]['advance_invest_ratio']).'% <i class="fa fa-question-circle" id="question_1"></i></span></li>' : '';
		$pgFlag  = ($PLIST[$i]['purchase_guarantees']=='Y' && preg_match("/dev\.hello/", G5_URL)) ? '<li><span class="p_pg">채권매입계약</span></li>' : '';
		$adpFlag = ($PLIST[$i]['advanced_payment']=='Y') ? '<li><span class="p_adpy">이자 선지급</span></li>' : '';
		$conFlag = ($PLIST[$i]['isConsor']=='1') ? '<li><span class="p_con">컨소시엄</span></li>' : '';
		// 2020년 10월 7일 이상규 과장의 요청으로 전승찬 처리 2020년 8월 27일 이후의 법인전용 상품에 대해서는 리스트에서만 법인 전용 표시를 안함
		//$onlyVipFlag = ($PLIST[$i]['only_vip']=='1') ? '<li><span class="p_vip">법인전용</span></li>' : '';
		$onlyVipFlag = ($PLIST[$i]['only_vip']=='1' AND $PLIST[$i]['start_datetime']<"2020-08-27 00:00:00") ? '<li><span class="p_vip">법인전용</span></li>' : '';


		if($PLIST[$i]['main_image_url']) {
			$main_image_tag = '<img src="/data/product/'.$PLIST[$i]['main_image_url'].'" alt="'.$PLIST[$i]['title'].'" width="100%" height="230px">';
		}
		else {
			$main_image_tag = '<img src="/shop/img/no_image.gif" alt="'.$PLIST[$i]['title'].'" width="100%" height="230px">';
		}


		$coverCaption = $buttonCaption = NULL;
		$coverCaptionBgClass = "s_cover";

		if($PLIST[$i]['display']=='Y') {

			// 모집중일 경우(사전투자포함) 모집중 블링블링 이미지로 출력
			$coverCaption = '<b>'.$PLIST[$i]['buttonAndCover']['coverCaption'].'</b>';
			if($PLIST[$i]['buttonAndCover']['code']=='B01') {
				$coverCaption = '<img src="/theme/2018/img/main_m/pro_ready_m.png" style="width:100%;height:100%;">';
				$coverCaptionBgClass = "s_cover2";
			}
			else if($PLIST[$i]['buttonAndCover']['code']=='B02') {
				$coverCaption = '<img src="/theme/2018/img/main_m/img_cover2_m.png" style="width:100%;height:100%;">';
				$coverCaptionBgClass = "s_cover2";
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

		}
		else {

			$coverCaption = '<b>준비상품</b>';
			$buttonCaption = '내용보기';

		}

?>

				<li <?=($PLIST[$i]['display']=='N' && ($is_admin=='super' || $developer || $goods_officer || $tmp_special_user)) ? 'style="opacity:0.5;"' : '';?>>
					<div class="p_flags">
						<ul>
							<?=$newFlag?><?=$cFlag?><?=$aiFlag?><?=$conFlag?><?=$srmFlag?><?=$adiFlag?><?=$pgFlag?><?=$adpFlag?><?=$onlyVipFlag?>
						</ul>
					</div>
					<p class="p_img_cover" onClick="<?=$PLIST[$i]['detail_url_script']?>"></p>
					<p class="<?=$coverCaptionBgClass?>" onClick="<?=$PLIST[$i]['detail_url_script']?>"><?=$coverCaption?></p>
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
							<div class="pull-left">펀딩 진행률</div>
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

<?		if($total_page > 1) { ?>
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
			var url = '<?=$_SERVER['SCRIPT_NAME']?>?<?=$qstr?>&page=' + $(this).attr('data-page');
			$(location).attr('href', url);
		});

<? if(false) { ?>
/*
		// page_navigation slide
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
*/
<?			} ?>
		</script>
<?		} ?>

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
		location.replace('/investment/invest_list.php');
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