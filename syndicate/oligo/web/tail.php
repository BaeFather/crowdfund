</div><!-- .container -->

<div style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>;width:1150px; margin:10px auto 10px;">
	<textarea id="ajax_return_txt" style="width:100%;height:200px;border:1px solid #EEE;background-color:#F7F7F7"></textarea>
</div>

<!--
<footer id="footer">
	<div class="foot_info">
		<div class="fintech">
			<ul style="display:inline-block;border-bottom:1px solid #4a5b87;width:1150px;">
				<li>
					<address>
						<span>고객센터 : 1588-6760  <a href="http://pf.kakao.com/_xgAdWu"><img src="<?=G5_THEME_IMG_URL?>/main/kakao_plus_btn02.png" alt="<?=$g5['title']?> 플러스친구"  style="padding-left:5px;"></a></span>
						(평일 : 10시~19시  |  점심시간 : 13시~14시 토.일.공휴일 : 휴무)<br>
					</address>
				</li>
				<li>
					<span class="p2p_btn">
						<a href="http://p2plending.or.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/p2p_btn01.png" alt="한국P2P금융협회" ></a>
						<span>헬로펀딩은 한국P2P금융협회 회원사로 협회 규정을 준수하고 있습니다.</span>
					</span>
				</li>
			</ul>
			<div style="padding-top:20px;">
				<span class="comp01_tit">(주)헬로핀테크</span>
				<span class="comp01">대표 : 남기중 | 사업자번호 : 789-81-00529 | 주소 : 서울시 강남구 대치동 945-10(테헤란로 98길 8) KT&G 대치타워 4층</span>
			</div>
		</div>
	</div>
</footer>
-->

<div id="loading" style="position:fixed; z-index:10001; top:0px; left:0px; width:100%; height:100%; display:none;">
	<table style="width:100%;height:100%;">
		<tr>
			<td style="text-align:center;height:100%;"><img src="/images/loading/ani_load.gif" style="width:24px;" alt="loading"><br>loading...</td>
		</tr>
	</table>
</div>

<script>
// 레이어 오프
$(document).on("click", "#no, #closeLayer, .close, .close_button", function() {
	$.unblockUI();
	return false;
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