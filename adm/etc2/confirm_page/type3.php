<?
include_once('./_common.php');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$sub_menu = "920400";
$g5['title'] = "확인서 작성";

include_once ('../../admin.head.php');


$kinds = $_GET['kinds'];
$prdidx = $_POST['prdListChk'];
$mb_no = $_GET['mbno'];


// 주민등록번호
$birth = getJumin($_GET['mbno']);
$birth = substr($birth, 0, 7);
$birth = substr($birth, 0, 6).'-'.substr($birth, 6, 1);

// 조건
$where = " AND B.member_group='L' AND B.mb_level='1'"; 
$where.= " AND B.mb_no='".$mb_no."'"; 

// 해당 상품
$where.= " AND A.idx IN(";
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
# 이자납입내역서
#####################
$sql1 = "
	SELECT 
		A.idx, A.state, A.category, A.mortgage_guarantees, A.recruit_amount, 
		A.loan_mb_no, A.loan_mb_f_no, A.loan_start_date, A.loan_end_date, 
		B.mb_f_no, B.mb_no, B.mb_name, B.mb_co_name, B.member_type, B.mb_addr1, B.mb_addr2
	FROM
		cf_product A
	LEFT JOIN
		g5_member B ON A.loan_mb_no = B.mb_no
	WHERE (1)
		$where
";
$row = sql_fetch($sql1);

