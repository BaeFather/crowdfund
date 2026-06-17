<?
$ymd = date('y-m-d');
$popup_chk_sql = "SELECT COUNT(no) pop_open_cnt FROM g5_open_popup WHERE check_use = 'Y' AND reg_date<='$ymd' AND end_date>='$ymd'";
$popup_chk_row = sql_fetch($popup_chk_sql);  // Array
$popup_chk_cnt = $popup_chk_row['pop_open_cnt'];

if (!$popup_chk_cnt) {
	return;
}
?>
<?
include_once('./_common.php');
include_once(G5_PATH.'/_head.php');
?>

<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css">

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-rwdImageMaps/1.6/jquery.rwdImageMaps.js"></script>

<script type="text/javascript">

$(function(){
	$('.popup_list').slick({
		infinite : true,              // 무한 반복 옵션     
		slidesToShow : 1,             // 한 화면에 보여질 컨텐츠 개수
		dots : true,				  // 스크롤바 아래 점으로 페이지네이션 여부
		autoplay : true,			  // 자동 스크롤 사용 여부
		arrows: false,				  // 양쪽 화살표 사용 여부
		autoplaySpeed : 5000,		  // 자동 스크롤 시 다음으로 넘어가는데 걸리는 시간 (ms)
		pauseOnHover : true,		  // 슬라이드 이동 시 마우스 오버하면 슬라이더 멈추게 설정
		vertical : false,			  // 세로 방향 슬라이드 옵션
		dotsClass : "slick-dots",     // 아래 나오는 페이지네이션(점) css class 지정
		draggable : true,			  // 드래그 가능 여부 

		responsive: [ 
			{  
				breakpoint: 960 
			},
			{ 
				breakpoint: 768 
			}
		]

	});
});


</script>

<style>
.font_11{font-size:11px;font-family:'돋움'}

