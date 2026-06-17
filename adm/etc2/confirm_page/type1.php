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
# 완납확인서
#####################
$sql1 = "
	SELECT 
		A.mb_f_no, A.mb_no, A.mb_name, A.mb_addr1, A.mb_addr2, 
		A.mb_co_name, A.member_type, 
		B.category, B.mortgage_guarantees, B.loan_mb_f_no
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

// 카테고리
$category = '';
if($row['category']=='2' && $row['mortgage_guarantees']=='') {
	$category = '부동산';
} else if ($row['category']=='2' && $row['mortgage_guarantees']=='1') {
	$category = '주택담보';
}

// 주소
$mb_addr = $row['mb_addr1'].' '.$row['mb_addr2'];

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
# 완납확인서 부채내역
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

		<h2>완 납 확 인 서</h2>
		<p class="sub-title">◎ 채무자</p>
		<table class="f-table ft01">
			<tr>
				<th>고객명</th>
				<td><?=$member_name?></td>
			</tr>
			<? if($row['category']=='2' && $row['mortgage_guarantees']=='1') { ?>
			<tr>
				<th>생년월일</th>
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

		<p class="sub-title">◎ 부채내역 <span class="tbl-cf-txt">단위(원)</span></p>
		<table class="f-table" id="detailList" style="margin-bottom: 0;">
			<colgroup>
				<col width="5%" />
				<col width="18%" />
				<col width="18%" />
				<col width="17%" />
				<col width="17%" />
				<col width="20%" />
				<col width="5%" />
			</colgroup>
			<thead>
				<tr>
					<th></th>
					<th>대출금액</th>
					<th>대출실행일</th>
					<th>만료일</th>
					<th>은행</th>
					<th>납입계좌</th>
				</tr>
			</thead>
			<tbody>
				<?
				for($i=0,$num=1; $i<$cnt; $i++,$num++) {
					$list[$i] = sql_fetch_array($res);
				?>
				<tr>
					<td><?=$num?></td>
					<td><input type="hidden" name="recruit_amount[]" value="<?=$list[$i]['recruit_amount']?>" class="read-input"/>￦ <?=number_format($list[$i]['recruit_amount'])?></td>
					<td><input type="hidden" name="loan_start_date[]" value="<?=$list[$i]['loan_start_date']?>" class="read-input"/><?=$list[$i]['loan_start_date']?></td>
					<td><input type="hidden" name="loan_end_date[]" value="<?=$list[$i]['loan_end_date']?>" class="read-input"/><?=$list[$i]['loan_end_date']?></td>
					<td><input type="hidden" name="bank_name[]" value="<?=$BANK[$list[$i]['va_bank_code2']]?>" class="read-input"/><?=$BANK[$list[$i]['va_bank_code2']]?></td>
					<td><input type="hidden" name="bank_acc[]" value="<?=$list[$i]['virtual_account2']?>" class="read-input"/><?=$list[$i]['virtual_account2']?></td>
					<td style="border:none;"><input type="hidden" name="prdidx[]" value="<?=$list[$i]['idx']?>"><div class="del-btn" onclick="delRow(this);">삭제</div></td>
				</tr>
				<?
				}
				?>
			</tbody>
		</table>
	</form>
	<button class="add-btn" onclick="addRow();">추가</button>

	<p class="bottom-info">◎ <input type="text" value="<?=$member_name?>" class="read-input" style="text-align: center;"/>님은 위 내용의 채무를 전액 상환하였음을 알려드립니다.</p>

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

	$(tbl).find('tbody').append(
		"<tr>"+
			"<td></td>"+
			"<td class='price-txt'>￦ <input type='text' name='recruit_amount[]' class='form-control input-sm input-width-auto' onkeyup='inputNumberFormat(this)'/></td>"+
			"<td><input type='text' name='loan_start_date[]' class='form-control input-sm datepicker' /></td>"+
			"<td><input type='text' name='loan_end_date[]' class='form-control input-sm datepicker' /></td>"+
			"<td><input type='text' name='bank_name[]' class='form-control input-sm' /></td>"+
			"<td class='price-txt'><input type='text' name='bank_acc[]' class='form-control input-sm' /></td>"+
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
	
	if(confirm('등록하시겠습니까?')) {
		f.action = "./type_insert.php";
		f.method = "POST";
		f.target = "_self";
		f.submit();
	}
}


</script>


<? include_once ('../../admin.tail.php'); ?>