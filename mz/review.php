<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>헬로펀딩 시안</title>

	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 

	
	<style>
	*{margin:0 auto; padding:0; border: 0; text-align: center;}	
	.allWrap{width: 1152px;margin:0 auto;}
        .tabBox{margin:0 0 20px}
        .tab-link{width: 15%;display: inline-block; padding:8px 1px; text-align: center; background-color:#f9f9f9; border-radius: 24px; color:#555; cursor: pointer; margin: 0 5px 50px; font-size:18px;
        }
        .tab-link.current{
            background-color: #33a5ed;
			color:#fff;
        }
        .tab-content{
            display: none;
        }
        .tab-content.current{
            display: block;
            width: 100%;
            text-align: center;
        }


</style>
	
</head>

<body>
	<div id="test_contents">
		<div class="header"><img src="img/gnb.jpg" usemap="#Map">
          <map name="Map">
            <area shape="rect" coords="541,24,618,70" href="index.php">
            <area shape="rect" coords="646,22,724,70" href="review.php">
            <area shape="rect" coords="753,23,856,73" href="magazine.php">
          </map>
		</div>
		<div class="title"><img src="img/rv_top.jpg"></div>
		<div class="allWrap">     
<div class="tabBox">
            <p class="tab-link current" data-tab="tab-1">인터뷰</p>
            <p class="tab-link"  data-tab="tab-2">추천평</p>
			<p class="tab-link"  data-tab="tab-3">SNS리뷰</p>
        </div>
<div  id="tab-1" class="tab-content current"><img src="img/rv_01.jpg"></div>
<div  id="tab-2" class="tab-content"><img src="img/rv_02.jpg"></div>
<div  id="tab-3" class="tab-content"><img src="img/rv_03.jpg"></div>			
</div>


		
		
		
		<div class="footer"><img src="img/footer.jpg"></div>
	</div>
	
		<script>
	
	  $('.tab-link').click(function () {
        var tab_id = $(this).attr('data-tab');
 
        $('.tab-link').removeClass('current');
        $('.tab-content').removeClass('current');
 
        $(this).addClass('current');
        $("#" + tab_id).addClass('current');
 
    });

	</script>
</body>
</html>
