<?
include_once('./_common.php');



$g5['title'] = $EVENT['title'];
$g5['top_bn'] = "";
$g5['top_bn_alt'] = "";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

$event_idx = 4;

?>
<!-- 본문내용 START -->

<div id="content">
	<div class="location"><span><a href="">헬로페이 자동투자</a></span></div>

<? if(G5_IS_MOBILE) { ?>
  <div style="width:100%; padding:10px 2% 10px 2%; ">
		<div style="width:100%; margin:0 auto; border:1px solid #e8e8e8;">
			<p>

				<a href="/deposit/deposit.php?tab=5"><img src="/images/event/auto_invest_img01.jpg" width="100%"></a>
				<img src="/images/event/auto_invest_img02.jpg" width="100%">
				<a href="/deposit/deposit.php?tab=5"><img src="/images/event/auto_invest_img03.jpg" width="100%"></a>

		    </p>
		</div>

	</div>
<? } else { ?>
  <div style="width:100%; padding:10px 0;">
		<div style="width:800px; margin:0 auto;border:1px solid #eee;">
			<p>
				<a href="/deposit/deposit.php?tab=5"><img src="/images/event/auto_invest_img01.jpg" ></a>
				<img src="/images/event/auto_invest_img02.jpg" >
				<a href="/deposit/deposit.php?tab=5"><img src="/images/event/auto_invest_img03.jpg" ></a>

			</p>

		</div>

	</div>
<? } ?>

</div>



<!-- 본문내용 E N D -->
<?

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>