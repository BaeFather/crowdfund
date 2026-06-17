<link rel="stylesheet" type="text/css" href="/investment/css/investment_info_old_m.css">

<!-- 본문내용 START -->
<div id="content" style="padding-top:0;margin:10px auto;">
	<div class="content investment" style="padding:2px;">

		<ul class="tab_type03">
			<li data-gubun="tab1" class="on" style="width:100%;height:32px;">투자상품 기본정보</li>
			<li data-gubun="tab2" style="float:left;height:32px;width:49.4%">증빙자료</li>
			<li data-gubun="tab3" style="float:right;height:32px;width:49.4%">안전장치 업데이트 <?=($PRDT["extend_8"])?" <span style='font-size:10pt;color:red;font-family:arial;'>√</span>" : "";?></li>
		</ul>
		<script>
		//탭 기능
		$(document).ready(function(){
			$('.tabArea:eq(0)').show();
			$('.tab_type03 li').click(function(){
				$(this).addClass('on').siblings().removeClass('on');
				var cur = $(this).index();
				$('.tabArea').hide();
				$('.tabArea:eq('+cur+')').slideToggle('slow');
				Size2Parent();
			});
		});
		</script>

		<div class="tabArea">
<? if($PRDT["extend_6"]!="") { ?>
			<dl class="profit_title">
				<dt>채권매입계약</dt>
				<dd>
					<? if( $PRDT["purchase_guarantees"]=='Y' ) { ?><div style='margin:0 0 16px;'><img src='/images/investment/guarantee_system_m.jpg' width='100%'></div><? } ?>
					<?=@preg_replace("/script/i", "script.", $PRDT["extend_6"])?>
				</dd>
			</dl>
<? } ?>

<? if($PRDT['invest_summary_m']!=''){ ?>
			<h3 style="padding-top:20px">투자요약</h3>
			<div class="point">
				<? if($PRDT["category"]=='1'){ ?><div style='margin:0 0 16px;'><img src='/images/investment/guarantee_port_m.jpg' width='100%'></div><? } ?>
				<?=@preg_replace("/script/i", "script.", $PRDT['invest_summary_m'])?>
				<? if($prd_idx=="102") { ?><div><img src="/images/investment/102_event_m.jpg" width="100%"></div><? } ?>
			</div>
<? } ?>

<? if($PRDT["core_invest_point"]!=""){ ?>
			<h3><?=($PRDT['category']=='1') ? '대출자 정보' : '핵심 투자포인트';?></h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["core_invest_point"])?></div>
<? } ?>

<? if($PRDT["extend_4"]!=""){ ?>
			<h3><?=($PRDT['category']=='1') ? '담보물 정보' : '투자자 보호장치';?></h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_4"])?></div>
<? } ?>

<? if($PRDT["extend_1"]!=""){ ?>
			<h3><?=($PRDT['category']=='1') ? '투자자보호장치' : '담보 분석 및 평가';?></h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_1"])?></div>
<? } ?>


<? if($grade) { ?>
			<h3>평가등급</h3>
	<? if( $grade_type=="v1" ) {	?>
			<div class="level_info">
				<div class="label"><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>"></div>
				<ul class="info">
					<li style="height:20px;"><li>
					<li>안정성 <span class="star<?=$PRDT["evaluate_star1"]?>"></span> <?=$PRDT["evaluate_score1"]?>/100</li>
					<li>수익성 <span class="star<?=$PRDT["evaluate_star2"]?>"></span> <?=$PRDT["evaluate_score2"]?>/100</li>
					<li>환급성 <span class="star<?=$PRDT["evaluate_star3"]?>"></span> <?=$PRDT["evaluate_score3"]?>/100</li>
					<!--<li>합계 <b class="green"><?=$_evaluation_grade_array[$total_evaluate_star]?></b></li>-->
				</ul>
			</div>
	<? } else if( $grade_type=="v2" ) { ?>
			<div style="width:100%;font-size:11px;color:brown">헬로펀딩은 안전투자를 위해 <span style="color:#FF2222"><b>A등급</b></span> 이상의 상품만 취급합니다.</div>
			<div class="level_info" style="height:130px">
				<div class="label" style="padding-top:15px"><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>" ></div>
				<div class="invest_graph">
					<div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score1?>%">&nbsp;안전성</div></div>
					<div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score4?>%">&nbsp;상환성</div></div>
					<div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score2?>%">&nbsp;수익성</div></div>
					<div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score3?>%">&nbsp;환금성</div></div>
				</div>
			</div>
	<? } else if( $grade_type=="v3") { ?>
			<div style="width:100%;font-size:11px;color:brown">헬로펀딩은 안전투자를 위해 <span style="color:#FF2222"><b>A등급</b></span> 이상의 상품만 취급합니다.</div>
			<div class="level_info">
				<div class="label"><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>" ></div>
				<div class="invest_graph">
					<div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score1?>%">&nbsp;안전성</div></div>
					<div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score4?>%">&nbsp;상환성</div></div>
					<div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score3?>%">&nbsp;환금성</div></div>
				</div>
			</div>
	<? } ?>
<? } ?>


