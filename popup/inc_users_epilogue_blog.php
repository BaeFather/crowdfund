<?php
include_once('./_common.php');

// 투자후기 조회
$best_review = array();
$sql = sql_query("SELECT * FROM epilogue_list WHERE display_yn = 'Y' AND best_review = 'Y' ORDER BY sort ASC LIMIT 3");
while($row = sql_fetch_array($sql))
{
    if (!empty($row["thumbnail"]) && file_exists(G5_IMG_PATH."/review/".$row["thumbnail"])) {
        $row["thumb_url"] = G5_IMG_URL."/review/".$row["thumbnail"];
    }else{
        $row["thumb_url"] = G5_IMAGES_URL.'/review/sumnail_img01.jpg';
    }
    $row["contents"] = get_text(strip_tags(html_clean($row["contents"])));
    $row["contents"] = utf8_strcut(trim($row["contents"]), 153);
    array_push($best_review, $row);
}

?>

<!---------- 투자후기 시작 ---------->
<div class="review_tit">
	<p>헬로펀딩 투자후기</p><p><a href="/bbs/epilogue.php"><img src="/images/main/review_more.jpg"/></a></p>
</div>
<div id="reviews_area" class="bor_non">
    <div class="reviews_cont" style="margin-top:25px;">
        <?php
            if(count($best_review) > 0){
        ?>
        <ul>
            <?php foreach($best_review as $best) { ?>
            <li>
                <div class="review">
                    <div class="review_box">
                        <span class="subject"><?php echo $best["subject"];?></span>
                        <span class="mem_name"><?php echo $best["mem_name"].' (ID: '.$best["mem_id"].')';?></span>
                        <div class="thumbnail">
                            <img src="<?php echo $best["thumb_url"];?>" width="100%" height="203" alt="<?php echo $best['thumbnail_origin'];?>"/>
                        </div>
                        <span class="contents"><?php echo $best["contents"];?></span>
                        <div class="link">
                            <a href="<?php echo $best["target_link"];?>" target="_blank">자세히 보기</a>
                        </div>
                    </div>
                </div>
            </li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
</div>

<?php if(false) { ?>
<!-- 투자후기 팝업 -->
<div id="epilogue_popup">
	<div class="title">헬로펀딩 투자후기 <img src="/images/btn_close.gif" alt="close" class="close"></div>
	<div class="gap"></div>
	<div class="con" id="epilogue_con"><!-- 내용 --></div>
</div>

<script>
// 레이어 온 (투자후기)
$('#review1, #review2, #review3').click(function() {
	var idx = $(this).attr('data-idx');
	$.ajax({
		url : "<?=G5_THEME_URL?>/ajax_invest_epilogue.php",
		type: "POST",
		data: {idx:idx},
		success: function(data){
			$('#epilogue_con').html(data);

			$.blockUI({
				message: $('#epilogue_popup'),
				<? if(G5_IS_MOBILE) { ?>
				css: { top:'10%',width:'98%',height:'80%',border:'1px solid #AAA',cursor:'default', left:'1%' }
				<? } else { ?>
				css: { top:'10%',width:'600px',height:'680px',border:'1px solid #AAA',cursor:'default' }
				<? } ?>
			});
		},
		error: function () {
			alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
		}
	});
});
</script>
<?php } ?>
<!---------- 투자후기 끝 ---------->