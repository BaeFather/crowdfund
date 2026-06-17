<?
####################################################
## 사업정보공시(pc, mobile)
####################################################

include_once('./_common.php');

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}


$FINAN_YEAR = sql_fetch("SELECT MIN(biz_year) AS min, MAX(biz_year) AS max FROM cf_biz_info_re WHERE section='1'");
$STAFF_YEAR = sql_fetch("SELECT MIN(biz_year) AS min, MAX(biz_year) AS max FROM cf_biz_info_re WHERE section='2'");
$STOCK_YEAR = sql_fetch("SELECT MIN(biz_year) AS min, MAX(biz_year) AS max FROM cf_biz_info_re WHERE section='3'");


$FINAN = array();		// 재무 현황 배열화
$STAFF = array();		// 임직원 현황 배열화
$STOCK = array();		// 대주주 현황 배열화


$sql = "
	SELECT
		*
	FROM
		cf_biz_info_re
	WHERE 1
		AND section != ''
	ORDER BY
		biz_year DESC";
$res = sql_query($sql);
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {

	$row = sql_fetch_array($res);

	if($row['section']=='1') {
		if(!$last_financial_year) $last_financial_year = $row['biz_year'];
		array_push($FINAN, $row);
	}
	else if($row['section']=='2') {
		if(!$last_staff_year) $last_staff_year = $row['biz_year'];
		array_push($STAFF, $row);
	}
	else if($row['section']=='3') {
		if(!$last_stockholder_year) $last_stockholder_year = $row['biz_year'];
		array_push($STOCK, $row);
	}

}


//$kmonth = date( 'Y-m', strtotime( date("Y-m-d") . ' -1 month' ) );
//$kmonth = date( 'Y-m', strtotime( date("Y-m-01") . ' -1 month' ) );
$kmonth = date( 'Y-m', strtotime( 'first day of '. date("Y-m-d") . ' -1 month' ) );

?>

<link href="status_2022.css" rel="stylesheet">

