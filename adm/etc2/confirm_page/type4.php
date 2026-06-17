<?
include_once('./_common.php');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$sub_menu = "920400";
$g5['title'] = "확인서 작성";

include_once ('../../admin.head.php');


$kinds   = $_GET['kinds'];
$prdidx  = $_POST['prdListChk'];
$mb_f_no = $_GET['fno'];

$where = " AND B.member_group='L' AND B.mb_level='1'"; 
$where.= " AND A.loan_mb_f_no='".$mb_f_no."'"; 

// 해당 상품
$where.= " AND A.idx IN(";
for($i=0,$j=1; $i<count($prdidx); $i++,$j++) {
	$where.= stripslashes("'".$prdidx[$i]."'");
	$where.= ($j < count($prdidx)) ? ",":"";
}
$where.= ")";

// 현재 날짜 가져오기
$date		= date("Y-m-d");
$date_y = date("Y");
$date_m = date("m");
$date_d = date("d");

// 현재 월초, 월말 구하기
$start_date = date("Y-m-d", mktime(0, 0, 0, $date_m , 1, $date_y));
$end_date		= date("Y-m-d", mktime(0, 0, 0, $date_m+1 , 0, $date_y));
$interval   = strtotime($end_date) - strtotime($start_date);
$days				= floor($interval/86400) + 1;

#####################
# 이자내역서
#####################
$sql1 = "
	SELECT 
		A.idx, A.title, A.state, A.gr_idx, A.category, A.mortgage_guarantees, 
		A.loan_mb_f_no, A.loan_start_date, A.loan_interest_rate, A.repay_acct_no, 
		B.mb_f_no, B.mb_no, B.mb_name, B.mb_co_name, B.member_type, 
		B.mb_addr1, B.mb_addr2, B.mb_level, B.member_group,
		C.loan_end_date, C.loan_amount
	FROM
		cf_product A
	LEFT JOIN 
		g5_member B ON A.loan_mb_no = B.mb_no
	LEFT JOIN
		cf_pf_accounts_rcv C ON A.gr_idx = C.group_idx
	WHERE (1)
		$where
	LIMIT 1
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

$sql2 = "
	SELECT 
		A.idx, A.gr_idx, A.title, A.loan_mb_no, 
		A.loan_mb_f_no, A.state, A.repay_acct_no, 
		B.mb_no, B.mb_f_no, B.mb_name, B.mb_co_name,
		C.bank_cd, C.acct_no, C.cmf_nm
	FROM
		cf_product A 
	LEFT JOIN
		g5_member B ON A.loan_mb_no = B.mb_no
	LEFT JOIN
		IB_vact_hellocrowd C ON A.repay_acct_no = C.acct_no
	WHERE (1)
		 AND B.mb_f_no='".$mb_f_no."'
	   AND A.state='1'
";

$res = sql_query($sql2);
$cnt = $res->num_rows;


add_stylesheet('<link rel="stylesheet" href="css/style.css" />', 0);

?>


