<?
include_once('./_common.php');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$sub_menu = "920400";
$g5['title'] = "확인서 작성";

include_once ('../../admin.head.php');


$kinds = $_GET['kinds'];
$prdidx = $_POST['prdListChk'];
$fno = $_GET['fno'];



// 주민등록번호
$birth = getJumin($_GET['mbno']);
$birth = substr($birth, 0, 7);
$birth = substr($birth, 0, 6).'-'.substr($birth, 6, 1);

// 조건
$where  = " AND B.state IN('1','2','5','8')
						AND A.member_group='L' AND A.mb_level='1'"; 
$where .= " AND B.loan_mb_f_no='".$fno."'"; 

// 해당 상품
$where.= " AND B.idx IN(";
for($i=0,$j=1; $i<count($prdidx); $i++,$j++) {
	$where.= stripslashes("'".$prdidx[$i]."'");
	$where.= ($j < count($prdidx)) ? ",":"";
}
$where.= ")";

// 현재 날짜 가져오기
$date = date("Y-m-d");
$date_y = date("Y");
$date_m = date("m");
$date_d = date("d");


#####################
# 금융거래확인서
#####################
$sql1 = "
	SELECT 
		A.mb_f_no, A.mb_no, A.mb_name, A.mb_addr1, A.mb_addr2, 
		A.mb_co_name, A.member_type, 
		B.category, B.mortgage_guarantees, B.state
	FROM
		g5_member A
	LEFT JOIN
		cf_product B ON A.mb_no = B.loan_mb_no
	WHERE (1)
		$where
	ORDER BY 
		B.idx DESC
";
$row = sql_fetch($sql1);


// 주소
$mb_addr = $row['mb_addr1'].' '.$row['mb_addr2'];

// 상품 카테고리
if($row['category']=='2' && $row['mortgage_guarantees']=='1') {
	$category = '주택담보';
} else if($row['category']=='2' && $row['mortgage_guarantees']=='') {
	$category = '부동산';
}

// 개인, 법인 회원
if($row['member_type']=='1') {
	$member_name = $row['mb_name'];
} else if($row['member_type']=='2') {
	$member_name = $row['mb_co_name'];
}

#########################################################################################

$where2 = " AND B.mb_f_no='".$fno."'"; 

// 해당 상품
$where2.= " AND A.idx IN(";
for($i=0,$j=1; $i<count($prdidx); $i++,$j++) {
	$where2.= stripslashes("'".$prdidx[$i]."'");
	$where2.= ($j < count($prdidx)) ? ",":"";
}
$where2.= ")";

#####################
# 금융거래확인서 거래현황
#####################
$sql2 = "
	SELECT 
		A.idx, A.recruit_amount, A.loan_mb_f_no, A.loan_start_date, A.loan_end_date,
		B.va_bank_code2, B.virtual_account2
	FROM
		cf_product A
	LEFT JOIN
		g5_member B ON A.loan_mb_no = B.mb_no
	WHERE (1)
		$where2
";

$res = sql_query($sql2);
$cnt = $res->num_rows;

#########################################################################################

