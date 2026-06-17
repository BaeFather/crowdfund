<?
include_once('./_common.php');


if ($_SERVER['REQUEST_METHOD']!="GET") { echo "ERROR-DATA"; exit; }
if (!$member['mb_id']){ echo "ERROR-LOGIN"; exit; }

$special_user = ($is_admin=='super' || in_array($member['mb_id'], array('akorea','yr4msp','hellosiesta','sori9th','hellofunding','test070','test999','romrom'))) ? true : false;


while(list($k, $v)=each($_GET)) { ${$k} = trim($v); }

//print_r($_GET);


$LIST = array();

$where = "";
$where.= " AND A.member_idx='".$member['mb_no']."' ";
if($search_state) {
	if($search_state=='recruit_ing')        $where.= " AND A.invest_state='Y' AND B.state='' AND B.invest_end_date='' AND recruit_period_end >= CURDATE()";
	else if($search_state=='recruit_end')   $where.= " AND A.invest_state='Y' AND B.state='' AND B.invest_end_date!=''";
	else if($search_state=='invest_cancel') $where.= " AND A.invest_state='N' ";
	else if($search_state=='6.7')           $where.= " AND A.invest_state='Y' AND B.state IN('6','7') ";
	else if($search_state=='2.5')           $where.= " AND A.invest_state='Y' AND B.state IN('2','5') ";
	else if($search_state=='3')             $where.= " AND A.invest_state='Y' AND B.state='3' ";
	else                                    $where.= " AND A.invest_state='Y' AND B.state='$search_state' ";
}
$where.=  ( in_array($member['mb_id'], $CONF['GOODS_OFFICER']) ) ? "" : " AND B.display='Y'";
$where.= " AND B.recruit_amount > 10000";
//if(!$special_user) $where.= " AND B.display='Y' ";

//echo $where."<br>";


$sql = "
	SELECT
		COUNT(A.idx) AS cnt
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx = B.idx
	WHERE 1
		{$where}";
//print_rr($sql);
$row = sql_fetch($sql);
$affect_num = $row['cnt'];

$page = (trim($_GET['page'])) ? trim($_GET['page']) : 1;
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
		LEFT JOIN
			cf_product B ON A.product_idx=B.idx
		WHERE 1
			{$where}
		ORDER BY
			A.idx DESC
		LIMIT ".$start_num.", ".$size;
	//print_rr($sql);
	$result = sql_query($sql);

	while($R = sql_fetch_array($result)){
		array_push($LIST, $R);
	}

	$list_count = count($LIST);

}

// 상품투자리스트 페이지
if($_REQUEST['mode']=='test') {
	$repay_schedule_url = "ajax_product_detail.test.php";
}
else if($_REQUEST['mode']=='latest') {
	$repay_schedule_url = "ajax_product_detail.20200518.php";
}
else {
	$repay_schedule_url = "ajax_product_detail.php";
}

//usleep(300000);

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

<div class="type03">
	<table>
		<colgroup>
			<col style="width:5%">
			<col style="width:%">
			<col style="width:12%">
			<col style="width:%">
			<col style="width:9%">
			<col style="width:16%">
			<col style="width:7%">
			<col style="width:8%">
			<col style="width:7%">
			<col style="width:7%">
		</colgroup>
		<tbody>
			<tr>
				<th>No</th>
				<th>상품명</th>
				<th>투자금액</th>
				<th class="btn"></th>
				<th>진행상태</th>
				<th>투자기간</th>
				<th>이자율(연)</th>
				<th>플랫폼<br>이용료율</th>
				<th>원리금<br>수취증서</th>
				<th class="btn"></th>
			</tr>
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
			<tr>
				<td><?=$num?></td>
				<td style="text-align:left"><a href="/investment/investment.php?prd_idx=<?=$LIST[$i]['product_idx']?>"><?=$LIST[$i]['title']?></a></td>
				<td style="text-align:right"><?=number_format($LIST[$i]['amount'])?> 원</td>
				<td class="btn">
<?
				if( $LIST[$i]['invest_state'] == 'Y' && ($LIST[$i]['open_datetime'] <= G5_TIME_YMDHIS && $LIST[$i]['end_datetime'] >= G5_TIME_YMDHIS) ) {
					if( ($LIST[$i]['recruit_amount'] > $LIST[$i]['total_invest_amount']) && $LIST[$i]['invest_end_date'] == '' && $LIST[$i]['state'] == '') {
						echo "<a href='./funding_cancel.php?idx=".$LIST[$i]['idx']."'><span class='btn_gray3'>투자취소</span></a>\n";
					}
				}
?>
				</td>
				<td><b style="color:<?=$fcolor?>"><?=($LIST[$i]['invest_state']=="N") ? '투자취소' : $product_state;?></b></td>
				<td><?=$print_date_range?></td>
				<td><?=$LIST[$i]['invest_return']?>%</td>
				<td><?=($LIST[$i]['invest_usefee'] > '0.00') ? '월 '.sprintf('%.2f', $LIST[$i]['invest_usefee']/12).'%' : '면제';?></td>
				<td class="btn"><? if($LIST[$i]['invest_state'] == 'Y' && $LIST[$i]['loan_start_date'] != '0000-00-00'){ ?><span class="btn_orange2 certificate_print_btn" data-idx="<?=$LIST[$i]['idx']?>">보기</span></a><? } ?></td>
				<td class="btn"><? if($LIST[$i]['invest_state'] == 'Y') { ?><span class="btn_blue2 funding_detail_btn" data-idx="<?=$LIST[$i]['idx']?>" prd-id="<?=$LIST[$i]['product_idx']?>" >상세보기</span><? } ?></td>
			</tr>
<?
		$num--;
	}
}
else {
	echo "<tr><td colspan='10'>데이터가 없습니다.</td></tr>\n";
}
?>
		</tbody>
	</table>
	<div id="paging_span" class="mt10 mb20 invest_list_paging">
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
					css: { position:'absolute', top:'10px', left:'24%', width:'1000px', margin:'auto', border:'0', cursor:'default' }
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
	<? if ( $loan_start_date < '2021-08-27' ) { ?>  // 기준 일자 이전
			if((navigator.appName == 'Netscape' && navigator.userAgent.search('Trident') != -1) || (agent.indexOf("msie") != -1) && agent.indexOf("chrome") != -1){
				var url = '/deposit/principal_interest_certificate_origin.php?idx='+$(this).attr("data-idx");
			} else {
				var url = '/deposit/principal_interest_certificate.php?idx='+$(this).attr("data-idx");
			}
	<? } else if ( $loan_start_date >= '2021-08-27' ) { ?>  // 기준 일자 이후
			if((navigator.appName == 'Netscape' && navigator.userAgent.search('Trident') != -1) || (agent.indexOf("msie") != -1) && agent.indexOf("chrome") != -1){
				var url = '/deposit/principal_interest_certificate_origin2.php?idx='+$(this).attr("data-idx");
			} else {
				var url = '/deposit/principal_interest_certificate2.php?idx='+$(this).attr("data-idx");
			}
	<? } ?>

	popup_window(url, 'certificate', 'width=936,height=768,left=0,top=0,scrolling=no');
});
</script>

<?

if($_COOKIE['debug_mode']) { echo "<div style='color:#FF6633;font-size:11px;'>".$_SERVER['PHP_SELF']."</div>"; }

@sql_close();
exit;

?>