<div class="form_write">
	<form id="confirmType" name="confirmType" method="post" class="form-horizontal">
		<input type="hidden" name="k_no" value="<?=$kinds?>" />
		<input type="hidden" name="creditor_no" value="<?=$C_INFO['c_no']?>" />
		<input type="hidden" name="category" value="<?=$category?>" />
		<input type="hidden" name="loan_f_no" value="<?=$row['mb_f_no']?>" />
		<input type="hidden" name="loan_name" value="<?=$member_name?>"/>
		<input type="hidden" name="loan_addr" value="<?=$mb_addr?>"/>
		<input type="hidden" name="reg_date" value="<?=$date?>"/>

		<h2>이 자 내 역 서</h2>
		<p class="sub-title">◎ 채무자</p>
		<table class="f-table ft01">
			<tr>
				<th>고객명</th>
				<td><?=$member_name?></td>
			</tr>
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

		<p class="sub-title">◎ 대출정보</p>
		<table class="f-table">
			<colgroup>
				<col width="20%" />
				<col width="20%" />
				<col width="20%" />
				<col width="20%" />
				<col width="20%" />
			</colgroup>
			<thead>
				<tr>
					<th>종별</th>
					<th>대출실행일</th>
					<th>대출만기일</th>
					<th>대출실행금액</th>
					<th>이자율</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select name="loan_kinds" id="loan_kinds" class="form-control input-sm">
							<option value="">::종별 선택::</option>
							<option value="1">PF</option>
							<option value="2">ABL</option>
							<option value="3">브릿지</option>
							<option value="4">기타담보대출</option>
						</select>
					</td>
					<td><input type="text" name="loan_sdate" value="<?=$row['loan_start_date']?>" class="form-control input-sm datepicker"/></td>
					<td><input type="text" name="loan_edate" value="<?=$row['loan_end_date']?>" class="form-control input-sm datepicker"/></td>
					<td class="price-txt">￦ <input type="text" name="loan_price" value="<?=number_format($row['loan_amount'])?>" class="form-control input-sm input01" onkeyup='inputNumberFormat(this)'/></td>
					<td class="price-txt"><input type="text" name="loan_eja_perc" value="<?=$row['loan_interest_rate']?>" class="form-control input-sm input01"/>%</td>
				</tr>
			</tbody>
		</table>
		
		<p class="sub-title txt02">◎ 납부 계좌 및 금액<button type="button" id="reCnt" class="recnt-btn">재계산</button></p>
		<table class="f-table" id="detailList">
			<colgroup>
				<col width="5%" />
				<col width="24%" />
				<col width="18%" />
				<col width="30%" />
				<col width="17%" />
			</colgroup>
			<thead>
				<tr>
					<th>순번</th>
					<th>금액</th>
					<th>예금주</th>
					<th>납부계좌</th>
					<th>비고</th>
				</tr>
			</thead>
			<tbody>
				<?
					for($i=0; $i<$cnt; $i++) {
						$LIST[$i] = sql_fetch_array($res);

						// 해당 상품 정산 테이블
						$bill_table = getBillTable($LIST[$i]['idx']);
						
						$bsql = "
								SELECT
									IFNULL(FLOOR(SUM(B.day_interest)),0) AS sum_price
								FROM
									cf_product A
								LEFT JOIN
									$bill_table B ON A.idx=B.product_idx
								WHERE 
									A.idx='".$LIST[$i]['idx']."' AND B.bill_date BETWEEN '".$start_date."' AND '".$end_date."'
								GROUP BY
									B.member_idx
						";
						$bres = sql_query($bsql);

						while($brow = sql_fetch_array($bres)) {
							$sum_price += $brow['sum_price'];
						}
						sql_free_result($bres);
						
						$account = $BANK[$LIST[$i]['bank_cd']].' '.$LIST[$i]['repay_acct_no'];
						
						$note = iconv_substr($LIST[$i]['title'], -3);  // iconv_substr <- 글자깨짐 방지

				?>
				<tr class="row-list">
					<td><?=$i+1?></td>
					<td class="price-txt"><input type="hidden" name="price[]" value="<?=$sum_price?>" class="read-input"/>￦ <?=number_format($sum_price)?></td>
					<td><input type="hidden" name="bank_name[]" value="<?=$LIST[$i]['cmf_nm']?>" class="read-input" /><?=$LIST[$i]['cmf_nm']?></td>
					<td><input type="hidden" name="bank_acc[]" value="<?=$account?>" class="read-input" /><?=$account?></td>
					<td><input type="text" name="note[]" value="<?=$note?>" class="form-control input-sm" /></td>
					<td style="border:none;">
						<input type="hidden" name="prdidx[]" value="<?=$LIST[$i]['idx']?>" class="form-control input-sm" />
						<div class="del-btn" onclick="delRow(this);">삭제</div>
					</td>
				</tr>
				<?
					}
					if(!$cnt) {
				?>
				<tr class="row-list">
					<td></td>
					<td class="price-txt">￦ <input type='text' name='price[]' class='form-control input-sm input-width-auto' onkeyup='inputNumberFormat(this)' /></td>
					<td><input type='text' name='bank_name[]' class='form-control input-sm' /></td>
					<td><input type='text' name='bank_acc[]' class='form-control input-sm' /></td>
					<td><input type='text' name='note[]' class='form-control input-sm' /></td>
					<td style="border:none;">
						<div class="del-btn" onclick="delRow(this);">삭제</div>
					</td>
				</tr>
				<?
					}
				?>
				<tr>
					<td>합계</td>
					<td class="price-txt"><span id="totalSum">￦ <?=number_format($sum_price)?></span></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td style="border: 0; padding: 0; text-align: left;">
						<button type="button" class="add-btn" onclick="addRow();">추가</button>
					</td>
				</tr>
			</tbody>
		</table>
	</form>

	<p class="txt03">위 내용과 같이 이자금액 <span id="ejaPrice" class="eja_price"><?=number_format($sum_price)?></span>원(<?=$start_date.'~'.$end_date?> <span> (<?=$days?>일치)</span>)를
	<br>납부해야함을 알려드립니다.</p>

	<ul class="bottom-fix-txt">
		<li><?=$date_y.'년 '.$date_m.'월 '.$date_d.'일'?></li>
		<li><?=$C_INFO['company_name']?> <span>(인)</span></li>
	</ul>

	<ul class="btn-box">
		<li><button class="btn btn-danger" onclick="history.back(-1);">취소</button></li>
		<li><button class="btn btn-primary" onclick="saveData();">저장</button></li>
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
			"<td></td>"+
			"<td class='price-txt'>￦ <input type='text' name='price[]' class='form-control input-sm input-width-auto' onkeyup='inputNumberFormat(this)' /></td>"+
			"<td><input type='text' name='bank_name[]' class='form-control input-sm' /></td>"+
			"<td><input type='text' name='bank_acc[]' class='form-control input-sm' /></td>"+
			"<td><input type='text' name='note[]' class='form-control input-sm' /></td>"+
			"<td style='border:none;'><div class='del-btn' onclick='delRow(this);'>삭제</div></td>"+
		"</tr>"
	);
}