// 채권자 정보
$C_INFO = sql_fetch("
	SELECT 
		c_no, company_name, company_addr, company_tel
	FROM
		cf_paper_creditor
	ORDER BY
		c_no DESC 
	LIMIT 1
");

#########################################################################################

$where3 = " AND B.loan_mb_no='".$fno."' AND A.invest_state='Y' AND B.state IN('1','2','5','8')";

$where3.= " AND B.idx IN(";
for($i=0,$j=1; $i<count($prdidx); $i++,$j++) {
	$where3.= stripslashes("'".$prdidx[$i]."'");
	$where3.= ($j < count($prdidx)) ? ",":"";

}
$where3.= ")";



add_stylesheet('<link rel="stylesheet" href="css/style.css" />', 0);

?>


<div class="form_write">
	<form id="confirmType" name="confirmType" method="post" class="form-horizontal">
		<input type="hidden" name="k_no" value="<?=$kinds?>" />
		<input type="hidden" name="creditor_no" value="<?=$C_INFO['c_no']?>" />
		<input type="hidden" name="category" value="<?=$category?>" />
		<input type="hidden" name="loan_f_no" value="<?=$row['mb_f_no']?>" />
		<input type="hidden" name="loan_name" value="<?=$member_name?>"/>
		<input type="hidden" name="loan_birth" value="<?=$birth?>"/>
		<input type="hidden" name="loan_addr" value="<?=$mb_addr?>"/>
		<input type="hidden" name="reg_date" value="<?=$date?>"/>

		<h2>금 융 거 래 확 인 서</h2>
		<p class="sub-title">◎ 채무자</p>
		<table class="f-table ft01">
			<tr>
				<th>고객명</th>
				<td><?=$member_name?></td>
			</tr>
			<? if($row['category']=='2' && $row['mortgage_guarantees']=='1') { ?>
			<tr>
				<th>주민등록번호</th>
				<td><?=$birth.'******'?></td>
			</tr>
			<? } ?>
			<tr>
				<th>주소</th>
				<td><?=$mb_addr?></td>
			</tr>
		</table>

		<p class="sub-title">◎ 채권자</p>
		<table class="f-table">
			<tr>
				<th>회사명</th>
				<td><?=$C_INFO['company_name']?></td>
			</tr>
			<tr>
				<th>주소</th>
				<td><?=$C_INFO['company_addr']?></td>
			</tr>
			<tr>
				<th>연락처</th>
				<td><?=$C_INFO['company_tel']?></td>
			</tr>
		</table>

		<p class="sub-title">◎ 대출금 거래현황
			<button type="button" id="reCnt" class="recnt-btn">재계산</button>
			<span class="tbl-cf-txt" style="margin-right: 73px;">단위(원)</span>
		</p>
		<table id="detailList" class="f-table ft02">
			<colgroup>
				<col width="10%" />
				<col width="10%" />
				<col width="10%" />
				<col width="20%" />
				<col width="20%" />
				<col width="10%" />
				<col width="5%" />
			</colgroup>
			<thead>
				<tr>
					<th>종별</th>
					<th>계약일자</th>
					<th>대출기한</th>
					<th>대출금액</th>
					<th>잔액</th>
					<th>비고</th>
				</tr>
			</thead>
			<tbody>
				<?
					for($i=0; $i<$cnt; $i++) {
						$LIST[$i] = sql_fetch_array($res);
						
						// 상품 상태 
						$sql = "
							SELECT
								state
							FROM
								cf_product
							WHERE 1
								AND idx='".$LIST[$i]['idx']."'
						";
						$row = sql_fetch($sql);

						// 총 대출금액
						$amt_sql = "
							SELECT
								IFNULL(SUM(A.amount),0) AS amount
							FROM
								cf_product_invest A
							LEFT JOIN
								cf_product B ON A.product_idx = B.idx
							WHERE 1
								AND B.idx='".$LIST[$i]['idx']."'
								AND B.state IN('1','2','5','8') AND A.invest_state='Y'
						";
						$amt = sql_fetch($amt_sql);
						
						// 상환원금
						$paid_sql = "
							SELECT
								IFNULL(SUM(A.amount),0) AS paid_amount
							FROM
								cf_partial_redemption A
							LEFT JOIN
								cf_product B ON A.product_idx = B.idx
							WHERE 1
								AND A.product_idx='".$LIST[$i]['idx']."'
						";
						$paid = sql_fetch($paid_sql);
						
						
						// 상품 상태에 맞게 대출잔액 계산 후, 값 출력
						if($row['state']=='1' || $row['state']=='8') {
							$remain = $amt['amount'] - $paid['paid_amount'];  // 대출잔액
						} else if($row['state']=='2' || $row['state']=='5') {
							$remain = '0';
						}
						
						// 총 대출금액이 있을 경우 
						if($LIST[$i]['recruit_amount']) {
							$tot_recruit_amount += $LIST[$i]['recruit_amount'];  // 대출금액 합계
							$tot_remain += $remain;  // 대출잔액 합계
						} else {
							$LIST[$i]['recruit_amount'] = '0';
						}
						
				?>
				<tr class="row-list">
					<td><input type="hidden" name="loan_kinds[]" value="<?=$category?>" class="read-input" /><?=$category?></td>
					<td><input type="hidden" name="loan_sdate[]" value="<?=$LIST[$i]['loan_start_date']?>" class="read-input" /><?=$LIST[$i]['loan_start_date']?></td>
					<td><input type="hidden" name="loan_edate[]" value="<?=$LIST[$i]['loan_end_date']?>" class="read-input" /><?=$LIST[$i]['loan_end_date']?></td>
					<td class="price-txt"><input type="hidden" name="loan_price[]" value="<?=$LIST[$i]['recruit_amount']?>" class="read-input" />￦ <?=number_format($LIST[$i]['recruit_amount'])?></td>
					<td class="price-txt"><input type="hidden" name="loan_remain[]" value="<?=$remain?>" class="read-input" />￦ <?=number_format($remain)?></td>
					<td><input type="text" name="loan_note[]" value="<?=$LIST[$i]['loan_note']?>" class="form-control input-sm" /></td>
					<td style="border:none;">
						<input type="hidden" name="prdidx[]" value="<?=$LIST[$i]['idx']?>" class="form-control input-sm" />
						<div class="del-btn" onclick="delRow(this);">삭제</div>
					</td>
				</tr>
				<?
					}
					// 리스트가 존재할 경우
					if($cnt) {
				?>
				<tr>
					<td>합계</td>
					<td><span id="totalCount"><?=$cnt?></span>건</td>
					<td></td>
					<td class="price-txt"><span id="totalSumAmount">￦ <?=number_format($tot_recruit_amount)?></span></td>
					<td class="price-txt"><span id="totalSumRemain">￦ <?=number_format($tot_remain)?></span></td>
					<td></td>
				</tr>
				<?
					}
				?>
				<tr>
					<td style="border: 0; padding: 0; text-align: left;">
						<button type="button" class="add-btn" onclick="addRow();">추가</button>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="sub-title">◎ 담보내용</p>
		<table class="f-table ft02">
			<colgroup>
				<col width="15%" />
				<col width="10%" />
				<col width="10%" />
				<col width="15%" />
				<col width="20%" />
				<col width="10%" />
				<col width="10%" />
				<col width="10%" />
			</colgroup>
			<thead>
				<tr>
					<th>소재지</th>
					<th>소유자</th>
					<th>관계</th>
					<th>종류</th>
					<th>감정가격</th>
					<th>감정일자</th>
					<th>설정내용</th>
					<th>비고</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?=$mb_addr?></td>
					<td><?=$member_name?></td>
					<td>본인</td>
					<td><input type="hidden" name="dambo_kinds" value="주택담보" class="form-control input-sm" />주택담보</td>
					<td class="price-txt">￦ <input type="text" name="dambo_price" value="" class="form-control input-sm input01" onkeyup='inputNumberFormat(this)' /></td>
					<td><input type="text" name="dambo_date" value="" class="form-control input-sm datepicker" /></td>
					<td>근저당권</td>
					<td>
						<select name="dambo_note" id="dambo_note" class="form-control input-sm">
							<option value="">::선택::</option>
							<option value="1">선순위</option>
							<option value="2">후순위</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="overdue-txt-box">
			<p class="txt01">◎ 기준일 현재 연체(연체대출금 및 지급보증대지급금 보유 또는 이자 분할상환금, 분할상환원리금지체 포함) 여부 :</p>
			<input type="radio" name="is_overdue" value="Y" />有
			<input type="radio" name="is_overdue" value="N" checked/>無
		</div>
		
		<p class="sub-title">◎ 최근 3개월 이내 10일 이상 계속된 연체 명세</p>
		<table class="f-table overdue-tbl">
			<colgroup>
				<col width="15%" />
				<col width="15%" />
				<col width="20%" />
				<col width="15%" />
				<col width="10%" />
				<col width="10%" />
				<col width="15%" />
			</colgroup>
			<thead>
				<tr>
					<th rowspan="2">종별</th>
					<th rowspan="2">연체발생일</th>
					<th colspan="2">연체금액</th>
					<th rowspan="2">연체정리일</th>
					<th rowspan="2">연체일수</th>
					<th rowspan="2">비고</th>
				</tr>
				<tr>
					<th>원금</th>
					<th>이자</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="7">해당 사항 없음</td>
				</tr>
			</tbody>
		</table>
	</form>
	<p class="txt03">위와 같이 이상 없음을 확인함.</p>

	<ul class="bottom-fix-txt">
		<li><?=$date_y.'년 '.$date_m.'월 '.$date_d.'일'?></li>
		<li><?=$C_INFO['company_name']?> <span>(인)</span></li>
	</ul>

	<ul class="btn-box">
		<li><button type="button" class="btn btn-danger" onclick="history.back(-1);">취소</button></li>
		<li><button type="button" class="btn btn-primary" onclick="saveData();">저장</button></li>
	</ul>
</div>


<script type="text/javascript">
function delRow(target) {
	var tr = $(target).parent().parent();
	tr.remove();
}

function addRow() {
	var tbl = document.getElementById('detailList');

	$(tbl).find('.row-list').last().after(
		"<tr>"+
			"<td><input type='text' name='loan_kinds[]' class='form-control input-sm' /></td>"+
			"<td><input type='text' name='loan_sdate[]' class='form-control input-sm datepicker' /></td>"+
			"<td><input type='text' name='loan_edate[]' class='form-control input-sm datepicker' /></td>"+
			"<td class='price-txt'>￦ <input type='text' name='loan_price[]' class='form-control input-sm input-width-auto' onkeyup='inputNumberFormat(this)' /></td>"+
			"<td class='price-txt'>￦ <input type='text' name='loan_remain[]' class='form-control input-sm input-width-auto' onkeyup='inputNumberFormat(this)' /></td>"+
			"<td><input type='text' name='loan_note[]' class='form-control input-sm' /></td>"+
			"<td style='border:none;'><div class='del-btn' onclick='delRow(this);'>삭제</div></td>"+
		"</tr>"
	);

	$(document).find('.datepicker').removeClass('hasDatepicker').datepicker({
		dateFormat: "yy-mm-dd",
		changeYear: true,
		changeMonth: true,
		showMonthAfterYear: true, 
		dayNamesMin: ['월', '화', '수', '목', '금', '토', '일'], 
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월']
	});

}

// 재계산 클릭 시 합계 반영
$('#reCnt').on('click', function() {
	var total_count = $('input[name="loan_kinds[]"]').length;
	var sum_amt = 0;
	var sum_rem = 0;
	

	// 추가된 행의 개수만큼 for문
	for(var i=0; i<total_count; i++) {
		var total_amount = $('input[name="loan_price[]"]').eq(i).val();
		var total_remain = $('input[name="loan_remain[]"]').eq(i).val();

		if(!total_amount || !total_remain) {

			alert('추가된 행에 빈 값이 있습니다.');
			return false;

		} else {

			total_amount = total_amount.replace(/,/g, "");
			total_remain = total_remain.replace(/,/g, "");

			sum_amt += parseInt(total_amount);
			sum_rem += parseInt(total_remain);

		}
	}

	sum_amt = sum_amt.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
	sum_rem = sum_rem.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");

	// 합계
	$('#totalCount').text(total_count);
	$('#totalSumAmount').text('￦ '+sum_amt);
	$('#totalSumRemain').text('￦ '+sum_rem);
	

});


// input 숫자 입력시 자동 콤마
function inputNumberFormat(obj) {
    obj.value = comma(uncomma(obj.value));
}

function comma(str) {
    str = String(str);
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}

function uncomma(str) {
    str = String(str);
    return str.replace(/[^\d]+/g, '');
}

// 저장
function saveData() {
	var f = document.confirmType;
	var loan_kinds = $("input[name='loan_kinds[]']").length - 1;

		
	// 유효성 체크
	for(var i=0; i<=loan_kinds; i++) {
		if($("input[name='loan_kinds[]']").eq(i).val()==''){
				alert('종별 값이 입력되지 않았습니다.');
				$("input[name='loan_kinds[]']").eq(i).focus();
				return false;
		} else if($("input[name='loan_sdate[]']").eq(i).val()==''){
				alert('계약일자 값이 입력되지 않았습니다.');
				$("input[name='loan_sdate[]']").eq(i).focus();
				return false;
		} else if($("input[name='loan_edate[]']").eq(i).val()==''){
				alert('대출기한 값이 입력되지 않았습니다.');
				$("input[name='loan_edate[]']").eq(i).focus();
				return false;
		} else if($("input[name='loan_price[]']").eq(i).val()==''){
				alert('대출금액 값이 입력되지 않았습니다.');
				$("input[name='loan_price[]']").eq(i).focus();
				return false;
		} else if($("input[name='loan_remain[]']").eq(i).val()==''){
				alert('잔액 값이 입력되지 않았습니다.');
				$("input[name='loan_remain[]']").eq(i).focus();
				return false;
		}
	}

	if(confirm('등록하시겠습니까?')) {
		f.action = "./type_insert.php";
		f.method = "POST";
		f.target = "_self";
		f.submit();
	}
}


</script>


<? include_once ('../../admin.tail.php'); ?>