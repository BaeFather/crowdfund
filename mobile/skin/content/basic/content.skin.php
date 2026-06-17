<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$content_skin_url.'/style.css">', 0);

?>
<script>
$(document).ready(function(){
    $("#ctt_con").html($("#ctt_con").html().replace(/&lt;/gi,"<").replace(/&gt;/gi,">"));
	$("#ctt_con img").css("width","100%");
});
</script>
<article id="ctt" style="margin:0; padding:<?=(G5_IS_MOBILE) ? 63 : 0; ?>px 10px 30px 10px;" class="ctt_<?=$co_id?>">
    <header>
        <h1><?php echo $g5['title']; ?></h1>
    </header>
    <div id="ctt_con" style="margin:0;padding:0;">
        <?php echo $str; ?>
    </div>
</article>