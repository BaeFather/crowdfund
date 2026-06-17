<?
include_once('./_common.php');



if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}



?>


<style>
	
	@import url(//fonts.googleapis.com/earlyaccess/notosanskr.css);

	*{list-style:none; padding:0; margin:0 auto; font-family: "Noto Sans KR", sans-serif !important;}
 /*추천평팝업*/


/* The Modal (background) */
	.modal {
		/*display: none;  /*Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 1; /* Sit on top */
		left: 0;
		top: 0;
		width: 100%; /* Full width */
		height: 100%; /* Full height */
		overflow: hidden; /* Enable scroll if needed */	
	}

	.pop_cont {margin:100px auto 0; width:550px; box-sizing: border-box; background-color:#fff; padding:20px 30px; border:1px solid #ddd; text-align: left; }		
	}	
	.p_25 {padding-bottom:25px;}
	.pop_cont .tt {font-size:26px; text-align: center; color:#333; font-weight: 500; margin:50px 0 40px;}
	.pop_cont .tt span {color:#1275d2; font-weight: bold;}			

	.listA {width:100%; float:left;  box-sizing: border-box; }
	.name {width:40%; color:#333; font-size:18px;  display: inline-block;}
	.sx {width:20%; color:#333; font-size:18px;  display: inline-block;}
	.num {width:30%; color:#333; font-size:18px;  display: inline-block; float:right; text-align: right; padding-right:5px;}
	.mark {width:4px; height: 4px; background-color: #1275d2; display: inline-block; margin: 0 5px 5px 0;}

	.listB {clear: both;  width:100%;  box-sizing: border-box; padding: 30px 0;}
	.listB li {float:left; font-size:18px; color:#333;}
	.listB .th {width:20%; padding-top: 5px;} 
	.listB .td {width:80%; box-sizing: border-box;}
	.pop_cont .nt {font-size:14px; color:#1275d2; font-weight:400; margin-top:15px; letter-spacing:-0.5px; }

	
	
	.listC {clear: both;  width:100%;  box-sizing: border-box; padding: 30px 0;}
	.listC li {float:left; font-size:18px; color:#333;}
	.listC .th {width:100%; padding-bottom: 5px;} 
	.listC .td {width:100%; box-sizing: border-box;}
	

	.ck ul li { clear: both; margin-top:10px; font-size:15px; color:#333;}

	.btn1 {clear: both; float:right; cursor: pointer;}
	.btn2 { clear: both; text-align:center; margin:30px 0 30px;}
	.btn2 a {font-size:18px; color:#fff; padding:12px 60px; background-color: #1275d2;  display: inline-block; box-sizing: border-box;}	


.filebox input[type="file"] {
	position: absolute; 
	width: 1px; 
	height: 1px; 
	padding: 0; 
	margin: -1px; 
	overflow: hidden; 
	border: 0; 
} 

.filebox label { 
	display: inline-block; 
	padding: 7px 0; 
	float:right;
	text-align: center;
	color: #fff; 
	width:25%;
	font-size:15px;
	line-height: normal; 
	vertical-align: middle;
	background-color: #1275d2;;
	cursor: pointer; 
	border-radius: 3px; 
} 

/* named upload */ 
.filebox .upload-name { 
	width:70%;
	display: inline-block; 
	padding: 7px 10px;  /* label의 패딩값과 일치 */ 
 	font-size:15px;
	color:#777;
	font-weight: 300;
	line-height: normal; 
	vertical-align: middle; 
	background-color: #f5f5f5; 
	border: 1px solid #ebebeb; 
	border-bottom-color: #e2e2e2; 
	border-radius: 3px; 
	-webkit-appearance: none; 
	/* 네이티브 외형 감추기 */ 
	-moz-appearance: none; appearance: none; 
}




select{
	-moz-appearance:none;
	-webkit-appearance:none;
	border: 1px solid #ddd;
    height: 40px;
    font-size: 15px;
	font-weight: 300;
    color: #777;
	width:100%;
    box-sizing: border-box;
    padding: 0 1em;
	background-image: url("img/arrow.jpg"); background-position: center right 1em; background-repeat: no-repeat;
	border-radius: 3px;
	}
	
	
textarea {
	width: 100%;
    height: 120px;
    margin: 0 0 5px 0;
	font-size: 15px;
	color:#777;
    padding: 5px 5px;
    border-radius: 3px;
    border: 1px solid #dcdcdc;
	
}
	
input[type=text] {
    -webkit-appearance: none;

    border: 1px solid #ddd;
    height: 40px;
    font-size: 15px;
    padding: 0 1em;
	width:230px;
    box-sizing: border-box;
	border-radius: 3px;
    color: #222;
	}

	input[type="checkbox"] {display: none;}


	input[type="checkbox"]:checked + span:before {
    border-color: #00a0ea;
			
	}	


	input[type="checkbox"] + span {
    position: relative;
    display: inline-block;
    padding-left: 35px;
    line-height: 26px;
	color: #383838;
	}


	input[type="checkbox"] + span:before {
    content: "";
    position: absolute;
    width: 24px;
    height: 24px;
    top: 0;
    left: 0;
    border: 1px solid #ccc;
    box-sizing: border-box;
    transition: all .3s;
    background-color: #fff;
	}


	input[type="checkbox"] + span:after {
    content: "";
    transition: all .2s;
    position: absolute;
    width: 2px;
    height: 2px;
    top: 12px;
    left: 12px;
	}


	input[type="checkbox"]:checked + span:after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 4px;
    left: 5px;
    background-image: url("img/check.png");
	background-position: center center;
	background-repeat: no-repeat;
	opacity: 1;	
	}	







@media all and (max-width: 900px){
	

	.pop_cont {margin:0 auto; width:100%; height: 100%; box-sizing: border-box; background-color:#fff; padding:20px 30px; border:1px solid #ddd; text-align: left; z-index: 1000; scroll-behavior: auto; }		
	}	
	.p_25 {padding-bottom:25px;}
	.pop_cont .tt {font-size:26px; text-align: center; color:#333; font-weight: 500; margin:50px 0 40px;}
	.pop_cont .tt span {color:#1275d2; font-weight: bold;}			

	.listA {width:100%; float:left;  box-sizing: border-box; }
	.name {width:40%; color:#333; font-size:18px;  display: inline-block;}
	.sx {width:20%; color:#333; font-size:18px;  display: inline-block;}
	.num {width:30%; color:#333; font-size:18px;  display: inline-block; float:right; text-align: right; padding-right:5px;}
	.mark {width:4px; height: 4px; background-color: #1275d2; display: inline-block; margin: 0 5px 5px 0;}

	.listB {clear: both;  width:100%;  box-sizing: border-box; padding: 30px 0;}
	.listB li {float:left; font-size:18px; color:#333;}
	.listB .th {width:20%; padding-top: 5px;} 
	.listB .td {width:80%; box-sizing: border-box;}
	.pop_cont .nt {font-size:14px; color:#1275d2; font-weight:400; margin-top:15px; letter-spacing:-0.5px; }

	
	
	.listC {clear: both;  width:100%;  box-sizing: border-box; padding: 30px 0;}
	.listC li {float:left; font-size:18px; color:#333;}
	.listC .th {width:100%; padding-bottom: 5px;} 
	.listC .td {width:100%; box-sizing: border-box;}
	

	.ck ul li { clear: both; margin-top:10px; font-size:15px; color:#333;}

	.btn1 {clear: both; float:right; cursor: pointer;}
	.btn2 { clear: both; text-align:center; margin:30px 0 30px;}
	.btn2 a {font-size:18px; color:#fff; padding:12px 60px; background-color: #1275d2;  display: inline-block; box-sizing: border-box;}	

	
	
	
	
	
	
	
}




</style>



<div id="myModal" class="modal recom_pop">
	<form name="write_form" id="form">
		<div class="pop_cont">
			<div class="btn1 btnServiceClose"><img src="img/x.png" alt=""></div>
			<p class="tt">헬로펀딩 <span>추천평 작성</span></p>
			<div class="req_list">
				<ul>
					<li class="listA">
						<span class="name"><span class="mark"></span>이름 : 김미선</span>
						<span class="sx"><span class="mark"></span>성별 : 여 </span>	
						<span class="num"><span class="mark"></span>투자참여 : 81회</span>
					</li>
					<li>
						<ul class="listB">
							<li class="th"><span class="mark"></span>선호상품</li>
							<li class="td">
								<select id="sns" name="sns" class="my_sns">
									<option value selected="selected" disabled class="selected">선호상품을 선택해주세요.</option>
									<option value="부동산">부동산</option>
									<option value="주택담보">주택담보</option>
									<option value="동산">동산</option>
									<option value="헬로페이-면세점">헬로페이-면세점</option>
									<option value="헬로페이-소상공인">헬로페이-소상공인</option>
								</select>
							</li>
						</ul>
					</li>
					<li>
						<ul class="listB">
							<li class="th"><span class="mark"></span>헬로포토</li>
							<li class="td filebox">
								<input class="upload-name" value="사진을 첨부해주세요" disabled="disabled">
								<label for="ex_filename">파일첨부</label>
								<input type="file" id="ex_filename" class="upload-hidden">
							</li>
						</ul>
						<p class="nt">※본인사진만 인정되며, 타인 사진 도용시 발생하는 책임은 본인에게 있습니다.</p>
					</li>
					
					
					<li>
						<ul class="listC">
							<li class="th"><span class="mark"></span>추천평 작성</li>
							<li class="td"><textarea class="in_add" type="textarea" name="address" value="" placeholder="추천평은 최소 50자 이상, 최대 500자 이하까지 작성 가능합니다."></textarea></li>
						</ul>
					</li>	
					<li class="ck p_25">
						<ul class="check">
							<li class="re_label_check"><label><input type="checkbox" name="check01" id="check01" value=""><span>개인정보 활용 및 마케팅 활용에 동의합니다.</span></label></li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="btn2"><a>작성완료</a></div>
		</div>
	</form>
</div>	





<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>
