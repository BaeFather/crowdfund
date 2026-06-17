<?php
/**
 * 헬로핀테크 회사소개
 */

header('HTTP/1.1 301 Moved Permanently');
header('Location: /company/introduce.php');
exit;


include_once('./_common.php');

$g5['title'] = '회사소개';
$g5['top_bn'] = "";
$g5['top_bn_alt'] = "당신의 설레는 내일, 헬로펀딩";

if ($co['co_include_head']){
    @include_once($co['co_include_head']);
} else {
    include_once('./_head.php');
}

// 투자후기 조회
$best_review = array();
$query = sql_query("SELECT * FROM `epilogue_list` WHERE `display_yn` = 'Y' AND `best_review` = 'Y' ORDER BY `sort` ASC LIMIT 3");
while($row = sql_fetch_array($query))
{
    if (!empty($row["thumbnail"]) && file_exists(G5_IMG_PATH."/review/".$row["thumbnail"])) {
        $row["thumb_url"] = G5_IMG_URL."/review/".$row["thumbnail"];
    }else{
        $row["thumb_url"] = G5_IMAGES_URL.'/review/sumnail_img01.jpg';
    }
    $row["contents"] = get_text(strip_tags(html_clean($row["contents"])));
    $row["contents"] = utf8_strcut(trim($row["contents"]), 153);
    array_push($best_review, $row);
}


// 헬로펀딩 투자내역 조회
$row = sql_fetch("SELECT `average_return`, `total_invest`, `bankruptcy`, `overdue_perc`, `standard_date` FROM `cf_invest` WHERE `display` = 'Y'");
$average_return = ($row["average_return"]) ? $row["average_return"] : 0;
$total_invest = ($row["total_invest"]) ? price_cutting($row["total_invest"]) : 0;
$bankruptcy = ($row["bankruptcy"] == '0.00') ? 0 : $row["bankruptcy"]; // 부실율
$overdue_perc = ($row["overdue_perc"] == '0.00') ? 0 : $row["overdue_perc"]; // 연체율
$standard_date = ($row["standard_date"]) ? date("Y.m.d", strtotime($row['standard_date'])) : date("Y.m.d");
unset($row);
?>

<!-- 본문내용 START -->
<div id="content">
	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">


