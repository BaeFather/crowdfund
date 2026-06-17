<link href="review.css?ver=<?=time();?>" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="review.js"></script>
<script type="text/javascript">
/*탭메뉴*/
$(function () {
	tab('#tab',0);
});
if( window.history.replaceState ) {
	window.history.replaceState( null, null, window.location.href );
}

function tab(e, num){
    var num = num || 0;
    var menu = $(e).children();
    var con = $(e+'_con').children();
    var select = $(menu).eq(num);
    var i = num;

    select.addClass('on');
    con.eq(num).show();

    menu.click(function(){
        if(select!==null){
            select.removeClass("on");
        }

        select = $(this);
        i = $(this).index();

        select.addClass('on');

		if(i == 0)
		{
			$("#interview_area").attr('class','interview cont');
			section = 1;
			page =1;
			$("#section").val(section);
			check_review_load(section);
		} else if(i == 1) {
			$("#interview_area").attr('class','sns cont');
			$("#viewbtnarea").html("SNS리뷰")
			section = 2;
			page =1;
			check_review_load(section);
			$("#section").val(section);
		} else if(i == 2) {
			$("#interview_area").attr('class','recommend cont');
			$("#viewbtnarea").html("추천평")
			section = 3;
			page =1;
			check_review_load(section);
			$("#section").val(section);
		}

    });
}

function check_view(obj, pkd, event)
{
	$("#SE").val(obj);
	$("#viewy").val(event.pageY);
	$("#reviewfm").attr("method","POST");
	$("#reviewfm").submit();
}

</script>

<div id="content">


	<div>
		<h2 class="title">헬로펀딩 <span class="sky">투자후기</span></h2>
		<p class="top_text">헬로펀딩을 믿고 사랑해주시는 회원님! <br class="br">소중한 리뷰에 깊은 감사드립니다.</p>

		<ul class="tab" id="tab">
			<li class="mg-r20">인터뷰</li>
			<li class="mg-r20">SNS리뷰</li>
			<li>추천평</li>
		</ul>
	</div>

	<div class="tab_con" id="tab_con">
		<div class="interview cont" id="interview_area">
			<ul id="interview_list">

			</ul>
			<p class="clear_both"></p>
			<p class="bt"><a href="#none" OnClick="check_review_list();">+ <span id="viewbtnarea">인터뷰</span> 더보기</a></p>
		</div>
	</div>

	<form name="reviewfm" id="reviewfm">
		<input type="hidden" name="RD" value="2" />
		<input type="hidden" name="page" id="page" value="" />
		<input type="hidden" name="section" id="section"  value="" />
		<input type="hidden" name="SE" id="SE" value="" />
		<input type="hidden" name="viewy" id="viewy" value="" />
	</form>

<script type="text/javascript">
	var pkd = "<?php ECHO $pkd;?>";
	var viewyn = "<?php ECHO $viewy;?>";

	if(pkd == 1)
	{
		page = "<?php ECHO $page;?>";
		section = "<?php ECHO $section;?>";

		check_review_load(section);

		$('html').animate({scrollTop : viewyn}, 400);

	} else {
		check_review_load(section);
	}

</script>

<div id="myModal" class="modal">
	<div class="box" id="box">

	</div>
</div>