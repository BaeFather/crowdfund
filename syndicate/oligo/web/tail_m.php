</div><!-- .container -->

<div style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>;width:100%; margin:10px auto 10px;">
	<textarea id="ajax_return_txt" style="width:100%;height:200px;border:1px solid #EEE;background-color:#F7F7F7"></textarea>
</div>

<button type="button" id="top_btn"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only">상단으로</span></button>

<div id="loading" style="position:fixed; z-index:10001; top:0px; left:0px; width:100%; height:100%; display:none;">
	<table style="width:100%;height:100%;">
		<tr>
			<td style="text-align:center;height:100%;"><img src="/images/loading/ani_load.gif" style="width:24px;" alt="loading"><br>loading...</td>
		</tr>
	</table>
</div>


<script type="text/javascript">
// 레이어 오프
$(document).on("click", "#no, #closeLayer, .close, .close_button", function() {
	$.unblockUI();
	return false;
});

$(document).ready(function() {
	//상단으로
	$("#top_btn").on("click", function() {
		$("html, body").animate({scrollTop:0}, '500');
		return false;
	});

});
</script>

</body>
</html>
<?

echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다.
@sql_close();

ob_end_flush();
ob_end_clean();

?>