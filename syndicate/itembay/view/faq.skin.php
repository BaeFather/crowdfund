<div id="content">
	<div class="content">

		<div class="location">
			<span></span><b class="blue">도움말</b>
		</div>

		<ul class="tab_type02">
<?
foreach( $faq_master_list as $v ){
	$category_msg = '';
?>
      <li <? if($fm_id==$v['fm_id']){ ?>class="on"<? } ?> onClick="location.href='<?=$category_href;?>?fm_id=<?=$v['fm_id']?>';">
				<?=$category_msg.$v['fm_subject']?>
			</li>

<?
}
?>
    </ul>

    <!-- 투자 -->
    <div class="FAQ" style="display:block;">
<?
foreach($faq_list as $key=>$v){
	if(empty($v))
		continue;
?>
			<dl>
				<dt class="title"><?=conv_content($v['fa_subject'], 1)?></dt>
				<dd class="text"><?=conv_content($v['fa_content'], 1)?></dd>
			</dl>

<?
}
?>
    </div>

	</div>
</div>

<script>
$(document).ready(function(){
  //faq 질문 클릭시 내용 오픈
  $('.FAQ dl').click(function(){
    $(this).css({background:'url(../images/bbs/arrow_up.gif) no-repeat right top'}).find('dd').slideDown('fast');
    $(this).siblings().css({background:'url(../images/bbs/arrow_down.gif) no-repeat right top'}).find('dd').slideUp('fast');
  });
});
</script>