<?php
include_once('./_common.php');


if ($_SERVER["REQUEST_METHOD"]!="GET") { echo "ERROR-DATA"; exit; }
if (!$member["mb_id"]){ echo "ERROR-LOGIN"; exit; }

$special_user = ($is_admin=='super' || in_array($member['mb_id'], array('akorea','yr4msp','hellosiesta','sori9th','hellofunding','test070','test999','romrom'))) ? true : false;


while(list($k, $v)=each($_GET)) { ${$k} = trim($v); }

//print_r($_GET);


$invest_list = array();

$where = " 1=1 ";
$where.= " AND A.member_idx='".$member['mb_no']."' ";
if($search_state) {
	if($search_state=='recruit_ing')        $where.= " AND A.invest_state='Y' AND B.state='' AND B.invest_end_date='' AND recruit_period_end >= CURDATE()";
	else if($search_state=='recruit_end')   $where.= " AND A.invest_state='Y' AND B.state='' AND B.invest_end_date!=''";
	else if($search_state=='invest_cancel') $where.= " AND A.invest_state='N' ";
	else if($search_state=='6.7')           $where.= " AND A.invest_state='Y' AND B.state IN('6','7') ";
	else if($search_state=='2.5')           $where.= " AND A.invest_state='Y' AND B.state IN('2','5') ";
	else if($search_state=='3')             $where.= " AND A.invest_state='Y' AND B.state=3 ";
	else                                    $where.= " AND A.invest_state='Y' AND B.state='$search_state' ";
}
if(!$special_user) $where.= " AND B.display='Y' ";
//echo $where."<br>";

$sql = "SELECT COUNT(*) FROM cf_product_invest A INNER JOIN cf_product B ON A.product_idx=B.idx WHERE $where";
$result = sql_query($sql);
$row = mysqli_fetch_array($result);
$affect_num = $row[0];

$page = (trim($_GET["page"])) ? trim($_GET["page"]) : 1;
$size = 5;

if($affect_num > 0) {

	if($page > ceil($affect_num / $size)) $page = ceil($affect_num / $size);
	$start_num = ($page - 1) * $size;

	$sql = "
		SELECT
			(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.product_idx AND invest_state='Y') AS total_invest_amount,
			A.idx, A.amount, A.member_idx, A.product_idx, A.invest_state,
			B.title, B.loan_interest_rate, B.loan_usefee, B.invest_period, B.recruit_period_start, B.recruit_period_end, B.recruit_amount,
			B.start_date, B.end_date, B.invest_return, B.invest_usefee, B.open_datetime, B.start_datetime, B.end_datetime,
			B.start_hour, B.start_minute, B.start_second, B.end_hour, B.end_minute, B.end_second, B.state, B.invest_end_date,
			B.loan_start_date, B.loan_end_date
		FROM
			cf_product_invest A
		INNER JOIN
			cf_product B ON A.product_idx=B.idx
		WHERE
			$where
		ORDER BY
			A.idx DESC
		LIMIT ".$start_num.", ".$size;
	$result = sql_query($sql);

	while($list = sql_fetch_array($result)){
		array_push($invest_list, $list);
	}

}

if(G5_IS_MOBILE) {
	include_once("ajax_invest_list_m.php");
	return;
}

