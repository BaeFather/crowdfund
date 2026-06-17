<div id="pop" class="pop_wrap">
  <div class="pop_layer">
	<div class="close">
		<i class="fas fa-times"></i>
	</div>
	<div class="popup_box">
	<div class="popup_list">

		<?php

		// 레이어보기
		function view_layer($no) {
			global $g5;

			$sql = "select * from {$g5[site_popup_table]} where no=$no";

			$result = sql_query($sql);
			$layer = sql_fetch_array($result);  // 쿼리 결과값 한 행 배열로 얻음

			$title = stripslashes($layer[title]);
			$check_input = $layer[check_input];

			$content = nl2br(stripslashes($layer[content]));

		}
		?>
		
			<?
			$ymd = date('Y-m-d');
			$sql = "select * from g5_open_popup where check_use = 'Y' AND (reg_date <= '$ymd' AND end_date >= '$ymd') ORDER BY no desc";  // 보여질 레이어 팝업 sql문
			$res = sql_query($sql);		 // sql_query($sql) 결과를 변수에 담음, 쿼리 실행함수
			$cnt = sql_num_rows($res);	 // 쿼리결과 레코드 수를 cnt 변수에 담음

			for ($i=0 ; $i<$cnt ; $i++) {
				$row = sql_fetch_array($res);
				//$row['content'] = str_replace("<p>","",stripslashes($row['content']));  // str_replace(바꿀 현재 문자열, 치환할 문자열, 원래 전체 문자열)
			?>

			<div class="popup_option">
				<p>
					<img src="/data/popup_img/<?=$row['content']?>" alt="<?=$row['content']?>" usemap="#link01"/>
					<!-- 이미지맵 영역 -->
					<map name="link01">
						<area shape="rect" coords="29,494,489,550" href="/bbs/board.php?bo_table=notice&wr_id=998" alt="헬로펀딩 팝업 안내" />
					</map>
				</p>
			</div>

			<?
			}
			?>
		</div>
		<div class="popup_pagination"></div>  
		</div>
		<div class="popup_bottom">
			<form name="popup_form">
				<div class="date_close">
					<input type="checkbox" value="1" name="todayPop" id="popupClose" onclick="closePop();">
					<label for="popupClose">오늘 하루 보지 않기</label>
				</div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">

var openPop = function() {
	if(get_cookie('popupOpen')==false) {
		$('.pop_wrap').css('display', 'block');
	} else {
		$('.pop_wrap').css('display', 'none');
	}
}

openPop();

function closePop() {
	if($('#popupClose').is(':checked')) {
		var exptime = $('#popupClose').val();
		set_cookie('popupOpen', true, exptime, g5_cookie_domain);
		$('.pop_wrap').css('display', 'none');
	}
	else {
		delete_cookie('popupOpen');
	}
}

$('.close').on('click', function(){
	$('.pop_wrap').hide();
});

$('.popup_option').find('br').remove();

</script>