<? if($PRDT["extend_2"]!=""){ ?>
			<h3><?=($PRDT['category']=='1') ? 'Q&A' : '신용 및 부채정보';?></h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_2"])?></div>
<? } ?>

<? if($PRDT["extend_3"]!=""){ ?>
			<h3>투자 구조도</h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_3"])?></div>
<? } ?>

<? if($PRDT["category"]!=1 && $PRDT["extend_5"]!=""){ ?>
			<h3>평가기관 의견</h3>
			<div class="point"><?=@preg_replace("/script/i", "script.", $PRDT["extend_5"])?></div>
<? } ?>

<? if($PRDT["category"]!=1 && $PRDT["screening"]!="") { ?>
			<h3>심사총평</h3>
			<div class="point">
<?
	if( $PRDT["judge"] ) {
		$judge_profile_image_name = (G5_IS_MOBILE) ? $PRDT["judge"]."_m.jpg" : $PRDT["judge"].".jpg";
		$judge_profile_image = "../images/judge/".$judge_profile_image_name;
		if( file_exists($judge_profile_image) ) { echo "<div style='margin:0 0 20px 0; width:100%;'><img src='$judge_profile_image'></div>"; }
	}
?>
				<div style='padding:10px;'><?=@preg_replace("/script/i", "script.", $PRDT["screening"])?></div>
			</div>
<? } ?>

