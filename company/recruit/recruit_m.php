<?

add_stylesheet('<link href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css" rel="stylesheet" type="text/css">', 0);
add_stylesheet('<link href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css" rel="stylesheet" type="text/css">', 0);
add_stylesheet('<link href="recruit_m.css?ver=2" rel="stylesheet">', 0);

?>

<div id="content">
	<div id="m_recruit">
		<div class="img1"><img src="img/m_recruit_01.jpg" alt=""></div>
		<div class="button"><a href="#list">채용중 공고 확인하기 <span class="num"><?=number_format($CLIST["total"])?></span></a></div>
		<div class="text1">
			<ul>
				<li class="title"><img src="img/m_title01.jpg"></li>
				<li class="word t01"><p>열정</p>헬로펀딩은 하고자 하는<br>열정을 갖고, 고객만족을 위해<br>끊임없이 노력합니다.</li>
				<li class="word t02"><p>집념</p>헬로펀딩은 목표를 이루고자<br>하는 강한 집념으로 모든 구성원이<br>하나되어 반드시 해냅니다.</li>
				<li class="word t03"><p>도전</p>헬로펀딩은 도전을 즐깁니다.<br>진취적이고 긍적적인 마인드로<br>혁신과 변화를 추구합니다.</li>
			</ul>
		</div>
		<div class="img2"><img src="img/m_recruit_02.jpg" alt=""></div>
		<div class="text1">
			<ul>
				<li class="title"><img src="img/m_title02.jpg"></li>
				<li class="word t01"><p>방향과소통</p>회사가 지금 무슨 일을<br>하는지 알고 있고,<br>내가 무슨 일을 하는지<br>회사가 알고 있습니다.</li>
				<li class="word t02"><p>자율과실행</p>어떤 일이든<br>마감시간을 정하고<br>업무하고 있습니다.</li>
				<li class="word t03"><p>직원의존중</p>정시 퇴근을 존중하고<br>퇴근한 직원들에게 업무로<br>연락하지 않습니다.</li>
			</ul>
		</div>
		<div class="img3"><img src="img/m_recruit_03.jpg" alt=""></div>
		<div class="title"><img src="img/m_title03.jpg"></div>
		<div class="text2">
			<ul>
				<li class="benefit b01"><p><span>1</span>불금을 즐기세요</p>금요일 5시 퇴근으로<br>2시간 조기 퇴근합니다.</li>
				<li class="benefit b02"><p><span>2</span>생일엔 빨리가요</p>생일에는 반차와 함께<br>상품권을 드립니다.</li>
				<li class="benefit b03"><p><span>3</span>마음의 양식도 함께</p>자기계발을 위하여 <br>매월 2권의 도서<br>구입비를 지원합니다.</li>
				<li class="benefit b04"><p><span>4</span>눈치보지 마세요</p>구성원의 리프레시를<br>위하여 자유로운<br>연차제도를 운영합니다.</li>
				<li class="benefit b05"><p><span>5</span>노고에 감사합니다</p>근속연수 3년 이상의<br>직원에게 축하금과<br>특별휴가를 제공합니다.</li>
				<li class="benefit b06"><p><span>6</span>전국 8도 어디든</p>헬로펀딩과 제휴된 리조트<br>시설을 할인혜택으로<br>이용가능합니다. </li>
				<li class="benefit b07"><p><span>7</span>살쪄도 좋아요</p>편의점 수준의<br>카페테리아에서<br>맘껏 드실 수 있습니다.</li>
			</ul>
		</div>
		<div class="img4"><img src="img/m_recruit_04.jpg" alt=""></div>
		<div id="list">진행중 공고</div>
			<select name="sgbn" id="sgbn" onchange="new_list('<?=$CLIST['list'][$i]['ca_name']?>');">
				<option selected="selected">전체보기</option>
				<? for ($i=0 ; $i<count($CLIST['list']) ; $i++) { ?>
				<option><?=$CLIST['list'][$i]['ca_name']?></option>
				<? } ?>
			</select>
		</div>

		<div class="board">
			<table id="rlist_tbl">
				<? for ($i=0 ; $i<count($LIST) ; $i++) { ?>
				<tr>
					<td>
						<div class="subject" style="width:100%;">
							<a href="/company/recruit/recruit_view.php?wr_id=<?=$LIST[$i]['wr_id']?>"><?=$LIST[$i]['wr_subject']?></a>
						</div>
						<div>
							<span style="color:#222; font-size:14px;"><?=$LIST[$i]['ca']?></span>
							<span style="font-size:14px;margin:0 5px;">|</span>
							<span style="font-size:14px;"><?=$LIST[$i]['gigan']?></span>
						</div>
					</td>
				</tr>
				<?
					/*
				<tr>
					<td class="type" style="width:10%;"><?=$LIST[$i]['ca']?></td>
					<td class="subject">
						<a href="/company/recruit/recruit_view.php?wr_id=<?=$LIST[$i]['wr_id']?>"><?=$LIST[$i]['wr_subject']?></a>
						<br/>
						<span style="font-size:16px;color:#777;"><?=$LIST[$i]['gigan']?></span>
					</td>
					<!--td class="date">
						<?=$LIST[$i]['gigan']?>
					</td-->
				</tr>
					*/
				}
				?>
			</table>
		</div>
	</div>
</div>

<script>
function new_list() {

	var gbn = $("#sgbn").val();

	table_reset();

	for (var i=0 ; i<<?=$cnt?> ; i++) {
		if (list[i]['ca_name']==gbn || gbn=='전체보기') {

			d1 = "<tr>"+
					"<td class='type'>"+list[i]['ca']+"</td>"+
					"<td class='subject'><a href='/company/recruit/recruit_view.php?wr_id="+ list[i]["wr_id"] +"'>"+list[i]['wr_subject']+"</td>"+
					"<td class='date'>"+list[i]['gigan']+"</td>"+
				"</tr>";

			d = "<tr>"+
					"<td>"+
						"<div class='subject' style='width:100%;'>"+
							"<a href='/company/recruit/recruit_view.php?wr_id="+list[i]["wr_id"] +"'>"+list[i]['wr_subject']+"</a>"+
						"</div>"+
						"<div>"+
							"<span style='color:#1A9DEB; font-size:14px;'>"+list[i]['ca']+"</span>"+
							"<span style='font-size:14px;margin:0 5px;'>|</span>"+
							"<span style='font-size:14px;'>"+list[i]['gigan']+"</span>"+
						"</div>"+
					"</td>"+
				  "</tr>";
			$('#rlist_tbl').append(d);
		}
	}
}
function table_reset() {
	var trCnt = $('#rlist_tbl tr').length;
	if (trCnt) $('#rlist_tbl').empty();
}
</script>

<?

if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('../_tail.php');
}

?>