<style>
.text2 { height:33px; padding:0 5px; background:#FFF;border:1px solid #AAA; border-radius:3px; vertical-align:middle; }
</style>

<div style="padding:0px 0px 6px;text-align:right">
	<span style="font-weight:bold;padding-right:8px">진행상태</span>
	<select id="search_state" class="text2">
		<option value="" <?=($search_state=='')?'selected':''?>>:: 전체 ::</option>
		<option value="recruit_ing" <?=($search_state=='recruit_ing')?'selected':''?>>투자모집중</option>
		<option value="recruit_end" <?=($search_state=='recruit_end')?'selected':''?>>투자모집종료</option>
		<option value="1" <?=($search_state=='1')?'selected':''?>>이자상환중</option>
		<option value="2.5" <?=($search_state=='2.5')?'selected':''?>>상환완료</option>
		<option value="invest_cancel" <?=($search_state=='invest_cancel')?'selected':''?>>투자취소</option>
		<option value="8" <?=($search_state=='8')?'selected':''?>>상환지연/연체</option>
		<option value="4" <?=($search_state=='4')?'selected':''?>>부실</option>
		<option value="9" <?=($search_state=='9')?'selected':''?>>매각</option>
		<!--<option value="3" <?=($search_state=='3')?'selected':''?>>투자금모집실패</option>-->
		<!--<option value="6.7" <?=($search_state=='6.7')?'selected':''?>>대출취소(기표전)</option>-->
	</select>
</div>
<div>
<?
if($list_count) {
	$num = $affect_num - $size * ($page - 1);
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		$product_state = '';
		if($LIST[$i]['state']=='8' && date('Y-m-d') <= date( 'Y-m-d', strtotime( $LIST[$i]['loan_end_date']. ' +30 day' )) ) {
			$product_state = "상환지연중";
		}
		else {
			$product_state = get_product_state(
				$LIST[$i]['recruit_period_start'],
				$LIST[$i]['recruit_period_end'],
				preg_replace("/(-| |:)/", "", $LIST[$i]['open_datetime']),
				preg_replace("/(-| |:)/", "", $LIST[$i]['start_datetime']),
				preg_replace("/(-| |:)/", "", $LIST[$i]['end_datetime']),
				$LIST[$i]['state'],
				$LIST[$i]['recruit_amount'],
				$LIST[$i]['total_invest_amount'],
				preg_replace("/-/", "", $LIST[$i]['invest_end_date'])
			);
		}

		$fcolor = "#AAAAAA";
		if($LIST[$i]['invest_state'] == 'Y') {
			$fcolor = "#00C5B0";
			if($LIST[$i]['state'] == '1') { $fcolor = "#3366FF"; }
			if( in_array($LIST[$i]['state'], array('6','7')) ) { $fcolor = "#AAAAAA"; }		//대출취소시
			if($LIST[$i]['state'] == '8') { $fcolor = "#ff9d24"; }
		}

		$print_date_range = "";
		if($LIST[$i]['loan_start_date'] > '0000-00-00' && $LIST[$i]['loan_end_date'] > '0000-00-00') {
			$print_date_range = preg_replace("/-/", ".", $LIST[$i]['loan_start_date']) . " ~ " . preg_replace("/-/", ".", $LIST[$i]['loan_end_date']);
		}

?>
  <table class="tblX table_invest_state mb10 type03">
    <colgroup>
      <col style="width:30%">
      <col style="width:70%">
    </colgroup>
    <tbody>
      <tr>
        <td colspan="2" style='text-align:left;background-color:#EFEFEF'>NO.<?=$num?> <a href="/investment/investment.php?prd_idx=<?=$LIST[$i]['product_idx']?>"><b><?=$LIST[$i]['title']?></b></a></td>
      </tr>
      <tr>
        <td style='text-align:center;background-color:#F7F7F7'>투자금액</td>
        <td style='text-align:right;'><?=number_format($LIST[$i]['amount'])?>원</td>
      </tr>
      <tr>
        <td style='text-align:center;background-color:#F7F7F7'>투자기간</td>
        <td style='text-align:right;'><?=$print_date_range?></td>
      </tr>
      <tr>
        <td style='text-align:center;background-color:#F7F7F7'>이자율(연)</td>
        <td style='text-align:right;'><?=$LIST[$i]['invest_return']?>%</td>
      </tr>
			<tr>
        <td style='text-align:center;background-color:#F7F7F7'>플랫폼 이용료율</td>
        <td style='text-align:right;'><?=($LIST[$i]['invest_usefee']>'0.00') ? '월 '.sprintf('%.2f', $LIST[$i]['invest_usefee']/12).'%' : '면제';?></td>
      </tr>
      <tr>
        <td style='text-align:center;background-color:#F7F7F7'>투자상태</td>
        <td style='text-align:right;'>
          <b style='color:<?=$fcolor?>'><?=($LIST[$i]['invest_state']=="N") ? '취소' : $product_state;?></b>
					<? if($LIST[$i]['invest_state'] == 'Y') { ?> <span class="btn_blue2_2 funding_detail_btn" data-idx="<?=$LIST[$i]["idx"]?>" prd-id="<?=$LIST[$i]['product_idx']?>">상세보기</span><? } ?>
					<?
						if( $LIST[$i]['invest_state'] == 'Y' && ($LIST[$i]['open_datetime'] <= G5_TIME_YMDHIS && $LIST[$i]['end_datetime'] >= G5_TIME_YMDHIS) ) {
							if( ($LIST[$i]['recruit_amount'] > $LIST[$i]['total_invest_amount']) && $LIST[$i]['invest_end_date'] == '' && $LIST[$i]['state'] == '') {
								echo "<a href=\"./funding_cancel.php?idx=".$LIST[$i]["idx"]."\"><span class=\"btn_gray3_2\">투자취소</span></a>";
							}
						}
					?>
				</td>
      </tr>
<?
		$num--;
?>
    </tbody>
  </table>
<?
	}
}
else {
	echo '
	<table class="table_invest_state mb10 type03" style="width:100%">
		<tr>
			<td style="text-align:center;height:100px;background:#FAFAFA">데이터가 없습니다.</td>
		</tr>
	</table>' . PHP_EOL;
}
?>

  <div id="paging_span" class="mb20 invest_list_paging">
    <? paging($affect_num, $page, $size); ?>
  </div>
