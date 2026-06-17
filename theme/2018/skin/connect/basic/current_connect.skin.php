<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$connect_skin_url.'/style.css">', 0);

$list_count = count($list);

?>

<!-- 현재접속자 목록 시작 { -->

<div id="content">

	<div class="location_top">
		<div class="location"><span></span><b class="blue">현재접속자정보</b></div>
		<div class="content">

			<div id="main_content" class="tbl_head01 tbl_wrap" style="min-height:300px;"></div>

			<div style="clear:both;height:30px;"></div>

		</div>
	</div>
</div>

<script type="text/javascript">
function conn_data() {
	$.ajax({
		url: '/etc/current_connect.ajax.php',
		success: function(data) {
			$('#main_content').html(data);
		}
	});
}

$(document).ready(function() {
	conn_data();
	setInterval(function() { conn_data();	}, 5*1000);
});
</script>

<!-- } 현재접속자 목록 끝 -->