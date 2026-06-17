<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>

	</div>
</div>

</body>
</html>

<script type="text/javascript">
// 레이어 오프
$(document).on("click", "#no, #closeLayer, .close, .close_button", function(){
	$.unblockUI();
	return false;
});

/*
$(document).keydown(function(e) {
    key = (e) ? e.keyCode : event.keyCode;
    if (key == 116) {
			return false;
    }
});
*/
</script>

<?
echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다.
@sql_close($g5['connect_db']);
?>