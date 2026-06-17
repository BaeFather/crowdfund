<link rel="stylesheet" href="<?=HF_URL?>/view/faq_style.m.css">

<div id="content">
	<div class="content">

		<div>
			<div class="location">
				<span></span><b class="blue">도움말</b>
			</div>
		</div>

		<ul class="tab_type02">
<?
foreach( $faq_master_list as $v ){
	$category_msg = '';
?>
			<li <? if($fm_id==$v['fm_id']){ ?>class="on"<? } ?> onClick="javascript:location.href='<?=$category_href?>?fm_id=<?=$v['fm_id']?>';">
				<?=$category_msg.$v['fm_subject'];?>
			</li>
<?
}
?>
		</ul>

		<!-- 투자 -->
		<div class="FAQ">
<?
foreach($faq_list as $key=>$v){
	if(empty($v))
		continue;
?>
			<dl>
				<dt class="title"><?=conv_content($v['fa_subject'], 1); ?></dt>
				<dd class="text"><?=conv_content($v['fa_content'], 1); ?></dd>
			</dl>
<?
}
?>
		</div>

	</div>
</div>

<script src="/js/viewimageresize.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	//faq 질문 클릭시 내용 오픈
	$('.FAQ dl').click(function(){
		var title = $(this);
		var content = $(this).find("dd.text");
		content.slideToggle("fast", function(e) {
			if($(this).is(':visible')) {
				title.css({background:'url("<?=G5_URL?>/theme/2018/img/bbs/arrow_up.gif") no-repeat right 1px', 'background-size': '25px auto'});
			}
			else {
				title.css({background:'url("<?=G5_URL?>/theme/2018/img/bbs/arrow_down.gif") no-repeat right 1px', 'background-size': '25px auto'});
				$(this).viewimageresize2();
			}
		});
		return false;
	});
});
</script>