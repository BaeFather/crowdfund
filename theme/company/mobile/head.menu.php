<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<button type="button" id="gnb_open" class="hd_opener">메뉴<span class="sound_only"> 열기</span></button>

<div id="gnb" class="hd_div">
<?
/*
    <div id="hd_sch" style="display:none;">
        <h2>사이트 내 전체검색</h2>
        <form name="fsearchbox" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);" method="get">
        <input type="hidden" name="sfl" value="wr_subject||wr_content">
        <input type="hidden" name="sop" value="and">
        <input type="text" name="stx" id="sch_stx"  required class="required" maxlength="20">
        <input type="submit" value="검색" id="sch_submit">
        </form>
    </div>
		<script>
		function fsearchbox_submit(f)
		{
			if (f.stx.value.length < 2) {
				alert("검색어는 두글자 이상 입력하십시오.");
				f.stx.select();
				f.stx.focus();
				return false;
			}

			// 검색에 많은 부하가 걸리는 경우 이 주석을 제거하세요.
			var cnt = 0;
			for (var i=0; i<f.stx.value.length; i++) {
				if (f.stx.value.charAt(i) == ' ')
					cnt++;
			}

			if (cnt > 1) {
				alert("빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.");
				f.stx.select();
				f.stx.focus();
				return false;
			}

			return true;
		}
		</script>
*/
?>

	<div id="menu_in_wrap">
	  <h3>HELLO FUNDING</h3>
		<ul id="hd_nb">
			<? if ($is_member) { ?>
			<li style="width:100%; color:#fff; padding:10px 0; text-align:left; font-size:150%; text-indent:30px;"><strong><?=$member["mb_name"]?></strong> 님</li>
			<? if ($is_admin) { ?>
			<li><a href="<?=G5_ADMIN_URL?>" id="snb_adm">관리자</a></li>
			<? } ?>
			<li style="margin-right:10px;"><a href="<?=G5_BBS_URL?>/member_confirm.php?url=/mypage/mypage.php">회원정보</a></li>
			<li><a href="<?=G5_URL?>/deposit/deposit.php">투자내역</a></li>
			<? } else { ?>
			<li style="width:100%;color:#fff;"><h3 style="padding:15px 10px 3px;font-size:130%;">헬로펀딩에 로그인 하시면</h3>다양한 혜택을 누리실 수 있습니다.</li>
			<li><a href="<?=G5_BBS_URL?>/register_choice.php" id="snb_login" style="border:1px solid #11a8ab;color:#11a8ab;margin-right:10px;margin-top:17px;background:#fff;padding:10px;">회원가입</a></li>
			<li><a href="<?=G5_BBS_URL?>/login.php" id="snb_login" style="border:1px solid #fff;margin-top:17px;bacakground:#3C95D5;padding:10px;">로그인</a></li>
			<? } ?>
		</ul>
		<ul id="gnb_1dul">
<?php
			$gnb_menus = array();

			$sql = "SELECT * FROM {$g5['menu_table']}
			        WHERE me_mobile_use = '1'
			        AND LENGTH(me_code) = '2'
			        ORDER BY me_order, me_id";
			$result = sql_query($sql, false);
			$gnb_zindex = 999; // gnb_1dli z-index 값 설정용

			for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
			<li class="gnb_1dli" style="z-index:<?=$gnb_zindex--;?>">
<?php
				$submenus = '';

				$sql2 = "SELECT * FROM {$g5['menu_table']}
				         WHERE me_mobile_use = '1'
				         AND LENGTH(me_code) = '4'
				         AND SUBSTRING(me_code, 1, 2) = '{$row['me_code']}'
				         ORDER BY me_order, me_id";
				$result2 = sql_query($sql2);

				for ($k=0; $row2=sql_fetch_array($result2); $k++) {
					if($k == 0) {
					   $submenus .= '<button type="button" class="gnb_op">하위메뉴</button><ul class="gnb_2dul">'.PHP_EOL;
					}

					$submenus .= '<li class="gnb_2dli"><a href="'.$row2['me_link'].'" target="_'.$row2['me_target'].'" class="gnb_2da">'.$row2['me_name'].'</a></li>'.PHP_EOL;
				}

				if($k > 0)
					$submenus .= '</ul>'.PHP_EOL;

				if($submenus)
					$gnb_class = 'gnb_1da gnb_bg';
				else
					$gnb_class = 'gnb_1da';
				?>
				<a href="<?=$row['me_link']?>" target="_<?=$row['me_target']?>" class="<?=$gnb_class?>"><?=$row['me_name']?></a>
				<?php echo $submenus; ?>
			</li>
			<?php
			}

			if ($i == 0) {  ?>
				<li id="gnb_empty">메뉴 준비 중입니다.<? if ($is_admin) { ?> <br><a href="<?=G5_ADMIN_URL?>/menu_list.php">관리자모드 &gt; 환경설정 &gt; 메뉴설정</a>에서 설정하실 수 있습니다.<? } ?></li>
			<?php } ?>
		</ul>
		<? if ($is_member) { ?>
			<a href="<?=G5_BBS_URL?>/logout.php" id="snb_logout" style="color:#fff;position:absolute;bottom:10px;right:10px;border:1px solid;padding: 10px;">로그아웃</a>
		<? } ?>
		<button type="button" id="gnb_close" class="hd_closer"><span class="sound_only">메뉴 </span>닫기</button>
	</div>
</div>

<script>
$(function () {
    $(".hd_opener").on("click", function() {
        var $this = $(this);
        var $hd_layer = $this.next(".hd_div");

        if($hd_layer.is(":visible")) {
            $hd_layer.hide();
            $this.find("span").text("열기");
        } else {
            var $hd_layer2 = $(".hd_div:visible");
            $hd_layer2.prev(".hd_opener").find("span").text("열기");
            $hd_layer2.hide();

            $hd_layer.show();
            $this.find("span").text("닫기");
        }
        $("#wrapper, #footer").css("position","fixed").bind('touchmove', function(e){e.preventDefault()});
			//$("body").css("position","fixed").bind('touchmove', function(e){e.preventDefault()});
			//$("body").css({overflow:'hidden'}).bind('touchmove', function(e){e.preventDefault()});
    });

    $(".hd_closer").on("click", function() {
        var idx = $(".hd_closer").index($(this));
        $(".hd_div:visible").hide();
        $(".hd_opener:eq("+idx+")").find("span").text("열기");
        $("#wrapper, #footer").css("position","relative").unbind('touchmove'); //스크롤 방지 해제
		//$("body").unbind('touchmove');

    });
});

$(function(){
    $(".gnb_op").click(function(){
        $(this).next().slideToggle(300).siblings(".gnb_2dul").slideUp("slow");
    });
	$("#gnb_1dul").children('li').each(function(i){
	    $(this).css('background-image','url(/theme/company/img/navi0'+(i+1)+'.gif)')
	});
});
function open_ch_d(){
    $(".gnb_2dul").slideToggle(300);
}
</script>
<style>
  #menu_in_wrap{width:50%;max-width:400px;position:relative;height:100%;background:#1e2126;padding:0;text-align:left;min-width:230px;}
  #menu_in_wrap > h3{color:#fff;padding:20px;font-size:15px;}
</style>