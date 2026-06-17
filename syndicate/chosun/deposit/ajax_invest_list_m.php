<style>
.text2 { height:33px; padding:0 5px; background:#FFF;border:1px solid #AAA; border-radius:3px; vertical-align:middle; }
</style>
<div style="padding:0px 0px 6px;text-align:right">
	<span style="font-weight:bold;padding-right:8px">진행상태</span>
	<select id="search_state" class="text2">
		<option value="" <?=($search_state=='')?'selected':''?>>:: 전체 ::</option>
		<option value="1" <?=($search_state=='1')?'selected':''?>>이자상환중</option>
		<option value="2.5" <?=($search_state=='2.5')?'selected':''?>>상환완료</option>
		<option value="recruit_ing" <?=($search_state=='recruit_ing')?'selected':''?>>투자모집중</option>
		<option value="recruit_end" <?=($search_state=='recruit_end')?'selected':''?>>투자모집종료</option>
		<option value="invest_cancel" <?=($search_state=='invest_cancel')?'selected':''?>>투자취소</option>
		<option value="3" <?=($search_state=='3')?'selected':''?>>투자금모집실패</option>
		<option value="4" <?=($search_state=='4')?'selected':''?>>연체/부실</option>
		<!--<option value="6.7" <?=($search_state=='6.7')?'selected':''?>>대출취소(기표전)</option>-->
	</select>
</div>
<div>
<?
if($invest_list != null) {
	$No = $affect_num - $size * ($page - 1);
	foreach($invest_list as $Rows) {

		$product_open_date    = preg_replace("/ |:|-/", "", $Rows["open_datetime"]);		// 상점오픈 (투자시작가능)
		$product_invest_sdate = preg_replace("/ |:|-/", "", $Rows["start_datetime"]);		// 상품오픈 (투자시작가능)
		$product_invest_edate = preg_replace("/ |:|-/", "", $Rows["end_datetime"]);			// 상품종료 (투자마감)

		$recruit_amount      = $Rows["recruit_amount"];
		$total_invest_amount = $Rows["total_invest_amount"];
		$invest_end_date     = str_replace("-", "", $Rows["invest_end_date"]);
		$product_state = get_product_state(
											 $Rows["recruit_period_start"],
											 $Rows["recruit_period_end"],
											 $product_open_date,
											 $product_invest_sdate,
											 $product_invest_edate,
											 $Rows["state"],
											 $recruit_amount,
											 $total_invest_amount,
											 $invest_end_date);

		if($Rows['invest_state']=='Y') {
			$fcolor = "#00C5B0";
			if($Rows["state"]==1) { $fcolor = "#3366FF"; }
			if( in_array($Rows["state"], array('6','7')) ) { $fcolor = "#AAAAAA"; }		//대출취소시
		}
		else {
			$fcolor = "#AAAAAA";
		}

?>
  <table class="table_invest_state mb10 type03" style="width:100%">
    <colgroup>
      <col style="width:30%">
      <col style="width:70%">
    </colgroup>
    <tbody>
      <tr>
        <td colspan="2" style='text-align:left;background-color:#EFEFEF'>NO.<?=$No?> <a href="/investment/investment.php?prd_idx=<?=$Rows['product_idx']?>"><b><?=$Rows['title']?></b></a></td>
      </tr>
      <tr>
        <td style='text-align:center;background-color:#F7F7F7'>투자금액</td>
        <td style='text-align:right;'><?=number_format($Rows['amount'])?>원</td>
      </tr>
      <tr>
        <td style='text-align:center;background-color:#F7F7F7'>투자기간</td>
        <td style='text-align:right;'><?=preg_replace("/-/", ".", $Rows['loan_start_date'])?> ~ <?=preg_replace("/-/", ".", $Rows['loan_end_date'])?></td>
      </tr>
      <tr>
        <td style='text-align:center;background-color:#F7F7F7'>이자율(연)</td>
        <td style='text-align:right;'><?=$Rows['invest_return']?>%</td>
      </tr>
			<tr>
        <td style='text-align:center;background-color:#F7F7F7'>플랫폼 이용료율</td>
        <td style='text-align:right;'><?=($Rows['invest_usefee']>'0.00') ? '월 '.sprintf('%.2f', $Rows['invest_usefee']/12).'%' : '면제';?></td>
      </tr>
      <tr>
        <td style='text-align:center;background-color:#F7F7F7'>투자상태</td>
        <td style='text-align:right;'>
          <b style='color:<?=$fcolor?>'><?=($Rows['invest_state']=="N") ? '취소' : $product_state;?></b>
					<? if($Rows['invest_state']=="Y") { ?> <span class="btn_blue2_2 funding_detail_btn" data-idx="<?=$Rows["idx"]?>">상세보기</span><? } ?>
					<?
						if($Rows['invest_state'] =="Y") {
							//if($product_invest_sdate<=date("YmdHis") && $product_invest_edate>=date("YmdHis")) {
							if($product_open_date<=date("YmdHis") && $product_invest_edate>=date("YmdHis")){
								if($recruit_amount > $total_invest_amount) {
									if($invest_end_date=="" && $Rows["state"]=="") {
										echo "<a href=\"./funding_cancel.php?idx=".$Rows["idx"]."\"><span class=\"btn_gray3_2\">펀딩취소</span></a>";
									}
								}
							}
						}
					?>
				</td>
      </tr>
<?
		$No--;
?>
    </tbody>
  </table>
<?
	}
}
else {
	echo '	<table class="table_invest_state mb10 type03" style="width:100%">
		<tr>
			<td style="text-align:center;height:100px;background:#FAFAFA">데이터가 없습니다.</td>
		</tr>
	</table>' . PHP_EOL;
}
?>

  <div id="paging_span" class="mb20">
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
  ajax_data = $("#frm").serialize();
  $.ajax({
    url : "./ajax_product_detail.php?idx="+ $(this).attr("data-idx"),
    type: "GET",
    data : ajax_data,
    success: function(data){
      if(data=="ERROR-DATA"){
        alert("시스템 에러입니다. 관리자에 문의해주세요.");
        return;
      }
      else if(data=="ERROR-DATE"){
        alert("펀딩 투자 기간이 아닙니다. 펀딩 취소는 투자 기간안에만 가능 합니다.");
        return;
      }
      else{
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