<style>
.text2 { width:200px;height:38px;line-height:36px; font-size:14pt; padding:0 5px; border:1px solid #AAA; border-radius:3px; vertical-align:middle; }
</style>

<div id="content">

	<div class="content invest_list2">
		<div class="location"><span><a href="<?=HF_URL?>/investment/invest_list.php">투자상품보기</a></span><b class="blue"><?=$subtitle?></b></div>

		<!--
		<ul class="tab_type03">
			<li onClick="location.href='/investment/invest_list.php'" <?=($category=='')?'class="on"':'';?>>전체</li>
			<li onClick="location.href='?CA=A'" <?=($CA=='A')?'class="on"':'';?>>부동산</li>
			<li onClick="location.href='?CA=A2'" <?=($CA=='A2')?'class="on"':'';?>>주택담보</li>
			<li onClick="location.href='?CA=C'" <?=($CA=='C')?'class="on"':'';?>>헬로페이</li>
		</ul>
		//-->

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
					<li style="float:left;width:20%;"><button type="submit" class="btn_blue" style="width:99%;margin-top:0;">검색</button></li>
				</ul>
			</form>
		</div>

<?
if($count_sum) {
?>
		<div class="p_list">
			<ul>
<?
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

		$coverCaption = $buttonCaption = NULL;

		// 모집중일 경우(사전투자포함) 모집중 블링블링 이미지로 출력
		$coverCaption = '<b>'.$PLIST[$i]['buttonAndCover']['coverCaption'].'</b>';
		if($PLIST[$i]['buttonAndCover']['code']=='B02') {
			$coverCaption = '<img src="/img/main_m/img_cover02.gif" height="40%">';
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

				<li <?=($PLIST[$i]['display']=='N' && ($special_user || $tmp_special_user)) ? 'style="opacity:0.5;"' : '';?>>
					<div class="p_flags">
						<ul>
							<?=$cFlag?><?=$aiFlag?><?=$newFlag?><?=$srmFlag?><?=$adiFlag?><?=$pgFlag?><?=$adpFlag?>
						</ul>
					</div>
					<p class="p_img_cover" onClick="<?=$PLIST[$i]['detail_url_script']?>"></p>
					<p class="s_cover"><?=$coverCaption?></p>
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

<?
}
else {
	echo '
		<div class="box product_count" style="padding:150px 0;background:#FAFAFA;text-align:center;">
			<p>등록된 상품이 없습니다.</p>
		</div>' . PHP_EOL;
}
?>

<?
if($count_sum) {
?>
		<div id="paging_start">
			<div id="paging_span">
				<? paging($product_count, $page, $size); ?>
			</div>
		</div>
<?
}
?>

	</div><!-- content invest_list2 -->

</div><!-- content -->

<script type="text/javascript">
$(document).on('click', '#paging_span span.btn_paging', function() {
	var url = '?<?=$qstr?>&page=' + $(this).attr('data-page');
	$(location).attr('href', url);
});
</script>

<?
include_once(HF_PATH.'/tail.php');
?>