$principal = sql_fetch("
	SELECT
		IFNULL(SUM(A.amount),0) AS paid_amount
	FROM
		cf_partial_redemption A
	LEFT JOIN
		cf_product B ON A.product_idx = B.idx
	WHERE 1
		AND A.product_idx='".$row['idx']."'
");


// 상품 상태에 맞게 대출잔액 계산 후, 값 출력
if($row['state']=='1' || $row['state']=='8') {
	// 해당 상품 대출 잔액
	$product_remain = $row['recruit_amount'] - $principal['paid_amount'];
	$product_remain = number_format($product_remain);
} else if($row['state']=='2' || $row['state']=='5') {
	$product_remain = '0';
}

// 대출금액
if($row['recruit_amount']) {
	$row['recruit_amount'] = number_format($row['recruit_amount']);
} else {
	$row['recruit_amount'] = 0;
}

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

// 원리금 및 비용 납입내역
$prd_acct = sql_fetch("	
	SELECT
		A.repay_acct_no,
		(SELECT COUNT(idx) AS cnt FROM cf_product WHERE display='Y' AND recruit_amount >= 10000 AND repay_acct_no=A.repay_acct_no) AS use_product_count
	FROM
		cf_product A
	WHERE
		A.idx='".$row['idx']."'
");

$add_where = "";
$add_where.= ($prd_acct['use_product_count'] > 1) ? " AND repay_prd_idx='".$row['idx']."'" : " AND repay_prd_idx IN('','".$row['idx']."')";

$sql2 = "
	SELECT
		BANK_ID, ACCT_NB, TR_AMT, REMITTER_NM, MEDIA_GBN, ERP_TRANS_DT
	FROM
		IB_FB_P2P_IP
	WHERE 1
		AND CUST_ID='".$row['loan_mb_no']."'
		AND ACCT_NB='".$prd_acct['repay_acct_no']."'
		AND TR_AMT_GBN='20'
		$add_where
";


$res = sql_query($sql2);
$cnt = $res -> num_rows;

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


add_stylesheet('<link rel="stylesheet" href="css/style.css" />', 0);

?>


<div class="form_write">
	<form id="confirmType" name="confirmType" method="post" class="form-horizontal">
		<input type="hidden" name="k_no" value="<?=$kinds?>" />
		<input type="hidden" name="creditor_no" value="<?=$C_INFO['c_no']?>" />
		<input type="hidden" name="loan_no" value="<?=$row['mb_no']?>" />
		<input type="hidden" name="p_idx" value="<?=$row['idx']?>" />
		<input type="hidden" name="category" value="<?=$category?>" />
		<input type="hidden" name="loan_name" value="<?=$member_name?>"/>
		<input type="hidden" name="loan_birth" value="<?=$birth?>"/>
		<input type="hidden" name="loan_addr" value="<?=$mb_addr?>"/>
		<input type="hidden" name="reg_date" value="<?=$date?>"/>

		<h2>이 자 납 입 내 역 서</h2>
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

		<p class="sub-title">◎ 대출정보 	<span class="tbl-cf-txt" style="margin-right: 0;">단위(원)</span></p>
		<table class="f-table">
			<colgroup>
				<col width="16%" />
				<col width="19%" />
				<col width="19%" />
				<col width="23%" />
				<col width="23%" />
			</colgroup>
			<thead>
				<tr>
					<th>종별</th>
					<th>대출실행일</th>
					<th>대출종료일</th>
					<th>대출금</th>
					<th>대출잔액</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select name="loan_kinds" id="loan_kinds" class="form-control input-sm">
							<option value="">::종별 선택::</option>
							<option value="1">주택담보대출</option>
							<option value="2">PF</option>
							<option value="3">ABL</option>
							<option value="4">브릿지</option>
							<option value="9">기타</option>
						</select>
					</td>
					<td><input type="text" name="loan_sdate" value="<?=$row['loan_start_date']?>" class="read-input"/></td>
					<td><input type="text" name="loan_edate" value="<?=$row['loan_end_date']?>" class="read-input"/></td>
					<td><input type="text" name="loan_price" value="<?=$row['recruit_amount']?>" class="read-input"/></td>
					<td><input type="text" name="loan_remain" value="<?=$product_remain?>" class="read-input"/></td>
				</tr>
			</tbody>
		</table>
	
		<p class="sub-title txt02">◎ 원리금 및 비용 납입내역 <button type="button" id="reCnt" class="recnt-btn" onclick="reCount()">재계산</button></p>
		<p class="today">기준일 : <input type="text" name="basic_date" class="form-control input-sm input01 datepicker" value="<?=$date?>"/></p>
		<table id="detailList" class="f-table">
			<colgroup>
				<col width="5%" />
				<col width="18%" />
				<col width="18%" />
				<col width="18%" />
				<col width="18%" />
				<col width="18%" />
				<col width="10%" />
			</colgroup>
			<thead>
				<tr>
					<th rowspan="2">순번</th>
					<th rowspan="2">납입일자</th>
					<th>원금</th>
					<th>이자(입금총액)</th>
					<th colspan="2">비용</th>
				</tr>
				<tr>
					<th>납입금액</th>
					<th>납입금액</th>
					<th><input type="text" class="form-control input-sm" name="price_field1" /></th>
					<th><input type="text" class="form-control input-sm" name="price_field2" /></th>
				</tr>
			</thead>
			<tbody>
			<?
				for($i=0,$num=1; $i<$cnt; $i++,$num++) {
					
					$LIST[$i] = sql_fetch_array($res);
					
					$datetime = substr($LIST[$i]['ERP_TRANS_DT'], 0, 4)."-".substr($LIST[$i]['ERP_TRANS_DT'], 4, 2)."-".substr($LIST[$i]['ERP_TRANS_DT'], 6, 2);

					if($LIST[$i]['TR_AMT']) {
						$insert_amount = substr($LIST[$i]['TR_AMT'], 0, -3);
						// 이자(입금총액) 합계
						$total_insert_amount += $insert_amount;
						$insert_amount = number_format($insert_amount);
					} else {
						$insert_amount = '0';
					}

			?>
				<tr class="row-list">
					<td><input type="hidden" name="count_num[]" /><?=$num?></td>
					<td><input type="text" name="ins_date[]" value="<?=$datetime?>" class="form-control input-sm datepicker" /></td>
					<td class="price-txt">￦ <input type="text" name="ins_principal[]" value="" class="form-control input-sm input-width-auto" onkeyup='inputNumberFormat(this)' /></td>
					<td class="price-txt">￦ <input type="text" name="ins_eja[]" value="<?=$insert_amount?>" class="form-control input-sm input-width-auto" onkeyup='inputNumberFormat(this)' /></td>
					<td class="price-txt">￦ <input type="text" name="field1_price[]" value="" class="form-control input-sm input-width-auto" onkeyup='inputNumberFormat(this)' /></td>
					<td class="price-txt">￦ <input type="text" name="field2_price[]" value="" class="form-control input-sm input-width-auto" onkeyup='inputNumberFormat(this)' /></td>
					<td style="border:none;">
						<div class="del-btn" onclick="delRow(this);">삭제</div>
					</td>
				</tr>
			<?
				}
			?>	
			<? if(!$cnt) { ?>
				<tr class="row-list">
					<td><input type="hidden" name="count_num[]" /><?=$num?></td>
					<td><input type="text" name="ins_date[]" value="<?=$datetime?>" class="form-control input-sm datepicker" /></td>
					<td class="price-txt">￦ <input type="text" name="ins_principal[]" value="" class="form-control input-sm input-width-auto" /></td>
					<td class="price-txt">￦ <input type="text" name="ins_eja[]" value="" class="form-control input-sm input-width-auto" /></td>
					<td class="price-txt">￦ <input type="text" name="field1_price[]" value="" class="form-control input-sm input-width-auto" /></td>
					<td class="price-txt">￦ <input type="text" name="field2_price[]" value="" class="form-control input-sm input-width-auto" /></td>
					<td style="border:none;">
						<div class="del-btn" onclick="delRow(this);">삭제</div>
					</td>
				</tr>
			<? } ?>
				<tr>
					<td colspan='2'>합계</td>
					<td class="price-txt"><span id="totalSumAmount">￦ </span></td>
					<td class="price-txt"><span id="totalSumRemain">￦ <?=number_format($total_insert_amount)?></span></td>
					<td class="price-txt"><span id="totalSumPrice1">￦ </span></td>
					<td class="price-txt"><span id="totalSumPrice2">￦ </span></td>
				</tr>
				<tr>
					<td colspan='6' style="border: 0; padding: 0; text-align: right;">
						<button type="button" class="add-btn" onclick="addRow();">추가</button>
					</td>
				</tr>
			</tbody>
		</table>

		<ul class="bottom-txt">
			<li>◎ 사용목적 : <input type="text" name="use_text" class="form-control input-sm input01" value="금융기관 제출용" /></li>
			<li>◎ 담보내역 : <?=$mb_addr?></li>
			<li>◎ 최근 3개월 이내 10일 이상 연체 사실 : 
				<input type="radio" name="is_overdue" id="overdueY" value="Y" />
				<label for="overdueY">有</label>
				<input type="radio" name="is_overdue" id="overdueN" value="N" checked/>
				<label for="overdueN">無</label>
			</li>
		</ul>

	</form>

	<p class="txt03">위와 같이 원리금 및 부대 비용을 납입하였음을 증명합니다.</p>

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
			"<td><input type='hidden' name='count_num[]' /></td>"+
			"<td><input type='text' name='ins_date[]' class='form-control input-sm datepicker' /></td>"+
			"<td class='price-txt'>￦ <input type='text' name='ins_principal[]' value='' class='form-control input-sm input-width-auto' onkeyup='inputNumberFormat(this)' /></td>"+
			"<td class='price-txt'>￦ <input type='text' name='ins_eja[]' value='' class='form-control input-sm input-width-auto' onkeyup='inputNumberFormat(this)' /></td>"+
			"<td class='price-txt'>￦ <input type='text' name='field1_price[]' value='' class='form-control input-sm input-width-auto' onkeyup='inputNumberFormat(this)' /></td>"+
			"<td class='price-txt'>￦ <input type='text' name='field2_price[]' value='' class='form-control input-sm input-width-auto' onkeyup='inputNumberFormat(this)' /></td>"+
			"<td style='border:none;'><div class='del-btn' onclick='delRow(this);'>삭제</div></td>"+
		"</tr>"
	);

	// datepicker
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
function reCount() { 
	var total_count   = $('input[name="count_num[]"]').length;
	var sum_price = '';
	var sum_eja = '';
	var sum_option1 = '';
	var sum_option2 = '';


	// 추가된 행의 개수만큼 for문
	for(var i=0; i<total_count; i++) {
		var price_val = $('input[name="ins_principal[]"]').eq(i).val();
		var eja_val = $('input[name="ins_eja[]"]').eq(i).val();
		var option1_val = $('input[name="field1_price[]"]').eq(i).val();
		var option2_val = $('input[name="field2_price[]"]').eq(i).val();
		
		price_val = Number(price_val.replace(/,/g, ""));
		eja_val = Number(eja_val.replace(/,/g, ""));
		option1_val = Number(option1_val.replace(/,/g, ""));
		option2_val = Number(option2_val.replace(/,/g, ""));

		sum_price = Number(sum_price);
		sum_eja = Number(sum_eja);
		sum_option1 = Number(sum_option1);
		sum_option2 = Number(sum_option2);


		if($('input[name="ins_principal[]"]').eq(i).val()) {
			sum_price = sum_price + price_val;
		} else {
			$('input[name="ins_principal[]"]').eq(i).val('0');
		}

		if($('input[name="ins_eja[]"]').eq(i).val()) {
			sum_eja = sum_eja + eja_val;
		} else {
			$('input[name="ins_eja[]"]').eq(i).val('0');
		}

		if($('input[name="field1_price[]"]').eq(i).val()) {
			sum_option1 = sum_option1 + option1_val;
		} else {
			$('input[name="field1_price[]"]').eq(i).val('0');
		}

		if($('input[name="field2_price[]"]').eq(i).val()) {
			sum_option2 = sum_option2 + option2_val;
		} else {
			$('input[name="field2_price[]"]').eq(i).val('0');
		}

	}
	
	// 합계 값이 빈 값일 때, 합계를 0으로 표시
	if(sum_price=='') {  
		sum_price = '0';
	} 
	if(sum_eja=='') { 
		sum_eja = '0';
	}
	if(sum_option1=='') {
		sum_option1 = '0';
	}
	if(sum_option2=='') {
		sum_option2 = '0';
	}

	// 합계 금액 3자리마다 , 생성 
	sum_price = sum_price.toLocaleString();
	sum_eja = sum_eja.toLocaleString();
	sum_option1 = sum_option1.toLocaleString();
	sum_option2 = sum_option2.toLocaleString();

	// 합계
	$('#totalSumAmount').text('￦ '+sum_price);
	$('#totalSumRemain').text('￦ '+sum_eja);
	$('#totalSumPrice1').text('￦ '+sum_option1);
	$('#totalSumPrice2').text('￦ '+sum_option2);

}

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
	var loan_kinds = $("input[name='ins_date[]']").length - 1;
			
	// 유효성 체크
	if($('select[name=loan_kinds]').val()=='') {
		alert('대출정보의 종별을 선택해주세요.');
		$('select[name=loan_kinds]').focus();
		return false;
	}

	for(var i=0; i<=loan_kinds; i++) {
		if($("input[name='ins_date[]']").eq(i).val()==''){
				alert('납입일자 값이 입력되지 않았습니다.');
				$("input[name='ins_date[]']").eq(i).focus();
				return false;
		}
	}

	if(confirm('등록하시겠습니까?')) {
		f.action = "./type_insert.php";
		f.method = "POST";
		f.target = "_self";
		f.submit();
		reCount();
	}
}


</script>

<? include_once ('../../admin.tail.php'); ?>