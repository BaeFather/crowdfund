<?
include_once('./_common.php');

$g5['title'] = '투자방법안내';
$g5['top_bn'] = "/images/investment/sub_guide.jpg";
$g5['top_bn_alt'] = "헬로펀딩 안전성 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if ($co['co_include_head']) {
    @include_once($co['co_include_head']);
}else {
    include_once(HF_PATH.'/hf_head.php');
}

?>

<!-- 본문내용 START -->
<div id="content">

	<div class="content">
<? if(G5_IS_MOBILE) { ?>
		<div>
			<div class="location"><span></span><b class="blue"><?=$g5['title'];?></b></div>
		</div>
<? } else { ?>
		<div class="location"><span></span><b class="blue"><?=$g5['title'];?></b></div>
<? } ?>

		<!--<img src="../images/investment/guide.jpg" alt="헬로펀딩 안전성" />-->
		<img src="<?=HF_URL?>/images/investment/guide_1_m.jpg" width="100%" alt="헬로펀딩 투자 가이드"/>
		<img src="<?=HF_URL?>/images/investment/guide_2_m.jpg" width="100%" alt="Step01 회원가입" />
		<img src="<?=HF_URL?>/images/investment/guide_3_m.jpg" width="100%" alt="Step02 가상계좌 발급받기" />
		<img src="<?=HF_URL?>/images/investment/guide_4_m.jpg" width="100%" alt="Step03 예치금 입금하기" />
		<img src="<?=HF_URL?>/images/investment/guide_5_m.jpg" width="100%" alt="Step03 예치금 입금하기" />


		<div style="text-align: center;margin-top:5%;">
			<a href="<?=HF_URL?>/investment/invest_list.php" class="btn_big_blue">투자상품보기</a>
    </div>

	</div>

</div>

<!-- 본문내용 E N D -->

<?
if ($co['co_include_tail']) {
    @include_once($co['co_include_tail']);
}else {
    include_once('./_tail.php');
}
?>