<!-- 본문내용 START -->
<div id="web">

	<div class="tops">
		<h1><img src="img/status_title.png" alt="사업정보공시"></h1>
	</div>


	<div class="title first">
		<ul>
			<li>
				<span class="h3">투자현황</span>
			</li>
			<li class="date">
				<select id="date_type" name="date_type" class="dates" onchange="go_change_sel(this.value);">
					<option value="monthly">월별</option>
					<option value="yearly">연도별</option>
				</select>
				<select id="ym1" name="ym1" class="month" onchange="get_data(this.value);">
				</select>
			</li>
		</ul>
	</div>
	<table class="investment">
		<colgroup>
			<col style="width:22.34%">
			<col style="width:22.33%">
			<col style="width:22.33%">
			<col style="width:13%">
			<col style="width:13%">
		</colgroup>
		<thead>
			<tr>
				<th>누적대출금액</th>
				<th>누적상환금액</th>
				<th>대출잔액</th>
				<th>연체율 <span>(%)</span></th>
				<th>연체건수 <span>(건)</span></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><div id="tot_amt" class="tot_amt"></div></td>
				<td><div id="tot_repay" class="tot_repay"></div></td>
				<td><div id="tot_remain" class="tot_remain"></div></td>
				<td><div id="overdue_rate" class="overdue_rate"></div></td>
				<td><div id="overdue_cnt" class="overdue_cnt"></div></td>
			</tr>
		</tbody>
	</table>

	<div class="title">
		<ul>
			<li><span class="h3">유형별 투자현황</span></li>
			<li class="date">
				<select id="date_type" name="date_type" class="dates" onchange="go_change_sel2(this.value);">
					<option value="monthly">월별</option>
					<option value="yearly">연도별</option>
				</select>
				<select id="ym2" name="ym2" class="month" onchange="get_data2(this.value);">
				</select>
			</li>
		</ul>
	</div>
	<table class="type_investment">
		<colgroup>
			<col style="width:13%">
			<col style="width:13.67%">
			<col style="width:13.66%">
			<col style="width:13.66%">
			<col style="width:11%">
			<col style="width:8%">
			<col style="width:11%">
		</colgroup>
		<thead>
			<tr align="center">
				<th>상품유형</th>
				<th>누적대출금액<span>(원)</span></th>
				<th>누적상환금액<span>(원)</span></th>
				<th>대출잔액<span>(원)</span></th>
				<th>연체율<span>(%)</span></th>
				<th>연체건수<span>(건)</span></th>
				<th>채권 매각<span>(건)</span></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>부동산PF</th>
				<td><div id="pf_tot_amt" class="pf_tot_amt">0</div></td>
				<td><div id="pf_tot_repay" class="pf_tot_repay">0</div></td>
				<td><div id="pf_tot_remain" class="pf_tot_remain">0</div></td>
				<td><div id="pf_overdue_rate" class="pf_overdue_rate">0</div></td>
				<td><div id="pf_overdue_cnt" class="pf_overdue_cnt">0</div></td>
				<td><div id="pf_bsell_cnt" class="pf_bsell_cnt">0</div></td>
			</tr>
			<tr>
				<th>주택담보</th>
				<td><div id="mgg_tot_amt" class="mgg_tot_amt">0</div></td>
				<td><div id="mgg_tot_repay" class="mgg_tot_repay">0</div></td>
				<td><div id="mgg_tot_remain" class="mgg_tot_remain">0</div></td>
				<td><div id="mgg_overdue_rate" class="mgg_overdue_rate">0</div></td>
				<td><div id="mgg_overdue_cnt" class="mgg_overdue_cnt">0</div></td>
				<td><div id="mgg_bsell_cnt" class="mgg_bsell_cnt">0</div></td>
			</tr>
			<tr>
				<th>매출채권</th>
				<td><div id="hp_tot_amt" class="hp_tot_amt">0</div></td>
				<td><div id="hp_tot_repay" class="hp_tot_repay">0</div></td>
				<td><div id="hp_tot_remain" class="hp_tot_remain">0</div></td>
				<td><div id="hp_overdue_rate" class="hp_overdue_rate">0</div></td>
				<td><div id="hp_overdue_cnt" class="hp_overdue_cnt">0</div></td>
				<td><div id="hp_bsell_cnt" class="hp_bsell_cnt">0</div></td>
			</tr>
			<tr>
				<th>동산</th>
				<td><div id="mvb_tot_amt" class="mvb_tot_amt">0</div></td>
				<td><div id="mvb_tot_repay" class="mvb_tot_repay">0</div></td>
				<td><div id="mvb_tot_remain" class="mvb_tot_remain">0</div></td>
				<td><div id="mvb_overdue_rate" class="mvb_overdue_rate">0</div></td>
				<td><div id="mvb_overdue_cnt" class="mvb_overdue_cnt">0</div></td>
				<td><div id="mvb_bsell_cnt" class="mvb_bsell_cnt">0</div></td>
			</tr>
			<tr>
				<th>합계</th>
				<td><div id="all_tot_amt" class="all_tot_amt">0</div></td>
				<td><div id="all_tot_repay" class="all_tot_repay">0</div></td>
				<td><div id="all_tot_remain" class="all_tot_remain">0</div></td>
				<td><div id="all_overdue_rate" class="all_overdue_rate">0</div></td>
				<td><div id="all_overdue_cnt" class="all_overdue_cnt">0</div></td>
				<td><div id="all_bsell_cnt" class="all_bsell_cnt">0</div></td>
			</tr>
		<tbody>
	</table>

	<div class="title">
		<ul>
			<li>
				<span class="h3">자기계산 투자현황</span>
			</li>
			<li class="date">
				<select id="date_type" name="date_type" class="dates" onchange="go_change_sel3(this.value);">
					<option value="monthly">월별</option>
					<option value="yearly">연도별</option>
				</select>
				<select id="ym3" name="ym3" class="month" onchange="get_data3(this.value);">
				</select>
			</li>
		</ul>
	</div>
	<table class="invest-data" id="inState">
		<thead>
			<tr>
				<th>누적투자금액<span>(원)</span></th>
				<th>투자잔액<span>(원)</span></th>
				<th>연체율<span>(%)</span></th>
				<th>연체건수<span>(건)</span></th>
			</tr>
		</thead>
		<tbody>
			<td align="right"><div id="nujuk_invest_amt" class="nujuk_invest_amt">0</div></td>
			<td align="right"><div id="remain_amt" class="remain_amt">0</div></td>
			<td><div id="self_overdue_rate" class="self_overdue_rate">0</div></td>
			<td><div id="self_overdue_cnt" class="self_overdue_cnt">0</div></td>
		</tbody>
	</table>

	<div class="title">
		<ul>
			<li>
				<span class="h3">채권 매각</span>
			</li>
			<li class="date">
				<select id="year3" name="year3" class="dates" onchange="loadBadDebtLoad(this.value, 'pc');">
					<? for($y=date(Y); $y>=2016; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
				</select>
			</li>
		</ul>
	</div>
	<table class="disposal" id="bsell">
		<colgroup>
			<col width="12%">
			<col width="%">
			<col width="11%">
			<col width="14%">
			<col width="23%">
			<col width="12%">
		</colgroup>
		<thead>
			<tr>
				<th>상품유형</th>
				<th>상품명</th>
				<th style="text-align: right;">채권원금<span>(원)</span></th>
				<th style="text-align: right;">매각금액<span>(원)</span></th>
				<th style="padding-left: 20px;">매각처</th>
				<th>매각일자</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>


	<div class="title">
		<ul>
			<li>
				<span class="h3">수시공시</span>
			</li>
			<li class="date">
				<select id="yearly" name="yearly" class="year">
				<? for($y=$STAFF_YEAR['max']; $y>=$STAFF_YEAR['min']; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
				</select>
			</li>
		</ul>
	</div>
	<div>
		<table class="gongsi">
			<colgroup>
				<col width="20%">
				<col width="20%">
				<col width="60%">
			</colgroup>
			<thead>
				<tr>
					<th>구분</th>
					<th>발생일자</th>
					<th>내용</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan='3'>수시공시 내역이 없습니다.</td>
				</tr>
			</tbody>
		</table>
	</div>



	<div class="hello_member">
		<div>
			<div class="title">
				<ul>
					<li>
						<span class="h3">임직원 현황</span>
					</li>
					<li class="date">
						<select id="yearly" name="yearly" class="year" onchange="loatStaffStatus(this.value, 'pc');">
						<? for($y=$STAFF_YEAR['max']; $y>=$STAFF_YEAR['min']; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
						</select>
					</li>
				</ul>
			</div>

			<table class="member">
				<colgroup>
					<col width="33.34%">
					<col width="33.33%">
					<col width="33.33%">
				</colgroup>
				<thead>
					<tr>
						<th>임직원</th>
						<th>여신심사역</th>
						<th>전문인력</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div>
			<div class="title">
				<ul>
					<li>
						<span class="h3">대주주 현황</span>
					</li>
					<li class="date">
						<select id="yearly" name="yearly" class="year" onclick="loadStockholderStatus(this.value,'pc');">
						<? for($y=$STAFF_YEAR['max']; $y>=$STOCK_YEAR['min']; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
						</select>
					</li>
				</ul>
			</div>

			<table class="stockholder">
				<colgroup>
					<col width="100%">
				</colgroup>
				<thead>
					<tr>
						<th>최대주주</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>

 	<div class="final_info">
		<div>
			<div class="title">
			<ul>
				<li>
					<span class="h3">재무 현황</span>
				</li>
				<li class="date">
					<select id="yearly" name="yearly" class="year" onchange="loadFinancialStatus(this.value);">
					<? for($y=$FINAN_YEAR['max']; $y>=$FINAN_YEAR['min']; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
					</select>
				</li>
			</ul>
			</div>
			<table class="finance">
				<tbody>
				</tbody>
			</table>

		</div>

		<div>
			<div class="title">
				<ul>
					<li>
						<span class="h3">안내사항</span>
					</li>
				</ul>
			</div>
			<div class="wrap_info">
				<table class="info">
					<tbody>
						<tr>
							<th>헬로펀딩 안내사항</th>
							<td><a href="hellofunding_info_20220517.pdf" target="_blank"><img src="img/view.png"></a></td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
	</div>

	<div class="tbl-flex-wrap">
		<div>
			<div class="title">
				<ul>
					<li>
						<span class="h3">온라인 개인정보 관리 실태점검</span>
					</li>
					<li class="date">
						<select id="yearly" name="yearly" class="year">
							<option value="2021">2021</option>
						</select>
					</li>
				</ul>
			</div>
			<table class="online-info">
				<colgroup>
					<col width="50%">
					<col width="50%">
				</colgroup>
				<thead>
					<tr>
						<th>점검일</th>
						<th>점검기관</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>2021년 11월</td>
						<td>한시큐리티(주)</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<div class="structure">
		<p class="h3">대출 구조</p>
		<p><img src="img/status_st.jpg" alt="헬로펀딩 구조도"></p>
	</div>

</div>

<? if(G5_IS_MOBILE) { ?>

<style type="text/css">
@media all and (max-width: 900px) {
	#web  {display:none;}
	#mobile {display:block;}
}
</style>

<div id="mobile">

	<div class="tops"><h1>사업 정보 공시</h1></div>


	<div class="title first">
		<ul>
			<li><span class="h3">투자현황</span></li>
			<li class="date">
				<select id="date_type" name="date_type" class="dates" onchange="go_change_sel(this.value);">
					<option value="monthly">월별</option>
					<option value="yearly">연도별</option>
				</select>
				<select id="ym1" name="ym1" class="month" onchange="get_data(this.value);">
				</select>
			</li>
		</ul>
	</div>
	<div class="wrap_investment">
		<table class="investment">
			<tr>
				<th>누적대출금액</th>
				<td><div class="tot_amt"><?=number_format($row1["tot_loan_amt"])?></div></td>
			</tr>
			<tr>
				<th>누적상환금액</th>
				<td><div class="tot_repay"><?=number_format($row1["tot_repay_amt"])?></div></td>
			</tr>
			<tr>
				<th>대출잔액</th>
				<td><div class="tot_remain"><?=number_format($row1["tot_remain_amt"])?></div></td>
			</tr>
			<tr>
				<th>연체율<span>(%)</span></th>
				<td><div class="overdue_rate"><?=number_format($row2["overdue_rate"])?></div></td>
			</tr>
			<tr>
				<th>연체건수<span>(건)</span></th>
				<td><div class="overdue_cnt"><?=number_format($row2["overdue_cnt"])?></div></td>
			</tr>

		</table>
	</div>

	<div class="title">
		<ul>
			<li><span class="h3">유형별 투자현황</span></li>
			<li class="date">
				<select id="date_type" name="date_type" class="dates" onchange="go_change_sel2(this.value);">
					<option value="monthly">월별</option>
					<option value="yearly">연도별</option>
				</select>
				<select id="ym2" name="ym2" class="month" onchange="get_data2(this.value);">
				</select>
			</li>
		</ul>
	</div>
	<div class="box_type_investment">
		<div class="wrap_type_investment">
			<table class="type_investment">
				<thead>
					<tr>
						<th>상품유형</th>
						<th>누적대출금액<span>(원)</span></th>
						<th>누적상환금액<span>(원)</span></th>
						<th>대출잔액<span>(원)</span></th>
						<th>연체율<span>(%)</span></th>
						<th>연체건수<span>(건)</span></th>
						<th>채권 매각<span>(건)</span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>부동산PF</th>
						<td><div id="pf_tot_amt_m" class="pf_tot_amt">0</div></th>
						<td><div id="pf_tot_repay_m" class="pf_tot_repay">0</div></td>
						<td><div id="pf_tot_remain_m" class="pf_tot_remain">0</div></td>
						<td><div id="pf_overdue_rate_m" class="pf_overdue_rate">0</div></td>
						<td><div id="pf_overdue_cnt_m" class="pf_overdue_cnt">0</div></td>
						<td><div id="pf_bsell_cnt_m" class="pf_bsell_cnt">0</div></td>
					</tr>
					<tr>
						<th>주택담보</th>
						<td><div id="mgg_tot_amt_m" class="mgg_tot_amt">0</div></td>
						<td><div id="mgg_tot_repay_m" class="mgg_tot_repay">0</div></td>
						<td><div id="mgg_tot_remain_m" class="mgg_tot_remain">0</div></td>
						<td><div id="mgg_overdue_rate_m" class="mgg_overdue_rate">0</div></td>
						<td><div id="mgg_overdue_cnt_m" class="mgg_overdue_cnt">0</div></td>
						<td><div id="mgg_bsell_cnt_m" class="mgg_bsell_cnt">0</div></td>
					</tr>
					<tr>
						<th>매출채권</th>
						<td><div id="hp_tot_amt_m" class="hp_tot_amt">0</div></td>
						<td><div id="hp_tot_repay_m" class="hp_tot_repay">0</div></td>
						<td><div id="hp_tot_remain_m" class="hp_tot_remain">0</div></td>
						<td><div id="hp_overdue_rate_m" class="hp_overdue_rate">0</div></td>
						<td><div id="hp_overdue_cnt_m" class="hp_overdue_cnt">0</div></td>
						<td><div id="hp_bsell_cnt_m" class="hp_bsell_cnt">0</div></td>
					</tr>
					<tr>
						<th>동산</th>
						<td><div id="mvb_tot_amt_m" class="mvb_tot_amt">0</div></td>
						<td><div id="mvb_tot_repay_m" class="mvb_tot_repay">0</div></td>
						<td><div id="mvb_tot_remain_m" class="mvb_tot_remain">0</div></td>
						<td><div id="mvb_overdue_rate_m" class="mvb_overdue_rate">0</div></td>
						<td><div id="mvb_overdue_cnt_m" class="mvb_overdue_cnt">0</div></td>
						<td><div id="mvb_bsell_cnt_m" class="mvb_bsell_cnt">0</div></td>
					</tr>
					<tr>
						<th>합계</th>
						<td><div id="all_tot_amt_m" class="all_tot_amt">0</div></td>
						<td><div id="all_tot_repay_m" class="all_tot_repay">0</div></td>
						<td><div id="all_tot_remain_m" class="all_tot_remain">0</div></td>
						<td><div id="all_overdue_rate_m" class="all_overdue_rate">0</div></td>
						<td><div id="all_overdue_cnt_m" class="all_overdue_cnt">0</div></td>
						<td><div id="all_bsell_cnt_m" class="all_bsell_cnt">0</div></td>
					</tr>
				<tbody>
			</table>
		</div>
	</div>

	<div class="title">
		<ul>
			<li>
				<span class="h3">자기계산 투자현황</span>
			</li>
			<li class="date">
				<select id="date_type" name="date_type" class="dates" onchange="go_change_sel3(this.value);">
					<option value="monthly">월별</option>
					<option value="yearly">연도별</option>
				</select>
				<select id="ym3" name="ym3" class="month" onchange="get_data3(this.value);">
				</select>
			</li>
		</ul>
	</div>
	<div class="box_type_invest_hello">
		<div class="wrap_type_invest_hello">
			<table class="invest-data" id="inState">
				<thead>
					<tr>
						<th align="right" width="20%">누적투자금액<span>(원)</span></th>
						<th align="right" width="20%">투자잔액<span>(원)</span></th>
						<th width="25%" style="padding-left: 50px;">연체율<span>(%)</span></th>
						<th width="25%">연체건수<span>(건)</span></th>
					</tr>
				</thead>
				<tbody>
					<td align="right"><div id="nujuk_invest_amt" class="nujuk_invest_amt">0</div></td>
					<td align="right"><div id="remain_amt" class="remain_amt">0</div></td>
					<td style="padding-left: 50px;"><div id="self_overdue_rate" class="self_overdue_rate">0</div></td>
					<td><div id="self_overdue_cnt" class="self_overdue_cnt">0</div></td>
				</tbody>
			</table>
		</div>
	</div>

	<div class="title">
		<ul>
			<li>
				<span class="h3">채권 매각</span>
			</li>
			<li class="date">
				<select id="year3" name="year3" class="dates" onchange="loadBadDebtLoad(this.value, 'mb');">
					<? for($y=date(Y); $y>=2016; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
				</select>
			</li>
		</ul>
	</div>
	<div class="box_disposal">
		<div class="wrap_disposal">
			<table class="disposal" name="bsell">
				<thead>
					<tr>
						<th>상품유형</th>
						<th>상품명</th>
						<th>채권원금</th>
						<th>매각금액</th>
						<th>매각처</th>
						<th>매각일자</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>


	<div class="title">
		<ul>
			<li>
				<span class="h3">수시공시</span>
			</li>
			<li class="date">
				<select id="yearly" name="yearly" class="year">
				<? for($y=$STAFF_YEAR['max']; $y>=$STAFF_YEAR['min']; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
				</select>
			</li>
		</ul>
	</div>
	<div class="wrap_gongsi">
		<table class="gongsi">
			<thead>
				<tr>
					<th>구분</th>
					<th>발생일자</th>
					<th>내용</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan='3'>수시공시 내역이 없습니다.</td>
				</tr>
			</tbody>
		</table>
	</div>




	<div class="title">
		<ul>
			<li>
				<span class="h3">임직원현황</span>
			</li>
			<li class="date">
				<select id="yearly" name="yearly" class="year" onchange="loatStaffStatus(this.value, 'mb');">
				<? for($y=$STAFF_YEAR['max']; $y>=$STAFF_YEAR['min']; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
				</select>
			</li>
		</ul>
	</div>
	<div class="box_member">
		<div class="wrap_member">
			<table class="member">
				<thead>
					<tr>
						<th>임직원</th>
						<th>여신심사역</th>
						<th>전문인력</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>

	<div class="title">
		<ul>
			<li>
				<span class="h3">대주주현황</span>
			</li>
			<li class="date">
				<select id="ym6" name="ym6" class="year" onclick="loadStockholderStatus(this.value,'mb');">
				<? for($y=$STAFF_YEAR['max']; $y>=$STOCK_YEAR['min']; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
				</select>
			</li>
		</ul>
	</div>
	<div class="wrap_stockholder">
		<table class="stockholder">
			<tbody>
			</tbody>
		</table>
	</div>


	<div class="title">
		<ul>
			<li>
				<span class="h3">재무 현황</span>
			</li>
			<li class="date">
				<select id="yearly" name="yearly" class="year" onchange="loadFinancialStatus(this.value);">
				<? for($y=$FINAN_YEAR['max']; $y>=$FINAN_YEAR['min']; $y--) { echo "<option value='".$y."'>".$y."</option>\n"; } ?>
				</select>
			</li>
		</ul>
	</div>
	<div class="wrap_finance">
		<table class="finance">
			<tbody>
			</tbody>
		</table>
	</div>


	<div class="title">
		<ul>
			<li>
				<span class="h3">안내사항</span>
			</li>
		</ul>
	</div>
	<div class="wrap_info">
		<table class="info">
			<tbody>
				<tr>
					<th>헬로펀딩 안내사항</th>
					<td><a href="hellofunding_info_20220517.pdf" target="_blank"><img src="img/view.png"></a></td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="tbl-flex-wrap">
		<div class="title">
			<ul>
				<li>
					<span class="h3">온라인 개인정보 관리 실태점검</span>
				</li>
				<li class="date">
					<select id="yearly" name="yearly" class="year">
						<option value="2021">2021</option>
					</select>
				</li>
			</ul>
		</div>
		<div class="online-info-wrap">
			<table class="online-info">
				<thead>
					<tr>
						<th>점검일</th>
						<th>점검기관</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>2021년 11월</td>
						<td>한시큐리티(주)</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<div class="structure">
		<span class="h3">대출구조</span>
		<p><img src="img/status_m_st.jpg" alt="헬로펀딩 구조도"></p>
	</div>

</div>

<? } ?>



<script>

// total 투자현황
function go_change_sel(gbn) {
	reset_chart1();

	var yy = new Array;
	<? for ($y = date("Y") ; $y>="2016" ; $y--) { ?>
	yy.push("<?=$y?>");
	<? } ?>

	var mm = new Array;
	<? for ($y = date("Y") ; $y>="2016" ; $y--) { ?>
		<? for ($m = 12 ; $m>=1 ; $m--) {
			$m1 = str_pad($m , 2 , "0" , STR_PAD_LEFT);
			if ("$y-$m1">$kmonth) continue;
			if ("$y-$m1"<"2016-09") continue;
			?>
			mm.push("<?=$y?>-<?=$m1?>");
		<? } ?>
	<? } ?>

	var changeItem;

	if (gbn=="monthly") {
		$("select[name=ym1]").attr('class','month');
		changeItem = mm;
	} else if (gbn=="yearly") {
		$("select[name=ym1]").attr('class','year');
		changeItem = yy;
	}

	$("select[name=ym1]").empty();

	for (var count=0 ; count<changeItem.length; count++) {
		var option = $("<option value="+ changeItem[count] + ">"+ changeItem[count] +"</option>");
		$("select[name=ym1]").append(option);
	}

	get_data($("select[name=ym1]").val());
}

function get_data(ym) {
	if (!ym) return;

	$.ajax({
		url : "zip_ajax_gosi.php",
		type : 'post',
		data : {'gb': 'a1', 'ym': ym},
		dataType : "json",
		success: function(data) {

			$(".tot_amt").text(numberToKorean(data.loan_amt)+'원');
			$(".tot_repay").text(numberToKorean(data.repay_amt)+'원');
			$(".tot_remain").text(numberToKorean(data.remain_amt)+'원');

			$(".overdue_rate").text(data.overdue_rate);
			$(".overdue_cnt").text(data.overdue_cnt);
		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});
}
go_change_sel("monthly");

function reset_chart1() {
	$("#tot_amt").text("-");
}


// 유형별 투자현황
function go_change_sel2(gbn) {
	var yy = new Array;
	<? for ($y = date("Y") ; $y>="2016" ; $y--) { ?>
	yy.push("<?=$y?>");
	<? } ?>

	var mm = new Array;
	<? for ($y = date("Y") ; $y>="2016" ; $y--) { ?>
		<? for ($m = 12 ; $m>=1 ; $m--) {
			$m1 = str_pad($m , 2 , "0" , STR_PAD_LEFT);
			if ("$y-$m1">$kmonth) continue;
			if ("$y-$m1"<"2016-09") continue;
			?>
			mm.push("<?=$y?>-<?=$m1?>");
		<? } ?>
	<? } ?>
	var changeItem;

	if (gbn=="monthly") {
		$("select[name=ym2]").attr('class','month');
		changeItem = mm;
	} else if (gbn=="yearly") {
		$("select[name=ym2]").attr('class','year');
		changeItem = yy;
	}

	$("select[name=ym2]").empty();

	for (var count=0 ; count<changeItem.length; count++) {
		var option = $("<option value="+ changeItem[count] + ">"+ changeItem[count] +"</option>");
		$("select[name=ym2]").append(option);
	}

	get_data2($("select[name=ym2]").val());
}


function get_data2(ym) {
	if (!ym) return;

	$.ajax({
		url : "zip_ajax_gosi2.php",
		type : 'post',
		data : {'gb': 'a1', 'ym': ym},
		dataType : "json",
		success: function(data) {

			// 부동산 PF
			$(".pf_tot_amt").text(number_format(data["1"].loan_amt));
			$(".pf_tot_repay").text(number_format(data["1"].repay_amt));
			$(".pf_tot_remain").text(number_format(data["1"].remain_amt));
			$(".pf_overdue_rate").text(data["1"].overdue_rate);
			$(".pf_overdue_cnt").text(data["1"].overdue_cnt);
			$(".pf_bsell_cnt").text(data["1"].bsell_cnt);

			// 주택담보
			$(".mgg_tot_amt").text(number_format(data["2"].loan_amt));
			$(".mgg_tot_repay").text(number_format(data["2"].repay_amt));
			$(".mgg_tot_remain").text(number_format(data["2"].remain_amt));
			$(".mgg_overdue_rate").text(data["2"].overdue_rate);
			$(".mgg_overdue_cnt").text(data["2"].overdue_cnt);
			$(".mgg_bsell_cnt").text(data["2"].bsell_cnt);

			// 매출채권
			$(".hp_tot_amt").text(number_format(data["3"].loan_amt));
			$(".hp_tot_repay").text(number_format(data["3"].repay_amt));
			$(".hp_tot_remain").text(number_format(data["3"].remain_amt));
			$(".hp_overdue_rate").text(data["3"].overdue_rate);
			$(".hp_overdue_cnt").text(data["3"].overdue_cnt);
			$(".hp_bsell_cnt").text(data["3"].bsell_cnt);

			// 동산
			$(".mvb_tot_amt").text(number_format(data["4"].loan_amt));
			$(".mvb_tot_repay").text(number_format(data["4"].repay_amt));
			$(".mvb_tot_remain").text(number_format(data["4"].remain_amt));
			$(".mvb_overdue_rate").text(data["4"].overdue_rate);
			$(".mvb_overdue_cnt").text(data["4"].overdue_cnt);
			$(".mvb_bsell_cnt").text(data["4"].bsell_cnt);

			// 합계
			$(".all_tot_amt").text(number_format(data["tot"].loan_amt));
			$(".all_tot_repay").text(number_format(data["tot"].repay_amt));
			$(".all_tot_remain").text(number_format(data["tot"].remain_amt));
			$(".all_overdue_rate").text(data["tot"].overdue_rate);
			$(".all_overdue_cnt").text(data["tot"].overdue_cnt);
			$(".all_bsell_cnt").text(data["tot"].bsell_cnt);

		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});
}
go_change_sel2("monthly");


// 자기계산 투자현황
function go_change_sel3(gbn) {

	var yy = new Array;
	<? for ($y = date("Y") ; $y>="2016" ; $y--) { ?>
	yy.push("<?=$y?>");
	<? } ?>

	var mm = new Array;
	<? for ($y = date("Y") ; $y>="2016" ; $y--) { ?>
		<? for ($m = 12 ; $m>=1 ; $m--) {
			$m1 = str_pad($m , 2 , "0" , STR_PAD_LEFT);
			if ("$y-$m1">$kmonth) continue;
			if ("$y-$m1"<"2016-09") continue;
			?>
			mm.push("<?=$y?>-<?=$m1?>");
		<? } ?>
	<? } ?>
	var changeItem;

	if (gbn=="monthly") {
		$("select[name=ym3]").attr('class','month');
		changeItem = mm;
	} else if (gbn=="yearly") {
		$("select[name=ym3]").attr('class','year');
		changeItem = yy;
	}

	$("select[name=ym3]").empty();

	for (var count=0 ; count<changeItem.length; count++) {
		var option = $("<option value="+ changeItem[count] + ">"+ changeItem[count] +"</option>");
		$("select[name=ym3]").append(option);
	}

	get_data3($("select[name=ym3]").val());
}

function get_data3(ym) {
	if (!ym) return;

	$.ajax({
		url : "zip_ajax_invest_status.php",
		type : 'post',
		data : {'gb': 'a1', 'ym': ym},
		dataType : "json",
		success: function(data) {
			//console.log(data);

			$(".nujuk_invest_amt").text(number_format(data.nujuk_invest_amt));
			$(".remain_amt").text(number_format(data.remain_amt));
			$(".self_overdue_rate").text(data.self_overdue_rate);
			$(".self_overdue_cnt").text(data.self_overdue_cnt);
		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});
}
go_change_sel3("monthly");


// 채권매각
function loadBadDebtLoad(year, gb) {
	$('.disposal > tbody').empty();

	$.ajax({
		url : "zip_ajax_bad_debt_status.php",
		type : 'post',
		data : { 'year': year, 'gb': gb },
		dataType : "json",
		success: function(data) {
			if(data.result == 'SUCCESS') {
				for(i=0; i<data.sdata.length; i++) {
					print_data  = '<tr>';
					print_data += '<td>' + data.sdata[i].category + '</td>';
					print_data += '<td>' + data.sdata[i].start_num + '</td>';
					print_data += '<td align="right">' + numberFormat(data.sdata[i].recruit_amount) + '</td>';
					print_data += '<td align="right">' + numberFormat(data.sdata[i].sale_amount) + '</td>';
					print_data += '<td style="padding-left: 25px;">' + data.sdata[i].sale_place + '</td>';
					print_data += '<td>' + data.sdata[i].sale_date + '</td>';
					print_data += '</tr>';

					$('.disposal tbody').append(print_data);
				}
			}
			else {
				$('.disposal tbody').append('<tr><td colspan="6" style="text-align:center;">' + data.message + '</td>');
			}
		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});
}
$(document).ready(function() { loadBadDebtLoad("<?=date('Y')?>", "<?=(G5_IS_MOBILE)?'mb':'pc'?>"); });


//재무 현황
function loadFinancialStatus(year) {
<? for($i=0; $i<count($FINAN); $i++) { ?>
	if(year=='<?=$FINAN[$i]['biz_year']?>') $('.finance').html('<tr><th><?=$FINAN[$i]['fin_contents']?></th><td><a href="javascript:;" onClick="popup_window(\'<?php ECHO set_http($FINAN[$i]['fin_url']) ?>\', \'\', \'left=1,top:1,width=1280,height=720,address=no\');"><img src="img/view.png"></a></td></tr>');
<? } ?>
}
$(document).ready(function() { loadFinancialStatus("<?=$FINAN_YEAR['max']?>"); });


//임직원 현황
function loatStaffStatus(year, gb) {
<? for($i=0; $i<count($STAFF); $i++) { ?>
	if(year == "<?=$STAFF[$i]['biz_year']?>") {
		$(".member > tbody").html("<tr><td><?=$STAFF[$i]['emp_member']?>명</td><td><?=$STAFF[$i]['emp_simsa']?>명</td><td><?=$STAFF[$i]['emp_professional']?>명</td></tr>");
	}
<? } ?>
}
$(document).ready(function() { loatStaffStatus("<?=$STAFF_YEAR['max']?>", "<?=(G5_IS_MOBILE)?'mb':'pc';?>"); });


//대주주 현황
function loadStockholderStatus(year, gb) {
<? for($i=0; $i<count($STOCK); $i++) { ?>
	if(year == "<?=$STOCK[$i]['biz_year']?>") {
		$(".stockholder > tbody").html('<tr><td><span style="color:navy"><?=$STOCK[$i]['major_shareholder']?></span></td></tr>');
	}
<? } ?>
}
$(document).ready(function() { loadStockholderStatus("<?=date('Y')?>", "<?=(G5_IS_MOBILE)?'mb':'pc'?>"); });
</script>



<script>

function numberFormat(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function numberToKorean(number){
    var inputNumber  = number < 0 ? false : number;
    var unitWords    = ['', '만', '억', '조', '경'];
    var splitUnit    = 10000;
    var splitCount   = unitWords.length;
    var resultArray  = [];
    var resultString = '';

    for (var i = 0; i < splitCount; i++){
        var unitResult = (inputNumber % Math.pow(splitUnit, i + 1)) / Math.pow(splitUnit, i);
        unitResult = Math.floor(unitResult);
        if (unitResult > 0){
            resultArray[i] = unitResult;
        }
    }

    for (var i = 0; i < resultArray.length; i++){
        if(!resultArray[i]) continue;
        resultString = " " + String(numberFormat(resultArray[i])) + unitWords[i] + resultString;
    }

    return resultString;
}
</script>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>