</div>

<script type="text/javascript">
$('#search_state').on('change', function() {
	var search_state = $(this).val();
	load_invest_list('', search_state);
});

//상세보기
$('.funding_detail_btn').click(function() {
	/*
	if ($(this).attr("prd-id")=="3023") {
		alert("면세점 확정매출채권 218호 원금상환을 위한 정산 작업중입니다.");
		return;
	}
	*/

  ajax_data = $("#frm").serialize();
  $.ajax({
		url : "<?=$repay_schedule_url?>?idx="+ $(this).attr("data-idx"),
    type: "GET",
    data : ajax_data,
		success: function(data) {
      if(data=="ERROR-DATA") {
        alert("시스템 에러입니다. 관리자에 문의해주세요.");
        return;
      }
      else if(data=="ERROR-DATE") {
        alert("펀딩 투자 기간이 아닙니다. 펀딩 취소는 투자 기간안에만 가능 합니다.");
        return;
      }
			else if(data=="ERROR-LOGIN") {
				location.replace("/bbs/login.php?url=<?=urlencode('/deposit/deposit.php');?>");
				return;
			}
			else {
        $("#detail").html(data);
        $.blockUI({
          message: $('#detail'),
          css: { top:'1%',left:'1%',width:'98%', height:'98%', overflow:'auto' ,border:0, cursor:'default' }
				});
      }
    },
		error: function(e) { }
  });
});
</script>

<?
@sql_close();
exit;
?>