<? if(G5_IS_MOBILE) { ?>
.pop_wrap {display: none; position: fixed; left: 0; right: 0; top: 0; bottom: 0; text-align: center; background-color: rgba(0, 0, 0, 0.5); z-index:9999;}
.pop_wrap:before {content: ""; display: inline-block; height: 100%; vertical-align: middle; margin-right: -.5em;}
.pop_layer {display: inline-block; position:relative; vertical-align: middle; width: 90%; z-index: 10;}
.popup_list {width: 100%;}        
.pop_wrap .close{ position:absolute; z-index: 10; top:2%; right:4%; cursor:pointer;}
.pop_wrap .close i {font-size: 7vw;}
.popup_option img {width: 100%; height: 100vw;}
.popup_pagination {z-index: 1;}
.popup_bottom {position: absolute; width: 100%; background-color: #fff; font-size: 2vw; padding: 1% 0; overflow: hidden;}
.date_close {float:left;}
.date_close input, .date_close label, .close_box .close_btn {vertical-align: middle;}
.date_close label {display: inline-block; margin: 3px 0; font-size: 3vw;}
.date_close input {width: 3vw; height: 3vw; margin: 2px 4px 1px 10px; vertical-align: middle;}
.close_box {padding: 4px 13px; float: right;}

.slick-dots {bottom: 7%;} 
.slick-dots li button:before {position: relative; line-height: 0;}
.slick-dots li {margin: 3px 5px;}
.slick-slide > div, .slick-dotted.slick-slider {margin-bottom: -4px;}


<? } else { ?>
.pop_wrap {display: none; position: fixed; left: 0; right: 0; top: 0; bottom: 0; text-align: center; background-color: rgba(0, 0, 0, 0.5); z-index:9999;}
.pop_wrap:before {content: ""; display: inline-block; height: 100%; vertical-align: middle; margin-right: -.25em;}
.pop_layer {display: inline-block; position:relative; vertical-align: middle; z-index: 10;}
.popup_list {width: 520px; height: 570px;}
.pop_wrap .close{ position:absolute; z-index: 10; top:15px; right:20px; cursor:pointer;}
.pop_wrap .close i {font-size: 2em;}
.popup_option img {width: 100%; height: 100%;}
.popup_pagination {z-index: 1;}
.popup_bottom {width: 100%; background-color: #fff; font-size: 14px; padding: 1% 0; overflow: hidden; border-top: 1px solid; border-color: #d1d1d1; margin-top: 1px;}
.date_close {float:left;}
.date_close input, .date_close label, .close_box .close_btn {vertical-align: middle;}
.date_close label {display: inline-block; margin: 3px 0;}
.date_close input {width: 16px; height: 28px; margin: 2px 4px 1px 10px;}
.close_box {padding: 4px 13px; float: right;}

/*.slick-list {height: 500px;}*/
.slick-slide > div, .slick-dotted.slick-slider {margin-bottom: -3px;}
.slick-dots {bottom: 0;} 
.slick-dots li button:before {position: relative; line-height: 0;}
.slick-dots li {margin: 3px 5px;}

<? } ?>


</style>


<?php

include G5_PATH."/popup_layer.php";  // layer 함수

if(!$member[mb_level] || $member[mb_level] == "0") {  // 회원이 아닌 경우[손님]

	$sql = "select * from $g5[site_popup_table] where DATE_FORMAT(now(),'%Y-%m-%d') >= reg_date and check_use='Y' and (level=0 or level=1)";
	$result = sql_query($sql);
	$total_num = sql_num_rows($result);

	for($i = 0; $row = sql_fetch_array($result); $i++) {

		$no = $row[no];
		$type = $row[type];

		if($type == "팝업창") {
			echo "<script language=\"javascript\">";
			
			if($row[menubar] == "Y") { $menubar = "yes"; } else { $menubar = "no"; }
			if($row[toolbar] == "Y") { $toolbar = "yes"; } else { $toolba = "no"; }
			if($row[scrollbars] == "Y") { $scrollbars = "yes"; } else { $scrollbars = "no"; }
			if($row[resizable] == "Y") { $resizable = "yes"; } else { $resizable = "no"; }
			if($row[status] == "Y") { $status = "yes"; } else { $status = "no"; }

			if($center != "Y") {
				$popup_left = $row[popup_left];
				$popup_top = $row[popup_top];
				$option = "menubar=$menubar, scrollbars=$scrollbars, status=$status, toolbar=$toolbar, resizable=$resizable, width=$width, height=$height, left=$popup_left, top=$popup_top";
			} else { ?>
				var left_pos_<?=$i?>, top_pos_<?=$i?>;
				left_pos_<?=$i?> = (screen.width-<?=$width?>)/2;
				top_pos_<?=$i?> = (screen.height-<?=$height?>)/2;
			<?
				$option = "menubar=$menubar, scrollbars=$scrollbars, status=$status, toolbar=$toolbar, resizable=$resizable, width=$width, height=$height, left='+left_pos_{$i}+', top='+top_pos_{$i}+'";
			}  // 중앙 정렬인 경우의 끝

			echo "window.open('".G5_URL."/popup_view.php?no=$no','new_win$i','$option');";
			echo "</script>";
			
		} else if($type == '레이어') { 
			view_layer($no);
		}

	}

} else {  // 회원인 경우

	$sql = "select * from {$g5[site_popup_table]} where DATE_FORMAT(now(),'%Y-%m-%d') >= reg_date and check_use='Y' and (level=0 or level={$member[mb_level]})";
	$result = sql_query($sql);

	for($i = 0; $row = sql_fetch_array($result); $i++) {

		$no = $row[no];
		$type = $row[type];

		if($type == "팝업창"){
			echo "<script language=\"javascript\">";
			
			if($row[menubar] == "Y") { $menubar = "yes"; } else { $menubar = "no"; }
			if($row[toolbar] == "Y") { $toolbar = "yes"; } else { $toolbar = "no"; }
			if($row[scrollbars] == "Y") { $scrollbars = "yes"; } else { $scrollbars = "no"; }
			if($row[resizable] == "Y") { $resizable = "yes"; } else { $resizable = "no"; }
			if($row[status] == "Y") { $status = "yes"; } else { $status = "no"; }

			if($center != "Y"){
				$popup_left = $row[popup_left];
				$popup_top = $row[popup_top];

				$option = "menubar=$menubar, scrollbars=$scrollbars, status=$status, toolbar=$toolbar, resizable=$resizable, width=$width, height=$height, left=$popup_left, top=$popup_top";
			} else { ?>
				var left_pos_<?=$i?>, top_pos_<?=$i?>;
				left_pos_<?=$i?> = (screen.width-<?=$width?>)/2;
				top_pos_<?=$i?> = (screen.height-<?=$height?>)/2;
			<?
				$option = "menubar=$menubar, scrollbars=$scrollbars, status=$status, toolbar=$toolbar, resizable=$resizable, width=$width, height=$height, left='+left_pos_{$i}+', top='+top_pos_{$i}+'";
			}

			echo "window.open('".G5_URL."/popup_view.php?no=$no','new_win$i','$option');"; 
			echo "</script>";

		} else if($type == '레이어') {
			view_layer($no);
		}


	}  // for문 끝

}  // 회원일 경우 끝

?>


<script language="javascript">
	
	var x = 0
	var y = 0
	drag = 0
	move = 0

	window.document.onmousemove = mouseMove
	window.document.onmousedown = mouseDown
	window.document.onmouseup = mouseUp
	window.document.ondragstart = mouseStop

	function mouseUp() {
		move = 0
	}

	function mouseDown() {
		if (drag) {
			clickleft = window.event.x - parseInt(dragObj.style.left)
			clicktop = window.event.y - parseInt(dragObj.style.top)
			dragObj.style.zIndex += 1
			move = 1
		}
	}

	function mouseMove() {
		if (move) {
			dragObj.style.left = window.event.x - clickleft
			dragObj.style.top = window.event.y - clicktop
		}
	}

	function mouseStop() {
		window.event.returnValue = false
	}

	function Show(divid)
	{
		divid.filters.blendTrans.apply();
		divid.style.visibility = "visible";
		divid.filters.blendTrans.play();
	}

	function Hide(divid) {
		divid.filters.blendTrans.apply();
		divid.style.visibility = "hidden";
		divid.filters.blendTrans.play();
	}
	
	// 이미지맵 반응형
	$(function(){
		$('img[usemap]').rwdImageMaps();
	});

</script>