// 재계산 클릭 시 합계 반영
$('#reCnt').on('click', function() {
	var total_count = $('input[name="price[]"]').length;
	var sum_amt = 0;
	

	// 추가된 행의 개수만큼 for문
	for(var i=0; i<total_count; i++) {
		var total_amount = $('input[name="price[]"]').eq(i).val();

		if(!total_amount) {

			alert('추가된 행에 빈 값이 있습니다.');
			return false;

		} else {

			total_amount = total_amount.replace(/,/g, "");

			sum_amt += parseInt(total_amount);

		}
	}

	sum_amt = sum_amt.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");

	// 합계
	$('#totalSum').text('￦ '+sum_amt);
	$('#ejaPrice').text(sum_amt);

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
	var loan_kinds = $("input[name='price[]']").length - 1;
		
	// 유효성 체크
	if($('select[name=loan_kinds]').val()=='') {
		alert('대출정보의 종별을 선택해주세요.');
		$('select[name=loan_kinds]').focus();
		return false;
	}

	for(var i=0; i<=loan_kinds; i++) {
		if($("input[name='price[]']").eq(i).val()==''){
				alert('금액 값이 입력되지 않았습니다.');
				$("input[name='price[]']").eq(i).focus();
				return false;
		} else if($("input[name='bank_name[]']").eq(i).val()==''){
				alert('예금주 값이 입력되지 않았습니다.');
				$("input[name='bank_name[]']").eq(i).focus();
				return false;
		} else if($("input[name='bank_acc[]']").eq(i).val()==''){
				alert('납부계좌 값이 입력되지 않았습니다.');
				$("input[name='bank_acc[]']").eq(i).focus();
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