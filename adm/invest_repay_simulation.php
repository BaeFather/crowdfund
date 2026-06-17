<?

set_time_limit(0);

$sub_menu = '700200';
include_once('./_common.php');


$g5['title'] = '투자상환 시뮬레이션';
include_once('./admin.head.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

include_once(G5_LIB_PATH.'/repay_calculation.php');		// 월별 정산내역 추출함수 호출


$prd_idx            = trim($_REQUEST['idx']);											// 상품번호기준
$mb_id              = trim($_REQUEST['mb_id']);										// 특정 투자자만 조회 할 경우
$invest_period      = trim($_REQUEST['invest_period']);						// (시뮬레이션용) 투자개월수
$loan_start_date    = trim($_REQUEST['loan_start_date']);					// (시뮬레이션용) 투자시작일
$loan_end_date      = trim($_REQUEST['loan_end_date']);						// (시뮬레이션용) 투자만기일
$invest_usefee      = trim($_REQUEST['invest_usefee']);						// (시뮬레이션용) 플랫폼이용료율
$invest_usefee_type = trim($_REQUEST['invest_usefee_type']);			// (시뮬레이션용) 플랫폼이용료 징수방식
//$turn               = trim($_REQUEST['turn']);


$INV_ARR   = repayCalculation($prd_idx, $mb_id, $invest_period, $loan_start_date, $loan_end_date, $invest_usefee, $invest_usefee_type);

$INI       = $INV_ARR['INI'];
$PRDT      = $INV_ARR['PRDT'];
$LOANER    = $INV_ARR['LOANER'];
$INVEST    = $INV_ARR['INVEST'];
$MTOTAL_INVEST_SUM = $INV_ARR['MTOTAL_INVEST_SUM'];
$REPAY     = $INV_ARR['REPAY'];
$REPAY_SUM = $INV_ARR['REPAY_SUM'];


$ib_trust = ($PRDT['ib_trust']=='Y' && $PRDT['ib_product_regist']=='Y') ? true : false;


$date  = date('Y-m-d H:i:s');
$state = '';
if($PRDT['state']) {
	if($PRDT['state']=='1') { $state = '이자상환중'; $state_code = '2'; }
	if($PRDT['state']=='2') { $state = '상품마감'; }
	if($PRDT['state']=='4') { $state = '부실'; }
	if($PRDT['state']=='5') { $state = '중도상환'; $state_code = '2'; }
	if($PRDT['state']=='6') { $state = '대출계약취소(기표전)'; }
	if($PRDT['state']=='7') { $state = '대출계약취소(기표후)'; }
}
else {
	if ($PRDT['open_datetime'] > $date) { $state = '투자대기중'; }
	if ($PRDT['start_datetime'] < $date && $PRDT['end_datetime'] > $date && $PRDT['invest_end_date'] == '') { $state = '투자모집중'; }
	if ($PRDT['end_datetime'] < $date && $PRDT['invest_end_date'] == '') { $state = '투자금 모집실패'; $state_code = '3'; }
	if ($PRDT['invest_end_date'] != '' && $PRDT['state'] == '') { $state = '대기중'; $state_code = '1'; }
}

?>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/jquery-ui.min.css" rel="stylesheet">
<script src="js/jquery-ui.min.js"></script>

<style>
#title_row_wrap { position:fixed; display:none; z-index:5; top:0; margin-left:0; }
</style>

<div class="row" style="width:99.9%;">
	<div class="col-lg-12">

		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<colgroup>
						<col width="5%">
						<col width="">
						<col width="">
						<col width="">
						<col width="">
						<col width="">
					</colgroup>
					<thead>
						<tr>
							<th class="text-center">고유번호</th>
							<th class="text-center">상품명</th>
							<th class="text-center">설정개월수</th>
							<th class="text-center">회차</th>
							<th class="text-center">투자기간</th>
							<th class="text-center">플랫폼이용료율</th>
							<th class="text-center">투자자ID</th>
							<th class="text-center"></th>
						</tr>
					</thead>

					<form name='form1' method="get" class="form-horizontal">
						<input type="hidden" name="idx" value="<?=$PRDT['idx']?>">
					<tbody>
						<tr class="odd">
							<td align="center"><?=$PRDT['idx']?></td>
							<td align="center"><?=$PRDT['title']?></td>
							<td align="center">
								<ul style="display:inline-block;margin:0 auto;padding:0;list-style:none;">
									<li style="float:left;padding:0 8px 0 0;"><input type="text" id="invest_period" name="invest_period" value="<?=$PRDT['invest_period']?>" class="form-control" style="width:50px" onKeyUp="onlyDigit(this);"></li>
									<li style="float:left;padding:8px 0 0 0;">개월</li>
								</ul>
							</td>
							<td align="center"><?=$INI['max_paied_turn']?> / <?=$INI['repay_count']?></td>
							<td align="center">
								<ul style="display:inline-block;margin:0 auto;padding:0;list-style:none;">
									<li style="float:left;"><input type="text" name="loan_start_date" value="<?=$PRDT['loan_start_date']?>" class="form-control datepicker" style="width:100px;"></li>
									<li style="float:left;padding:6px 8px 0;">~</li>
									<li style="float:left"><input type="text" name="loan_end_date" value="<?=$INI['loan_end_date']?>" class="form-control datepicker" style="width:100px;"></li>
								</ul>
							</td>
							<td align="center">
								<ul style="display:inline-block;margin:0 auto;padding:0;list-style:none;">
									<li style="float:left;padding:8px 8px 0 0;">연</li>
									<li style="float:left;padding:0 8px 0 0;"><input type="text" id="invest_usefee" name="invest_usefee" value="<?=sprintf('%.2f', $PRDT['invest_usefee'])?>" class="form-control" style="width:60px"></li>
									<li style="float:left;padding:8px 24px 0 0;">%</li>
									<li style="float:left;padding:0;">
										<select id="invest_usefee_type" name="invest_usefee_type" class="form-control" style="width:120px">
											<option value="">:: 징수방식 ::</option>
											<option value="A" <?=($PRDT['invest_usefee_type']=='A')?'selected':''?>>월별분할</option>
											<option value="B" <?=($PRDT['invest_usefee_type']=='B')?'selected':''?>>만기일시</option>
										</select>
									</li>
								</ul>
							</td>
							<td align="center"><input type="text" id="mb_id" name="mb_id" value="<?=$mb_id?>" class="form-control" style="width:120px"></td>
							<td align="center">
								<button type="submit" id="callable_simulation_btn" class="btn btn-success">투자시뮬레이션</button>
								<button type="button" class="btn btn-default" onClick="location.replace('?idx=<?=$idx?>');">초기화</button>
							</td>
						</tr>
					</tbody>
					</form>

				</table>
			</div>
		</div>

		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center">대출금액</th>
							<th class="text-center">투자기간</th>
							<th class="text-center">이자계산일수</th>
							<th class="text-center">연수익률</th>
							<th class="text-center">예상이자</th>
							<th class="text-center">플랫폼 이용료율</th>
							<th class="text-center">플랫폼 이용료</th>
							<th class="text-center">원천징수</th>
							<th class="text-center">지급이자</th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd">
							<td align="center"><?=number_format($PRDT['invest_principal'])?></td>
							<td align="center"><?=preg_replace('/-/', '.', $PRDT['loan_start_date'])?> ~ <?=preg_replace('/-/', '.', $INI['loan_end_date'])?></td>
							<td align="center"><?=$INI['total_day_count']?>일</td>
							<td align="center"><?=$PRDT['invest_return']?>%</td>
							<td align="center"><?=number_format($REPAY_SUM['invest_interest'])?>원</td>
							<td align="center">연 <?=sprintf('%.2f', $PRDT['invest_usefee'])?>%</td>
							<td align="center"><?=number_format($REPAY_SUM['invest_usefee'])?>원</td>
							<td align="center"><?=number_format($REPAY_SUM['TAX']['sum'])?>원</td>
							<td align="center"><?=number_format($REPAY_SUM['interest'])?>원</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="panel-body">
			<div id="title_row_wrap" style="padding-right:32px;">
				<table class="table table-striped table-bordered table-hover" style="margin-bottom:0; font-size:12px; opacity:0.9">
					<colgroup>
						<col width="3%"><col width="5%"><col width=""><col width="7%"><col width="7%">
						<col width="7%"><col width="7%">
						<col width="7%"><col width="7%"><col width="7%">
						<col width="7%"><col width="7%"><col width="7%"><col width="7%">
					</colgroup>
					<thead style="background-color:#F8F8EF;">
						<tr align="center">
							<th colspan="5" style="border-right:1px solid #999">투자자</th>
							<th colspan="2" style="border-right:1px solid #999">예상이자(세전)</th>
							<th colspan="3" style="border-right:1px solid #999">누적</th>
							<th colspan="4">당월</th>
						</tr>
						<tr align="center">
							<th>NO</th>
							<th>구분</th>
							<th>ID</th>
							<th>이름</th>
							<th style="border-right:1px solid #999">투자금</th>

							<th>전체</th>
							<th style="border-right:1px solid #999">당월</th>

							<th>플랫폼이용료</th>
							<th>원천징수</th>
							<th style="border-right:1px solid #999">실수령이자</th>

							<th>플랫폼이용료</th>
							<th>원천징수</th>
							<th>실수령이자</th>
							<th>상환원금</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

	</div>
<?

	$repay_count = count($REPAY);
	for($i=0,$turn=1; $i<$repay_count; $i++, $turn++)
	{
?>
	<div class="col-lg-12">
		<div class="panel-body" style="padding-bottom: 0;" <?=($i==0)?"id='list_start'" : "";?>>
			<div style="width:100%;margin:4px 0 4px 0; padding:4px 20px 4px 20px; border:1px solid #ddd; border-radius:15px; background-color:#ffebcc;">
			이자지급 회차 : <?=$turn?>차 <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			정산일 : <?=$REPAY[$i]['repay_date']?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			대상기간 : <?=preg_replace('/-/', '.', $REPAY[$i]['target_sdate'])?> ~ <?=preg_replace('/-/', '.', $REPAY[$i]['target_edate'])?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			이자계산일수 : <?=$REPAY[$i]['day_count']?>일
			</div>
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover" style="margin-bottom:0; font-size:12px">
					<colgroup>
						<col width="3%"><col width="5%"><col width=""><col width="7%"><col width="7%">
						<col width="7%"><col width="7%">
						<col width="7%"><col width="7%"><col width="7%">
						<col width="7%"><col width="7%"><col width="7%"><col width="7%">
					</colgroup>
					<thead style="background-color:#F8F8EF;">
						<tr align="center">
							<th colspan="5" style="border-right:1px solid #999">투자자</th>
							<th colspan="2" style="border-right:1px solid #999">예상이자(세전)</th>
							<th colspan="3" style="border-right:1px solid #999">누적</th>
							<th colspan="4">당월</th>
						</tr>
						<tr align="center">
							<th>NO</th>
							<th>구분</th>
							<th>ID</th>
							<th>이름</th>
							<th style="border-right:1px solid #999">투자금</th>

							<th>전체</th>
							<th style="border-right:1px solid #999">당월</th>

							<th>플랫폼이용료</th>
							<th>원천징수</th>
							<th style="border-right:1px solid #999">실수령이자</th>

							<th>플랫폼이용료</th>
							<th>원천징수</th>
							<th>실수령이자</th>
							<th>상환원금</th>
						</tr>
					</thead>
					<tbody>

<?
		$list_count = count($REPAY[$i]['LIST']);
		for($j=0,$num=$list_count; $j<$list_count; $j++,$num--)
		{

			$member_id   = $REPAY[$i]['LIST'][$j]['mb_id'];
			$member_name = $REPAY[$i]['LIST'][$j]['mb_name'];
			$member_name = preg_replace("/주식회사/", "(주)", $member_name);

			$member_type = "";
			$member_type.= ($REPAY[$i]['LIST'][$j]['member_type']=='2') ? "기업" : "개인";
			$member_type.= ($REPAY[$i]['LIST'][$j]['is_creditor']=='Y') ? "-대부" : "";

			if($REPAY[$i]['LIST'][$j]['receive_method']) {
				$receive_method = ($REPAY[$i]['LIST'][$j]['receive_method']=='1') ? '환급계좌' : '<font color="#FF2222">예치금</font>';
			}
			else {
				$receive_method = "미지정";
			}

			$principal = ($turn < $INI['repay_count']) ? 0 : $REPAY[$i]['LIST'][$j]['amount'];
			$principal_sum = $principal_sum + $principal;
?>
						<tr>
							<td align="center"><?=$num?></td>
							<td align="center"><?=$member_type?></td>
							<td align="center"><?=$member_id?></td>
							<td align="center"><?=$member_name?></td>
							<td align="right" style="border-right:1px solid #999"><strong><?=number_format($REPAY[$i]['LIST'][$j]['amount'])?></strong></td>

							<td align="right"><?=number_format($REPAY_SUM[$member_id]['invest_interest'])?></td>
							<td align="right" style="border-right:1px solid #999;color:#3366FF"><?=number_format($REPAY[$i]['LIST'][$j]['invest_interest'])?></td>

							<td align="right"><?=number_format($REPAY[$i]['MEMBER_NUJUK'][$member_id]['invest_usefee'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['MEMBER_NUJUK'][$member_id]['TAX']['sum'])?></td>
							<td align="right" style="border-right:1px solid #999"><?=number_format($REPAY[$i]['MEMBER_NUJUK'][$member_id]['interest'])?></td>

							<td align="right"><?=number_format($REPAY[$i]['LIST'][$j]['invest_usefee'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['LIST'][$j]['TAX']['sum'])?></td>
							<td align="right"><span style='color:#3366FF'><?=number_format($REPAY[$i]['LIST'][$j]['interest'])?></span></td>
							<td align="right"><span style='color:#3366FF'><?=number_format($principal)?></span></td>
						</tr>
<?
		}
?>
						<tr style="background:#EDF4FC;color:blue;">
							<td align="center" colspan="4">합계</td>
							<td align="right" style="border-right:1px solid #999"><?=number_format($REPAY[$i]['SUM']['amount'])?></td>

							<td align="right"><?=number_format($REPAY_SUM['invest_interest'])?></td>
							<td align="right" style="border-right:1px solid #999"><?=number_format($REPAY[$i]['SUM']['invest_interest'])?></td>

							<td align="right"><?=number_format($REPAY[$i]['NUJUK_SUM']['invest_usefee'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['NUJUK_SUM']['TAX']['sum'])?></td>
							<td align="right" style="border-right:1px solid #999"><?=number_format($REPAY[$i]['NUJUK_SUM']['interest'])?></td>

							<td align="right"><?=number_format($REPAY[$i]['SUM']['invest_usefee'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['SUM']['TAX']['sum'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['SUM']['interest'])?></td>
							<td align="right"><?=number_format($principal_sum)?></td>
						</tr>

					</tbody>
				</table>
			</div>
		</div>
		<div class="panel-body pull-right">
			<a href="./invest_repay_simulation_excel.php?idx=<?=$PRDT['idx']?>&turn=<?=$turn?>&loan_start_date=<?=$PRDT['loan_start_date']?>&loan_end_date=<?=$PRDT['loan_end_date']?>" target="_blank" class="btn btn-primary">엑셀다운</a>
		</div>

	</div>
<?
	}
?>
</div>

<br><br>

<script>
$(window).scroll(function(){
	if($(window).scrollTop() > $('#list_start').offset().top){
		$("#title_row_wrap").css('position','fixed');
		$("#title_row_wrap").css('display','block');
		$("#title_row_wrap").css('z-index','5');
		$("#title_row_wrap").css('width',$(window).width() - 30);
	}
	else {
		$("#title_row_wrap").css('display','none');
	}
});


$(function() {
	$(".datepicker").datepicker({
		dateFormat: "yy-mm-dd",
		monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
		monthNamesShort : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
		dayNames : ['일', '월', '화', '수', '목', '금', '토'],
		dayNamesShort : ['일', '월', '화', '수', '목', '금', '토'],
		dayNamesMin : ['일', '월', '화', '수', '목', '금', '토']
	});
});
</script>

</body>
</html>

<?
unset($INI);
unset($REPAY);
unset($REPAY_SUM);
?>