<?php if(G5_IS_MOBILE) { ?>
	<img src="../images/comp_m01_1.jpg" width="100%">
	<img src="../images/comp_m01_2.jpg" width="100%" id="d2">
	<img src="../images/comp_m01_3_1.jpg" width="100%">
	<!--헬로펀딩 투자자 데이터-->
	<div id="invest_data">
		<div class="data_tit">
			헬로펀딩 투자자 데이터 <span>(<?php echo $standard_date;?> 기준)</span>
		</div>
		<div class="tbg">
			<table colspan="3" rowspan="2">
				<tr>
					<td class="data_info">
						평균 수익률(연)
						<span><?php echo $average_return;?>%</span>
					</td>
					<td class="r_line"><td>
					<td class="data_info">
						누적 대출액
						<span><?php echo $total_invest;?>원</span>
					</td>
					<td class="r_line"><td>
					<td class="data_info1">
						평균 투자기간
						<span>5개월</span>
					</td>
				</tr>

				<tr>
					<td class="data_info">
						회원 평균 누적 투자액
						<span>2,199만원</span>
					</td>
					<td class="r_line"><td>
					<td class="data_info">
						연체율
						<span><?php echo $bankruptcy;?>%</span>
					</td>
					<td class="r_line"><td>
					<td class="data_info1">
						부실율
						<span><?php echo $overdue_perc;?>%</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="data_bot">
			※ 헬로펀딩은 투자심의위원회의 심의를 통과한 담보 상품만을 출시하여 서비스 오픈 후
			현재까지 연체율, 부실율 제로를 기록하고 있습니다.
		</div>
	</div>
	<img src="../images/comp_m01_3_2.jpg" width="100%">
	<img src="../images/comp_m01_3_3.jpg" width="100%">
	<img src="../images/comp_m01_3_4.jpg" width="100%">
	<img src="../images/comp_m01_3_4_1.jpg" width="100%">
	<img src="../images/comp_m01_3_5_1.jpg" width="100%">
	<!--위원약력 2017.09.20-->
	<!--남기중 위원장-->
			<div id="member_m01"></div>
			<div id="member_m_info1" class="memb_info01">
				<img src="../images/company/member01_m.png" width="100%">
			</div>
			<script type="text/javascript">
				$('#member_m01').on('click',function(event){
				event.stopPropagation();
				$('#member_m_info1').toggle();
				});

				$(document).on('click',function(){
					$('#member_m_info1').hide();
				});
			</script>

	<!--최수석 위원-->
			<div id="member_m02" ></div>
			<div id="member_m_info2" >
				<img src="../images/company/member02_m.png" width="100%">
			</div>
			<script type="text/javascript">
				$('#member_m02').on('click',function(event){
				event.stopPropagation();
				$('#member_m_info2').toggle();
				});

				$(document).on('click',function(){
					$('#member_m_info2').hide();
				});
			</script>
	<!--최윤현 위원-->
			<div id="member_m03" ></div>
			<div id="member_m_info3" >
				<img src="../images/company/member03_m.png" width="100%">
			</div>
			<script type="text/javascript">
				$('#member_m03').on('click',function(event){
				event.stopPropagation();
				$('#member_m_info3').toggle();
				});

				$(document).on('click',function(){
					$('#member_m_info3').hide();
				});
			</script>
	<!--채영민 위원-->
			<div id="member_m04" ></div>
			<div id="member_m_info4" >
				<img src="../images/company/member04_m.png" width="100%">
			</div>
			<script type="text/javascript">
				$('#member_m04').on('click',function(event){
				event.stopPropagation();
				$('#member_m_info4').toggle();
				});

				$(document).on('click',function(){
					$('#member_m_info4').hide();
				});
			</script>
	<!--김인 위원-->
			<div id="member_m05" ></div>
			<div id="member_m_info5" >
				<img src="../images/company/member05_m.png" width="100%">
			</div>
			<script type="text/javascript">
				$('#member_m05').on('click',function(event){
				event.stopPropagation();
				$('#member_m_info5').toggle();
				});

				$(document).on('click',function(){
					$('#member_m_info5').hide();
				});
			</script>
	<!--이정우 위원-->
			<div id="member_m06" ></div>
			<div id="member_m_info6" >
				<img src="../images/company/member06_m.png" width="100%">
			</div>
			<script type="text/javascript">
				$('#member_m06').on('click',function(event){
				event.stopPropagation();
				$('#member_m_info6').toggle();
				});

				$(document).on('click',function(){
					$('#member_m_info6').hide();
				});
			</script>
	<!--김숙현 위원-->
			<div id="member_m07" ></div>
			<div id="member_m_info7" >
				<img src="../images/company/member07_m.png" width="100%">
			</div>
			<script type="text/javascript">
				$('#member_m07').on('click',function(event){
				event.stopPropagation();
				$('#member_m_info7').toggle();
				});

				$(document).on('click',function(){
					$('#member_m_info7').hide();
				});
			</script>
	<img src="../images/comp_m01_3_5_2.jpg" width="100%"/>
	<img src="../images/comp_m01_3_6.jpg" width="100%"/>

    <?php if(false){ ?>
	    <!--<a href="http://blog.naver.com/pfar1863/221045770137"><img src="../images/comp_m07.jpg" width="100%"/></a>
            <a href="https://blog.naver.com/PostView.nhn?blogId=yanne240&logNo=221036922866&proxyReferer=http%3A%2F%2Fnaver.me%2F5q3gx8Zc"><img src="../images/comp_m04.jpg" width="100%"/></a>
            <a href="http://blog.naver.com/facemake/221035432513"><img src="../images/comp_m02.jpg" width="100%"/></a>
            <a href="/etc/epilogue_blog.php"><img src="../images/comp_m05.jpg" width="100%"></a><br/><br/>
            <a href="/investment/invest_list.php"><img src="../images/comp_m06.jpg" width="100%"/></a>-->
            <!--<div id="investment_link" style="bottom:12px; z-index:2;text-align:center;" >
	            <a href="/investment/invest_list.php" ><img src="../images/company_m_button.jpg" width="80%"/></a>
            </div-->
    <?php } ?>

    <span id="reviews_area_title">투자후기</span>
    <div id="reviews_area" class="bg_non">
        <div class="reviews_cont">
            <?php if (count($best_review) > 0){ ?>
            <ul>
                <?php foreach($best_review as $best) { ?>
                    <li>
                        <div class="review">
                            <div class="review_box">
                                <span class="subject"><?php echo $best["subject"];?></span>
                                <span class="mem_name"><?php echo $best["mem_name"].' (ID: '.$best["mem_id"].')';?></span>
                                <div class="thumbnail">
                                    <img src="<?php echo $best["thumb_url"];?>" width="100%" height="203" alt="<?php echo $best['thumbnail_origin'];?>"/>
                                </div>
                                <span class="contents"><?php echo $best["contents"];?></span>
                                <div class="link">
                                    <a href="<?php echo $best["target_link"];?>" target="_blank">자세히 보기</a>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
		<center style="margin:10px 0;"><a href="/bbs/epilogue.php" class="btn_white_big">투자후기더보기</a></center>
        <?php } ?>

    </div>
	<div style="padding-top:10px;text-align: center;"><a href="/investment/invest_list.php" class="btn_blue_big">투자상품보기</a></div>
<? }else {?>
	<!--img src="../images/company.jpg" width="100%" usemap="#Map1"-->

	<img src="../images/company01.jpg" width="100%"/>
	<img src="../images/company01_1.jpg" width="100%" id="d2"/>
	<img src="../images/company03.jpg" width="100%"/>

	<!--헬로펀딩 투자자 데이터-->
	<div id="invest_data">
		<div class="data_tit">
			헬로펀딩 투자자 데이터 <span>(<?php echo $standard_date;?> 기준)</span>
		</div>
		<div class="tbg">
			<table colspan="3" rowspan="2">
				<tr>
					<td class="data_info">
						평균 수익률(연)
						<span><?php echo $average_return;?>%</span>
					</td>
					<td class="r_line"><td>
					<td class="data_info">
						누적 대출액
						<span><?php echo $total_invest;?></span>
					</td>
					<td class="r_line"><td>
					<td class="data_info">
						평균 투자기간
						<span>5개월</span>
					</td>
				</tr>

				<tr>
					<td class="data_info">
						회원 평균 누적 투자액
						<span>2,199만원</span>
					</td>
					<td class="r_line"><td>
					<td class="data_info">
						연체율
						<span><?php echo $overdue_perc;?>%</span>
					</td>
					<td class="r_line"><td>
					<td class="data_info">
						부실율
						<span><?php echo $bankruptcy;?>%</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="data_bot">
			※ 헬로펀딩은 투자심의위원회의 심의를 통과한 담보 상품만을 출시하여 서비스 오픈 후<br>
			현재까지 연체율, 부실율 제로를 기록하고 있습니다.
		</div>
	</div>

	<?php if(false){?><!--<img src="../images/company03_1.jpg" width="100%" >--><?php } ?>
	<img src="../images/company04.jpg" width="100%"/>
	<img src="../images/company04_1.jpg" width="100%"/>
	<img src="../images/company04_2.jpg" width="100%"/>
	<img src="../images/company05_1.jpg" width="100%"/>
	<!--위원약력 2017.09.20-->
		<!--남기중 위원장-->
			<div style="position:absolute;margin-top:293px;margin-left:380px;width:75px;height:20px;cursor:pointer;" onmouseover="view1(true)" onmouseout="view1(false)"></div>
			<div id="member_info1" style="display:none;position:absolute;margin-top:315px;margin-left:234px; z-index:10;">
				<img src="../images/company/member01.png" width="100%"/>
			</div>
			<script>
				function view1(opt) {
				  if(opt) {
					 member_info1.style.display = "block";
				  }
				  else {
					 member_info1.style.display = "none";
				  }
				}
			</script>
	    <!--최수석 위원-->
			<div style="position:absolute;margin-top:293px;margin-left:680px;width:75px;height:20px;cursor:pointer;" onmouseover="view2(true)" onmouseout="view2(false)"></div>
			<div id="member_info2" style="display:none;position:absolute;margin-top:315px;margin-left:468px; z-index:10;">
				<img src="../images/company/member02.png" width="100%"/>
			</div>
			<script>
				function view2(opt) {
				  if(opt) {
					 member_info2.style.display = "block";
				  }
				  else {
					 member_info2.style.display = "none";
				  }
				}
			</script>
		 <!--최윤현 위원-->
			<div style="position:absolute;margin-top:578px;margin-left:235px;width:75px;height:20px;cursor:pointer;" onmouseover="view3(true)" onmouseout="view3(false)"></div>
			<div id="member_info3" style="display:none;position:absolute;margin-top:603px;margin-left:85px; z-index:10;">
				<img src="../images/company/member03.png" width="100%"/>
			</div>
			<script>
				function view3(opt) {
				  if(opt) {
					 member_info3.style.display = "block";
				  }
				  else {
					 member_info3.style.display = "none";
				  }
				}
			</script>
		  <!--채영민 위원-->
			<div style="position:absolute;margin-top:578px;margin-left:535px;width:75px;height:20px;cursor:pointer;" onmouseover="view4(true)" onmouseout="view4(false)"></div>
			<div id="member_info4" style="display:none;position:absolute;margin-top:603px;margin-left:384px; z-index:10;">
				<img src="../images/company/member04.png" width="100%"/>
			</div>
			<script>
				function view4(opt) {
				  if(opt) {
					 member_info4.style.display = "block";
				  }
				  else {
					 member_info4.style.display = "none";
				  }
				}
			</script>
		  <!--김인 위원-->
			<div style="position:absolute;margin-top:578px;margin-left:833px;width:75px;height:20px;cursor:pointer;" onmouseover="view5(true)" onmouseout="view5(false)"></div>
			<div id="member_info5" style="display:none;position:absolute;margin-top:603px;margin-left:680px; z-index:10;">
				<img src="../images/company/member05.png" width="100%"/>
			</div>
			<script>
				function view5(opt) {
				  if(opt) {
					 member_info5.style.display = "block";
				  }
				  else {
					 member_info5.style.display = "none";
				  }
				}
			</script>
		  <!--이정우 위원-->
			<div style="position:absolute;margin-top:862px;margin-left:385px;width:75px;height:20px;cursor:pointer;" onmouseover="view6(true)" onmouseout="view6(false)"></div>
			<div id="member_info6" style="display:none;position:absolute;margin-top:889px;margin-left:223px; z-index:10;">
				<img src="../images/company/member06.png" width="100%"/>
			</div>
			<script>
				function view6(opt) {
				  if(opt) {
					 member_info6.style.display = "block";
				  }
				  else {
					 member_info6.style.display = "none";
				  }
				}
			</script>
		  <!--김숙현 위원-->
			<div style="position:absolute;margin-top:862px;margin-left:685px;width:75px;height:20px;cursor:pointer;" onmouseover="view7(true)" onmouseout="view7(false)"></div>
			<div id="member_info7" style="display:none;position:absolute;margin-top:889px;margin-left:529px; z-index:10;">
				<img src="../images/company/member07.png" width="100%"/>
			</div>
			<script>
				function view7(opt) {
				  if(opt) {
					 member_info7.style.display = "block";
				  }
				  else {
					 member_info7.style.display = "none";
				  }
				}
			</script>
    <!--위원약력 2017.09.20-->
	<img src="../images/company05_2.jpg" width="100%"/>
	<img src="../images/company05_3.jpg" width="100%"/>

    <span id="reviews_area_title" style="padding-top:40px;">투자후기</span>
    <div id="reviews_area" class="bg_non" >
        <div class="reviews_cont">

            <?php if (count($best_review) > 0){ ?>
            <ul>
                <?php foreach($best_review as $best) { ?>
                    <li>
                        <div class="review">
                            <div class="review_box">
                                <span class="subject"><?php echo $best["subject"];?></span>
                                <span class="mem_name"><?php echo $best["mem_name"].' (ID: '.$best["mem_id"].')';?></span>
                                <div class="thumbnail">
                                    <img src="<?php echo $best["thumb_url"];?>" width="100%" height="203" alt="<?php echo $best['thumbnail_origin'];?>"/>
                                </div>
                                <span class="contents"><?php echo $best["contents"];?></span>
                                <div class="link">
                                    <a href="<?php echo $best["target_link"];?>" target="_blank">자세히 보기</a>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>

        </div>
		<div style="margin-top:10px;text-align: center;"><a href="/bbs/epilogue.php" class="btn_white_big">투자후기더보기</a></div>
        <?php } ?>
    </div>
	<div style="padding-top:30px;text-align: center;"><a href="/investment/invest_list.php" class="btn_blue_big">투자상품보기</a></div>
<? } ?>

	</div>
</div>

<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail']){
    @include_once($co['co_include_tail']);
} else {
    include_once('./_tail.php');
}
?>