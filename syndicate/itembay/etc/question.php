<?
include_once('./_common.php');

include_once(HF_PATH.'/hf_head.php');


add_stylesheet('<link rel="stylesheet" href="'.HF_PATH.'/css/epilogue.css">', 0);

?>
<div id="content">
  <div class="content">
		<? if(G5_IS_MOBILE) { ?>
		<div >
			<div class="location">
				<span><a href="<?php echo G5_URL;?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">문의하기</b>
			</div>
		</div>
		<? } else { ?>
		<div class="location">
			<span><a href="<?php echo G5_URL;?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">문의하기</b>
		</div>
		<? } ?>

		<img src="/img/main/question.jpg" alt="문의하기" width="100%">

	</div>
</div>

<?

include_once(HF_PATH.'/_tail.php');

?>