?>
<style>
.text2 { height:33px; padding:0 5px; border:1px solid #AAA; border-radius:3px; vertical-align:middle; }
</style>

<div style="padding:0px 8px 6px;text-align:right">
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

<div class="type03">
	<table>
		<colgroup>
			<col style="width:5%">
			<col style="width:*">
			<col style="width:12%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:5%">
			<col style="width:10%">
			<col style="width:11%">
			<col style="width:16%">
		</colgroup>
		<tbody>
			<tr>
				<th>No</th>
				<th>상품명</th>
				<th>투자금액</th>
				
				<th>진행상태</th>
				<th>투자기간</th>
				<th>이자율(연)</th>
				<th>플랫폼<br>이용료율</th>
				<th>원리금<br>수취증서</th>
				<th></th>
			</tr>
<?
if($invest_list != null) {
	$No = $affect_num - $size * ($page - 1);
	foreach($invest_list as $Rows) {

		$product_open_date    = preg_replace("/ |:|-/", "", $Rows["open_datetime"]);		// 상점공개 (사전투자시작가능)
		$product_invest_sdate = preg_replace("/ |:|-/", "", $Rows["start_datetime"]);		// 투자시작일시
		$product_invest_edate = preg_replace("/ |:|-/", "", $Rows["end_datetime"]);			// 투자마감일시

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
			<tr>
				<td><?=$No?></td>
				<td style="text-align:left;overflow-y:hidden;height:35px;line-height:18px;"><a href="/investment/investment.php?prd_idx=<?=$Rows['product_idx']?>"><?=$Rows['title']?></a></td>
				<td style="text-align:right;"><?=number_format($Rows['amount'])?> 원
				<!--td class="btn"--><?
				if($Rows['invest_state'] =="Y") {
					//if($product_invest_sdate<=date("YmdHis") && $product_invest_edate>=date("YmdHis")){
					if($product_open_date<=date("YmdHis") && $product_invest_edate>=date("YmdHis")) {
						if($recruit_amount > $total_invest_amount) {
							if($invest_end_date=="" && $Rows["state"]==""){
								echo "<br/><span class=btn style='width:100px;'><a href=\"./funding_cancel.php?idx=".$Rows["idx"]."\"><span class=\"btn_gray3\">펀딩취소</span></a></span";
							}
						}
					}
				}
				?><!--/td--><!--모집 기간에만 취소 가능 -->
				</td>
				<td><b style="color:<?=$fcolor?>"><?=($Rows['invest_state']=="N") ? '투자취소' : $product_state;?></b></td>
				<td><?=preg_replace("/-/", ".", $Rows['loan_start_date'])?> ~ <?=preg_replace("/-/", ".", $Rows['loan_end_date'])?></td>
				<td><?=$Rows['invest_return']?>%</td>
				<td><?=($Rows['invest_usefee']>'0.00') ? '월 '.sprintf('%.2f', $Rows['invest_usefee']/12).'%' : '면제';?></td>
				<td><? if($Rows['invest_state']=="Y" && $Rows['loan_start_date']!='0000-00-00'){ ?><span class="btn_orange2 certificate_print_btn" data-idx="<?=$Rows["idx"]?>">보기</span></a><? } ?></td>
				<td><? if($Rows['invest_state']=="Y") { ?><span class="btn_blue2 funding_detail_btn" data-idx="<?=$Rows["idx"]?>">상세보기</span><? } ?></td>
				
			</tr>
<?
		$No--;
	}
}
else {
	echo '<tr><td colspan="10">데이터가 없습니다.</td></tr>' . PHP_EOL;
}
?>
		</tbody>
	</table>
	<div id="paging_span" class="mt10 mb20">
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
					css: { top:'10px', left:'50%', transform:'translateX(-50%)', margin:'0 auto;',width:'930px', border:'0', cursor:'default' }
				});
			}
		},
		error: function(e) { }
	});
});

//원리금 수취증서 프린트
$('.certificate_print_btn').click(function() {

	var agent = navigator.userAgent.toLowerCase();

	// 인터넷 익스플로어 또는 사파리인가?
	if((navigator.appName == 'Netscape' && navigator.userAgent.search('Trident') != -1) || (agent.indexOf("msie") != -1) && agent.indexOf("chrome") != -1){
		var url = '/deposit/principal_interest_certificate_origin.php?idx='+$(this).attr("data-idx");
	}else{
		var url = '/deposit/principal_interest_certificate.php?idx='+$(this).attr("data-idx");
	}

	popup_window(url, 'certificate', 'width=936,height=768,left=0,top=0,scrolling=no');
});
</script>

<?if($_COOKIE['debug_mode']) { echo "<div style='color:#FF6633;font-size:11px;'>".$_SERVER['PHP_SELF']."</div>"; } ?>