<?
	if( !preg_match("/\<div class=\"invest_cont\"\>/i", $PRDT['invest_summary']) ) {

		$invest_period_month= ceil($PRDT["invest_period"]/12);
		$invest_period_month= $invest_period_month*12;
?>
			<div style="clear:both;height:20px;"></div>
			<h3>투자안내</h3>
			<div class="invest_info">
				<div class="table">
					<div class="title">투자금액별 총예상수익</div>
					<table>
						<tbody>
							<tr>
								<th>투자금액</th>
								<th>예상수익<br >(연 수익 기준 / 세전)</th>
								<!--<th>총예상수익 (세전)</th>-->
							</tr>
							<tr>
								<td>100,000원</td>
								<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*100000))))?>원</td>
							</tr>
							<tr>
								<td>500,000원</td>
								<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*500000))))?>원</td>
							</tr>
							<tr>
								<td>1,000,000원</td>
								<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*1000000))))?>원</td>
							</tr>
							<tr>
								<td>10,000,000원</td>
								<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*10000000))))?>원</td>
							</tr>
							<tr>
								<td>50,000,000원</td>
								<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*50000000))))?>원</td>
							</tr>
							<tr>
								<td>100,000,000원</td>
								<td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*100000000))))?>원</td>
							</tr>
						</tbody>
					</table>
					<p style="margin-top:5px;color:#777">
					* 상환일: 매월 5일 (영업일기준)<br >
					* 연 수익률 기준
					</p>
				</div>
				<div class="notes">
					<div class="title">투자시 참고사항</div>
					<div class="text" style="font-size:10pt">
						○ 투자수익 시뮬레이션<br >
						<ul>
							<li style="list-style:disc;margin-left:24px;">투자수익 시뮬레이션은 예상수익을 표기해주는 것으로 펀딩완료 후 대출실행일과의 일수차이, 조기상환 및 기타 이유로 기재된 예상수익은 변동될 수 있습니다.</li>
						</ul>
						<br>
						○ 원금 및 이자 보장에 대한 사항<br>
						<ul>
							<li style="list-style:disc;margin-left:24px;">헬로펀딩은 투자금에 대하여 원금 및 이자수익을 보장하지 않습니다.</li>
							<li style="list-style:disc;margin-left:24px;">채무자의 채무 불이행시 경,공매등의 절차 과정에서 원금의 일부 손실이 발생할 수 있습니다.</li>
						</ul>
						<br>
						○ 이자소득세 원천징수<br>
						<ul>
							<li style="list-style:disc;margin-left:24px;">일반투자자의 투자수익은 '비영업대금의 이익'으로 소득세법 제 16조 제 1항 제 11호에 의해 25%의 소득세가 발생되며, 주민세 2.5%가 추가되어 총 27.5%의 세금을 납부해야 합니다. 이러한 세금납부에 대해 헬로핀테크에서 원천징수를 하므로 일반투자자는 별도로 세금신고를 하실 필요가 없습니다.</li>
						</ul>
						<br>
						○ 투자일과 원금상환 입금날짜가 다른 이유<br>
						<ul>
							<li style="list-style:disc;margin-left:24px;">투자금이 100% 펀딩된 이후 대출약정을 통해 대출이 실행되며 이 기간에 수일이 소요될 수 있으며, 대출자 분이 대출금을 받은날 부터 이자가 계산되어지기 때문에 실 투자일과 상환일에 차이가 발생합니다.</li>
						</ul>
						<!--
						○ 이자소득세 원천징수<br>
						<ul>
							<li style="list-style:disc;margin-left:24px;">이자소득에 대하여 이자소득세25% + 주민세2.5%가 가산되어, 총 이자소득의27.5%가 원천징수 됩니다.</li>
						</ul>
						<br>
						○ 플랫폼이용료<br>
						<ul>
							<li style="list-style:disc;margin-left:24px;">투자자 플랫폼 이용료는 상품 투자금액의0~3%(연) 수수료를 매월 분할 차감</li>
						</ul>
						-->
					</div>
					<? if($invest_finished==false) { ?><a href="./simulation.php?prd_idx=<?=$PRDT["idx"]?>" class="btn_big_blue">투자 수익 시뮬레이션</a><? } ?>
				</div>
			</div>
<?
	}
?>

<? if($PRDT['extend_7']) { ?>
			<h3>투자관련 도움말</h3>
			<div class="point invest_info"><?=$PRDT['extend_7']?></div>
<? } ?>

		</div>

		<div class="tabArea" style="padding-top:20px;">
			<h3>증빙자료</h3>
			<div class="point"><?=($PRDT["extend_9"])?$PRDT["extend_9"]:'<p style="text-align:center;color:#aaa">내용이 없습니다.</p>';?></div>
		</div>

		<div class="tabArea" style="padding-top:20px;">
			<h3>안전장치 업데이트</h3>
			<div class="point"><?=($PRDT["extend_8"])?$PRDT["extend_8"]:'<p style="text-align:center;color:#aaa">내용이 없습니다.</p>';?></div>
		</div>

	</div>
</div>

<? include_once('./member/tail.php'); ?>


<script type="text/javascript">
function Size2Parent() {
	setTimeout(function(){
		var w = $('#content').width()
		var h = $('#content').height();
		h = (h < 500) ? 500 : h;
		$rtn_json =  '{"height": ' + h + ',"width": ' + w + '}';
		<? /* if($_SERVER['REMOTE_ADDR']=='220.117.134.164'){ ?>alert($rtn_json);<? } */ ?>
		window.parent.postMessage($rtn_json, '*');
	}, 800);
}

/* 상위프레임(와우스타측) 으로 본 페이지 사이즈 전송 */
$(document).ready(function(){
	Size2Parent();
});
</script>