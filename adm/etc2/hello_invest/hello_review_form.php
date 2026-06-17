<?

include_once('./_common.php');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$sub_menu = "950100";
$g5['title'] = $menu['menu950'][2][1];

include_once ('../../admin.head.php');

$ymd = date('Y-m-d');
$srch_ym = date('Y-m');
$before_ymd = date('Y-m-d', strtotime($day.' -1 day'));

$where = "AND A.member_idx='48343' AND A.invest_state='Y'";

if($title) $where.= " AND B.title='$title'";

if($mode=='modify') {   // 수정할 때
	$sql = "
		SELECT
			*
		FROM
			hello_self_review
		WHERE
			idx = '$idx'
	";
	$row = sql_fetch($sql);

	if($row['h2_type'] == '1') {
		$category = '주택담보대출';
	} else if($row['h2_type'] == '2') {
		$category = '매출채권';
	} else if($row['h2_type'] == '3') {
		$category = 'PF';
	} else if($row['h2_type'] == '4'){
		$category = '동산';
	}

} else {				// 등록할 때
	$sql = "
		SELECT
			idx, start_date, end_date, price
		FROM
			hello_self_invest
		WHERE
			start_date >= '".$ymd."' OR end_date >= '".$ymd."'
	";
	$row = sql_fetch($sql);

	$nujuk_invest_amt = sql_fetch("
		SELECT
			IFNULL(SUM(A.amount),0) AS amount
		FROM
			cf_product_invest A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.member_idx='48343' AND A.invest_state='Y'
			AND B.state IN('1','2','4','5','8','9')
			AND LEFT(B.loan_start_date, 7) <= '".$srch_ym."'
		")['amount'];

	$paid_principal = sql_fetch("
		SELECT
			IFNULL(SUM(principal),0) AS principal
		FROM
			cf_product_give
		WHERE 1
			AND member_idx='48343'
			AND LEFT(banking_date, 7) <= '".$srch_ym."'
		")['principal'];

	// 투자잔액
	$invest_remain = $nujuk_invest_amt - $paid_principal;

	// 투자비율
	if(!$row['price']) {
		$row['price'] = 0;
		$invest_perc = 0;
	} else {
		$invest_perc = ($invest_remain/$row['price'])*100;
	}

	// 검토 시점의 전체 연체율

	$sql2 = "
		SELECT
			overdue_perc
		FROM
			cf_loan_repay_status
		WHERE (1)
			AND tDate = '".$before_ymd."'
			AND	g_type = 'A'
	";
	$row2 = sql_fetch($sql2);


}

// 투자 가능(현재 모집 중인 상품) 리스트 SELECT
$prdsql = "
	SELECT 
		idx, title
	FROM
		cf_product
	WHERE
		display='Y' AND isTest='' AND state='' AND invest_end_date='' AND start_datetime<=NOW()
	GROUP BY
		idx
	ORDER BY 
		idx desc
";

$prdres = sql_query($prdsql);


add_stylesheet('<link rel="stylesheet" href="css/hello_invest.css" />', 0);

?>

<div class="tbl_head02 tbl_wrap" id="helloReviewForm">
	<h3>자기계산투자 적정성 검토(안)</h3>

	<form id="frmReview" name="frmReview" method="post" class="form-horizontal">
		<input type="hidden" name="mode" value="<?=$mode?>" />

		<? if($mode == 'modify') { ?>
		<input type="button" value="PDF 저장" class="pdf-btn" id="pdfDownload" onclick="pdfDown(<?=$idx?>)"/>
		<? } ?>

		<p class="tit">1. 자기자본 및 연체율 검토</p>
		<table>
			<thead>
				<tr>
					<th colspan='2' width='25%'>구분</th>
					<th width='30%'>값</th>
					<th width='35%'>기준</th>
					<th width='10%'>판단</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan='2'>최근 결산(2020.12) 기준 자기자본</td>
					<td align='right'><input type="text" name="invest_price" class="input-data" value="<?if($mode=='modify') {echo $row['h1_invest_price'];} else {echo number_format($row['price']);}?>" readonly /></td>
					<td>자기자본 100%내의 범위에서만 투자 가능</td>
					<td></td>
				</tr>
				<tr>
					<td rowspan='2'>검토 시점의 자기계산투자 총 잔액<br />(자기자본 대비 자기계산투자 잔액비)</td>
					<td>금</td>
					<td align='right'><input type="text" name="remain" class="input-data" value="<?if($mode=='modify') {echo $row['h1_remain'];} else {echo number_format($invest_remain);}?>" readonly /></td>
					<td rowspan='2'>자기자본 100%내의 범위에서만 투자 가능<br />(100% 초과 시 부적정)</td>
					<td rowspan='2'>
						<select name="res1" id="res1" class="form-control input-sm">
							<option value="">선택</option>
							<option value="Y" <?if($res1=='Y' || $row['h1_res1']=='Y') echo"selected";?>>적정</option>
							<option value="N" <?if($res1=='N' || $row['h1_res1']=='N') echo"selected";?>>부적정</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>비율</td>
					<td align='right'><input type="text" name="perc" class="input-data" value="<?if($mode=='modify') {echo floatRtrim(floatCutting($row['h1_perc'], 2));} else {echo floatRtrim(floatCutting($invest_perc, 2));}?>" readonly />%</td>
				</tr>
				<tr>
					<td colspan='2'>검토 시점의 전체 연체율</td>
					<td align='right'><input type="text" name="overdue_perc" class="input-data" value="<?if($mode=='modify') {echo $row['h1_overdue_perc'];} else {echo $row2['overdue_perc'];}?>" readonly />%</td>
					<td>연체율 10% 초과 시 부적정</td>
					<td>
						<select name="res2" id="res2" class="form-control input-sm">
							<option value="">선택</option>
							<option value="Y" <?if($res2=='Y' || $row['h1_res2']=='Y') echo"selected";?>>적정</option>
							<option value="N" <?if($res2=='N' || $row['h1_res2']=='N') echo"selected";?>>부적정</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="tit">2. LTV 및 동일인 한도 검토</p>
		<table>
			<thead>
				<tr>
					<th colspan='2' width='25%'>구분</th>
					<th width='30%'>값</th>
					<th width='35%'>기준</th>
					<th width='10%'>판단</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan='2'>상품명</td>
					<td>
						<select name="title" id="title" class="form-control input-sm" <?=($mode == 'modify') ? 'disabled' : ''?>>
							<?
								if($mode=='modify') {
									echo '<option value="'.$row['h2_prdidx'].'">'.$row['h2_title'].'</option>';
								} else {
									echo '<option value="">상품 선택</option>';
								}
								while($prdrow = sql_fetch_array($prdres)) {
									$selected = ($prdrow['idx']==$row['h2_prdidx']) ? 'selected' : '';
									echo '<option value="'.$prdrow['idx'].'" '.$selected.'>'.$prdrow['title'].'</option>';
								}
							?>
						</select>
					</td>
					<td>-</td>
					<td></td>
				</tr>
				<tr>
					<td colspan='2'>상품구분</td>
					<td><input type="text" class="input-data input-align" name="type" value="<?=$category?>" id="prdType" readonly /></td>
					<td>구분 : 주택담보대출(주담대) / 매출채권 / PF</td>
					<td></td>
				</tr>
				<tr>
					<td colspan='2'>총 모집금액</td>
					<td align='right'><input type="text" class="input-data" name="recruit_amount" value="<?=$row['h2_recruit_amount']?>" id="prdTotalAmount" readonly /></td>
					<td>-</td>
					<td></td>
				</tr>
				<tr>
					<td>검토일 기준 모집된 금액</td>
					<td>금</td>
					<td align='right'><input type="text" class="input-data" name="live_amount" value="<?=$row['h2_live_amount']?>" id="prdLiveAmount" readonly /></td>
					<td rowspan='4'>총 모집금액 대비 모집된 금액이 80%이상 시 투자가능</td>
					<td rowspan='4'>
						<select name="res3" id="res3" class="form-control input-sm">
							<option value="">선택</option>
							<option value="Y" <?if($res3=='Y' || $row['h2_res3']=='Y') echo"selected";?>>적정</option>
							<option value="N" <?if($res3=='N' || $row['h2_res3']=='N') echo"selected";?>>부적정</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>총 모집금액 대비 모집된 금액의 비율</td>
					<td>비율</td>
					<td align='right'><input type="text" class="input-data" name="tot_perc" value="<?=$row['h2_tot_perc']?>" id="prdLivePerc" readonly />%</td>
				</tr>
				<tr>
					<td>자기계산 투자 요청 금액</td>
					<td>금</td>
					<td align='right'><input type="text" class="form-control input-sm" name="request_price" value="<?=number_format($row['h2_request_price'])?>" id="RequestPrice" autocomplete='off' onclick="valReset(id);"/></td>
				</tr>
				<tr> 
					<td>총 모집금액 대비 자기계산 투자비율</td>
					<td>비율</td>
					<td align='right'><input type="text" class="input-data" name="hello_perc" value="<?=$row['h2_hello_perc']?>" id="helloInvestPerc" readonly />%</td>
				</tr>
				<tr>
					<td colspan='2'>LTV</td>
					<td align='right'><input type="text" class="input-data" name="ltv" value="<?=$row['h2_ltv']?>" id="prdLtv" readonly />%</td>
					<td>주택담보대출상품의 경우만 입력<br />LTV 70% 초과시 부적정</td>
					<td>
						<select name="res4" id="res4" class="form-control input-sm">
							<option value="">선택</option>
							<option value="Y" <?if($res4=='Y' || $row['h2_res4']=='Y') echo"selected";?>>적정</option>
							<option value="N" <?if($res4=='N' || $row['h2_res4']=='N') echo"selected";?>>부적정</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan='2'>차입자명</td>
					<td><input type="text" class="input-data input-align" name="loan_mb_name" value="<?=$row['h2_loan_mb_name']?>" id="prdLoaner" readonly /></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>차입자에 대한 당사 자기계산투자 잔액</td>
					<td>금</td>
					<td align='right'><input type="text" class="input-data" name="loan_remain" value="<?=$row['h2_loan_remain']?>" id="loan_remain" readonly /></td>
					<td rowspan='2'>동일차주에게 자기자본대비 5%이상 투자 부적정<br />(본 투자를 포함한 자기자본 대비 자기계산투자 잔액비가 5% 이하일 것)</td>
					<td rowspan='2'>
						<select name="res5" id="res5" class="form-control input-sm">
							<option value="">선택</option>
							<option value="Y" <?if($res5=='Y' || $row['h2_res5']=='Y') echo"selected";?>>적정</option>
							<option value="N" <?if($res5=='N' || $row['h2_res5']=='N') echo"selected";?>>부적정</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>(자기자본 대비 자기계산투자 잔액비)</td>
					<td>비율</td>
					<td align='right'><input type="text" class="input-data" name="loan_perc" value="<?=$row['h2_loan_perc']?>"  id="loan_perc" readonly />%</td>
				</tr>
			</tbody>
		</table>

		<ul class="review-res">
			<li>검토결과</li>
			<li>
				<?
					if($row['resYN'] == 'Y') {
						echo '적정';
					} else if($row['resYN']  == 'N') {
						echo '부적정';
					} else {
						echo '';
					}
				?>
			</li>
		</ul>

	</form>
	<ul class="notice">
		<li>- 자기계산투자 적정성 검토서는 운영기획팀 작성한다.</li>
		<li>- 본 검토서 상 부적정 사항이 있는 경우 자기계산투자는 불가하다.</li>
		<li>- 검토자(작성자)는 본 내용을 정확하게 작성하여야 한다.</li>
		<li>- 검토자(작성자)는 본 내용을 작성하여 준법감시인에게 사전 승인을 얻어야한다.</li>
	</ul>

	<p id="saveDate" class="save-date">
		<?
		if($row['reg_date']) {
			$reg_date = str_replace('-', '.', $row['reg_date']);
			echo substr($reg_date, 0, -9);
		}
		?>
	</p>
	<button id="submitData" class="save-btn" value="<?=($mode == "modify") ? 'modify' : 'save';?>" onclick="saveForm(<?=$idx?>);"><?=($mode == "modify") ? '수정' : '저장';?></button>
</div>


<script type="text/javascript">
$(function() {

	$('#title').on('change', function() {
		var opt = $(this).val();

		//console.log(opt);

		if(opt != "") {
			$.ajax({
				type: "POST",
				url: "ajax_hello_review.info.php",
				data: {'opt' : opt},
				dataType : "json",
				success: function(data) {
					console.log(data);

					$('#prdType').attr('value', data.category);
					$('#prdTotalAmount').attr('value', numberFormat(data.recruit_amount));
					$('#prdLiveAmount').attr('value', numberFormat(data.live_invest_amount));
					$('#prdLivePerc').attr('value', data.live_invest_perc);
					$('#prdLtv').attr('value', data.ltv);
					$('#prdLoaner').attr('value', data.mb_name);
					$('#loan_remain').attr('value', numberFormat(data.remain_principal));
					$('#loan_perc').attr('value', data.remain_perc);

				},
				error: function(xhr, status, error) {
					alert('Error');
				}
			});

		}  // if문 end

	});

	$('#RequestPrice').on('keyup', function() {
		var requestPrice = $(this).val();
		var opt = $('#title').val();

		if(requestPrice != "" && opt != "") {
			$.ajax({
				type: "POST",
				url: "ajax_hello_review.req.price.php",
				data: {'opt' : opt, 'requestPrice' : requestPrice},
				dataType: "json",
				success: function(data) {

					$('#helloInvestPerc').attr('value', data.perc);

				},
				error: function(xhr, status, error) {
					console.log('Error');
				}
			});
		}
	});


});

function numberFormat(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function saveForm(idx) {

	var f = document.getElementById('frmReview');

	if(f.mode.value == 'modify') {
		if(confirm('수정하시겠습니까?')) {
			$('select').attr('disabled', false);

			f.mode.value = 'modify';
			f.action = 'hello_review_update.php?idx='+idx;
			f.submit();
		}
	} else {
		f.mode.value = "save";
		f.action = "./hello_review_update.php";
		f.target = "_self";
		f.submit();
	}

}


// PDF 저장
function pdfDown(idx) {
	var f = document.getElementById('frmReview');

	f.action = './review_pdf.download.php?idx='+idx;
	f.method = 'post';
	f.submit();
}

// input 값 초기화
function valReset(id) {
	var input = document.getElementById(id);
	if(input != "") {
		input.value = "";
	}
}
</script>


<? include_once ('../../admin.tail.php'); ?>